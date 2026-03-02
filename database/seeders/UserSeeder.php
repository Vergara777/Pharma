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
            'last_name' => 'Sistema',
            'document_number' => '12345678',
            'birth_date' => '1990-01-01',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        $Admin->assignRole('admin');

        $Trabajador = User::create([
            'name' => 'Trabajador',
            'last_name' => 'Prueba',
            'document_number' => '87654321',
            'birth_date' => '1995-05-05',
            'email' => 'trabajador@gmail.com',
            'password' => Hash::make('trabajador123'),
            'role' => 'user',
        ]);

        $Trabajador->assignRole('trabajador');
    }
}