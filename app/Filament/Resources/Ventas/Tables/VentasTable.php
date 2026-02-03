<?php

namespace App\Filament\Resources\Ventas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables;

class VentasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('N° Factura')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->toggleable(),
                TextColumn::make('customer_name')
                    ->label('Cliente')
                    ->searchable()
                    ->placeholder('Cliente general')
                    ->description(fn ($record) => $record->customer_phone ? "Tel: {$record->customer_phone}" : null)
                    ->toggleable(),
                TextColumn::make('customer_email')
                    ->label('Email Cliente')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('items')
                    ->label('Producto')
                    ->formatStateUsing(function ($record) {
                        $items = $record->items;
                        if ($items->isEmpty()) {
                            return $record->product ? $record->product->name : '-';
                        }
                        return $items->first()->product ? $items->first()->product->name : '-';
                    })
                    ->description(function ($record) {
                        $items = $record->items;
                        if ($items->count() > 1) {
                            return '+' . ($items->count() - 1) . ' producto(s) más';
                        }
                        return null;
                    })
                    ->limit(30)
                    ->toggleable(),
                TextColumn::make('items_qty')
                    ->label('Cant.')
                    ->getStateUsing(function ($record) {
                        if ($record->items->isNotEmpty()) {
                            return $record->items->sum('qty');
                        }
                        return $record->qty ?? 0;
                    })
                    ->alignCenter()
                    ->toggleable(),
                TextColumn::make('items_price')
                    ->label('Precio Unit.')
                    ->getStateUsing(function ($record) {
                        if ($record->items->isEmpty()) {
                            return $record->unit_price ? '$' . number_format($record->unit_price, 0, ',', '.') : '-';
                        }
                        if ($record->items->count() === 1) {
                            return '$' . number_format($record->items->first()->unit_price, 0, ',', '.');
                        }
                        return 'Varios';
                    })
                    ->toggleable(),
                TextColumn::make('grand_total')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.'))
                    ->weight('bold')
                    ->sortable()
                    ->toggleable(),
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
                    })
                    ->toggleable(),
                TextColumn::make('user_name')
                    ->label('Vendedor')
                    ->searchable()
                    ->description(function ($record) {
                        if (!$record->user_role) return null;
                        
                        $roleText = match ($record->user_role) {
                            'admin' => 'Administrador',
                            'tech' => 'Trabajador',
                            default => $record->user_role,
                        };
                        
                        return $roleText;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activa',
                        'cancelled' => 'Cancelada',
                        'returned' => 'Devuelta',
                    ])
                    ->default('active'),
                Tables\Filters\SelectFilter::make('product_id')
                    ->label('Producto')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('desde')
                            ->label('Desde')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(today()),
                        \Filament\Forms\Components\DatePicker::make('hasta')
                            ->label('Hasta')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(today()),
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
                Tables\Filters\Filter::make('today')
                    ->label('Solo Hoy')
                    ->query(fn ($query) => $query->whereDate('created_at', today()))
                    ->toggle()
                    ->default(true), // ← ACTIVADO POR DEFECTO
                Tables\Filters\Filter::make('yesterday')
                    ->label('Ayer')
                    ->query(fn ($query) => $query->whereDate('created_at', today()->subDay()))
                    ->toggle(),
                Tables\Filters\Filter::make('this_week')
                    ->label('Esta Semana')
                    ->query(fn ($query) => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
                    ->toggle(),
                Tables\Filters\Filter::make('last_week')
                    ->label('Semana Pasada')
                    ->query(fn ($query) => $query->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]))
                    ->toggle(),
                Tables\Filters\Filter::make('this_month')
                    ->label('Este Mes')
                    ->query(fn ($query) => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year))
                    ->toggle(),
                Tables\Filters\Filter::make('last_month')
                    ->label('Mes Pasado')
                    ->query(fn ($query) => $query->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year))
                    ->toggle(),
                Tables\Filters\Filter::make('all')
                    ->label('Ver Todas')
                    ->query(fn ($query) => $query)
                    ->toggle(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalWidth('7xl'),
                Action::make('print_invoice')
                    ->label('Factura')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->url(fn ($record) => route('ventas.invoice', $record))
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => !empty($record->invoice_number)),
                Action::make('cancel')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Cancelar Venta')
                    ->modalDescription('¿Estás seguro de cancelar esta venta? Esta acción devolverá el stock al inventario.')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'cancelled',
                            'cancel_reason' => 'Cancelada por ' . auth()->user()->name,
                            'cancelled_by' => auth()->id(),
                            'cancelled_at' => now(),
                        ]);
                        
                        // Devolver stock al producto
                        if ($record->items->isNotEmpty()) {
                            foreach ($record->items as $item) {
                                if ($item->product) {
                                    $item->product->increment('stock', $item->qty);
                                }
                            }
                        } elseif ($record->product_id && $record->qty) {
                            $record->product->increment('stock', $record->qty);
                        }
                    })
                    ->visible(fn ($record) => $record->status === 'active' && auth()->user()->role === 'admin'),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession()
            ->paginationPageOptions([10, 25, 50, 100])
            ->defaultPaginationPageOption(25);
    }
}
