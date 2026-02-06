<?php

namespace App\Filament\Resources\Lotes\Pages;

use App\Filament\Resources\Lotes\LoteResource;
use Filament\Resources\Pages\ViewRecord;

class ViewLote extends ViewRecord
{
    protected static string $resource = LoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\EditAction::make()
                ->visible(fn () => auth()->user()->role === 'admin'),
            \Filament\Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()->role === 'admin'),
        ];
    }


}
