<?php

namespace App\Imports;

use App\Models\Cliente;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ClientesImport implements ToModel, WithHeadingRow, WithUpserts
{
    public function model(array $row)
    {
        return new Cliente([
            'name'      => $row['name'] ?? $row['nombre'] ?? null,
            'document'  => $row['document'] ?? $row['documento'] ?? null,
            'email'     => $row['email'] ?? null,
            'phone'     => $row['phone'] ?? $row['telefono'] ?? null,
            'address'   => $row['address'] ?? $row['direccion'] ?? null,
            'is_active' => $row['is_active'] ?? $row['activo'] ?? true,
        ]);
    }

    public function uniqueBy()
    {
        return 'document';
    }
}
