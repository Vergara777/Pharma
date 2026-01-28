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
            ['name' => 'Analgesicos'],
            ['name' => 'Antibioticos'],
            ['name' => 'Vitaminas y Suplementos'],
            ['name' => 'Cuidado Personal'],
            ['name' => 'Pediatria']
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}