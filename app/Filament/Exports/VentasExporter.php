<?php

namespace App\Filament\Exports;

use App\Models\Ventas;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class VentasExporter extends Exporter
{
    protected static ?string $model = Ventas::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('invoice_number')
                ->label('N° Factura'),
            ExportColumn::make('customer_name')
                ->label('Cliente'),
            ExportColumn::make('grand_total')
                ->label('Total'),
            ExportColumn::make('status')
                ->label('Estado'),
            ExportColumn::make('user_name')
                ->label('Vendedor'),
            ExportColumn::make('created_at')
                ->label('Fecha'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de ventas ha finalizado y se han exportado ' . Number::format($export->successful_rows) . ' ' . str('fila')->plural($export->successful_rows) . '.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }

    public function getCompletedNotification(): \Filament\Notifications\Notification
    {
        return parent::getCompletedNotification()
            ->title('Exportación de Ventas Finalizada')
            ->success()
            ->database();
    }
}
