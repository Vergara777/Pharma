<?php

namespace App\Filament\Exports;

use App\Models\CashSession;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class CashSessionExporter extends Exporter
{
    protected static ?string $model = CashSession::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('user.name')
                ->label('Usuario'),
            ExportColumn::make('opened_at')
                ->label('Apertura'),
            ExportColumn::make('closed_at')
                ->label('Cierre'),
            ExportColumn::make('initial_amount')
                ->label('Monto Inicial'),
            ExportColumn::make('theoretical_amount')
                ->label('Monto Teórico'),
            ExportColumn::make('counted_amount')
                ->label('Monto Contado'),
            ExportColumn::make('difference')
                ->label('Diferencia'),
            ExportColumn::make('status')
                ->label('Estado'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de cajas ha finalizado y se han exportado ' . Number::format($export->successful_rows) . ' ' . str('fila')->plural($export->successful_rows) . '.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }

    public function getCompletedNotification(): \Filament\Notifications\Notification
    {
        return parent::getCompletedNotification()
            ->title('Exportación de Cajas Finalizada')
            ->success()
            ->database();
    }
}
