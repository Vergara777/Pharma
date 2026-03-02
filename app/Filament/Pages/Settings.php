<?php

namespace App\Filament\Pages;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Cache;
use BackedEnum;
use UnitEnum;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Configuración General';

    protected static ?string $title = 'Configuración del Sistema';

    protected static ?int $navigationSort = 99;

    // protected static UnitEnum|string|null $navigationGroup = 'Configuración';

    public ?array $data = [];
    
    protected string $view = 'filament.pages.settings';

    public function mount(): void
    {
        $this->form->fill([
            'pharmacy_name' => \App\Models\Setting::get('pharmacy_name', config('app.name')),
            'pharmacy_address' => \App\Models\Setting::get('pharmacy_address', ''),
            'pharmacy_phone' => \App\Models\Setting::get('pharmacy_phone', ''),
            'pharmacy_email' => \App\Models\Setting::get('pharmacy_email', ''),
            'low_stock_alert' => \App\Models\Setting::get('low_stock_alert', true),
            'default_stock_minimum' => \App\Models\Setting::get('default_stock_minimum', 20),
            'currency' => \App\Models\Setting::get('currency', 'COP'),
            'expiration_alert' => \App\Models\Setting::get('expiration_alert', true),
            'expiration_alert_days' => \App\Models\Setting::get('expiration_alert_days', 30),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Información de la Farmacia')
                    ->description('Datos generales de tu farmacia')
                    ->schema([
                        TextInput::make('pharmacy_name')
                            ->label('Nombre de la Farmacia')
                            ->required()
                            ->maxLength(255),
                        
                        Textarea::make('pharmacy_address')
                            ->label('Dirección')
                            ->rows(3)
                            ->maxLength(500),
                        
                        TextInput::make('pharmacy_phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),
                        
                        TextInput::make('pharmacy_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Configuración de Inventario')
                    ->description('Parámetros para el control de stock')
                    ->schema([
                        Toggle::make('low_stock_alert')
                            ->label('Alertas de Stock Bajo')
                            ->helperText('Mostrar notificaciones cuando el stock esté bajo')
                            ->default(true)
                            ->inline(false),
                        
                        TextInput::make('default_stock_minimum')
                            ->label('Stock Mínimo por Defecto')
                            ->helperText('Valor predeterminado para nuevos productos')
                            ->numeric()
                            ->default(20)
                            ->minValue(0),
                        
                        Select::make('currency')
                            ->label('Moneda del Sistema')
                            ->helperText('Símbolo de moneda para mostrar precios')
                            ->options([
                                'COP' => '🇨🇴 Peso Colombiano (COP) - $',
                                'USD' => '🇺🇸 Dólar Estadounidense (USD) - $',
                                'EUR' => '🇪🇺 Euro (EUR) - €',
                                'MXN' => '🇲🇽 Peso Mexicano (MXN) - $',
                                'ARS' => '🇦🇷 Peso Argentino (ARS) - $',
                                'CLP' => '🇨🇱 Peso Chileno (CLP) - $',
                                'PEN' => '🇵🇪 Sol Peruano (PEN) - S/',
                                'BRL' => '🇧🇷 Real Brasileño (BRL) - R$',
                            ])
                            ->default('COP')
                            ->required()
                            ->searchable(),
                    ])
                    ->columns(2),

                Section::make('Configuración de Vencimientos')
                    ->description('Alertas de productos próximos a vencer')
                    ->schema([
                        Toggle::make('expiration_alert')
                            ->label('Alertas de Vencimiento')
                            ->helperText('Notificar productos próximos a vencer')
                            ->default(true)
                            ->inline(false),
                        
                        TextInput::make('expiration_alert_days')
                            ->label('Días de Anticipación')
                            ->helperText('Notificar cuando falten X días para vencer')
                            ->numeric()
                            ->default(30)
                            ->minValue(1)
                            ->suffix('días'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Guardar en base de datos
        \App\Models\Setting::set('pharmacy_name', $data['pharmacy_name'] ?? config('app.name'), 'string');
        \App\Models\Setting::set('pharmacy_address', $data['pharmacy_address'] ?? '', 'string');
        \App\Models\Setting::set('pharmacy_phone', $data['pharmacy_phone'] ?? '', 'string');
        \App\Models\Setting::set('pharmacy_email', $data['pharmacy_email'] ?? '', 'string');
        \App\Models\Setting::set('low_stock_alert', $data['low_stock_alert'] ?? true, 'boolean');
        \App\Models\Setting::set('default_stock_minimum', $data['default_stock_minimum'] ?? 20, 'integer');
        \App\Models\Setting::set('currency', $data['currency'] ?? 'COP', 'string');
        \App\Models\Setting::set('expiration_alert', $data['expiration_alert'] ?? true, 'boolean');
        \App\Models\Setting::set('expiration_alert_days', $data['expiration_alert_days'] ?? 30, 'integer');

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->body('Los cambios se han guardado correctamente. Recarga la página para ver los cambios.')
            ->persistent()
            ->send();
    }
}
