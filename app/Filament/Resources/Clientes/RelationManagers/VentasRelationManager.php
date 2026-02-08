<?php

namespace App\Filament\Resources\Clientes\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;

class VentasRelationManager extends RelationManager
{
    protected static string $relationship = 'ventas';

    protected static ?string $title = 'Ventas del Cliente';

    protected static ?string $recordTitleAttribute = 'invoice_number';

    public function infolist(Schema $schema): Schema
    {
        return \App\Filament\Resources\Ventas\Schemas\VentaInfolist::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Número de Venta')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                TextColumn::make('grand_total')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                
                TextColumn::make('status')
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
                
                TextColumn::make('factura.invoice_number')
                    ->label('Factura Generada')
                    ->badge()
                    ->color('success')
                    ->placeholder('Sin factura')
                    ->url(fn ($record) => $record->factura_id 
                        ? route('filament.admin.resources.facturas.index') . '?tableSearch=' . $record->factura->invoice_number 
                        : null)
                    ->openUrlInNewTab(false),
                
                TextColumn::make('paymentMethod.name')
                    ->label('Método de Pago')
                    ->placeholder('-'),
                
                TextColumn::make('user.name')
                    ->label('Vendedor')
                    ->placeholder('-'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                \Filament\Actions\Action::make('ver_factura')
                    ->label('Ver Factura')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->visible(fn ($record) => $record->factura_id !== null)
                    ->url(fn ($record) => $record->factura_id 
                        ? route('filament.admin.resources.facturas.index') . '?tableSearch=' . $record->factura->invoice_number 
                        : null)
                    ->openUrlInNewTab(false),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
