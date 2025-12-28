<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $doctor = Role::firstOrCreate(['name' => 'doctor']);
        $patient = Role::firstOrCreate(['name' => 'patient']);

        // Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@docdot.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('super_admin');

        // Doctor
        $doc = User::firstOrCreate(
            ['email' => 'doctor@docdot.com'],
            [
                'name' => 'Dr. Budi Santoso',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $doc->assignRole('doctor');

        // User biasa
        $user = User::firstOrCreate(
            ['email' => 'user@docdot.com'],
            [
                'name' => 'Andi Pratama',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $user->assignRole('patient');

        $this->command->info('Demo accounts created:');
        $this->command->info('- Admin: admin@docdot.com / password');
        $this->command->info('- Doctor: doctor@docdot.com / password');
        $this->command->info('- User: user@docdot.com / password');
    }
}
