<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;



class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Analgesicos', 'description' => 'Medicamentos para el dolor', 'status' => 'active'],
            ['name' => 'Antibioticos', 'description' => 'Medicamentos para infecciones bacterianas', 'status' => 'active'],
            ['name' => 'Vitaminas y Suplementos', 'description' => 'Vitaminas y suplementos', 'status' => 'active'],
            ['name' => 'Cuidado Personal', 'description' => 'Productos de cuidado personal', 'status' => 'active'],
            ['name' => 'Pediatria', 'description' => 'Productos para niños', 'status' => 'active']
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
            ['name' => $category['name']],
                $category
            );
        }
    }
}