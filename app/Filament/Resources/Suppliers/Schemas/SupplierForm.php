<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre del Proveedor')
                    ->required()
                    ->placeholder('Distribuidora Pharma S.A.S')
                    ->prefixIcon('heroicon-m-user'),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->placeholder('300 123 4567')
                    ->prefixIcon('heroicon-m-phone'),
                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->placeholder('contacto@proveedor.com')
                    ->prefixIcon('heroicon-m-envelope'),
                TextInput::make('address')
                    ->label('Dirección')
                    ->required()
                    ->placeholder('Calle 123 #45-67')
                    ->prefixIcon('heroicon-m-map-pin'),
                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activo',
                        'inactive' => 'Inactivo',
                    ])
                    ->default('active')
                    ->required()
                    ->prefixIcon('heroicon-m-check-circle'),
            ]);
    }
}
