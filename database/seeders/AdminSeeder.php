<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah Super Admin sudah ada
        $existingAdmin = User::where('email', 'admin@supplyguard.com')->first();
        
        if (!$existingAdmin) {
            // Buat akun Super Admin default
            User::create([
                'name' => 'Super Administrator',
                'email' => 'admin@supplyguard.com',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('✓ Akun Super Admin berhasil dibuat');
            $this->command->info('  Email: admin@supplyguard.com');
            $this->command->info('  Password: admin123');
        } else {
            // Update role jika user sudah ada tapi belum super_admin
            if ($existingAdmin->role !== 'super_admin') {
                $existingAdmin->update(['role' => 'super_admin']);
                $this->command->info('✓ Role user admin@supplyguard.com diperbarui menjadi Super Admin');
            } else {
                $this->command->info('✓ Akun Super Admin sudah ada');
            }
        }
    }
}
