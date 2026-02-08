<?php

namespace App\Filament\Resources\Facturas\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FacturasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Número')
                    ->searchable()
                    ->sortable()
                    ->sortable(query: function ($query, string $direction): void {
                        $query->orderByRaw("CAST(SUBSTRING(invoice_number, 5) AS UNSIGNED) {$direction}");
                    })
                    ->toggleable(),
                
                TextColumn::make('cliente.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('fecha_emision')
                    ->label('Fecha Emisión')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('fecha_vencimiento')
                    ->label('Fecha Vencimiento')
                    ->date()
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(),
                
                TextColumn::make('total')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->toggleable(),
                
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
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('user.name')
                    ->label('Creado por')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'paid' => 'Pagada',
                        'cancelled' => 'Cancelada',
                    ]),
                
                SelectFilter::make('payment_method')
                    ->label('Método de Pago')
                    ->options([
                        'cash' => 'Efectivo',
                        'card' => 'Tarjeta',
                        'transfer' => 'Transferencia',
                        'check' => 'Cheque',
                    ]),
                
                \Filament\Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('desde')
                            ->label('Desde')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        \Filament\Forms\Components\DatePicker::make('hasta')
                            ->label('Hasta')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['desde'], fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['hasta'], fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['desde'] && !$data['hasta']) {
                            return null;
                        }
                        
                        $desde = $data['desde'] ? \Carbon\Carbon::parse($data['desde'])->format('d/m/Y') : '';
                        $hasta = $data['hasta'] ? \Carbon\Carbon::parse($data['hasta'])->format('d/m/Y') : '';
                        
                        if ($desde && $hasta) {
                            return "Fecha: {$desde} - {$hasta}";
                        } elseif ($desde) {
                            return "Desde: {$desde}";
                        } else {
                            return "Hasta: {$hasta}";
                        }
                    }),
                \Filament\Tables\Filters\Filter::make('today')
                    ->label('Solo Hoy')
                    ->query(fn ($query) => $query->whereDate('created_at', today()))
                    ->toggle()
                    ->default(true),
                \Filament\Tables\Filters\Filter::make('this_week')
                    ->label('Esta Semana')
                    ->query(fn ($query) => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
                    ->toggle(),
                \Filament\Tables\Filters\Filter::make('this_month')
                    ->label('Este Mes')
                    ->query(fn ($query) => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year))
                    ->toggle(),
                \Filament\Tables\Filters\Filter::make('all')
                    ->label('Ver Todas')
                    ->query(fn ($query) => $query)
                    ->toggle(),
            ])
            ->recordActions([
                Action::make('ver_ticket')
                    ->label('Ver Factura')
                    ->icon('heroicon-o-document-text')
                    ->color('warning')
                    ->url(fn ($record) => route('facturas.ticket', ['factura' => $record->id]))
                    ->openUrlInNewTab(),
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn () => auth()->user()->role === 'admin'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('invoice_number', 'desc')
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession()
            ->paginationPageOptions([10, 25, 50, 100])
            ->defaultPaginationPageOption(25);
    }
}
