<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // Información Básica
                TextInput::make('display_no')
                    ->label('Número de Display')
                    ->numeric()
                    ->default(0)
                    ->placeholder('0')
                    ->helperText('Orden de visualización'),
                    
                TextInput::make('sku')
                    ->label('Digite SKU o Código de Barras')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('7701234567890')
                    ->helperText('Escanea el código de barras')
                    ->suffixIcon('gmdi-barcode-reader')
                    ->autocomplete(false),
                    
                TextInput::make('name')
                    ->label('Nombre del Producto')
                    ->required()
                    ->placeholder('Acetaminofén 500mg')
                    ->columnSpanFull()
                    ->suffixIcon('gmdi-box'),
                    
                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(2)
                    ->placeholder('Caja por 10 tabletas')
                    ->columnSpanFull(),                    
                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activo',
                        'retired' => 'Retirado',
                    ])
                    ->default('active')
                    ->required(),
                    
                Select::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name', 'description', 'status', 'created_at', 'updated_at', fn ($query) => $query->where('status', 'active'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Selecciona una categoría')
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required(),
                        TextInput::make('description')
                            ->label('Descripción')
                            ->required(),
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'active' => 'Activo',
                                'inactive' => 'Inactivo',
                            ])
                            ->default('active')
                            ->required(),
                    ]),

                // Precios y Costos
                TextInput::make('cost')
                    ->label('Costo de Compra')
                    ->prefix('$')
                    ->default(0)
                    ->placeholder('15.000')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, '', '.') : '0')
                    ->dehydrateStateUsing(fn ($state) => $state ? (int) str_replace('.', '', $state) : 0),
                    
                TextInput::make('price')
                    ->label('Precio de Venta')
                    ->required()
                    ->prefix('$')
                    ->default(0)
                    ->placeholder('25.000')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, '', '.') : '0')
                    ->dehydrateStateUsing(fn ($state) => $state ? (int) str_replace('.', '', $state) : 0),

                // Unidades y Presentaciones (juntos)
                TextInput::make('unit_name')
                    ->label('Unidad')
                    ->default('unidad')
                    ->placeholder('tableta, cápsula, ml')
                    ->helperText('Nombre de la unidad suelta'),
                    
                TextInput::make('units_per_package')
                    ->label('Unids/Presentación')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->placeholder('10')
                    ->helperText('Cantidad por presentación'),
                    
                TextInput::make('package_name')
                    ->label('Presentación')
                    ->placeholder('Caja x 10, Blíster x 8')
                    ->helperText('Nombre de la presentación'),
                    
                TextInput::make('price_unit')
                    ->label('Precio por Unidad')
                    ->prefix('$')
                    ->placeholder('2.500')
                    ->helperText('Si se vende suelto')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, '', '.') : '')
                    ->dehydrateStateUsing(fn ($state) => $state ? (int) str_replace('.', '', $state) : null),
                    
                TextInput::make('price_package')
                    ->label('Precio Presentación')
                    ->prefix('$')
                    ->placeholder('25.000')
                    ->helperText('Si se vende completo')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, '', '.') : '')
                    ->dehydrateStateUsing(fn ($state) => $state ? (int) str_replace('.', '', $state) : null),
                    
                Select::make('supplier_id')
                    ->label('Proveedor')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Selecciona un proveedor')
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required(),
                        TextInput::make('phone')
                            ->label('Teléfono'),
                        TextInput::make('email')
                            ->label('Email')
                            ->email(),
                        TextInput::make('address')
                            ->label('Dirección'),
                    ]),

                // Inventario
                TextInput::make('stock')
                    ->label('Stock Actual')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->placeholder('Ingresa la cantidad'),
                    
                DatePicker::make('expires_at')
                    ->label('Fecha de Vencimiento')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->placeholder('dd/mm/aaaa'),

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
                    ->visible(fn ($get) => $get('custom_stock_limits'))
                    ->dehydrateStateUsing(fn ($state) => $state ?? 5),
                    
                TextInput::make('max_stock')
                    ->label('Stock Máximo')
                    ->numeric()
                    ->default(100)
                    ->placeholder('100')
                    ->helperText('Capacidad máxima recomendada')
                    ->visible(fn ($get) => $get('custom_stock_limits'))
                    ->dehydrateStateUsing(fn ($state) => $state ?? 100),

                // Ubicación
                TextInput::make('shelf')
                    ->label('Estante')
                    ->maxLength(10)
                    ->placeholder('A, B, C'),
                    
                TextInput::make('row')
                    ->label('Fila')
                    ->maxLength(10)
                    ->placeholder('1, 2, 3'),
                    
                TextInput::make('position')
                    ->label('Posición')
                    ->maxLength(10)
                    ->placeholder('1, 2, 3')
                    ->columnSpanFull(),

                // Imagen
                Toggle::make('use_image_url')
                    ->label('Usar URL de imagen')
                    ->live()
                    ->default(fn ($record) => $record && $record->image && str_starts_with($record->image, 'http'))
                    ->columnSpanFull()
                    ->dehydrated(false),
                    
                FileUpload::make('image_file')
                    ->label('Subir Imagen')
                    ->image()
                    ->imageEditor()
                    ->disk('local')
                    ->directory('private')
                    ->visibility('private')
                    ->hidden(fn ($get) => $get('use_image_url'))
                    ->columnSpanFull()
                    ->live()
                    ->afterStateHydrated(function ($component, $state, $record, $get) {
                        if ($record && $record->image && !str_starts_with($record->image, 'http') && !$get('use_image_url')) {
                            $component->state($record->image);
                        }
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
                    ->hidden(fn ($get) => !$get('use_image_url'))
                    ->columnSpanFull(),
            ]);
    }
}
