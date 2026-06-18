<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions grouped by module
        $permissions = [
            'user' => [
                'user_add',
                'user_edit',
                'user_delete',
                'user_view',
            ],
            'role' => [
                'role_add',
                'role_edit',
                'role_delete',
                'role_view',
            ],
            'permission' => [
                'permission_add',
                'permission_edit',
                'permission_delete',
                'permission_view',
            ],
             'posts' => [
                'post_add',
                'post_edit',
                'post_delete',
                'post_view',
            ],
        ];

        // Create permissions
        foreach ($permissions as $module => $modulePermissions) {
            foreach ($modulePermissions as $permission) {
                Permission::firstOrCreate(
                    ['name' => $permission],
                    ['guard_name' => 'web']
                );
            }
        }

        // Create Admin role and assign all permissions
        $adminRole = Role::firstOrCreate(
            ['name' => 'Admin'],
            ['guard_name' => 'web']
        );

        // Get all permissions and sync with admin role
        $allPermissions = Permission::all();
        $adminRole->syncPermissions($allPermissions);

        // Create User role with limited permissions
        $userRole = Role::firstOrCreate(
            ['name' => 'User'],
            ['guard_name' => 'web']
        );

        // Assign view permissions to User role
        $userPermissions = Permission::whereIn('name', [
            'user_view',
            'role_view',
            'permission_view',
        ])->get();
        $userRole->syncPermissions($userPermissions);
    }
}
