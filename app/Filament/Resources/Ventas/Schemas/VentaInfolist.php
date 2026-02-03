<?php

namespace App\Filament\Resources\Ventas\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class VentaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->schema([
                // Columna izquierda
                Section::make('Información de la Venta')
                    ->schema([
                        TextEntry::make('invoice_number')
                            ->label('N° Factura'),
                        TextEntry::make('status')
                            ->label('Estado')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'cancelled' => 'danger',
                                'returned' => 'warning',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'active' => 'Activa',
                                'cancelled' => 'Cancelada',
                                'returned' => 'Devuelta',
                            }),
                        TextEntry::make('created_at')
                            ->label('Fecha')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(3),
                
                Section::make('Cliente')
                    ->schema([
                        TextEntry::make('customer_name')
                            ->label('Nombre')
                            ->placeholder('Cliente general'),
                        TextEntry::make('customer_phone')
                            ->label('Teléfono')
                            ->placeholder('-'),
                        TextEntry::make('customer_email')
                            ->label('Email')
                            ->placeholder('-'),
                    ])
                    ->columns(3),
                
                Section::make('Factura')
                    ->schema([
                        TextEntry::make('invoice_name')
                            ->label('Nombre / Razón Social')
                            ->columnSpanFull(),
                        TextEntry::make('invoice_document')
                            ->label('Documento / NIT')
                            ->placeholder('-')
                            ->copyable()
                            ->copyMessage('Documento copiado')
                            ->columnSpanFull(),
                        TextEntry::make('invoice_address')
                            ->label('Dirección')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('invoice_phone')
                            ->label('Teléfono')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('invoice_email')
                            ->label('Email')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->visible(fn ($record) => !empty($record->invoice_number)),
                
                Section::make('Producto')
                    ->schema([
                        \Filament\Infolists\Components\ViewEntry::make('items_list')
                            ->view('filament.resources.ventas.infolists.items-list')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
                
                Section::make('Vendedor')
                    ->schema([
                        TextEntry::make('user_name')
                            ->label('Nombre'),
                        TextEntry::make('user_role')
                            ->label('Rol')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'admin' => 'danger',
                                'tech' => 'primary',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'admin' => 'Administrador',
                                'tech' => 'Trabajador',
                                default => $state,
                            }),
                    ])
                    ->columns(2),
                
                // Columna derecha
                Section::make('Cálculos')
                    ->schema([
                        Grid::make(6)
                            ->schema([
                                TextEntry::make('discount_percent')
                                    ->label('Descuento (%)')
                                    ->suffix('%')
                                    ->default(0),
                                TextEntry::make('discount_amount')
                                    ->label('Descuento')
                                    ->formatStateUsing(fn ($state) => '$' . number_format($state ?? 0, 0, ',', '.')),
                                TextEntry::make('tax_rate')
                                    ->label('IVA (%)')
                                    ->suffix('%')
                                    ->default(19),
                                TextEntry::make('tax_amount')
                                    ->label('IVA')
                                    ->formatStateUsing(fn ($state) => '$' . number_format($state ?? 0, 0, ',', '.')),
                                TextEntry::make('grand_total')
                                    ->label('TOTAL')
                                    ->formatStateUsing(fn ($state) => '$' . number_format($state ?? 0, 0, ',', '.'))
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('success')
                                    ->columnSpan(2),
                            ]),
                    ]),
                
                Section::make('Pago')
                    ->schema([
                        TextEntry::make('paymentMethod.name')
                            ->label('Método de Pago')
                            ->badge()
                            ->color('primary'),
                        TextEntry::make('payment_reference')
                            ->label('Referencia')
                            ->default('-')
                            ->copyable()
                            ->copyMessage('Referencia copiada')
                            ->icon('heroicon-o-document-duplicate'),
                        TextEntry::make('amount_received')
                            ->label('Monto Recibido')
                            ->formatStateUsing(fn ($state) => $state > 0 ? '$' . number_format($state, 0, ',', '.') : '-')
                            ->weight('medium')
                            ->color('warning'),
                        TextEntry::make('change_amount')
                            ->label('Cambio')
                            ->formatStateUsing(fn ($state) => $state > 0 ? '$' . number_format($state, 0, ',', '.') : '-')
                            ->weight('medium')
                            ->color('success'),
                    ])
                    ->columns(4),
                
                Section::make('Cancelación')
                    ->schema([
                        TextEntry::make('cancelledBy.name')
                            ->label('Cancelado por'),
                        TextEntry::make('cancelled_at')
                            ->label('Fecha de cancelación')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('cancel_reason')
                            ->label('Motivo')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->visible(fn ($record) => $record->status === 'cancelled')
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }
}
