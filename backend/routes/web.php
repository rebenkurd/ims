<?php

use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\DatabaseExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/purchases/{id}/invoice', [PurchaseController::class, 'showInvoice'])
    ->name('purchases.invoice')
    ->middleware('auth');

Route::get('/debug-backup', function() {
    $output = new \Symfony\Component\Console\Output\BufferedOutput();
    $exitCode = Artisan::call('backup:run', ['--only-db' => true], $output);
    
    return [
        'exit_code' => $exitCode,
        'output' => $output->fetch(),
        'config' => [
            'disks' => config('backup.backup.destination.disks'),
            'databases' => config('backup.backup.source.databases'),
            'name' => config('backup.backup.name'),
        ]
    ];
});

Route::get('/debug-backup-content', [DatabaseExportController::class, 'debugBackup']);
Route::get('/debug-backup-full', [DatabaseExportController::class, 'debugBackupFull']);
Route::get('/manual-backup', [DatabaseExportController::class, 'manualBackup']);
Route::get('/simple-backup-debug', [DatabaseExportController::class, 'simpleBackupDebug']);
Route::get('/test-backup-comparison', [DatabaseExportController::class, 'testBackupComparison']);

Route::get('/diagnose-database-dump', [DatabaseExportController::class, 'diagnoseDatabaseDump']);
Route::get('/fix-backup-config', [DatabaseExportController::class, 'fixBackupConfig']);
Route::get('/quick-backup-fix', [DatabaseExportController::class, 'quickBackupFix']);
require __DIR__.'/auth.php';
