<?php

namespace App\Filament\Resources\Clientes\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FacturasRelationManager extends RelationManager
{
    protected static string $relationship = 'facturas';

    protected static ?string $title = 'Facturas del Cliente';

    protected static ?string $recordTitleAttribute = 'invoice_number';

    public function infolist(Schema $schema): Schema
    {
        return \App\Filament\Resources\Facturas\Schemas\FacturaInfolist::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Número de Factura')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('fecha_emision')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
                
                TextColumn::make('total')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                
                TextColumn::make('status')
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
                    })
                    ->sortable()
                    ->action(
                        Action::make('cambiar_estado')
                            ->label('Cambiar Estado')
                            ->form([
                                \Filament\Forms\Components\Radio::make('status')
                                    ->label('Selecciona el nuevo estado')
                                    ->options([
                                        'pending' => 'Pendiente',
                                        'paid' => 'Pagada',
                                        'cancelled' => 'Cancelada',
                                    ])
                                    ->default(fn ($record) => $record->status)
                                    ->required()
                                    ->inline()
                                    ->inlineLabel(false),
                            ])
                            ->action(function ($record, array $data) {
                                $record->update(['status' => $data['status']]);
                                
                                \Filament\Notifications\Notification::make()
                                    ->success()
                                    ->title('Estado actualizado')
                                    ->body("El estado de la factura {$record->invoice_number} fue cambiado a " . match($data['status']) {
                                        'pending' => 'Pendiente',
                                        'paid' => 'Pagada',
                                        'cancelled' => 'Cancelada',
                                    })
                                    ->send();
                            })
                            ->modalHeading('Cambiar Estado de Factura')
                            ->modalSubmitActionLabel('Cambiar')
                            ->modalWidth('md')
                    ),
                
                TextColumn::make('payment_method')
                    ->label('Método de Pago')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'cash' => 'Efectivo',
                        'card' => 'Tarjeta',
                        'transfer' => 'Transferencia',
                        'check' => 'Cheque',
                        default => '-',
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('ver_ticket')
                    ->label('Ver Factura')
                    ->icon('heroicon-o-document-text')
                    ->color('warning')
                    ->url(fn ($record) => route('facturas.ticket', ['factura' => $record->id]))
                    ->openUrlInNewTab(),
                ViewAction::make(),
            ])
            ->defaultSort('fecha_emision', 'desc');
    }
}
