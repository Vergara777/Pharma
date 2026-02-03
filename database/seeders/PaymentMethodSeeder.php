<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'name' => 'Efectivo',
                'code' => 'cash',
                'description' => 'Pago en efectivo',
                'is_active' => true,
            ],
            [
                'name' => 'Tarjeta de Crédito',
                'code' => 'credit_card',
                'description' => 'Pago con tarjeta de crédito',
                'is_active' => true,
            ],
            [
                'name' => 'Tarjeta de Débito',
                'code' => 'debit_card',
                'description' => 'Pago con tarjeta de débito',
                'is_active' => true,
            ],
            [
                'name' => 'Transferencia Bancaria',
                'code' => 'bank_transfer',
                'description' => 'Transferencia bancaria',
                'is_active' => true,
            ],
            [
                'name' => 'Nequi',
                'code' => 'nequi',
                'description' => 'Pago por Nequi',
                'is_active' => true,
            ],
            [
                'name' => 'Daviplata',
                'code' => 'daviplata',
                'description' => 'Pago por Daviplata',
                'is_active' => true,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }
    }
}
