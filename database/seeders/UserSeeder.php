<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@treeo.com'],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_admin' => true,
                'timezone' => 'Africa/Kampala',
            ]
        );

        // Create regular user
        User::firstOrCreate(
            ['email' => 'user@treeo.com'],
            [
                'name' => 'Regular User',
                'username' => 'user',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_admin' => false,
                'timezone' => 'Africa/Kampala',
            ]
        );
    }
}
