<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DoctorRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create doctor role if not exists
        $doctorRole = Role::firstOrCreate(
            ['name' => 'doctor', 'guard_name' => 'web']
        );

        // Basic permissions for doctor
        $doctorPermissions = [
            'view_drug',
            'view_any_drug',
            'view_health::article',
            'view_any_health::article',
            'view_medical::document',
            'view_any_medical::document',
        ];

        foreach ($doctorPermissions as $permissionName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web']
            );

            if (!$doctorRole->hasPermissionTo($permission)) {
                $doctorRole->givePermissionTo($permission);
            }
        }

        $this->command->info('Doctor role created with basic permissions.');
    }
}
