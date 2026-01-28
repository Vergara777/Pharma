<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Drogueria Central S.A.',
                'phone' => '3001234567',
                'email' => 'ventas@central.com',
                'address' => 'Av. Principal #123'
            ],
            [
                'name' => 'Laboratorios PharmaVida',
                'phone' => '3159876543',
                'email' => 'contacto@pharmavida.com',
                'address' => 'Zona Industrial Local 4'
            ]
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
