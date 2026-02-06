<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\Action;

class OutOfStockProductsWidget extends BaseWidget
{
    protected static ?int $sort = 8;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('🚫 Productos Sin Stock')
            ->query(
                Product::query()
                    ->where('stock', 0)
                    ->orderBy('updated_at', 'desc')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Product $record): string => $record->sku)
                    ->toggleable(),
                
                TextColumn::make('category.name')
                    ->label('Categoría')
                    ->badge()
                    ->color('warning')
                    ->toggleable(),
                
                TextColumn::make('supplier.name')
                    ->label('Proveedor')
                    ->badge()
                    ->color('info')
                    ->toggleable(),
                
                TextColumn::make('price')
                    ->label('Precio')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.'))
                    ->toggleable(),
            ])
            ->paginationPageOptions([5, 10, 25, 50])
            ->defaultPaginationPageOption(10);
    }
}
