<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Grid;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sku')
                    ->label('SKU / Código de Barras')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Escanea el código de barras o escríbelo manualmente')
                    ->suffixIcon('heroicon-o-qr-code')
                    ->autocomplete(false),
                TextInput::make('name')
                    ->label('Nombre del Producto')
                    ->required(),
                Textarea::make('description')
                    ->label('Descripción')
                    ->columnSpanFull(),
                Toggle::make('use_image_url')
                    ->label('Usar URL de imagen')
                    ->live()
                    ->default(fn ($record) => $record && $record->image && str_starts_with($record->image, 'http'))
                    ->afterStateUpdated(function ($state, callable $set, $record) {
                        // Al cambiar el toggle, limpiar el campo que no se usará
                        if ($state) {
                            $set('image_file', null);
                            // Si hay un registro y tiene imagen tipo archivo, mantenerla en image
                            if ($record && $record->image && !str_starts_with($record->image, 'http')) {
                                $set('image', $record->image);
                            }
                        } else {
                            // Si hay un registro y tiene imagen tipo URL, mantenerla
                            if ($record && $record->image && str_starts_with($record->image, 'http')) {
                                $set('image', $record->image);
                            }
                        }
                    })
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
                        // Al cargar el formulario, si hay imagen y no es URL, mostrarla
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
                    ->helperText('Pega la URL completa de la imagen')
                    ->hidden(fn ($get) => !$get('use_image_url'))
                    ->columnSpanFull()
                    ->afterStateHydrated(function ($component, $state, $record, $get) {
                        // Al cargar el formulario, si hay imagen y es URL, mostrarla
                        if ($record && $record->image && str_starts_with($record->image, 'http') && $get('use_image_url')) {
                            $component->state($record->image);
                        }
                    }),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('custom_stock_limits')
                    ->label('Personalizar límites de stock')
                    ->live()
                    ->default(false)
                    ->columnSpanFull()
                    ->dehydrated(false),
                TextInput::make('stock_minimum')
                    ->label('Stock Mínimo')
                    ->numeric()
                    ->default(20)
                    ->required()
                    ->hidden(fn ($get) => !$get('custom_stock_limits'))
                    ->dehydrateStateUsing(fn ($state) => $state ?? 20),
                TextInput::make('stock_maximum')
                    ->label('Stock Máximo')
                    ->numeric()
                    ->default(500)
                    ->required()
                    ->hidden(fn ($get) => !$get('custom_stock_limits'))
                    ->dehydrateStateUsing(fn ($state) => $state ?? 500),
                \Filament\Forms\Components\DatePicker::make('expiration_date')
                    ->label('Fecha de Vencimiento')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->helperText('Fecha en que vence el producto'),
                Select::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required(),
                    ]),
                Select::make('supplier_id')
                    ->label('Proveedor')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required(),
                        TextInput::make('contact')
                            ->label('Contacto'),
                        TextInput::make('phone')
                            ->label('Teléfono'),
                        TextInput::make('email')
                            ->label('Email')
                            ->email(),
                    ]),
            ]);
    }
}
