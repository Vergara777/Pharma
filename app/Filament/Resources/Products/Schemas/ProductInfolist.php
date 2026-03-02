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
                Section::make('Detalles del Producto')
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
                        TextEntry::make('status')
                            ->label('Estado')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'retired' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'active' => 'Activo',
                                'retired' => 'Retirado',
                                default => ucfirst($state),
                            }),
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
                        TextEntry::make('cost')
                            ->label('Costo')
                            ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.'))
                            ->color('info'),
                        TextEntry::make('stock')
                            ->label('Stock Actual')
                            ->formatStateUsing(function ($state, $record) {
                                $stock = $record->stock;
                                $min = $record->min_stock ?? 5;
                                
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
                                $min = $record->min_stock ?? 5;
                                
                                if ($stock == 0 || $stock <= $min) {
                                    return 'danger';
                                } elseif ($stock <= ($min + 10)) {
                                    return 'warning';
                                } else {
                                    return 'success';
                                }
                            })
                            ->weight('bold'),
                        TextEntry::make('min_stock')
                            ->label('Stock Mínimo')
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('max_stock')
                            ->label('Stock Máximo')
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('expires_at')
                            ->label('Fecha de Vencimiento')
                            ->date('d/m/Y')
                            ->placeholder('Sin fecha de vencimiento')
                            ->color(function ($record) {
                                if (!$record->expires_at) return 'gray';
                                
                                $daysUntilExpiration = now()->diffInDays($record->expires_at, false);
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
                                if (!$record->expires_at) return '';
                                
                                $daysUntilExpiration = now()->diffInDays($record->expires_at, false);
                                
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
                
                Section::make('Ubicación en Bodega')
                    ->schema([
                        TextEntry::make('shelf')
                            ->label('Estante')
                            ->badge()
                            ->color('primary')
                            ->placeholder('-'),
                        TextEntry::make('row')
                            ->label('Fila')
                            ->badge()
                            ->color('warning')
                            ->placeholder('-'),
                        TextEntry::make('position')
                            ->label('Posición')
                            ->badge()
                            ->color('info')
                            ->placeholder('-'),
                    ])
                    ->columns(3),
                
                Section::make('Presentación y Empaque')
                    ->schema([
                        TextEntry::make('unit_name')
                            ->label('Nombre de Unidad')
                            ->placeholder('unidad'),
                        TextEntry::make('units_per_package')
                            ->label('Unidades por Paquete')
                            ->numeric()
                            ->placeholder('1'),
                        TextEntry::make('package_name')
                            ->label('Nombre del Paquete')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('price_unit')
                            ->label('Precio por Unidad')
                            ->formatStateUsing(fn ($state) => $state ? '$' . number_format($state, 0, ',', '.') : '-'),
                        TextEntry::make('price_package')
                            ->label('Precio por Paquete')
                            ->formatStateUsing(fn ($state) => $state ? '$' . number_format($state, 0, ',', '.') : '-'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                
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
