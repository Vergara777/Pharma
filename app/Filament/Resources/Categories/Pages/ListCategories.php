<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        // Solo admins pueden crear
        if (auth()->user()->role === 'admin') {
            return [
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ];
        }
        
        return [];
    }
}
