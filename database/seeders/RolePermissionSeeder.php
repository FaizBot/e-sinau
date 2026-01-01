<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // =====================
        // PERMISSIONS
        // =====================
        $permissions = [
            // admin
            'manage-users',
            'manage-roles',

            // teacher
            'manage-classes',
            'manage-grades',

            // student
            'view-classes',
            'view-grades',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // =====================
        // ROLES
        // =====================
        $admin   = Role::firstOrCreate(['name' => 'admin']);
        $teacher = Role::firstOrCreate(['name' => 'teacher']);
        $student = Role::firstOrCreate(['name' => 'student']);

        // =====================
        // ASSIGN PERMISSIONS
        // =====================
        $admin->syncPermissions([
            'manage-users',
            'manage-roles',
            'manage-classes',
            'manage-grades',
        ]);

        $teacher->syncPermissions([
            'manage-classes',
            'manage-grades',
        ]);

        $student->syncPermissions([
            'view-classes',
            'view-grades',
        ]);
    }
}
