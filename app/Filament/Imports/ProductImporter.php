<?php

namespace App\Filament\Imports;

use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('sku')
                ->label('SKU')
                ->requiredMapping()
                ->rules(['required', 'max:191']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:191']),
            ImportColumn::make('description')
                ->rules(['nullable']),
            ImportColumn::make('price')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'numeric']),
            ImportColumn::make('cost')
                ->numeric()
                ->rules(['nullable', 'numeric']),
            ImportColumn::make('stock')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('min_stock')
                ->numeric()
                ->rules(['nullable', 'integer']),
            ImportColumn::make('max_stock')
                ->numeric()
                ->rules(['nullable', 'integer']),
            ImportColumn::make('unit_name')
                ->rules(['nullable', 'max:50']),
            ImportColumn::make('package_name')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('units_per_package')
                ->numeric()
                ->rules(['nullable', 'integer']),
            ImportColumn::make('price_unit')
                ->numeric()
                ->rules(['nullable', 'numeric']),
            ImportColumn::make('price_package')
                ->numeric()
                ->rules(['nullable', 'numeric']),
            ImportColumn::make('shelf')
                ->rules(['nullable', 'max:10']),
            ImportColumn::make('row')
                ->rules(['nullable', 'max:10']),
            ImportColumn::make('position')
                ->rules(['nullable', 'max:10']),
            ImportColumn::make('expires_at')
                ->rules(['nullable', 'date']),
            ImportColumn::make('status')
                ->rules(['nullable']),
            ImportColumn::make('category_id')
                ->numeric()
                ->rules(['nullable', 'integer']),
            ImportColumn::make('supplier_id')
                ->numeric()
                ->rules(['nullable', 'integer']),
        ];
    }

    public function resolveRecord(): Product
    {
        return Product::firstOrNew([
            'sku' => $this->data['sku'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Tu importación de productos se completó y ' . Number::format($import->successful_rows) . ' ' . str('fila')->plural($import->successful_rows) . ' importadas.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }
}