<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Cache;

class ExpiringProductsWidget extends BaseWidget
{
    protected static ?int $sort = 7;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $alertDays = Cache::get('settings.expiration_alert_days', 30);

        return $table
            ->heading('Productos Próximos a Vencer o Vencidos')
            ->query(
                Product::query()
                    ->whereNotNull('expires_at')
                    ->where(function ($query) use ($alertDays) {
                        $query->whereDate('expires_at', '<=', now()->addDays($alertDays))
                              ->orWhereDate('expires_at', '<', now());
                    })
                    ->orderBy('expires_at', 'asc')
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
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Fecha de Vencimiento')
                    ->date('d/m/Y')
                    ->badge()
                    ->color(function ($record) {
                        $daysUntilExpiration = now()->diffInDays($record->expires_at, false);
                        
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
                        $daysUntilExpiration = now()->diffInDays($record->expires_at, false);
                        
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
                        $min = $record->min_stock ?? 5;
                        
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
