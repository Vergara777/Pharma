<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\CreateAction;
use Filament\Actions\Action as RecordAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->searchPlaceholder('Buscar por nombre, SKU o escanear código de barras...')
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(url('/Images/Pharma1.jpeg'))
                    ->url(fn ($record) => $record->image && str_starts_with($record->image, 'http') ? $record->image : null)
                    ->toggleable(),
                TextColumn::make('name')
                    ->label('Nombre del Producto')
                    ->searchable(['name', 'sku'])
                    ->sortable()
                    ->description(fn ($record) => 'SKU: ' . $record->sku)
                    ->weight('medium')
                    ->toggleable(),
                TextColumn::make('category.name')
                    ->label('Categoría')
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->toggleable(),
                TextColumn::make('price')
                    ->label('Precio Venta')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('cost')
                    ->label('Costo')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('price_unit')
                    ->label('Precio Unidad')
                    ->formatStateUsing(fn ($state) => $state ? '$' . number_format($state, 0, ',', '.') : '-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('stock')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(function ($record) {
                        $stock = $record->stock;
                        $min = $record->min_stock ?? 5;
                        $max = $record->max_stock ?? 100;
                        
                        // Sin stock
                        if ($stock == 0) {
                            return 'danger';
                        }
                        // Stock excede el máximo
                        elseif ($stock > $max) {
                            return 'info';
                        }
                        // Stock bajo (menor o igual al mínimo)
                        elseif ($stock <= $min) {
                            return 'danger';
                        }
                        // Stock por agotar (entre mínimo y mínimo + 10)
                        elseif ($stock <= ($min + 10)) {
                            return 'warning';
                        }
                        // Stock normal
                        else {
                            return 'success';
                        }
                    })
                    ->description(function ($record) {
                        $stock = $record->stock;
                        $min = $record->min_stock ?? 5;
                        $max = $record->max_stock ?? 100;
                        
                        if ($stock == 0) {
                            return '¡Sin stock!';
                        } elseif ($stock > $max) {
                            return '¡Sobrepasa máximo! (Máx: ' . $max . ')';
                        } elseif ($stock <= $min) {
                            return '¡Stock bajo! (Mín: ' . $min . ')';
                        } elseif ($stock <= ($min + 10)) {
                            return 'Por agotar (Mín: ' . $min . ')';
                        } else {
                            return 'Stock normal (Mín: ' . $min . ', Máx: ' . $max . ')';
                        }
                    })
                    ->toggleable(),
                TextColumn::make('unit_name')
                    ->label('Unidad')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('package_name')
                    ->label('Presentación')
                    ->placeholder('N/A')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('units_per_package')
                    ->label('Unids/Pres')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('expires_at')
                    ->label('Vencimiento')
                    ->sortable()
                    ->date('d/m/Y')
                    ->placeholder('Sin fecha')
                    ->badge()
                    ->color(function ($record) {
                        if (!$record->expires_at) return 'gray';
                        
                        $daysUntilExpiration = now()->diffInDays($record->expires_at, false);
                        
                        if ($daysUntilExpiration < 0) {
                            return 'danger';
                        } elseif ($daysUntilExpiration <= 30) {
                            return 'warning';
                        } else {
                            return 'success';
                        }
                    })
                    ->description(function ($record) {
                        if (!$record->expires_at) return null;
                        
                        $daysUntilExpiration = now()->diffInDays($record->expires_at, false);
                        
                        if ($daysUntilExpiration < 0) {
                            return new \Illuminate\Support\HtmlString('<span style="color: #ef4444; font-weight: 600;">¡Vencido!</span>');
                        } elseif ($daysUntilExpiration == 0) {
                            return new \Illuminate\Support\HtmlString('<span style="color: #ef4444; font-weight: 600;">¡Vence hoy!</span>');
                        } elseif ($daysUntilExpiration <= 30) {
                            return new \Illuminate\Support\HtmlString('<span style="color: #f59e0b; font-weight: 600;">Vence en ' . round($daysUntilExpiration) . ' días</span>');
                        } else {
                            return new \Illuminate\Support\HtmlString('<span style="color: #10b981; font-weight: 600;">Vigente</span>');
                        }
                    })
                    ->toggleable(),
                TextColumn::make('shelf')
                    ->label('Ubicación')
                    ->formatStateUsing(fn ($record) => 
                        collect([$record->shelf, $record->row, $record->position])
                            ->filter()
                            ->join('-') ?: 'N/A'
                    )
                    ->description(fn ($record) => 'Estante-Fila-Posición')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('supplier.name')
                    ->label('Proveedor')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('min_stock')
                    ->label('Stock Mín.')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('max_stock')
                    ->label('Stock Máx.')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->icon(fn (string $state): string => match ($state) {
                        'active' => 'heroicon-o-check-circle',
                        'retired' => 'heroicon-o-x-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'retired' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => '')
                    ->tooltip(fn (string $state): string => match ($state) {
                        'active' => 'Activo',
                        'retired' => 'Retirado',
                    })
                    ->alignment('center')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                SelectFilter::make('supplier_id')
                    ->label('Proveedor')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Filter::make('low_stock')
                    ->label('Stock Bajo')
                    ->query(fn ($query) => $query->whereColumn('stock', '<=', 'min_stock')->where('stock', '>', 0))
                    ->toggle(),
                Filter::make('out_of_stock')
                    ->label('Sin Stock')
                    ->query(fn ($query) => $query->where('stock', 0))
                    ->toggle(),
                Filter::make('overstock')
                    ->label('Stock Excedido')
                    ->query(fn ($query) => $query->whereColumn('stock', '>', 'max_stock'))
                    ->toggle(),
                Filter::make('expiring_soon')
                    ->label('Próximos a Vencer')
                    ->query(function ($query) {
                        $alertDays = \Illuminate\Support\Facades\Cache::get('settings.expiration_alert_days', 30);
                        return $query->whereNotNull('expires_at')
                            ->whereDate('expires_at', '<=', now()->addDays($alertDays))
                            ->whereDate('expires_at', '>=', now());
                    })
                    ->toggle(),
                Filter::make('expired')
                    ->label('Vencidos')
                    ->query(fn ($query) => $query->whereNotNull('expires_at')->whereDate('expires_at', '<', now()))
                    ->toggle(),
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activo',
                        'retired' => 'Retirado',
                    ])
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->color('info'),
                EditAction::make()
                    ->color('primary')
                    ->visible(fn () => auth()->user()->role === 'admin'),
                RecordAction::make('deactivate')
                    ->label('Desactivar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Desactivar Producto')
                    ->modalDescription(fn ($record) => 
                        $record->stock == 0 
                            ? "Este producto no tiene stock disponible. ¿Deseas desactivarlo?"
                            : ($record->expires_at && now()->diffInDays($record->expires_at, false) < 0
                                ? "Este producto está vencido. ¿Deseas desactivarlo?"
                                : "¿Deseas desactivar este producto?")
                    )
                    ->modalSubmitActionLabel('Sí, desactivar')
                    ->action(function ($record) {
                        $record->update(['status' => 'retired']);
                        
                        $reason = $record->stock == 0 ? 'sin stock' : 'vencido';
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Producto desactivado')
                            ->body("El producto ha sido desactivado por estar {$reason}")
                            ->success()
                            ->duration(5000)
                            ->send();
                    })
                    ->visible(fn ($record) => 
                        $record->status === 'active' && (
                            $record->stock == 0 || 
                            ($record->expires_at && now()->diffInDays($record->expires_at, false) < 0)
                        )
                    ),
                RecordAction::make('activate')
                    ->label('Activar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Activar Producto')
                    ->modalDescription('¿Deseas activar este producto nuevamente?')
                    ->modalSubmitActionLabel('Sí, activar')
                    ->action(function ($record) {
                        $record->update(['status' => 'active']);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Producto activado')
                            ->body('El producto ha sido activado exitosamente')
                            ->success()
                            ->duration(5000)
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === 'retired'),
                RecordAction::make('addToCart')
                    ->label('Agregar')
                    ->icon('heroicon-o-shopping-cart')
                    ->color('success')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required()
                            ->live(),
                    ])
                    ->action(function ($record, array $data, $livewire) {
                        // Verificar si hay una caja abierta
                        $openSession = \App\Models\CashSession::where('user_id', auth()->id())
                            ->where('status', 'open')
                            ->first();
                        
                        if (!$openSession) {
                            \Filament\Notifications\Notification::make()
                                ->title('Caja Cerrada')
                                ->body('Debes abrir una caja antes de realizar ventas')
                                ->danger()
                                ->duration(5000)
                                ->send();
                            return;
                        }
                        
                        $quantity = $data['quantity'] ?? 1;
                        
                        // Recargar el producto para tener el stock actualizado
                        $product = \App\Models\Product::find($record->id);
                        
                        // Verificar stock disponible
                        if ($product->stock < $quantity) {
                            \Filament\Notifications\Notification::make()
                                ->title('Stock insuficiente')
                                ->body("Solo hay {$product->stock} unidades disponibles")
                                ->danger()
                                ->duration(5000)
                                ->send();
                            return;
                        }
                        
                        $cart = session()->get('cart', []);
                        $cartKey = $product->id;
                        
                        if (isset($cart[$cartKey])) {
                            // Si ya existe, verificar stock para cantidad adicional
                            if ($product->stock < $quantity) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Stock insuficiente')
                                    ->body("No hay suficiente stock para agregar más unidades")
                                    ->danger()
                                    ->duration(5000)
                                    ->send();
                                return;
                            }
                            $cart[$cartKey]['quantity'] += $quantity;
                        } else {
                            $cart[$cartKey] = [
                                'product_id' => $product->id,
                                'name' => $product->name,
                                'price' => $product->price,
                                'quantity' => $quantity,
                            ];
                        }
                        
                        // Descontar del inventario usando update directo
                        $product->update(['stock' => $product->stock - $quantity]);
                        
                        session()->put('cart', $cart);
                        
                        // Disparar eventos para actualizar el badge, modal y tabla
                        $livewire->dispatch('cartUpdated');
                        $livewire->dispatch('refreshProducts');
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Producto agregado')
                            ->body("{$product->name} x{$quantity} agregado (Stock actualizado)")
                            ->success()
                            ->duration(5000)
                            ->send();
                    })
                    ->modalHeading('Agregar al Carrito')
                    ->modalDescription(fn ($record) => "¿Cuántas unidades de {$record->name} deseas agregar?")
                    ->modalSubmitActionLabel('Agregar al Carrito')
                    ->modalWidth('md')
                    ->visible(fn ($record) => $record->stock > 0 && $record->status === 'active'),
                DeleteAction::make()
                    ->visible(fn () => auth()->user()->role === 'admin'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])
                    ->visible(fn () => auth()->user()->role === 'admin'),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->visible(fn () => auth()->user()->role === 'admin'),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])
                    ->visible(fn () => auth()->user()->role === 'admin'),
            ])
            ->recordClasses(fn ($record) => match (true) {
                // Productos desactivados (gris con opacidad) - PRIORIDAD MÁXIMA
                $record->status === 'retired' => 'opacity-50',
                
                // Crítico: Sin stock (rojo)
                $record->stock == 0 => 'fi-row-danger',
                
                // Crítico: Vencido (rojo)
                ($record->expires_at && now()->diffInDays($record->expires_at, false) < 0) => 'fi-row-danger',
                
                // Advertencia: Stock bajo (naranja)
                $record->stock <= ($record->min_stock ?? 5) => 'fi-row-warning',
                
                // Advertencia: Próximo a vencer (naranja)
                ($record->expires_at && now()->diffInDays($record->expires_at, false) <= 30) => 'fi-row-warning',
                
                // Info: Stock excedido (azul)
                $record->stock > ($record->max_stock ?? 100) => 'fi-row-info',
                
                // Normal
                default => null,
            })
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession()
            ->paginationPageOptions([10, 25, 50, 100])
            ->defaultPaginationPageOption(25);
    }
}
