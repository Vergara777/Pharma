<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rol_Admin = Role::firstOrCreate(['name' => 'admin']);
        $rol_Trabajador = Role::firstOrCreate(['name' => 'trabajador']);


        $permission_create_rol = Permission::firstOrCreate(['name' => 'Create roles']);
        $permission_read_rol = Permission::firstOrCreate(['name' => 'Read roles']);
        $permission_update_rol = Permission::firstOrCreate(['name' => 'Update roles']);
        $permission_delete_rol = Permission::firstOrCreate(['name' => 'Delete roles']);

        $permission_create_product = Permission::firstOrCreate(['name' => 'Create products']);
        $permission_read_product = Permission::firstOrCreate(['name' => 'Read products']);
        $permission_update_product = Permission::firstOrCreate(['name' => 'Update products']);
        $permission_delete_product = Permission::firstOrCreate(['name' => 'Delete products']);

        $permission_create_category = Permission::firstOrCreate(['name' => 'Create categories']);
        $permission_read_category = Permission::firstOrCreate(['name' => 'Read categories']);
        $permission_update_category = Permission::firstOrCreate(['name' => 'Update categories']);
        $permission_delete_category = Permission::firstOrCreate(['name' => 'Delete categories']);

        $permission_admin = [
            'Create roles',
            'Read roles',
            'Update roles',
            'Delete roles',
            'Create products',
            'Read products',
            'Update products',
            'Delete products',
            'Create categories',
            'Read categories',
            'Update categories',
            'Delete categories',
        ];

        $permission_trabajador = [
            'Read products',
            'Read categories',
        ];

        $rol_Admin->syncPermissions($permission_admin);
        $rol_Trabajador->syncPermissions($permission_trabajador);
    }
}
