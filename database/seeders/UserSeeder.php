<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
        ]);

        $Admin->assignRole('admin');

        $Trabajador = User::create([
            'name' => 'Trabajador',
            'email' => 'trabajador@gmail.com',
            'password' => Hash::make('trabajador123'),
        ]);

        $Trabajador->assignRole('trabajador');
    }
}