<?php

namespace App\Filament\Resources\Facturas\Schemas;

use App\Models\Cliente;
use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FacturaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la Factura')
                    ->schema([
                        TextInput::make('invoice_number')
                            ->label('Número de Factura')
                            ->default(fn () => \App\Models\Factura::generateInvoiceNumber())
                            ->required()
                            ->unique(ignoreRecord: true),
                        
                        Select::make('cliente_id')
                            ->label('Cliente')
                            ->relationship('cliente', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->required(),
                                TextInput::make('document')
                                    ->label('Documento'),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email(),
                                TextInput::make('phone')
                                    ->label('Teléfono')
                                    ->tel(),
                                Textarea::make('address')
                                    ->label('Dirección'),
                            ]),
                        
                        DatePicker::make('fecha_emision')
                            ->label('Fecha de Emisión')
                            ->default(now())
                            ->required(),
                        
                        DatePicker::make('fecha_vencimiento')
                            ->label('Fecha de Vencimiento')
                            ->after('fecha_emision'),
                        
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'pending' => 'Pendiente',
                                'paid' => 'Pagada',
                                'cancelled' => 'Cancelada',
                            ])
                            ->default('pending')
                            ->required(),
                        
                        Select::make('payment_method')
                            ->label('Método de Pago')
                            ->options([
                                'cash' => 'Efectivo',
                                'card' => 'Tarjeta',
                                'transfer' => 'Transferencia',
                                'check' => 'Cheque',
                            ]),
                    ])
                    ->columns(2),
                
                Section::make('Items de la Factura')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->label('Producto')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $product = Product::find($state);
                                        if ($product) {
                                            $set('price', $product->price);
                                        }
                                    })
                                    ->columnSpan(2),
                                
                                TextInput::make('quantity')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->reactive()
                                    ->columnSpan(1),
                                
                                TextInput::make('price')
                                    ->label('Precio')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->reactive()
                                    ->columnSpan(1),
                                
                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled()
                                    ->dehydrated()
                                    ->reactive()
                                    ->afterStateHydrated(function ($state, callable $get, callable $set) {
                                        $set('subtotal', $get('quantity') * $get('price'));
                                    })
                                    ->columnSpan(1),
                            ])
                            ->columns(5)
                            ->defaultItems(1)
                            ->reorderable(false)
                            ->collapsible(),
                    ])
                    ->columnSpanFull(),
                
                Section::make('Totales')
                    ->schema([
                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->required(),
                        
                        TextInput::make('tax')
                            ->label('Impuesto')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),
                        
                        TextInput::make('discount')
                            ->label('Descuento')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),
                        
                        TextInput::make('total')
                            ->label('Total')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                    ])
                    ->columns(4),
                
                Section::make('Notas')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notas')
                            ->rows(3),
                    ]),
            ]);
    }
}
