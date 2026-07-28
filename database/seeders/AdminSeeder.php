<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Super Admin Default
        User::updateOrCreate(
            ['email' => 'anan19@gmail.com'],
            [
                'name'              => 'Super Administrator',
                'password'          => Hash::make('12345'),
                'role'              => 'super_admin',
                'email_verified_at' => now(),
            ]
        );

        // 2. Admin Default
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'              => 'Risk Operations Manager',
                'password'          => Hash::make('12345'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 3. Regular Viewer User Default
        User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name'              => 'Supply Chain Analyst',
                'password'          => Hash::make('12345'),
                'role'              => 'viewer',
                'email_verified_at' => now(),
            ]
        );
    }
}
