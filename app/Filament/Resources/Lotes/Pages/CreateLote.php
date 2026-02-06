<?php

namespace App\Filament\Resources\Lotes\Pages;

use App\Filament\Resources\Lotes\LoteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLote extends CreateRecord
{
    protected static string $resource = LoteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['usuario_registro_id'] = auth()->id();
        $data['fecha_ingreso'] = $data['fecha_ingreso'] ?? now();
        
        // Si la cantidad actual no se especificó, usar la cantidad inicial
        if (!isset($data['cantidad_actual']) || $data['cantidad_actual'] === 0) {
            $data['cantidad_actual'] = $data['cantidad_inicial'];
        }
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
