<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Check if admin user already exists
        $adminExists = DB::table('users')
            ->where('email', 'admin@resikan.com')
            ->exists();

        if (!$adminExists) {
            DB::table('users')->insert([
                'id' => Str::uuid(),
                'fullname' => 'Admin Resikan',
                'email' => 'admin@resikan.com',
                'phone' => '08123456789',
                'address' => 'Jakarta, Indonesia',
                'password_hash' => Hash::make('password'), 
                'password' => Hash::make('password'), 
                'email_verified_at' => now(),
                'metadata' => json_encode(['role' => 'admin', 'is_admin' => true]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info('✅ Admin user created successfully!');
            $this->command->info('📧 Email: admin@resikan.com');
            $this->command->info('🔑 Password: password');
            $this->command->warn('⚠️  Please change the password after first login!');
        } else {
            $this->command->warn('⚠️  Admin user already exists. Skipping...');
        }
    }
}
