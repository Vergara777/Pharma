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
                    ->required(),
                TextInput::make('description')
                    ->required(),
                Select::make('status')
                    ->options([
                        'active' => 'Activo',
                        'inactive' => 'Inactivo',
                    ])
                    ->default('active')
                    ->required(),
                Action::make('delete')
                    ->action(fn(Post $record) => $record->delete())
                    ->requiresConfirmation()
            ]);


    }
}
