<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Cache;

class ExpiringProductsWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $alertDays = Cache::get('settings.expiration_alert_days', 30);

        return $table
            ->heading('Productos Próximos a Vencer o Vencidos')
            ->query(
                Product::query()
                    ->whereNotNull('expiration_date')
                    ->where(function ($query) use ($alertDays) {
                        $query->whereDate('expiration_date', '<=', now()->addDays($alertDays))
                              ->orWhereDate('expiration_date', '<', now());
                    })
                    ->orderBy('expiration_date', 'asc')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(url('/images/placeholder.png'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Producto')
                    ->searchable()
                    ->description(fn ($record) => 'SKU: ' . $record->sku)
                    ->weight('medium')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->badge()
                    ->color('warning')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('expiration_date')
                    ->label('Fecha de Vencimiento')
                    ->date('d/m/Y')
                    ->badge()
                    ->color(function ($record) {
                        $daysUntilExpiration = now()->diffInDays($record->expiration_date, false);
                        
                        if ($daysUntilExpiration < 0) {
                            return 'danger';
                        } elseif ($daysUntilExpiration <= 7) {
                            return 'danger';
                        } elseif ($daysUntilExpiration <= 15) {
                            return 'warning';
                        } else {
                            return 'info';
                        }
                    })
                    ->description(function ($record) {
                        $daysUntilExpiration = now()->diffInDays($record->expiration_date, false);
                        
                        if ($daysUntilExpiration < 0) {
                            return 'VENCIDO hace ' . round(abs($daysUntilExpiration)) . ' días';
                        } elseif ($daysUntilExpiration == 0) {
                            return 'Vence hoy';
                        } else {
                            return 'Vence en ' . round($daysUntilExpiration) . ' días';
                        }
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock')
                    ->numeric()
                    ->badge()
                    ->color(function ($record) {
                        $stock = $record->stock;
                        $min = $record->stock_minimum ?? 20;
                        
                        if ($stock == 0) {
                            return 'danger';
                        } elseif ($stock <= $min) {
                            return 'danger';
                        } elseif ($stock <= ($min + 10)) {
                            return 'warning';
                        } else {
                            return 'success';
                        }
                    })
                    ->toggleable(),
            ])
            ->actions([
                //
            ])
            ->paginated([5, 10, 25, 50]);
    }
}
