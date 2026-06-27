<?php

namespace App\Filament\Resources\Clientes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ClienteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre del Cliente')
                    ->required()
                    ->placeholder('Ej: Juan Pérez')
                    ->prefixIcon('heroicon-o-user'),
                TextInput::make('document')
                    ->label('Documento / NIT')
                    ->placeholder('Ej: 123456789')
                    ->prefixIcon('heroicon-o-identification'),
                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->placeholder('Ej: cliente@email.com')
                    ->prefixIcon('heroicon-o-envelope'),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->placeholder('Ej: 300 123 4567')
                    ->prefixIcon('heroicon-o-phone'),
                TextInput::make('address')
                    ->label('Dirección')
                    ->placeholder('Ej: Calle 123 # 45 - 67')
                    ->prefixIcon('heroicon-o-map-pin'),
                Toggle::make('is_active')
                    ->required()
                    ->onColor('success')
                    ->offColor('danger'),
            ]);
    }
}
