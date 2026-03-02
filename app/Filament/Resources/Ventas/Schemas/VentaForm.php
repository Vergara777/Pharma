<?php

namespace App\Filament\Resources\Ventas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Notifications\Notification;
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
                                    ->afterStateUpdated(function ($state, callable $set, callable $get, $livewire) {
                                        if (empty($state)) return;
                                        self::addItemToCart($state, $set, $get, 'sku', $livewire);
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
                                    ->afterStateUpdated(function ($state, callable $set, callable $get, $livewire) {
                                        if (empty($state)) return;
                                        self::addItemToCart($state, $set, $get, 'id', $livewire);
                                        $set('manual_search', null);
                                    })
                                    ->columnSpan(7),
                            ]),

                        \Filament\Forms\Components\Repeater::make('products')
                            ->label('Productos Añadidos')
                            ->schema([
                                Grid::make(12)
                                    ->schema([
                                        // Fila 1: Info del Producto y Presentación
                                        \Filament\Forms\Components\Placeholder::make('product_info')
                                            ->hiddenLabel()
                                            ->content(function (callable $get) {
                                                $name = $get('product_name');
                                                $sku = $get('product_sku');
                                                $image = $get('product_image');
                                                $imageUrl = $image && str_starts_with($image, 'http') ? $image : url('/Images/Pharma1.jpeg');
                                                return new \Illuminate\Support\HtmlString("
                                                    <div class='flex items-center gap-4 py-2'>
                                                        <img src='{$imageUrl}' class='w-14 h-14 rounded-xl object-cover shadow-sm ring-1 ring-gray-200' />
                                                        <div class='flex flex-col'>
                                                            <span class='font-bold text-gray-950 text-base leading-tight'>{$name}</span>
                                                            <span class='text-xs font-medium text-gray-500 mt-1'>SKU: {$sku}</span>
                                                        </div>
                                                    </div>
                                                ");
                                            })
                                            ->columnSpan(7),

                                        \Filament\Forms\Components\Select::make('type')
                                            ->label('Presentación de Venta')
                                            ->options([
                                                'unit' => 'Venta por Unidad',
                                                'package' => 'Venta por Paquete/Caja'
                                            ])
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                $productId = $get('product_id');
                                                $product = Product::find($productId);
                                                if ($product) {
                                                    $price = $state === 'package' ? ($product->price_package ?: $product->price) : ($product->price_unit ?: $product->price);
                                                    $set('unit_price', $price);
                                                    $set('unit_price_display', number_format($price, 0, ',', '.'));
                                                    self::syncWithService($set, $get, $livewire);
                                                }
                                            })
                                            ->columnSpan(5),

                                        // Fila 2: Cantidad, Precio Unitario y Subtotal
                                        TextInput::make('qty')
                                            ->label('Cantidad a Vender')
                                            ->numeric()
                                            ->required()
                                            ->minValue(1)
                                            ->default(1)
                                            ->live(debounce: 300) // Debounce para suavidad
                                            ->afterStateUpdated(function ($state, callable $set, callable $get, $livewire) {
                                                self::syncWithService($set, $get, $livewire);
                                            })
                                            ->columnSpan(4),

                                        TextInput::make('unit_price_display')
                                            ->label('Precio de Venta')
                                            ->readOnly()
                                            ->prefix('$')
                                            ->extraInputAttributes(['class' => 'bg-gray-50 font-medium'])
                                            ->columnSpan(4),

                                        TextInput::make('subtotal_item_display')
                                            ->label('Subtotal Item')
                                            ->readOnly()
                                            ->prefix('$')
                                            ->dehydrated(false)
                                            ->extraInputAttributes(['class' => 'bg-amber-50 font-black text-amber-700 text-lg border-amber-200'])
                                            ->columnSpan(4),

                                        // Hiddens
                                        Hidden::make('product_id'),
                                        Hidden::make('product_name'),
                                        Hidden::make('product_sku'),
                                        Hidden::make('product_image'),
                                        Hidden::make('unit_price'),
                                        Hidden::make('cart_id'),
                                    ]),
                            ])
                            ->defaultItems(0)
                            ->addable(false)
                            ->reorderable(false)
                            ->deletable(true)
                            ->deleteAction(
                                fn ($action) => $action->after(fn (callable $get, callable $set, $livewire) => 
                                    self::syncWithService($set, $get, $livewire)
                                )
                            )
                            ->itemLabel(fn (array $state): ?string => $state['product_name'] ?? null)
                            ->collapsible()
                            ->columnSpanFull(),

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
                                Hidden::make('grand_total'),
                            ]),
                    ]),
                
                Section::make('Información del Cliente')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Toggle::make('anonymous_invoice')
                                    ->label('Venta Anónima (Consumidor Final)')
                                    ->helperText('Activa esto para no solicitar datos al cliente.')
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $set('cliente_id', null);
                                            $set('customer_name', 'CONSUMIDOR FINAL');
                                            $set('customer_phone', null);
                                            $set('customer_email', null);
                                            $set('invoice_document', '222222222222'); // NIT genérico Colombia
                                        } else {
                                            $set('customer_name', null);
                                            $set('invoice_document', null);
                                        }
                                    })
                                    ->columnSpanFull()
                                    ->onColor('warning'),

                                Select::make('cliente_id')
                                    ->label('Seleccionar Cliente Registrado')
                                    ->placeholder('Buscar cliente existente...')
                                    ->relationship('cliente', 'name', fn ($query) => $query->where('is_active', true))
                                    ->searchable(['name', 'document', 'phone'])
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $cliente = \App\Models\Cliente::find($state);
                                            if ($cliente) {
                                                // Verificar si el cliente está inactivo
                                                if (!$cliente->is_active) {
                                                    $set('cliente_id', null);
                                                    $set('customer_name', null);
                                                    $set('customer_phone', null);
                                                    $set('customer_email', null);
                                                    $set('invoice_document', null);
                                                    $set('invoice_address', null);
                                                    
                                                    \Filament\Notifications\Notification::make()
                                                        ->danger()
                                                        ->title('Cliente Inactivo')
                                                        ->body('No se puede realizar una venta a un cliente inactivo. Por favor, activa el cliente primero o selecciona otro.')
                                                        ->persistent()
                                                        ->send();
                                                    return;
                                                }
                                                
                                                $set('customer_name', $cliente->name);
                                                $set('customer_phone', $cliente->phone);
                                                $set('customer_email', $cliente->email);
                                                $set('invoice_document', $cliente->document);
                                                $set('invoice_address', $cliente->address);
                                            }
                                        }
                                    })
                                    ->disabled(fn (callable $get) => $get('anonymous_invoice'))
                                    ->columnSpanFull()
                                    ->helperText('Solo se muestran clientes activos. O ingresa los datos manualmente abajo para crear un nuevo cliente')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name . ($record->document ? " - {$record->document}" : '')),

                                TextInput::make('customer_name')
                                    ->label('Nombre Completo')
                                    ->placeholder('Ej: Juan Pérez')
                                    ->required()
                                    ->disabled(fn (callable $get) => $get('anonymous_invoice'))
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        // Si hay un cliente seleccionado y se modifica el nombre, deseleccionarlo
                                        if ($get('cliente_id')) {
                                            $cliente = \App\Models\Cliente::find($get('cliente_id'));
                                            if ($cliente && $cliente->name !== $state) {
                                                $set('cliente_id', null);
                                            }
                                        }
                                    })
                                    ->dehydrated(),
                                TextInput::make('customer_phone')
                                    ->placeholder('Ej: 300 123 4567')
                                    ->label('Teléfono')
                                    ->disabled(fn (callable $get) => $get('anonymous_invoice')),
                                TextInput::make('customer_email')
                                    ->placeholder('Ej: juan@email.com')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->disabled(fn (callable $get) => $get('anonymous_invoice'))
                                    ->dehydrated(),
                            ]),
                        Grid::make(3)
                            ->schema([
                                TextInput::make('invoice_document')
                                    ->placeholder('Ej: 123456789')
                                    ->label('Documento / NIT')
                                    ->disabled(fn (callable $get) => $get('anonymous_invoice'))
                                    ->dehydrated(),
                                TextInput::make('invoice_address')
                                    ->placeholder('Ej: Calle 123 # 45 - 67')
                                    ->label('Dirección')
                                    ->columnSpan(2)
                                    ->disabled(fn (callable $get) => $get('anonymous_invoice'))
                                    ->dehydrated(),
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
                                    ->columnSpan(6),

                                Toggle::make('exact_amount')
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
                                    ->inline(false)
                                    ->onColor('success')
                                    ->offColor('gray'),
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
                                        $cleanState = (float) str_replace(['.', ','], '', $state);
                                        $formatted = number_format($cleanState, 0, ',', '.');
                                        $set('amount_received', $formatted);
                                        self::calculateChange($set, $cleanState, (float) $get('grand_total'));
                                    })
                                    ->extraInputAttributes(function (callable $get) {
                                        $received = (float) str_replace(['.', ','], '', $get('amount_received') ?? '0');
                                        $total = (float) $get('grand_total');
                                        if ($received > 0 && $received < $total) {
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
                                            // Verde (Cambio listo)
                                            return ['class' => 'font-bold text-xl text-white bg-success-600 border-success-700'];
                                        } elseif ($change === 0 || $change === 0.0) {
                                            // Amarillo/Primario (Exacto)
                                            return ['class' => 'font-bold text-xl text-white bg-amber-500 border-amber-600'];
                                        } else {
                                            // Rojo (Insuficiente)
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
                            ->label('Generar Factura Automática')
                            ->helperText('Al activar esto, se creará una factura formal en el módulo de Facturas con los datos del cliente y productos de esta venta.')
                            ->inline(false)
                            ->live()
                            ->default(true) // Activado por defecto
                            ->disabled(fn (callable $get) => $get('anonymous_invoice'))
                            ->onColor('success')
                            ->offColor('danger')
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                if ($state && $get('anonymous_invoice')) {
                                    $set('generate_invoice', false);
                                    Notification::make()
                                        ->warning()
                                        ->title('No se puede generar factura')
                                        ->body('Las ventas anónimas no generan facturas. Desactiva "Venta Anónima" primero.')
                                        ->send();
                                }
                            }),
                        
                        \Filament\Forms\Components\Placeholder::make('invoice_note')
                            ->content('Las ventas anónimas (Consumidor Final) no generan facturas automáticas.')
                            ->visible(fn (callable $get) => $get('anonymous_invoice')),
                    ])
                    ->collapsed(false)
                    ->compact(),
            ]);
    }

    public static function addItemToCart($identifier, callable $set, callable $get, $type = 'sku', $livewire = null): void
    {
        $product = $type === 'id' 
            ? Product::find($identifier)
            : Product::where('sku', $identifier)->first();
        
        if (!$product) {
            \Filament\Notifications\Notification::make()->title('Producto no encontrado')->danger()->send();
            return;
        }

        \App\Services\CartService::add($product, 1, 'unit');
        self::loadCartIntoForm($set);

        if ($livewire) {
            $livewire->dispatch('cartUpdated');
        }
    }

    public static function loadCartIntoForm(callable $set): void
    {
        $data = self::getCartFormData();
        foreach ($data as $key => $value) {
            $set($key, $value);
        }
    }

    public static function getCartFormData(): array
    {
        $cart = \App\Services\CartService::getCart();
        $products = [];
        $total = 0;

        foreach ($cart as $id => $item) {
            $price = (float)$item['price'];
            $qty = (int)$item['qty'];
            $subtotal = $price * $qty;
            $total += $subtotal;

            $products[] = [
                'cart_id' => $id,
                'product_id' => $item['product_id'],
                'product_name' => $item['name'],
                'product_sku' => $item['sku'],
                'product_image' => $item['image'],
                'qty' => $qty,
                'type' => $item['type'],
                'unit_price' => $price,
                'unit_price_display' => number_format($price, 0, ',', '.'),
                'subtotal_item_display' => number_format($subtotal, 0, ',', '.')
            ];
        }

        return [
            'products' => $products,
            'grand_total' => $total,
            'grand_total_display' => number_format($total, 0, ',', '.'),
        ];
    }

    public static function syncWithService(callable $set, callable $get, $livewire = null): void
    {
        $products = $get('products') ?? [];
        \App\Services\CartService::clear();
        
        $adjusted = false;
        foreach ($products as $key => $item) {
            $product = Product::find($item['product_id'] ?? null);
            if ($product) {
                $qty = (int)($item['qty'] ?? 1);
                $type = $item['type'] ?? 'unit';
                
                // Cálculo de unidades reales
                $requestedUnits = $qty;
                if ($type === 'package') {
                    $requestedUnits *= ($product->units_per_package ?: 1);
                }

                // Validar stock
                if ($product->stock < $requestedUnits) {
                    $maxPossibleQty = $type === 'package' 
                        ? floor($product->stock / ($product->units_per_package ?: 1))
                        : $product->stock;

                    // Ajustar el objeto en el array del form
                    $products[$key]['qty'] = $maxPossibleQty;
                    $qty = $maxPossibleQty;
                    $adjusted = true;

                    \Filament\Notifications\Notification::make()
                        ->title('Stock insuficiente para ' . $product->name)
                        ->body("Ajustado al máximo disponible: {$maxPossibleQty}")
                        ->warning()
                        ->send();
                }

                \App\Services\CartService::add($product, $qty, $type);
            }
        }

        if ($adjusted) {
            $set('products', $products);
        }
        
        self::loadCartIntoForm($set);

        // Notificar al Badge y otros componentes de inmediato
        if ($livewire) {
            $livewire->dispatch('cartUpdated');
        }
        
        $total = (float)$get('grand_total');
        $receivedStr = $get('amount_received');
        $received = (float) str_replace(['.', ','], '', $receivedStr ?? '0');

        if ($get('exact_amount')) {
            $set('amount_received', number_format($total, 0, ',', '.'));
            self::calculateChange($set, $total, $total);
        } else {
            self::calculateChange($set, $received, $total);
        }
    }

    protected static function isCash(callable $get): bool 
    {
        $methodId = $get('payment_method_id');
        if (!$methodId) return false;
        $method = \App\Models\PaymentMethod::find($methodId);
        return $method && $method->code === 'cash';
    }

    protected static function calculateChange(callable $set, float $received, float $total): void
    {
        $change = $received - $total;
        $set('change_amount', $change);
        $set('change_amount_display', number_format($change, 0, ',', '.'));
    }
}
