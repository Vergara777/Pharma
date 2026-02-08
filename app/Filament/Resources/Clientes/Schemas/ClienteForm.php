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
                    ->required(),
                TextInput::make('document'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('address'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
