<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DatabaseExportController extends Controller
{


public function download()
{
    try {
        \Log::info("=== BACKUP DOWNLOAD START ===");
        
        // 1. Clean old backups first
        \Log::info("Cleaning old backups...");
        try {
            Artisan::call('backup:clean');
            \Log::info("Clean completed: " . Artisan::output());
        } catch (\Exception $e) {
            \Log::warning("Clean failed: " . $e->getMessage());
        }

        // 2. Run backup with same parameters as debug
        \Log::info("Starting backup process...");
        $startTime = microtime(true);
        
        $exitCode = Artisan::call('backup:run', ['--only-db' => true]);
        $output = Artisan::output();
        
        $duration = round(microtime(true) - $startTime, 2);
        \Log::info("Backup completed in {$duration}s with exit code: $exitCode");
        \Log::info("Backup output: " . $output);

        if ($exitCode !== 0) {
            throw new \Exception("Backup command failed with exit code: $exitCode. Output: $output");
        }

        // 3. Wait for file system to sync (important for some systems)
        usleep(1000000); // 1 second

        // 4. Search for backup files in all possible locations
        $searchDirs = [
            storage_path('app'),
            storage_path('app/Laravel'),
            storage_path('app/backups'),
            storage_path('app/' . config('backup.backup.name', 'Laravel')),
        ];

        $foundFiles = [];
        foreach ($searchDirs as $dir) {
            if (is_dir($dir)) {
                $files = glob($dir . '/*.zip');
                foreach ($files as $file) {
                    $foundFiles[] = [
                        'path' => $file,
                        'size' => filesize($file),
                        'modified' => filemtime($file),
                        'age_seconds' => time() - filemtime($file)
                    ];
                }
            }
        }

        if (empty($foundFiles)) {
            \Log::error("No backup files found in directories: " . implode(', ', $searchDirs));
            throw new \Exception("No backup files found after running backup command");
        }

        // 5. Get the most recent file
        usort($foundFiles, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });

        $latestFile = $foundFiles[0];
        $backupFile = $latestFile['path'];
        $fileSize = $latestFile['size'];

        \Log::info("Found latest backup: $backupFile (Size: $fileSize bytes, Age: {$latestFile['age_seconds']}s)");

        // 6. Validate the backup file
        if (!file_exists($backupFile)) {
            throw new \Exception("Backup file does not exist: $backupFile");
        }

        if (!is_readable($backupFile)) {
            throw new \Exception("Backup file is not readable: $backupFile");
        }

        // 7. Check file size and content
        if ($fileSize < 1024) {
            $content = file_get_contents($backupFile);
            \Log::error("Backup file is too small. Size: $fileSize bytes. Content preview: " . substr($content, 0, 200));
            
            // Check if it's an error message
            if (stripos($content, 'error') !== false || stripos($content, 'exception') !== false) {
                throw new \Exception("Backup file contains error message: " . substr($content, 0, 200));
            }
            
            throw new \Exception("Backup file is too small ($fileSize bytes), indicating backup failure");
        }

        // 8. Verify it's a valid ZIP file
        if (!$this->isValidZipFile($backupFile)) {
            throw new \Exception("Backup file is not a valid ZIP archive");
        }

        // 9. Generate download filename
        $downloadName = 'backup-' . date('Y-m-d-His') . '.zip';
        
        \Log::info("Serving backup file: $backupFile as $downloadName");
        \Log::info("=== BACKUP DOWNLOAD SUCCESS ===");

        // 10. Return the file for download
        return response()->download(
            $backupFile,
            $downloadName,
            [
                'Content-Type' => 'application/zip',
                'Content-Length' => $fileSize,
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]
        );

    } catch (\Exception $e) {
        \Log::error("Backup download failed: " . $e->getMessage());
        \Log::error("Stack trace: " . $e->getTraceAsString());
        \Log::info("=== BACKUP DOWNLOAD FAILED ===");
        
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'debug_info' => [
                'timestamp' => now()->toISOString(),
                'backup_config' => [
                    'name' => config('backup.backup.name'),
                    'disks' => config('backup.backup.destination.disks'),
                    'databases' => config('backup.backup.source.databases'),
                ],
                'searched_directories' => $searchDirs ?? [],
                'found_files' => $foundFiles ?? [],
            ]
        ], 500);
    }
}

private function isValidZipFile($filePath)
{
    // Check if file starts with ZIP signature
    $handle = fopen($filePath, 'rb');
    if (!$handle) {
        return false;
    }
    
    $signature = fread($handle, 4);
    fclose($handle);
    
    // ZIP files start with 'PK\x03\x04' or 'PK\x05\x06' (empty zip) or 'PK\x07\x08'
    return substr($signature, 0, 2) === 'PK';
}


public function testBackupComparison()
{
    $results = [];
    
    try {
        // Test 1: Run backup exactly like the debug method
        $results['test1_debug_style'] = $this->runBackupTest('debug_style');
        
        // Test 2: Run backup exactly like the download method
        $results['test2_download_style'] = $this->runBackupTest('download_style');
        
        // Test 3: Check current environment
        $results['environment'] = [
            'php_memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'current_user' => get_current_user(),
            'web_request' => request()->isMethod('GET'),
        ];
        
        return response()->json($results, 200, [], JSON_PRETTY_PRINT);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'partial_results' => $results
        ], 500, [], JSON_PRETTY_PRINT);
    }
}

private function runBackupTest($style)
{
    $startTime = microtime(true);
    $testResult = [
        'style' => $style,
        'start_time' => date('Y-m-d H:i:s'),
    ];
    
    try {
        // Clean old backups
        Artisan::call('backup:clean');
        
        // Run backup based on style
        if ($style === 'debug_style') {
            $exitCode = Artisan::call('backup:run', ['--only-db' => true]);
            $output = Artisan::output();
        } else {
            // download_style - exactly as in your original method
            $exitCode = Artisan::call('backup:run --only-db', [], $output);
        }
        
        $testResult['backup_execution'] = [
            'exit_code' => $exitCode,
            'output_length' => strlen($output),
            'output_preview' => substr($output, 0, 200),
        ];
        
        // Wait and look for files
        usleep(500000); // 0.5 seconds
        
        $backupFiles = [];
        $searchDirs = [
            storage_path('app'),
            storage_path('app/Laravel'),
            storage_path('app/backups'),
        ];
        
        foreach ($searchDirs as $dir) {
            if (is_dir($dir)) {
                $files = glob($dir . '/*.zip');
                foreach ($files as $file) {
                    $age = time() - filemtime($file);
                    if ($age < 60) { // Only files created in last minute
                        $backupFiles[] = [
                            'path' => $file,
                            'size' => filesize($file),
                            'age_seconds' => $age,
                        ];
                    }
                }
            }
        }
        
        $testResult['found_files'] = $backupFiles;
        $testResult['duration'] = round(microtime(true) - $startTime, 2);
        $testResult['success'] = true;
        
    } catch (\Exception $e) {
        $testResult['error'] = $e->getMessage();
        $testResult['success'] = false;
        $testResult['duration'] = round(microtime(true) - $startTime, 2);
    }
    
    return $testResult;
}

public function diagnoseDatabaseDump()
{
    $results = [];
    
    try {
        // 1. Check database connection
        $database = config('database.default');
        $config = config("database.connections.{$database}");
        
        $results['database_config'] = [
            'default_connection' => $database,
            'driver' => $config['driver'] ?? 'not set',
            'host' => $config['host'] ?? 'not set',
            'port' => $config['port'] ?? 'not set',
            'database' => $config['database'] ?? 'not set',
            'username' => isset($config['username']) ? 'set' : 'not set',
            'password' => isset($config['password']) ? 'set' : 'not set',
        ];
        
        // 2. Test database connection
        try {
            DB::connection()->getPdo();
            $results['database_connection'] = 'SUCCESS';
            $results['database_name'] = DB::connection()->getDatabaseName();
        } catch (\Exception $e) {
            $results['database_connection'] = 'FAILED: ' . $e->getMessage();
        }
        
        // 3. Check mysqldump availability
        $mysqldumpPaths = [
            'mysqldump', // System PATH
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/opt/homebrew/bin/mysqldump', // macOS with Homebrew
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe', // Windows
            'C:\\xampp\\mysql\\bin\\mysqldump.exe', // XAMPP
            'C:\\wamp64\\bin\\mysql\\mysql8.0.21\\bin\\mysqldump.exe', // WAMP
        ];
        
        $mysqldumpFound = null;
        foreach ($mysqldumpPaths as $path) {
            $command = $path . ' --version 2>&1';
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0) {
                $mysqldumpFound = $path;
                $results['mysqldump_version'] = implode(' ', $output);
                break;
            }
        }
        
        $results['mysqldump_path'] = $mysqldumpFound ?: 'NOT FOUND';
        
        // 4. Test manual mysqldump
        if ($mysqldumpFound && $config['driver'] === 'mysql') {
            $testCommand = sprintf(
                '%s --host=%s --port=%d --user=%s --password=%s --single-transaction --no-data %s 2>&1',
                $mysqldumpFound,
                $config['host'],
                $config['port'] ?? 3306,
                $config['username'],
                $config['password'],
                $config['database']
            );
            
            $output = [];
            $returnCode = 0;
            exec($testCommand, $output, $returnCode);
            
            $results['manual_mysqldump_test'] = [
                'command' => str_replace($config['password'], '***', $testCommand),
                'return_code' => $returnCode,
                'output' => implode("\n", array_slice($output, 0, 10)), // First 10 lines
                'success' => $returnCode === 0
            ];
        }
        
        // 5. Check backup package configuration
        $backupConfig = config('backup');
        $results['backup_config'] = [
            'package_loaded' => !empty($backupConfig),
            'mysql_dump_path' => $backupConfig['database']['mysql']['dump']['dump_binary_path'] ?? 'default',
            'connection_name' => $backupConfig['backup']['source']['databases'][0] ?? 'not set',
        ];
        
        // 6. Operating system info
        $results['system_info'] = [
            'os' => PHP_OS,
            'php_os_family' => PHP_OS_FAMILY,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
            'path_separator' => PATH_SEPARATOR,
        ];
        
        return response()->json($results, 200, [], JSON_PRETTY_PRINT);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'partial_results' => $results
        ], 500, [], JSON_PRETTY_PRINT);
    }
}

// Add this method to your controller to fix the backup configuration
public function fixBackupConfig()
{
    try {
        // 1. Find mysqldump
        $mysqldumpPaths = [
            'mysqldump',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/opt/homebrew/bin/mysqldump',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.21\\bin\\mysqldump.exe',
        ];
        
        $mysqldumpPath = null;
        foreach ($mysqldumpPaths as $path) {
            $output = [];
            $returnCode = 0;
            exec($path . ' --version 2>&1', $output, $returnCode);
            if ($returnCode === 0) {
                $mysqldumpPath = $path;
                break;
            }
        }
        
        if (!$mysqldumpPath) {
            return response()->json([
                'success' => false,
                'message' => 'mysqldump not found. Please install MySQL client tools.',
                'suggestions' => [
                    'Ubuntu/Debian: sudo apt-get install mysql-client',
                    'CentOS/RHEL: sudo yum install mysql',
                    'macOS: brew install mysql-client',
                    'Windows: Install MySQL or use XAMPP/WAMP'
                ]
            ]);
        }
        
        // 2. Update backup configuration
        $configPath = config_path('backup.php');
        if (!file_exists($configPath)) {
            // Publish the config file
            Artisan::call('vendor:publish', [
                '--provider' => 'Spatie\\Backup\\BackupServiceProvider'
            ]);
        }
        
        // 3. Create a custom backup config update
        $customConfig = [
            'backup' => [
                'name' => config('app.name', 'Laravel'),
                'source' => [
                    'files' => [
                        'include' => [
                            // base_path(), // Comment out if you only want database
                        ],
                        'exclude' => [
                            base_path('vendor'),
                            base_path('node_modules'),
                        ],
                    ],
                    'databases' => [
                        config('database.default')
                    ],
                ],
                'database' => [
                    'mysql' => [
                        'dump' => [
                            'dump_binary_path' => $mysqldumpPath,
                            'use_single_transaction' => true,
                            'timeout' => 60 * 5, // 5 minutes
                            'exclude_tables' => [],
                            'add_extra_option' => '--skip-comments --skip-dump-date',
                        ],
                    ],
                ],
                'destination' => [
                    'filename_prefix' => '',
                    'disks' => [
                        'local',
                    ],
                ],
            ],
        ];
        
        return response()->json([
            'success' => true,
            'mysqldump_path' => $mysqldumpPath,
            'message' => 'Configuration updated. Now test the backup again.',
            'config_file' => $configPath,
            'suggested_config' => $customConfig
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}


public function quickBackupFix()
{
    try {
        // 1. Update the backup config temporarily in memory
        config([
            'backup.backup.database.mysql.dump.dump_binary_path' => $this->findMysqlDump(),
            'backup.backup.database.mysql.dump.use_single_transaction' => true,
            'backup.backup.database.mysql.dump.timeout' => 300,
            'backup.backup.database.mysql.dump.add_extra_option' => '--skip-comments --skip-dump-date --single-transaction',
        ]);
        
        // 2. Test the backup
        \Log::info("=== QUICK BACKUP FIX TEST ===");
        
        Artisan::call('backup:clean');
        $exitCode = Artisan::call('backup:run', ['--only-db' => true]);
        $output = Artisan::output();
        
        \Log::info("Backup result - Exit code: $exitCode");
        \Log::info("Backup output: $output");
        
        // 3. Check for backup files
        sleep(2); // Wait 2 seconds
        
        $backupFiles = [];
        $searchDirs = [
            storage_path('app/Laravel'),
            storage_path('app/backups'),
            storage_path('app'),
        ];
        
        foreach ($searchDirs as $dir) {
            if (is_dir($dir)) {
                $files = glob($dir . '/*.zip');
                foreach ($files as $file) {
                    if (time() - filemtime($file) < 300) { // Created in last 5 minutes
                        $backupFiles[] = [
                            'path' => $file,
                            'size' => filesize($file),
                            'size_mb' => round(filesize($file) / 1024 / 1024, 2),
                            'created' => date('Y-m-d H:i:s', filemtime($file)),
                        ];
                    }
                }
            }
        }
        
        return response()->json([
            'mysqldump_path' => $this->findMysqlDump(),
            'backup_exit_code' => $exitCode,
            'backup_successful' => $exitCode === 0,
            'output_preview' => substr($output, 0, 500),
            'backup_files' => $backupFiles,
            'largest_backup' => !empty($backupFiles) ? max(array_column($backupFiles, 'size')) : 0,
        ], 200, [], JSON_PRETTY_PRINT);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
}

private function findMysqlDump()
{
    $paths = [
        'mysqldump',
        '/usr/bin/mysqldump',
        '/usr/local/bin/mysqldump',
        '/opt/homebrew/bin/mysqldump',
        'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
        'C:\\xampp\\mysql\\bin\\mysqldump.exe',
        'C:\\wamp64\\bin\\mysql\\mysql8.0.21\\bin\\mysqldump.exe',
    ];
    
    foreach ($paths as $path) {
        $output = [];
        $returnCode = 0;
        exec($path . ' --version 2>&1', $output, $returnCode);
        if ($returnCode === 0) {
            return $path;
        }
    }
    
    return 'mysqldump'; // fallback
}

public function debugBackup()
{
    try {
        // Find the backup file
        $backupPaths = [
            storage_path('app/Laravel'),
            storage_path('app/backups'),
            storage_path('app/backup-temp'),
            storage_path('app')
        ];

        $backupFile = null;
        foreach ($backupPaths as $path) {
            if (!file_exists($path)) continue;
            $files = glob($path.'/*.zip');
            if (!empty($files)) {
                $backupFile = end($files);
                break;
            }
        }

        if (!$backupFile) {
            return response()->json(['error' => 'No backup file found']);
        }

        $fileSize = filesize($backupFile);
        $content = file_get_contents($backupFile);
        
        return response()->json([
            'file_path' => $backupFile,
            'file_size' => $fileSize,
            'content_preview' => substr($content, 0, 1000),
            'content_hex' => bin2hex(substr($content, 0, 100)),
            'is_zip' => substr($content, 0, 2) === 'PK', // ZIP files start with 'PK'
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
}


public function debugBackupFull()
{
    $debug = [];
    
    try {
        // 1. Check if backup package is installed
        $debug['package_installed'] = class_exists('\Spatie\Backup\Commands\BackupCommand');
        
        // 2. Check configuration
        $debug['config'] = [
            'backup_name' => config('backup.backup.name'),
            'destination_disks' => config('backup.backup.destination.disks'),
            'source_databases' => config('backup.backup.source.databases'),
            'include_files' => config('backup.backup.source.files.include'),
        ];
        
        // 3. Check database connection
        try {
            DB::connection()->getPdo();
            $debug['database_connection'] = 'OK';
            $debug['database_name'] = DB::connection()->getDatabaseName();
        } catch (\Exception $e) {
            $debug['database_connection'] = 'FAILED: ' . $e->getMessage();
        }
        
        // 4. Check disk configuration
        $disk = config('backup.backup.destination.disks')[0] ?? 'local';
        try {
            $diskInstance = \Storage::disk($disk);
            $debug['disk_config'] = 'OK';
            
            // Try different methods to get disk path based on Laravel version
            try {
                if (method_exists($diskInstance->getAdapter(), 'getPathPrefix')) {
                    $debug['disk_root'] = $diskInstance->getAdapter()->getPathPrefix();
                } else {
                    // For newer Laravel versions
                    $debug['disk_root'] = config("filesystems.disks.{$disk}.root", 'Not configured');
                }
            } catch (\Exception $pathException) {
                $debug['disk_root'] = 'Could not determine path: ' . $pathException->getMessage();
            }
        } catch (\Exception $e) {
            $debug['disk_config'] = 'FAILED: ' . $e->getMessage();
        }
        
        // 5. Check permissions
        $storagePath = storage_path('app');
        $debug['storage_permissions'] = [
            'path' => $storagePath,
            'exists' => file_exists($storagePath),
            'writable' => is_writable($storagePath),
            'permissions' => substr(sprintf('%o', fileperms($storagePath)), -4),
        ];
        
        // 6. Run backup with detailed logging
        \Log::info("=== BACKUP DEBUG START ===");
        
        // Capture all output
        $output = new \Symfony\Component\Console\Output\BufferedOutput();
        $exitCode = Artisan::call('backup:run --only-db --disable-notifications', [], $output);
        
        $outputText = $output->fetch();
        \Log::info("Backup command output: " . $outputText);
        
        $debug['backup_execution'] = [
            'exit_code' => $exitCode,
            'output' => $outputText,
        ];
        
        // 7. Check for backup files immediately after
        usleep(1000000); // Wait 1 second
        
        $backupFiles = [];
        $searchPaths = [
            storage_path('app'),
            storage_path('app/Laravel'),
            storage_path('app/backups'),
            storage_path('app/' . config('backup.backup.name', 'Laravel')),
        ];
        
        foreach ($searchPaths as $path) {
            if (file_exists($path)) {
                $files = glob($path . '/*.zip');
                foreach ($files as $file) {
                    $backupFiles[] = [
                        'path' => $file,
                        'size' => filesize($file),
                        'modified' => filemtime($file),
                        'readable' => is_readable($file),
                    ];
                }
            }
        }
        
        $debug['backup_files'] = $backupFiles;
        
        // 8. If we found a small file, examine its contents
        if (!empty($backupFiles)) {
            $latestFile = collect($backupFiles)->sortByDesc('modified')->first();
            if ($latestFile['size'] < 5000) { // If smaller than 5KB
                $content = file_get_contents($latestFile['path']);
                $debug['small_file_analysis'] = [
                    'path' => $latestFile['path'],
                    'size' => $latestFile['size'],
                    'content_preview' => substr($content, 0, 500),
                    'is_zip_format' => substr($content, 0, 2) === 'PK',
                    'contains_error' => (strpos($content, 'error') !== false || strpos($content, 'Error') !== false),
                ];
            }
        }
        
        \Log::info("=== BACKUP DEBUG END ===");
        
        return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
        
    } catch (\Exception $e) {
        $debug['exception'] = [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ];
        
        return response()->json($debug, 500, [], JSON_PRETTY_PRINT);
    }
}

public function manualBackup()
{
    try {
        $database = config('database.default');
        $config = config("database.connections.{$database}");
        
        if ($config['driver'] !== 'mysql') {
            throw new \Exception('Manual backup only supports MySQL databases');
        }
        
        $host = $config['host'];
        $port = $config['port'] ?? 3306;
        $username = $config['username'];
        $password = $config['password'];
        $dbname = $config['database'];
        
        // Create backup filename
        $backupFile = storage_path('app/manual-backup-' . date('Y-m-d-His') . '.sql');
        
        // Build mysqldump command
        $command = sprintf(
            'mysqldump --host=%s --port=%d --user=%s --password=%s --single-transaction --routines --triggers %s > %s',
            escapeshellarg($host),
            $port,
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($dbname),
            escapeshellarg($backupFile)
        );
        
        // Execute the command
        $output = [];
        $returnCode = 0;
        exec($command . ' 2>&1', $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception('mysqldump failed: ' . implode("\n", $output));
        }
        
        // Check if file was created and has content
        if (!file_exists($backupFile)) {
            throw new \Exception('Backup file was not created');
        }
        
        $fileSize = filesize($backupFile);
        if ($fileSize < 1024) {
            $content = file_get_contents($backupFile);
            throw new \Exception("Backup file is too small ($fileSize bytes). Content: " . substr($content, 0, 200));
        }
        
        // Compress the SQL file
        $zipFile = storage_path('app/manual-backup-' . date('Y-m-d-His') . '.zip');
        $zip = new \ZipArchive();
        
        if ($zip->open($zipFile, \ZipArchive::CREATE) !== TRUE) {
            throw new \Exception('Could not create ZIP file');
        }
        
        $zip->addFile($backupFile, basename($backupFile));
        $zip->close();
        
        // Remove the SQL file, keep only ZIP
        unlink($backupFile);
        
        $zipSize = filesize($zipFile);
        \Log::info("Manual backup created: $zipFile (Size: $zipSize bytes)");
        
        return response()->download(
            $zipFile,
            'manual-backup-' . date('Y-m-d-His') . '.zip',
            [
                'Content-Type' => 'application/zip',
                'Content-Length' => $zipSize
            ]
        );
        
    } catch (\Exception $e) {
        \Log::error("Manual backup failed: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'suggestion' => 'Try the debug method first to identify the issue'
        ], 500);
    }
}
public function simpleBackupDebug()
{
    $debug = [];
    
    try {
        // 1. Test database connection
        try {
            $pdo = DB::connection()->getPdo();
            $debug['database'] = [
                'status' => 'Connected',
                'name' => DB::connection()->getDatabaseName(),
                'driver' => DB::connection()->getDriverName(),
            ];
        } catch (\Exception $e) {
            $debug['database'] = [
                'status' => 'Failed',
                'error' => $e->getMessage()
            ];
        }
        
        // 2. Check backup package
        $debug['backup_package'] = class_exists('\Spatie\Backup\Commands\BackupCommand') ? 'Installed' : 'Not found';
        
        // 3. Check basic config
        $debug['config'] = [
            'backup_name' => config('backup.backup.name', 'Not set'),
            'destination_disks' => config('backup.backup.destination.disks', []),
            'source_databases' => config('backup.backup.source.databases', []),
        ];
        
        // 4. Check storage paths
        $paths = [
            'storage_app' => storage_path('app'),
            'backup_laravel' => storage_path('app/Laravel'),
            'backup_backups' => storage_path('app/backups'),
        ];
        
        foreach ($paths as $name => $path) {
            $debug['paths'][$name] = [
                'path' => $path,
                'exists' => file_exists($path),
                'writable' => file_exists($path) ? is_writable($path) : false,
                'permissions' => file_exists($path) ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A',
            ];
        }
        
        // 5. Run backup command and capture everything
        \Log::info("=== SIMPLE BACKUP TEST START ===");
        
        // Clean old backups first
        $cleanOutput = '';
        try {
            Artisan::call('backup:clean');
            $cleanOutput = Artisan::output();
        } catch (\Exception $e) {
            $cleanOutput = 'Clean failed: ' . $e->getMessage();
        }
        
        // Run backup
        $backupOutput = '';
        $exitCode = null;
        try {
            $exitCode = Artisan::call('backup:run', ['--only-db' => true]);
            $backupOutput = Artisan::output();
        } catch (\Exception $e) {
            $backupOutput = 'Backup failed: ' . $e->getMessage();
        }
        
        $debug['backup_execution'] = [
            'clean_output' => $cleanOutput,
            'backup_output' => $backupOutput,
            'exit_code' => $exitCode,
        ];
        
        // 6. Look for backup files
        $backupFiles = [];
        $searchDirs = [
            storage_path('app'),
            storage_path('app/Laravel'),
            storage_path('app/backups'),
            storage_path('app/' . config('backup.backup.name', 'Laravel')),
        ];
        
        foreach ($searchDirs as $dir) {
            if (is_dir($dir)) {
                $files = glob($dir . '/*.zip');
                foreach ($files as $file) {
                    $size = filesize($file);
                    $backupFiles[] = [
                        'file' => $file,
                        'size' => $size,
                        'size_human' => $this->formatBytes($size),
                        'modified' => date('Y-m-d H:i:s', filemtime($file)),
                        'age_minutes' => round((time() - filemtime($file)) / 60, 1),
                    ];
                }
            }
        }
        
        // Sort by modification time
        usort($backupFiles, function($a, $b) {
            return filemtime($b['file']) - filemtime($a['file']);
        });
        
        $debug['found_backups'] = $backupFiles;
        
        // 7. If we have a recent small file, examine it
        if (!empty($backupFiles)) {
            $latest = $backupFiles[0];
            if ($latest['size'] < 5000 && $latest['age_minutes'] < 10) {
                $content = file_get_contents($latest['file']);
                $debug['small_file_analysis'] = [
                    'file' => $latest['file'],
                    'size' => $latest['size'],
                    'first_100_chars' => substr($content, 0, 100),
                    'is_zip' => substr($content, 0, 2) === 'PK',
                    'contains_sql' => strpos($content, 'CREATE TABLE') !== false || strpos($content, 'INSERT INTO') !== false,
                    'error_indicators' => [
                        'contains_error' => stripos($content, 'error') !== false,
                        'contains_exception' => stripos($content, 'exception') !== false,
                        'contains_failed' => stripos($content, 'failed') !== false,
                    ]
                ];
            }
        }
        
        \Log::info("=== SIMPLE BACKUP TEST END ===");
        
        return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'partial_debug' => $debug ?? []
        ], 500, [], JSON_PRETTY_PRINT);
    }
}

private function formatBytes($size, $precision = 2)
{
    $base = log($size, 1024);
    $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}

}
