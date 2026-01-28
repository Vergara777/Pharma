<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'sku' => 'MED-7701',
            'name' => 'Acetaminofen 500mg',
            'description' => 'Caja por 10 tabletas',
            'image' => '',
            'price' => 25.000,
            'stock' => 100,
            'stock_minimum' => 10,
            'stock_maximum' => 500,
            'category_id' => 1, // Analgesicos
            'supplier_id' => 1, // Drogueria Central
        ]);

        Product::create([
            'sku' => 'VIT-5502',
            'name' => 'Vitamina C Efervescente',
            'description' => 'Tubo por 10 tabletas sabor naranja',
            'image' => '',
            'price' => 15.500,
            'stock' => 50,
            'stock_minimum' => 5,
            'stock_maximum' => 200,
            'category_id' => 3, // Vitaminas
            'supplier_id' => 2, // PharmaVida
        ]);
    }
}
