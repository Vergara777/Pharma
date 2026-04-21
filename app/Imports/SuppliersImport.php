<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class SuppliersImport implements ToModel, WithHeadingRow, WithUpserts
{
    public function model(array $row)
    {
        return new Supplier([
            'name'    => $row['name'] ?? $row['nombre'] ?? null,
            'phone'   => $row['phone'] ?? $row['telefono'] ?? null,
            'email'   => $row['email'] ?? null,
            'status'  => $row['status'] ?? $row['estado'] ?? 'active',
            'address' => $row['address'] ?? $row['direccion'] ?? null,
        ]);
    }

    public function uniqueBy()
    {
        return 'name';
    }
}
