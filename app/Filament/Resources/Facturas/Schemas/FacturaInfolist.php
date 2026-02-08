<?php

namespace App\Filament\Resources\Facturas\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FacturaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la Factura')
                    ->schema([
                        TextEntry::make('invoice_number')
                            ->label('Número de Factura'),
                        
                        TextEntry::make('cliente.name')
                            ->label('Cliente'),
                        
                        TextEntry::make('user.name')
                            ->label('Creado por'),
                        
                        TextEntry::make('fecha_emision')
                            ->label('Fecha de Emisión')
                            ->date(),
                        
                        TextEntry::make('fecha_vencimiento')
                            ->label('Fecha de Vencimiento')
                            ->date()
                            ->placeholder('-'),
                        
                        TextEntry::make('status')
                            ->label('Estado')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'paid' => 'success',
                                'cancelled' => 'danger',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'Pendiente',
                                'paid' => 'Pagada',
                                'cancelled' => 'Cancelada',
                            }),
                        
                        TextEntry::make('payment_method')
                            ->label('Método de Pago')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'cash' => 'Efectivo',
                                'card' => 'Tarjeta',
                                'transfer' => 'Transferencia',
                                'check' => 'Cheque',
                                default => '-',
                            })
                            ->placeholder('-'),
                    ])
                    ->columns(2),
                
                Section::make('Items de la Factura')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label('Producto'),
                                
                                TextEntry::make('quantity')
                                    ->label('Cantidad'),
                                
                                TextEntry::make('price')
                                    ->label('Precio')
                                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.')),
                                
                                TextEntry::make('subtotal')
                                    ->label('Subtotal')
                                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.')),
                            ])
                            ->columns(4),
                    ]),
                
                Section::make('Totales')
                    ->schema([
                        TextEntry::make('subtotal')
                            ->label('Subtotal')
                            ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.')),
                        
                        TextEntry::make('tax')
                            ->label('Impuesto')
                            ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.')),
                        
                        TextEntry::make('discount')
                            ->label('Descuento')
                            ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.')),
                        
                        TextEntry::make('total')
                            ->label('Total')
                            ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.'))
                            ->weight('bold')
                            ->size('lg'),
                    ])
                    ->columns(4),
                
                Section::make('Notas')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Notas')
                            ->placeholder('-'),
                    ]),
                
                Section::make('Información del Sistema')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Creado')
                            ->dateTime(),
                        
                        TextEntry::make('updated_at')
                            ->label('Actualizado')
                            ->dateTime(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
