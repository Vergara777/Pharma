<?php

namespace App\Filament\Exports;

use App\Models\Supplier;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class SupplierExporter extends Exporter
{
    protected static ?string $model = Supplier::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name')
                ->label('Nombre'),
            ExportColumn::make('phone')
                ->label('Teléfono'),
            ExportColumn::make('email')
                ->label('Email'),
            ExportColumn::make('status')
                ->label('Estado'),
            ExportColumn::make('address')
                ->label('Dirección'),
            ExportColumn::make('created_at')
                ->label('Creado el'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de proveedores ha finalizado y se han exportado ' . Number::format($export->successful_rows) . ' ' . str('fila')->plural($export->successful_rows) . '.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }

    public function getCompletedNotification(): \Filament\Notifications\Notification
    {
        return parent::getCompletedNotification()
            ->title('Exportación de Proveedores Finalizada')
            ->success()
            ->database();
    }
}
