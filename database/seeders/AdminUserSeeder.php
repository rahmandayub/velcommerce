<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@velcommerce.test'],
            [
                'name' => 'Velcommerce Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('admin');

        $customer = User::firstOrCreate(
            ['email' => 'customer@velcommerce.test'],
            [
                'name' => 'Test Customer',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $customer->assignRole('customer');
    }
}
