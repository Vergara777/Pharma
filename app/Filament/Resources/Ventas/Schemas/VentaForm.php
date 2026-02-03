<?php

namespace App\Filament\Resources\Ventas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\Product;

class VentaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalle de Venta')
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                TextInput::make('barcode_search')
                                    ->label('Escanear (SKU/Código)')
                                    ->placeholder('Ej: 770123456789')
                                    ->autofocus()
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        if (empty($state)) return;
                                        self::addProductToCart($state, $set, $get, 'sku');
                                        $set('barcode_search', null);
                                    })
                                    ->suffixIcon('heroicon-m-qr-code')
                                    ->columnSpan(5),
                                
                                Select::make('manual_search')
                                    ->label('Buscar Producto Manualmente')
                                    ->placeholder('Ej: Acetaminofén...')
                                    ->options(Product::query()->where('stock', '>', 0)->pluck('name', 'id'))
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        if (empty($state)) return;
                                        self::addProductToCart($state, $set, $get, 'id');
                                        $set('manual_search', null);
                                    })
                                    ->columnSpan(7),
                            ]),

                        \Filament\Forms\Components\Repeater::make('products')
                            ->label('Productos Añadidos')
                            ->schema([
                                Grid::make(12)
                                    ->schema([
                                        // Imagen + Detalles (4/12 = 33%)
                                        \Filament\Forms\Components\Placeholder::make('product_info')
                                            ->hiddenLabel()
                                            ->content(function (callable $get) {
                                                $name = $get('product_name');
                                                $sku = $get('product_sku');
                                                $image = $get('product_image');
                                                
                                                $imageUrl = $image && str_starts_with($image, 'http') 
                                                    ? $image 
                                                    : url('/Images/Pharma1.jpeg');
                                                
                                                return new \Illuminate\Support\HtmlString("
                                                    <div class='flex items-center gap-3 h-full'>
                                                        <img src='{$imageUrl}' class='w-16 h-16 rounded-lg object-cover shadow-sm' />
                                                        <div class='flex flex-col justify-center'>
                                                            <span class='font-semibold text-gray-900 text-base'>{$name}</span>
                                                            <span class='text-sm text-gray-500'>SKU: {$sku}</span>
                                                        </div>
                                                    </div>
                                                ");
                                            })
                                            ->columnSpan(4),

                                        // Cantidad (2/12 = 17%)
                                        TextInput::make('qty')
                                            ->label('Cant.')
                                            ->numeric()
                                            ->required()
                                            ->minValue(1)
                                            ->default(1)
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                $stock = $get('stock_available') ?? 0;
                                                if ($state > $stock) {
                                                    $set('qty', $stock);
                                                    \Filament\Notifications\Notification::make()
                                                        ->title('Stock límite')
                                                        ->body("Solo hay $stock unidades.")
                                                        ->warning()
                                                        ->send();
                                                }
                                                self::calculateGrandTotal($set, $get);
                                            })
                                            ->columnSpan(2),

                                        // Precio (3/12 = 25%)
                                        TextInput::make('unit_price_display')
                                            ->label('Precio Unit.')
                                            ->readOnly()
                                            ->prefix('$')
                                            ->extraInputAttributes(['class' => 'bg-gray-50'])
                                            ->columnSpan(3),

                                        // Subtotal (3/12 = 25%)
                                        TextInput::make('subtotal_item_display')
                                            ->label('Subtotal')
                                            ->readOnly()
                                            ->prefix('$')
                                            ->dehydrated(false)
                                            ->extraInputAttributes(['class' => 'bg-gray-100 font-bold text-gray-900'])
                                            ->columnSpan(3),

                                        // Hiddens
                                        Hidden::make('product_id'),
                                        Hidden::make('product_name'),
                                        Hidden::make('product_sku'),
                                        Hidden::make('product_image'),
                                        Hidden::make('unit_price'),
                                        Hidden::make('stock_available'),
                                    ])
                                    ->extraAttributes(['class' => 'items-center gap-4']),
                            ])
                            ->defaultItems(0)
                            ->addable(false) // Deshabilitar botón de agregar manual del repeater, usamos el buscador de arriba
                            ->reorderable(false)
                            ->collapsible(false)
                            ->deleteAction(
                                fn ($action) => $action->after(fn (callable $get, callable $set) => 
                                    self::calculateGrandTotal($set, $get)
                                )
                            )
                            ->columnSpanFull()
                            ->grid(1)
                            ->extraAttributes(['class' => 'gap-4 space-y-4']),

                        Grid::make(3)
                            ->schema([
                                \Filament\Forms\Components\Placeholder::make('space')
                                    ->columnSpan(2)
                                    ->content(''),
                                TextInput::make('grand_total_display')
                                    ->label('TOTAL A PAGAR')
                                    ->prefix('$')
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->default(0)
                                    ->extraInputAttributes(['class' => 'font-black text-4xl text-primary-600 text-right'])
                                    ->columnSpan(1),
                                Hidden::make('grand_total'), // Raw total for DB
                            ]),
                    ]),
                
                Section::make('Información del Cliente')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('customer_name')
                                    ->label('Nombre Completo')
                                    ->placeholder('Ej: Juan Pérez')
                                    ->required(),
                                TextInput::make('customer_phone')
                                    ->placeholder('Ej: 300 123 4567')
                                    ->label('Teléfono'),
                                TextInput::make('customer_email')
                                    ->placeholder('Ej: juan@email.com')
                                    ->label('Correo Electrónico')
                                    ->email(),
                            ]),
                        Grid::make(3)
                            ->schema([
                                TextInput::make('invoice_document')
                                    ->placeholder('Ej: 123456789')
                                    ->label('Documento / NIT'),
                                TextInput::make('invoice_address')
                                    ->placeholder('Ej: Calle 123 # 45 - 67')
                                    ->label('Dirección')
                                    ->columnSpan(2),
                            ]),
                    ]),
                
                Section::make('Pago')
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                Select::make('payment_method_id')
                                    ->label('Método de Pago')
                                    ->relationship('paymentMethod', 'name', fn ($query) => $query->where('is_active', true))
                                    ->required()
                                    ->live()
                                    ->default(1)
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $set('exact_amount', false);
                                        $set('amount_received', null);
                                        $set('change_amount_display', null);
                                        $set('payment_reference', null);
                                    })
                                    ->columnSpan(5),
                                
                                \Filament\Forms\Components\Placeholder::make('spacer_check')
                                    ->content('')
                                    ->hiddenLabel()
                                    ->columnSpan(1),

                                Checkbox::make('exact_amount')
                                    ->label('Pago Exacto')
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        if ($state) {
                                            $total = (float) $get('grand_total');
                                            $set('amount_received', number_format($total, 0, ',', '.'));
                                            self::calculateChange($set, $total, $total);
                                        } else {
                                            $set('amount_received', null);
                                            $set('change_amount_display', null);
                                            $set('change_amount', 0);
                                        }
                                    })
                                    ->visible(fn (callable $get) => self::isCash($get))
                                    ->columnSpan(6)
                                    ->inline(true),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('payment_reference')
                                    ->label('Referencia / Voucher')
                                    ->placeholder('Ej: 000123')
                                    ->required(fn (callable $get) => !self::isCash($get))
                                    ->visible(fn (callable $get) => !self::isCash($get))
                                    ->columnSpanFull(),
                                
                                TextInput::make('amount_received')
                                    ->label('Monto Recibido')
                                    ->prefix('$')
                                    ->placeholder('Ej: 50.000')
                                    ->live(onBlur: true)
                                    ->readOnly(fn (callable $get) => $get('exact_amount'))
                                    ->required(fn (callable $get) => self::isCash($get))
                                    ->visible(fn (callable $get) => self::isCash($get))
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        if ($get('exact_amount')) return;
                                        
                                        // Limpiar y formatear
                                        $cleanState = (float) str_replace(['.', ','], '', $state);
                                        
                                        // Formatear con puntos
                                        $formatted = number_format($cleanState, 0, ',', '.');
                                        $set('amount_received', $formatted);
                                        
                                        self::calculateChange($set, $cleanState, (float) $get('grand_total'));
                                    })
                                    ->extraInputAttributes(function (callable $get) {
                                        $received = (float) str_replace(['.', ','], '', $get('amount_received') ?? '0');
                                        $total = (float) $get('grand_total');
                                        
                                        if ($received > 0 && $received < $total) {
                                            // Rojo si es insuficiente
                                            return ['class' => 'border-danger-500 bg-danger-50 text-danger-900 font-semibold'];
                                        }
                                        return [];
                                    })
                                    ->columnSpan(1),
                                    
                                TextInput::make('change_amount_display')
                                    ->label('Cambio / Faltante')
                                    ->prefix('$')
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->visible(fn (callable $get) => self::isCash($get))
                                    ->extraInputAttributes(function (callable $get) {
                                        $change = $get('change_amount') ?? 0;
                                        $received = $get('amount_received');
                                        
                                        if ($received === null || $received === '') return [];

                                        if ($change > 0) {
                                            // Verde brillante para cambio positivo
                                            return ['class' => 'font-bold text-xl text-white bg-success-600 border-success-700'];
                                        } elseif ($change === 0 || $change === 0.0) {
                                            // Azul para pago exacto
                                            return ['class' => 'font-bold text-xl text-white bg-primary-600 border-primary-700'];
                                        } else {
                                            // Rojo para faltante
                                            return ['class' => 'font-bold text-xl text-white bg-danger-600 border-danger-700'];
                                        }
                                    })
                                    ->columnSpan(1),
                                
                                Hidden::make('change_amount'),
                            ]),
                    ]),
                
                Section::make('Facturación')
                    ->schema([
                        Toggle::make('generate_invoice')
                            ->label('Generar Factura Electrónica / Ticket')
                            ->helperText('Se usarán los datos del cliente capturados arriba.')
                            ->inline(false)
                            ->live()
                            ->onColor('success')
                            ->offColor('danger'),
                    ])
                    ->collapsed(false)
                    ->compact(),
            ]);
    }

    public static function addProductToCart($identifier, callable $set, callable $get, $type = 'sku'): void
    {
        $product = $type === 'id' 
            ? Product::find($identifier)
            : Product::where('sku', $identifier)->first();
        
        if (!$product) {
            \Filament\Notifications\Notification::make()
                ->title('Producto no encontrado')
                ->danger()
                ->send();
            return;
        }

        if ($product->stock <= 0) {
            \Filament\Notifications\Notification::make()
                ->title('Sin Stock')
                ->body("El producto {$product->name} está agotado.")
                ->warning()
                ->send();
            return;
        }

        $currentItems = $get('products') ?? [];
        $existingIndex = null;
        
        foreach ($currentItems as $index => $item) {
            if (isset($item['product_id']) && $item['product_id'] == $product->id) {
                $existingIndex = $index;
                break;
            }
        }
        
        if ($existingIndex !== null) {
            if (($currentItems[$existingIndex]['qty'] + 1) > $product->stock) {
                \Filament\Notifications\Notification::make()
                    ->title('Stock insuficiente')
                    ->body("No hay más unidades disponibles")
                    ->warning()
                    ->send();
            } else {
                $currentItems[$existingIndex]['qty']++;
                
                \Filament\Notifications\Notification::make()
                    ->title('Cantidad actualizada')
                    ->success()
                    ->duration(1000)
                    ->send();
            }
        } else {
            $currentItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'product_image' => $product->image,
                'stock_available' => $product->stock,
                'qty' => 1,
                'unit_price' => $product->price,
                // 'subtotal_item' is calculated in calculateGrandTotal
            ];
            \Filament\Notifications\Notification::make()
                ->title('Agregado')
                ->body($product->name)
                ->success()
                ->duration(1000)
                ->send();
        }
        
        $set('products', $currentItems);
        self::calculateGrandTotal($set, $get);
    }

    protected static function isCash(callable $get): bool 
    {
        // Ajustar lógica según IDs reales de tu BD. Asumo ID 1 = Efectivo.
        $methodId = $get('payment_method_id');
        if (!$methodId) return false;
        $method = \App\Models\PaymentMethod::find($methodId);
        return $method && $method->code === 'cash';
    }

    protected static function calculateChange(callable $set, float $received, float $total): void
    {
        $change = $received - $total;
        $set('change_amount', $change);
        
        // Format display with dots, allow negative with sign
        $formatted = number_format($change, 0, ',', '.');
        $set('change_amount_display', $formatted);
    }

    protected static function calculateGrandTotal(callable $set, callable $get): void
    {
        $products = $get('products') ?? [];
        $total = 0;
        
        $updatedProducts = [];
        foreach ($products as $item) {
            $price = isset($item['unit_price']) ? (float)$item['unit_price'] : 0;
            $qty = isset($item['qty']) ? (int)$item['qty'] : 1;
            $subtotal = $price * $qty;
            
            $total += $subtotal;
            
            // Update display fields for this row
            $item['unit_price_display'] = number_format($price, 0, ',', '.');
            $item['subtotal_item_display'] = number_format($subtotal, 0, ',', '.');
            $updatedProducts[] = $item;
        }
        
        // Update repeater state to show formatted values
        $set('products', $updatedProducts);
        
        $set('grand_total', $total);
        $set('grand_total_display', number_format($total, 0, ',', '.'));
        
        // Recalcular cambio si hay monto recibido
        $amountReceivedStr = $get('amount_received');
        $amountReceived = 0;
        if ($amountReceivedStr) {
             $amountReceived = (float) str_replace('.', '', $amountReceivedStr);
        }

        if ($get('exact_amount')) {
            $set('amount_received', number_format($total, 0, ',', '.'));
            self::calculateChange($set, $total, $total);
        } elseif ($amountReceived > 0) {
            self::calculateChange($set, $amountReceived, $total);
        }
    }
}
