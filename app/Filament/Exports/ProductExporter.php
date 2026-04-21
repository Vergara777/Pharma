<?php

namespace App\Filament\Exports;

use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('sku')
                ->label('SKU'),
            ExportColumn::make('name')
                ->label('Nombre'),
            ExportColumn::make('description')
                ->label('Descripción'),
            ExportColumn::make('price')
                ->label('Precio'),
            ExportColumn::make('cost')
                ->label('Costo'),
            ExportColumn::make('stock')
                ->label('Stock'),
            ExportColumn::make('min_stock')
                ->label('Stock Mínimo'),
            ExportColumn::make('max_stock')
                ->label('Stock Máximo'),
            ExportColumn::make('unit_name')
                ->label('Unidad'),
            ExportColumn::make('package_name')
                ->label('Presentación'),
            ExportColumn::make('units_per_package')
                ->label('Unidades por Paquete'),
            ExportColumn::make('price_unit')
                ->label('Precio por Unidad'),
            ExportColumn::make('price_package')
                ->label('Precio por Paquete'),
            ExportColumn::make('shelf')
                ->label('Estante'),
            ExportColumn::make('row')
                ->label('Fila'),
            ExportColumn::make('position')
                ->label('Posición'),
            ExportColumn::make('expires_at')
                ->label('Vencimiento'),
            ExportColumn::make('status')
                ->label('Estado'),
            ExportColumn::make('created_at')
                ->label('Creado el'),
            ExportColumn::make('updated_at')
                ->label('Actualizado el'),
            ExportColumn::make('category.name')
                ->label('Categoría'),
            ExportColumn::make('supplier.name')
                ->label('Proveedor'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de productos ha finalizado y se han exportado ' . Number::format($export->successful_rows) . ' ' . str('fila')->plural($export->successful_rows) . '.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }

    public function getCompletedNotification(): \Filament\Notifications\Notification
    {
        return parent::getCompletedNotification()
            ->title('Exportación de Productos Finalizada')
            ->success()
            ->database();
    }
}
