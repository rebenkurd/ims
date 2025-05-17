<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

    //    $admin = User::create([
    //         'name' => 'Admin',
    //         'username' => 'admin',
    //         'email' => 'admin@example.com',
    //         'phone' => '1234567890',
    //         'dob' => now()->subYears(rand(18, 50)),
    //         'mobile' => '0987654321',
    //         'role_id' => 1,
    //         'email_verified_at' => now(),
    //         'password' => bcrypt(123),
    //         'image' => 'https://placehold.co/400x400',
    //         'created_by' => 1, // assuming user with ID 1 created the record
    //         'deleted_by' => null,
    //         'status' => true,
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);

        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
            CustomersSeeder::class,
            SuppliersSeeder::class,
            SalesSeeder::class,
            PurchasesSeeder::class,
            SaleItemSeeder::class,
            PurchaseItemSeeder::class,
        ]);
    }
}
