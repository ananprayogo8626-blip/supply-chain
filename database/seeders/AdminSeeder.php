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
        $existingAdmin = User::where('email', 'anan19@gmail.com')->first();
        
        if (!$existingAdmin) {
            // Buat akun Super Admin default
            User::create([
                'name' => 'Super Administrator',
                'email' => 'anan19@gmail.com',
                'password' => Hash::make('12345'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('✓ Akun Super Admin berhasil dibuat');
            $this->command->info('  Email: anan19@gmail.com');
            $this->command->info('  Password: 12345');
        } else {
            // Update role jika user sudah ada tapi belum super_admin
            if ($existingAdmin->role !== 'super_admin') {
                $existingAdmin->update(['role' => 'super_admin']);
                $this->command->info('✓ Role user anan19@gmail.com diperbarui menjadi Super Admin');
            } else {
                $this->command->info('✓ Akun Super Admin sudah ada');
            }
        }
    }
}
