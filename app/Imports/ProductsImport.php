<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToModel, WithHeadingRow, WithUpserts
{
    public function model(array $row)
    {
        return new Product([
            'sku'               => $row['sku'] ?? null,
            'name'              => $row['name'] ?? null,
            'description'       => $row['description'] ?? null,
            'price'             => $row['price'] ?? 0,
            'cost'              => $row['cost'] ?? 0,
            'stock'             => $row['stock'] ?? 0,
            'min_stock'         => $row['min_stock'] ?? 5,
            'max_stock'         => $row['max_stock'] ?? 100,
            'unit_name'         => $row['unit_name'] ?? 'Unidad',
            'package_name'      => $row['package_name'] ?? null,
            'units_per_package' => $row['units_per_package'] ?? 1,
            'price_unit'        => $row['price_unit'] ?? null,
            'price_package'     => $row['price_package'] ?? null,
            'shelf'             => $row['shelf'] ?? null,
            'row'               => $row['row'] ?? null,
            'position'          => $row['position'] ?? null,
            'expires_at'        => $row['expires_at'] ?? null,
            'status'            => $row['status'] ?? 'active',
            'category_id'       => $row['category_id'] ?? null,
            'supplier_id'       => $row['supplier_id'] ?? null,
        ]);
    }

    public function uniqueBy()
    {
        return 'sku';
    }
}
