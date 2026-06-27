<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use App\Filament\Resources\Categories\Schemas\Post;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre de la Categoría')
                    ->required()
                    ->placeholder('Escribe el nombre...')
                    ->prefixIcon('heroicon-m-tag'),
                TextInput::make('description')
                    ->label('Descripción')
                    ->required()
                    ->placeholder('Escribe una descripción...')
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
            ]);


    }
}
