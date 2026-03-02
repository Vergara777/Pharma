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
            'description' => 'El acetaminofén, también conocido como paracetamol,
             es uno de los medicamentos más utilizados a nivel mundial para el tratamiento del dolor y la fiebre. A diferencia de otros analgésicos comunes, 
             se destaca por ser suave con el estómago,
             aunque requiere un control estricto de la dosis para evitar daños al hígado.',
            'image' => '',
            'price' => 25000,
            'cost' => 15000,
            'stock' => 100,
            'min_stock' => 10,
            'max_stock' => 500,
            'unit_name' => 'Tableta',
            'package_name' => 'Caja x 10 Tabletas',
            'units_per_package' => 10,
            'status' => 'active',
            'category_id' => 1, // Analgesicos
            'supplier_id' => 1, // Drogueria Central
        ]);

        Product::create([
            'sku' => 'VIT-5502',
            'name' => 'Vitamina C Efervescente',
            'description' => 'La vitamina C efervescente es una forma popular y conveniente de consumir este nutriente esencial. 
            Al disolverse en agua, crea una bebida burbujeante que facilita la absorción y es ideal para personas con dificultades para tragar pastillas.',
            'image' => '',
            'price' => 15500,
            'cost' => 9000,
            'stock' => 50,
            'min_stock' => 5,
            'max_stock' => 200,
            'unit_name' => 'Tableta',
            'package_name' => 'Tubo x 10 Tabletas',
            'units_per_package' => 10,
            'status' => 'active',
            'category_id' => 3, // Vitaminas
            'supplier_id' => 2, // PharmaVida
        ]);
    }
}
