<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // Información Básica del producto
                TextInput::make('display_no')
                    ->label('Número de posición')
                    ->numeric()
                    ->default(0)
                    ->placeholder('0')
                    ->helperText('Orden de visualización en la lista')
                    ->prefixIcon('heroicon-m-list-bullet'),
                    
                TextInput::make('sku')
                    ->label('Digite SKU o Código de Barras')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('7701234567890')
                    ->helperText('Escanea el código de barras')
                    ->prefixIcon('heroicon-m-qr-code')
                    ->autocomplete(false),
                    
                TextInput::make('name')
                    ->label('Nombre del Producto')
                    ->required()
                    ->placeholder('Acetaminofén 500mg')
                    ->columnSpanFull()
                    ->prefixIcon('heroicon-m-tag'),
                    
                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(6)
                    ->placeholder('Caja por 10 tabletas')
                    ->columnSpanFull(),                    
                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activo',
                        'retired' => 'Retirado',
                    ])
                    ->default('active')
                    ->required()
                    ->prefixIcon('heroicon-m-check-circle'),
                    
                Select::make('category_id')
                    ->label('Categoría')
                    ->relationship(
                        name: 'category', 
                        titleAttribute: 'name', 
                        modifyQueryUsing: fn ($query) => $query->where('status', 'active')
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Selecciona una categoría')
                    ->prefixIcon('heroicon-m-squares-2x2')
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->prefixIcon('heroicon-m-tag'),
                        TextInput::make('description')
                            ->label('Descripción')
                            ->required()
                            ->prefixIcon('heroicon-m-chat-bubble-bottom-center-text'),
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'active' => 'Activo',
                                'inactive' => 'Inactivo',
                            ])
                            ->default('active')
                            ->required()
                            ->prefixIcon('heroicon-m-check-circle'),
                    ]),

                // Precios y Costos del producto
                TextInput::make('cost')
                    ->label('Costo de Compra')
                    ->prefix('$')
                    ->prefixIcon('heroicon-m-banknotes')
                    ->default(0)
                    ->placeholder('15.000')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, '', '.') : '0')
                    ->dehydrateStateUsing(fn ($state) => $state ? (int) str_replace('.', '', $state) : 0),
                    
                TextInput::make('price')
                    ->label('Precio de Venta')
                    ->required()
                    ->prefix('$')
                    ->prefixIcon('heroicon-m-currency-dollar')
                    ->default(0)
                    ->placeholder('25.000')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, '', '.') : '0')
                    ->dehydrateStateUsing(fn ($state) => $state ? (int) str_replace('.', '', $state) : 0),

                // Unidades y Presentaciones
                TextInput::make('unit_name')
                    ->label('Unidad')
                    ->default('unidad')
                    ->placeholder('tableta, cápsula, ml')
                    ->helperText('Nombre de la unidad suelta')
                    ->prefixIcon('heroicon-m-beaker'),
                    
                TextInput::make('units_per_package')
                    ->label('Unids/Presentación')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->placeholder('10')
                    ->helperText('Cantidad por presentación')
                    ->prefixIcon('heroicon-m-hashtag'),
                    
                TextInput::make('package_name')
                    ->label('Presentación')
                    ->placeholder('Caja x 10, Blíster x 8')
                    ->helperText('Nombre de la presentación')
                    ->prefixIcon('heroicon-m-cube'),
                    
                TextInput::make('price_unit')
                    ->label('Precio por Unidad')
                    ->prefix('$')
                    ->prefixIcon('heroicon-m-banknotes')
                    ->placeholder('2.500')
                    ->helperText('Si se vende suelto')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, '', '.') : '')
                    ->dehydrateStateUsing(fn ($state) => $state ? (int) str_replace('.', '', $state) : null),
                    
                TextInput::make('price_package')
                    ->label('Precio Presentación')
                    ->prefix('$')
                    ->prefixIcon('heroicon-m-banknotes')
                    ->placeholder('25.000')
                    ->helperText('Si se vende completo')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, '', '.') : '')
                    ->dehydrateStateUsing(fn ($state) => $state ? (int) str_replace('.', '', $state) : null),
                    
                Select::make('supplier_id')
                    ->label('Proveedor')
                    ->relationship('supplier', 'name', fn ($query) => $query->where('status', 'active'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Selecciona un proveedor')
                    ->prefixIcon('heroicon-m-truck')
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->prefixIcon('heroicon-m-user'),
                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->prefixIcon('heroicon-m-phone'),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->prefixIcon('heroicon-m-envelope'),
                        TextInput::make('address')
                            ->label('Dirección')
                            ->prefixIcon('heroicon-m-map-pin'),
                    ]),

                // Inventario
                TextInput::make('stock')
                    ->label('Stock Actual')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->placeholder('Ingresa la cantidad')
                    ->prefixIcon('heroicon-m-circle-stack'),
                    
                DatePicker::make('expires_at')
                    ->label('Fecha de Vencimiento')
                    ->timezone('America/Bogota')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->placeholder('dd/mm/aaaa')
                    ->prefixIcon('heroicon-m-calendar'),

                // Toggle para límites de stock personalizados
                Toggle::make('custom_stock_limits')
                    ->label('Personalizar límites de stock')
                    ->live()
                    ->default(false)
                    ->columnSpanFull()
                    ->dehydrated(false),
                    
                TextInput::make('min_stock')
                    ->label('Stock Mínimo')
                    ->numeric()
                    ->default(5)
                    ->placeholder('5')
                    ->helperText('Alerta cuando llegue a este nivel')
                    ->prefixIcon('heroicon-m-arrow-down-circle')
                    ->visible(fn ($get) => $get('custom_stock_limits'))
                    ->dehydrateStateUsing(fn ($state) => $state ?? 5),
                    
                TextInput::make('max_stock')
                    ->label('Stock Máximo')
                    ->numeric()
                    ->default(100)
                    ->placeholder('100')
                    ->helperText('Capacidad máxima recomendada')
                    ->prefixIcon('heroicon-m-arrow-up-circle')
                    ->visible(fn ($get) => $get('custom_stock_limits'))
                    ->dehydrateStateUsing(fn ($state) => $state ?? 100),

                // Ubicación
                TextInput::make('shelf')
                    ->label('Estante')
                    ->maxLength(10)
                    ->placeholder('A, B, C')
                    ->prefixIcon('heroicon-m-archive-box'),
                    
                TextInput::make('row')
                    ->label('Fila')
                    ->maxLength(10)
                    ->placeholder('1, 2, 3')
                    ->prefixIcon('heroicon-m-bars-3'),
                    
                TextInput::make('position')
                    ->label('Posición')
                    ->maxLength(10)
                    ->placeholder('1, 2, 3')
                    ->columnSpanFull()
                    ->prefixIcon('heroicon-m-map-pin'),

                // Imagen
                Toggle::make('use_image_url')
                    ->label('Usar URL de imagen')
                    ->live()
                    ->afterStateHydrated(function ($set, $record) {
                        if ($record && $record->image && str_starts_with($record->image, 'http')) {
                            $set('use_image_url', true);
                        } else {
                            $set('use_image_url', false);
                        }
                    })
                    ->columnSpanFull()
                    ->dehydrated(false),
                    
                FileUpload::make('image_file')
                    ->label('Subir Imagen')
                    ->image()
                    ->avatar()
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '1:1',
                    ])
                    ->disk('public')
                    ->directory('products')
                    ->visibility('public')
                    ->previewable(true)
                    ->hidden(fn ($get) => $get('use_image_url'))
                    ->columnSpanFull()
                    ->live()
                    ->formatStateUsing(function ($record) {
                        if ($record && $record->image && !str_starts_with($record->image, 'http')) {
                            return $record->image;
                        }
                        return null;
                    })
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (filled($state)) {
                            $set('image', $state);
                        }
                    })
                    ->dehydrated(false),
                    
                TextInput::make('image')
                    ->label('URL de Imagen')
                    ->placeholder('https://ejemplo.com/imagen.jpg')
                    ->url()
                    ->live(onBlur: true)
                    ->hidden(fn ($get) => !$get('use_image_url'))
                    ->columnSpanFull()
                    ->prefixIcon('heroicon-m-link'),

                Placeholder::make('url_preview')
                    ->label('Vista Previa (URL)')
                    ->content(function ($get) {
                        $url = $get('image');
                        if (!$url || !str_starts_with($url, 'http')) return null;
                        
                        return new \Illuminate\Support\HtmlString("
                            <div style='display: flex; justify-content: center; padding: 10px;'>
                                <div style='width: 160px; height: 160px; border-radius: 50%; overflow: hidden; border: 4px solid white; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);'>
                                    <img src='{$url}' style='width: 100%; height: 100%; object-fit: cover;' onerror=\"this.style.display='none';\" />
                                </div>
                            </div>
                        ");
                    })
                    ->hidden(fn ($get) => !$get('use_image_url'))
                    ->columnSpanFull(),
            ]);
    }
}
