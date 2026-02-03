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
                    ->label('Precio')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('stock')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(function ($record) {
                        $stock = $record->stock;
                        $min = $record->stock_minimum ?? 20;
                        
                        if ($stock == 0) {
                            return 'danger';
                        } elseif ($stock <= $min) {
                            return 'danger';
                        } elseif ($stock <= ($min + 10)) {
                            return 'warning'; // Naranja/amarillo
                        } else {
                            return 'success';
                        }
                    })
                    ->description(function ($record) {
                        $stock = $record->stock;
                        $min = $record->stock_minimum ?? 20;
                        
                        if ($stock == 0) {
                            return '¡Sin stock!';
                        } elseif ($stock <= $min) {
                            return 'Stock bajo';
                        } elseif ($stock <= ($min + 10)) {
                            return 'Stock medio (Mín: ' . $min . ')';
                        } else {
                            return 'Stock normal (Mín: ' . $min . ')';
                        }
                    })
                    ->toggleable(),
                TextColumn::make('expiration_date')
                    ->label('Vencimiento')
                    ->sortable()
                    ->date('d/m/Y')
                    ->placeholder('Sin fecha')
                    ->badge()
                    ->color(function ($record) {
                        if (!$record->expiration_date) return 'gray';
                        
                        $daysUntilExpiration = now()->diffInDays($record->expiration_date, false);
                        
                        if ($daysUntilExpiration < 0) {
                            return 'danger';
                        } elseif ($daysUntilExpiration <= 30) {
                            return 'warning';
                        } else {
                            return 'success';
                        }
                    })
                    ->description(function ($record) {
                        if (!$record->expiration_date) return null;
                        
                        $daysUntilExpiration = now()->diffInDays($record->expiration_date, false);
                        
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
                TextColumn::make('supplier.name')
                    ->label('Proveedor')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('stock_minimum')
                    ->label('Stock Mín.')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('stock_maximum')
                    ->label('Stock Máx.')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->query(fn ($query) => $query->whereColumn('stock', '<=', 'stock_minimum')->where('stock', '>', 0))
                    ->toggle(),
                Filter::make('out_of_stock')
                    ->label('Sin Stock')
                    ->query(fn ($query) => $query->where('stock', 0))
                    ->toggle(),
                Filter::make('expiring_soon')
                    ->label('Próximos a Vencer')
                    ->query(function ($query) {
                        $alertDays = \Illuminate\Support\Facades\Cache::get('settings.expiration_alert_days', 30);
                        return $query->whereNotNull('expiration_date')
                            ->whereDate('expiration_date', '<=', now()->addDays($alertDays))
                            ->whereDate('expiration_date', '>=', now());
                    })
                    ->toggle(),
                Filter::make('expired')
                    ->label('Vencidos')
                    ->query(fn ($query) => $query->whereNotNull('expiration_date')->whereDate('expiration_date', '<', now()))
                    ->toggle(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn () => auth()->user()->role === 'admin'),
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
                    ->visible(fn ($record) => $record->stock > 0),
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
                // Crítico: Sin stock o vencido (rojo)
                $record->stock == 0 => 'fi-row-danger',
                ($record->expiration_date && now()->diffInDays($record->expiration_date, false) < 0) => 'fi-row-danger',
                
                // Advertencia: Stock bajo (naranja)
                $record->stock <= ($record->stock_minimum ?? 20) => 'fi-row-warning',
                
                // Advertencia: Próximo a vencer (naranja)
                ($record->expiration_date && now()->diffInDays($record->expiration_date, false) <= 30) => 'fi-row-warning',
                
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
