<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage themes',
            'manage products',
            'manage orders',
            'manage users',
            'manage settings',
            'assign roles',
            'manage stock',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $user = Role::firstOrCreate(['name' => 'user']);

        $superAdmin->syncPermissions(Permission::all());
        $admin->syncPermissions([
            'manage themes',
            'manage products',
            'manage orders',
            'manage users',
            'manage stock',
        ]);
        $manager->syncPermissions(['manage orders', 'manage stock']);
        $user->syncPermissions([]);
    }
}
