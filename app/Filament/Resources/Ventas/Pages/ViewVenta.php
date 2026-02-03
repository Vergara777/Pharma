<?php

namespace App\Filament\Resources\Ventas\Pages;

use App\Filament\Resources\Ventas\VentasResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

class ViewVenta extends ViewRecord
{
    protected static string $resource = VentasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print_invoice')
                ->label('Ver Factura')
                ->icon('heroicon-o-printer')
                ->color('warning')
                ->url(fn ($record) => route('ventas.invoice', $record))
                ->openUrlInNewTab()
                ->visible(fn ($record) => !empty($record->invoice_number)),
        ];
    }
}
