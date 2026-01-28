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
        $rol_Admin = Role::create(['name' => 'admin']);
        $rol_Trabajador = Role::create(['name' => 'trabajador']);


        $permission_create_rol = Permission::create(['name' => 'Create roles']);
        $permission_read_rol = Permission::create(['name' => 'Read roles']);
        $permission_update_rol = Permission::create(['name' => 'Update roles']);
        $permission_delete_rol = Permission::create(['name' => 'Delete roles']);

        $permission_create_product = Permission::create(['name' => 'Create products']);
        $permission_read_product = Permission::create(['name' => 'Read products']);
        $permission_update_product = Permission::create(['name' => 'Update products']);
        $permission_delete_product = Permission::create(['name' => 'Delete products']);

        $permission_create_category = Permission::create(['name' => 'Create categories']);
        $permission_read_category = Permission::create(['name' => 'Read categories']);
        $permission_update_category = Permission::create(['name' => 'Update categories']);
        $permission_delete_category = Permission::create(['name' => 'Delete categories']);

        $permission_admin = [
            $permission_create_rol,
            $permission_read_rol,
            $permission_update_rol,
            $permission_delete_rol,
            $permission_create_product,
            $permission_read_product,
            $permission_update_product,
            $permission_delete_product,
            $permission_create_category,
            $permission_read_category,
            $permission_update_category,
            $permission_delete_category,
        ];

        $permission_trabajador = [
            $permission_read_product,
            $permission_read_category,
        ];

        $rol_Admin->syncPermissions($permission_admin);
        $rol_Trabajador->syncPermissions($permission_trabajador);
    }
}
