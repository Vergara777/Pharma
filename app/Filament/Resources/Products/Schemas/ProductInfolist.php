<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Producto')
                    ->schema([
                        ImageEntry::make('image')
                            ->label('Imagen')
                            ->circular()
                            ->size(120)
                            ->columnSpan(2),
                        TextEntry::make('name')
                            ->label('Nombre')
                            ->size('lg')
                            ->weight('bold')
                            ->columnSpan(2),
                        TextEntry::make('sku')
                            ->label('SKU')
                            ->badge()
                            ->color('primary'),
                        TextEntry::make('category.name')
                            ->label('Categoría')
                            ->badge()
                            ->color('warning'),
                        TextEntry::make('supplier.name')
                            ->label('Proveedor')
                            ->badge()
                            ->color('info')
                            ->columnSpan(2),
                        TextEntry::make('description')
                            ->label('Descripción')
                            ->placeholder('Sin descripción')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Section::make('Precio e Inventario')
                    ->schema([
                        TextEntry::make('price')
                            ->label('Precio')
                            ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.'))
                            ->size('lg')
                            ->weight('bold')
                            ->color('success'),
                        TextEntry::make('stock')
                            ->label('Stock Actual')
                            ->formatStateUsing(function ($state, $record) {
                                $stock = $record->stock;
                                $min = $record->stock_minimum ?? 20;
                                
                                if ($stock == 0) {
                                    $status = '¡Sin stock!';
                                } elseif ($stock <= $min) {
                                    $status = 'Stock bajo';
                                } elseif ($stock <= ($min + 10)) {
                                    $status = 'Stock medio';
                                } else {
                                    $status = 'Stock normal';
                                }
                                
                                return $state . ' unidades - ' . $status;
                            })
                            ->color(function ($record) {
                                $stock = $record->stock;
                                $min = $record->stock_minimum ?? 20;
                                
                                if ($stock == 0 || $stock <= $min) {
                                    return 'danger';
                                } elseif ($stock <= ($min + 10)) {
                                    return 'warning';
                                } else {
                                    return 'success';
                                }
                            })
                            ->weight('bold'),
                        TextEntry::make('stock_minimum')
                            ->label('Stock Mínimo')
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('stock_maximum')
                            ->label('Stock Máximo')
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('expiration_date')
                            ->label('Fecha de Vencimiento')
                            ->date('d/m/Y')
                            ->placeholder('Sin fecha de vencimiento')
                            ->color(function ($record) {
                                if (!$record->expiration_date) return 'gray';
                                
                                $daysUntilExpiration = now()->diffInDays($record->expiration_date, false);
                                $alertDays = \Illuminate\Support\Facades\Cache::get('settings.expiration_alert_days', 30);
                                
                                if ($daysUntilExpiration < 0) {
                                    return 'danger';
                                } elseif ($daysUntilExpiration <= $alertDays) {
                                    return 'warning';
                                } else {
                                    return 'success';
                                }
                            })
                            ->suffix(function ($record) {
                                if (!$record->expiration_date) return '';
                                
                                $daysUntilExpiration = now()->diffInDays($record->expiration_date, false);
                                
                                if ($daysUntilExpiration < 0) {
                                    return ' (Vencido)';
                                } elseif ($daysUntilExpiration == 0) {
                                    return ' (Vence hoy)';
                                } elseif ($daysUntilExpiration <= 30) {
                                    return ' (Vence en ' . round($daysUntilExpiration) . ' días)';
                                } else {
                                    return '';
                                }
                            })
                            ->weight('bold')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Section::make('Información Adicional')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Fecha de Creación')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Última Actualización')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
