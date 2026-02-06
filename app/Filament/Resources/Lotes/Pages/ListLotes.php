<?php

namespace App\Filament\Resources\Lotes\Pages;

use App\Filament\Resources\Lotes\LoteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLotes extends ListRecords
{
    protected static string $resource = LoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nuevo Lote')
                ->icon('heroicon-o-plus')
                ->modalWidth('5xl')
                ->form(\App\Filament\Resources\Lotes\Schemas\LoteForm::getSchema())
                ->mutateFormDataUsing(function (array $data): array {
                    $data['usuario_registro_id'] = auth()->id();
                    $data['fecha_ingreso'] = $data['fecha_ingreso'] ?? now();
                    
                    if (isset($data['cantidad_inicial']) && (!isset($data['cantidad_actual']) || $data['cantidad_actual'] === 0)) {
                        $data['cantidad_actual'] = $data['cantidad_inicial'];
                    }
                    
                    return $data;
                })
                ->successNotificationTitle('Lote creado exitosamente')
                ->after(function ($record) {
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Stock actualizado')
                        ->body("Se agregaron {$record->cantidad_inicial} unidades al producto {$record->product->name}")
                        ->send();
                }),
        ];
    }
}
