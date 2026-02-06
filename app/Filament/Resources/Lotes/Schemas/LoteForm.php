<?php

namespace App\Filament\Resources\Lotes\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;

class LoteForm
{
    public static function getSchema(): array
    {
        return [
            Tabs::make('Información del Lote')
                ->tabs([
                    Tabs\Tab::make('Información Básica')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Section::make()
                                ->schema([
                                    Select::make('product_id')
                                        ->label('Producto')
                                        ->relationship('product', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->columnSpan(2),
                                    
                                    TextInput::make('codigo_lote')
                                        ->label('Código de Lote')
                                        ->required()
                                        ->unique(ignoreRecord: true)
                                        ->maxLength(100)
                                        ->placeholder('LOT-2024-001'),
                                    
                                    TextInput::make('lote_fabricante')
                                        ->label('Lote del Fabricante')
                                        ->maxLength(100)
                                        ->placeholder('Lote original del fabricante'),
                                ])
                                ->columns(3),

                            Section::make('Cantidades')
                                ->schema([
                                    TextInput::make('cantidad_inicial')
                                        ->label('Cantidad Inicial')
                                        ->required()
                                        ->numeric()
                                        ->default(0)
                                        ->minValue(0)
                                        ->suffix('unidades'),
                                    
                                    TextInput::make('cantidad_actual')
                                        ->label('Cantidad Actual')
                                        ->required()
                                        ->numeric()
                                        ->default(0)
                                        ->minValue(0)
                                        ->suffix('unidades')
                                        ->helperText('Se actualizará automáticamente con las ventas'),
                                    
                                    TextInput::make('cantidad_minima_alerta')
                                        ->label('Stock Mínimo para Alerta')
                                        ->numeric()
                                        ->default(10)
                                        ->minValue(0)
                                        ->suffix('unidades'),
                                ])
                                ->columns(3),

                            Section::make('Fechas')
                                ->schema([
                                    DatePicker::make('fecha_fabricacion')
                                        ->label('Fecha de Fabricación')
                                        ->displayFormat('d/m/Y')
                                        ->native(false),
                                    
                                    DatePicker::make('fecha_vencimiento')
                                        ->label('Fecha de Vencimiento')
                                        ->required()
                                        ->displayFormat('d/m/Y')
                                        ->native(false)
                                        ->minDate(now()),
                                    
                                    DatePicker::make('fecha_ingreso')
                                        ->label('Fecha de Ingreso')
                                        ->displayFormat('d/m/Y H:i')
                                        ->native(false)
                                        ->default(now()),
                                    
                                    TextInput::make('dias_alerta_vencimiento')
                                        ->label('Días de Alerta antes de Vencer')
                                        ->numeric()
                                        ->default(90)
                                        ->minValue(1)
                                        ->suffix('días'),
                                ])
                                ->columns(2),

                            Section::make('Estado')
                                ->schema([
                                    Select::make('estado')
                                        ->label('Estado')
                                        ->options([
                                            'activo' => 'Activo',
                                            'agotado' => 'Agotado',
                                            'vencido' => 'Vencido',
                                            'bloqueado' => 'Bloqueado',
                                        ])
                                        ->default('activo')
                                        ->required()
                                        ->live(),
                                    
                                    Textarea::make('motivo_bloqueo')
                                        ->label('Motivo de Bloqueo')
                                        ->rows(2)
                                        ->visible(fn ($get) => $get('estado') === 'bloqueado')
                                        ->columnSpanFull(),
                                ])
                                ->columns(2),
                        ]),

                    Tabs\Tab::make('Información Comercial')
                        ->icon('heroicon-o-currency-dollar')
                        ->schema([
                            Section::make('Costos y Precios')
                                ->description('Puedes usar puntos como separador de miles. Ejemplo: para $4.000 escriba 4.000')
                                ->schema([
                                    TextInput::make('costo_unitario')
                                        ->label('Costo Unitario ($)')
                                        ->required()
                                        ->default(0)
                                        ->minValue(0)
                                        ->placeholder('4.000')
                                        ->helperText('Puedes usar puntos como separador de miles. Ej: 4.000 = $4.000')
                                        ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, '', '.') : '0')
                                        ->dehydrateStateUsing(fn ($state) => $state ? (int) str_replace('.', '', $state) : 0),
                                    
                                    TextInput::make('precio_venta_sugerido')
                                        ->label('Precio de Venta Sugerido ($)')
                                        ->default(0)
                                        ->minValue(0)
                                        ->placeholder('5.000')
                                        ->helperText('Puedes usar puntos como separador de miles. Ej: 5.000 = $5.000')
                                        ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, '', '.') : '0')
                                        ->dehydrateStateUsing(fn ($state) => $state ? (int) str_replace('.', '', $state) : 0),
                                    
                                    TextInput::make('descuento_proveedor')
                                        ->label('Descuento del Proveedor')
                                        ->numeric()
                                        ->suffix('%')
                                        ->default(0)
                                        ->minValue(0)
                                        ->maxValue(100),
                                    
                                    TextInput::make('iva_porcentaje')
                                        ->label('IVA')
                                        ->numeric()
                                        ->suffix('%')
                                        ->default(0)
                                        ->minValue(0)
                                        ->maxValue(100),
                                ])
                                ->columns(2),

                            Section::make('Proveedor y Documentación')
                                ->schema([
                                    Select::make('proveedor_id')
                                        ->label('Proveedor')
                                        ->relationship('proveedor', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->createOptionForm([
                                            TextInput::make('name')->required(),
                                            TextInput::make('contact_name'),
                                            TextInput::make('phone'),
                                            TextInput::make('email')->email(),
                                        ]),
                                    
                                    TextInput::make('documento_compra')
                                        ->label('Número de Factura/Documento')
                                        ->maxLength(100)
                                        ->placeholder('FAC-2024-001'),
                                    
                                    FileUpload::make('documento_archivo')
                                        ->label('Archivo del Documento')
                                        ->directory('lotes/documentos')
                                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                                        ->maxSize(5120)
                                        ->columnSpanFull(),
                                ])
                                ->columns(2),
                        ]),

                    Tabs\Tab::make('Control de Calidad')
                        ->icon('heroicon-o-shield-check')
                        ->schema([
                            Section::make('Información Regulatoria')
                                ->schema([
                                    TextInput::make('registro_sanitario')
                                        ->label('Registro Sanitario (INVIMA)')
                                        ->maxLength(100)
                                        ->placeholder('INVIMA 2024M-0001234'),
                                    
                                    Toggle::make('requiere_receta')
                                        ->label('Requiere Receta Médica')
                                        ->default(false)
                                        ->inline(false),
                                    
                                    Toggle::make('es_muestra_medica')
                                        ->label('Es Muestra Médica')
                                        ->default(false)
                                        ->inline(false)
                                        ->helperText('Producto de muestra gratis'),
                                ])
                                ->columns(3),

                            Section::make('Almacenamiento')
                                ->schema([
                                    TextInput::make('temperatura_almacenamiento')
                                        ->label('Temperatura de Almacenamiento')
                                        ->placeholder('2-8°C o Temperatura ambiente')
                                        ->maxLength(50),
                                    
                                    Toggle::make('requiere_cadena_frio')
                                        ->label('Requiere Cadena de Frío')
                                        ->default(false)
                                        ->inline(false),
                                    
                                    TextInput::make('ubicacion_fisica')
                                        ->label('Ubicación Física')
                                        ->placeholder('Estante A, Pasillo 2, Bodega')
                                        ->maxLength(100),
                                    
                                    Textarea::make('condiciones_especiales')
                                        ->label('Condiciones Especiales')
                                        ->rows(2)
                                        ->placeholder('Proteger de la luz, mantener en lugar seco...')
                                        ->columnSpanFull(),
                                ])
                                ->columns(3),

                            Section::make('Observaciones')
                                ->schema([
                                    Textarea::make('observaciones_calidad')
                                        ->label('Observaciones de Control de Calidad')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                    
                                    Textarea::make('notas')
                                        ->label('Notas Generales')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ])
                ->columnSpanFull(),
        ];
    }
}
