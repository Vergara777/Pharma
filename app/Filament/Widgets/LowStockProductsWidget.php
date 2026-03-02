<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\Action;

class LowStockProductsWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Productos con Stock Bajo')
            ->query(
                Product::query()
                    ->whereColumn('stock', '<=', 'min_stock')
                    ->where('stock', '>', 0)
                    ->orderBy('stock', 'asc')
                    ->limit(10)
            )
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->circular()
                    ->size(40)
                    ->toggleable(),

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
                
                TextColumn::make('stock')
                    ->label('Stock Actual')
                    ->badge()
                    ->color('danger')
                    ->suffix(' unidades')
                    ->toggleable(),
                
                TextColumn::make('min_stock')
                    ->label('Stock Mínimo')
                    ->suffix(' unidades')
                    ->toggleable(),
            ])
            ->paginationPageOptions([5, 10, 25, 50])
            ->defaultPaginationPageOption(10);
    }
}
