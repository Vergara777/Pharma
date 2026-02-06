<?php

namespace App\Filament\Resources\Lotes\Tables;

use App\Filament\Resources\Lotes\LoteResource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action as RecordAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;

class LotesTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo_lote')
                    ->label('Código de Lote')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Código copiado')
                    ->weight('bold')
                    ->toggleable(),
                
                TextColumn::make('product.name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->toggleable(),
                
                BadgeColumn::make('estado')
                    ->label('Estado')
                    ->icon(fn ($state, $record) => match(true) {
                        $record->estaVencido() => 'heroicon-o-x-circle',
                        $state === 'activo' => 'heroicon-o-check-circle',
                        $state === 'agotado' => 'heroicon-o-x-circle',
                        $state === 'bloqueado' => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-check-circle',
                    })
                    ->colors([
                        'success' => 'activo',
                        'danger' => fn ($state, $record) => $state === 'vencido' || $record->estaVencido() || $state === 'agotado',
                        'warning' => fn ($state, $record) => $state === 'bloqueado' || $record->estaProximoAVencer(),
                    ])
                    ->formatStateUsing(fn ($state, $record) => '')
                    ->tooltip(fn ($state, $record) => $record->estaVencido() ? 'Vencido' : ucfirst($state))
                    ->alignment('center')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('cantidad_actual')
                    ->label('Stock')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn ($record) => $record->tieneStockBajo() ? 'danger' : 'success')
                    ->weight(fn ($record) => $record->tieneStockBajo() ? 'bold' : 'normal')
                    ->suffix(' un.')
                    ->toggleable(),
                
                TextColumn::make('fecha_vencimiento')
                    ->label('Vencimiento')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => match(true) {
                        $record->estaVencido() => 'danger',
                        $record->estaProximoAVencer() => 'warning',
                        default => 'success',
                    })
                    ->description(fn ($record) => $record->diasParaVencer() > 0 
                        ? $record->diasParaVencer() . ' días restantes'
                        : ($record->diasParaVencer() === 0 ? 'Vence hoy' : 'Vencido hace ' . abs($record->diasParaVencer()) . ' días')
                    )
                    ->toggleable(),
                
                TextColumn::make('costo_unitario')
                    ->label('Costo')
                    ->formatStateUsing(fn ($state) => $state ? '$' . number_format($state, 0, ',', '.') : '-')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('precio_venta_sugerido')
                    ->label('Precio Venta')
                    ->formatStateUsing(fn ($state) => $state ? '$' . number_format($state, 0, ',', '.') : '-')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('proveedor.name')
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->wrap(),
                
                TextColumn::make('ubicacion_fisica')
                    ->label('Ubicación')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('Sin ubicación'),
                
                IconColumn::make('requiere_cadena_frio')
                    ->label('Frío')
                    ->boolean()
                    ->trueIcon('heroicon-o-snowflake')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('info')
                    ->falseColor('gray')
                    ->toggleable()
                    ->alignCenter(),
                
                IconColumn::make('requiere_receta')
                    ->label('Receta')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-text')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->toggleable()
                    ->alignCenter(),
                
                TextColumn::make('registro_sanitario')
                    ->label('Reg. Sanitario')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('Sin registro'),
                
                TextColumn::make('fecha_ingreso')
                    ->label('Fecha Ingreso')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('usuarioRegistro.name')
                    ->label('Registrado por')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('Sistema'),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'activo' => 'Activo',
                        'agotado' => 'Agotado',
                        'vencido' => 'Vencido',
                        'bloqueado' => 'Bloqueado',
                    ])
                    ->multiple(),
                
                SelectFilter::make('product_id')
                    ->label('Producto')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('proveedor_id')
                    ->label('Proveedor')
                    ->relationship('proveedor', 'name')
                    ->searchable()
                    ->preload(),
                
                Filter::make('proximos_a_vencer')
                    ->label('Próximos a Vencer (30 días)')
                    ->query(fn (Builder $query) => $query->proximosAVencer(30)),
                
                Filter::make('vencidos')
                    ->label('Vencidos')
                    ->query(fn (Builder $query) => $query->vencidos()),
                
                Filter::make('stock_bajo')
                    ->label('Stock Bajo')
                    ->query(fn (Builder $query) => $query->stockBajo()),
                
                Filter::make('requiere_cadena_frio')
                    ->label('Requiere Frío')
                    ->query(fn (Builder $query) => $query->where('requiere_cadena_frio', true)),
                
                Filter::make('requiere_receta')
                    ->label('Requiere Receta')
                    ->query(fn (Builder $query) => $query->where('requiere_receta', true)),
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make()
                    ->label('Ver Historial')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalWidth('7xl'),
                
                RecordAction::make('ver_factura')
                    ->label('Factura')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->visible(fn ($record) => $record->documento_archivo)
                    ->url(fn ($record) => route('lotes.documento', $record))
                    ->openUrlInNewTab(),
                
                RecordAction::make('registrar_movimiento')
                    ->label('Movimiento')
                    ->icon('heroicon-o-arrows-right-left')
                    ->color('warning')
                    ->modalWidth('2xl')
                    ->form([
                        Select::make('tipo_movimiento')
                            ->label('Tipo de Movimiento')
                            ->options([
                                'entrada' => '📥 Entrada',
                                'salida' => '📤 Salida',
                                'ajuste' => '⚙️ Ajuste',
                                'merma' => '📉 Merma',
                                'vencimiento' => '⏰ Vencimiento',
                                'devolucion' => '↩️ Devolución',
                            ])
                            ->required()
                            ->native(false),
                        
                        TextInput::make('cantidad')
                            ->label('Cantidad')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->suffix('unidades'),
                        
                        Textarea::make('motivo')
                            ->label('Motivo')
                            ->required()
                            ->rows(3)
                            ->placeholder('Describe el motivo del movimiento...'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->registrarMovimiento(
                            $data['tipo_movimiento'],
                            $data['cantidad'],
                            null,
                            $data['motivo']
                        );
                        
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Movimiento registrado')
                            ->body("Se registró {$data['tipo_movimiento']} de {$data['cantidad']} unidades")
                            ->send();
                    }),
                
                EditAction::make()
                    ->color('primary')
                    ->modalWidth('5xl')
                    ->form(\App\Filament\Resources\Lotes\Schemas\LoteForm::getSchema()),
                
                DeleteAction::make()
                    ->color('danger'),
            ])
            ->defaultSort('codigo_lote', 'asc')
            ->poll('30s');
    }
}
