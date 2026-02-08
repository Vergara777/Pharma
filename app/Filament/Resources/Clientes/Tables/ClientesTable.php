<?php

namespace App\Filament\Resources\Clientes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClientesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('document')
                    ->label('Documento')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('facturas_count')
                    ->label('Facturas')
                    ->counts('facturas')
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('ventas_count')
                    ->label('Ventas')
                    ->counts('ventas')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('address')
                    ->label('Dirección')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->toggleable(),
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
                SelectFilter::make('is_active')
                    ->label('Estado')
                    ->options([
                        1 => 'Activo',
                        0 => 'Inactivo',
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
                    ->label('Ver Todos')
                    ->query(fn ($query) => $query)
                    ->toggle(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record): string => route('filament.admin.resources.clientes.view', ['record' => $record])),
                EditAction::make()
                    ->visible(fn () => auth()->user()->role === 'admin'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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
