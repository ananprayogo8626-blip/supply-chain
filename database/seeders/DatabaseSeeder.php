<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

User::create([
    'name' => 'User',
    'email' => 'user@gmail.com',
    'password' => Hash::make('12345'),
    'role' => 'viewer',
    'email_verified_at' => now(),
]);

        $this->call(SentimentSeeder::class);
        $this->call(WatchlistSeeder::class);
    }
}
