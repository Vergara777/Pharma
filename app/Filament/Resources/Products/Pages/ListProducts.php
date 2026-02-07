<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\LowStockNotification;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected $listeners = ['refreshProducts' => '$refresh', 'cartUpdated' => '$refresh'];

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public function mount(): void
    {
        parent::mount();

        // Sincronizar parámetros de URL con los filtros nativos de Filament
        // Esto hace que aparezcan los "chips" de filtros activos
        $filter = request()->query('filter');
        if ($filter && in_array($filter, ['low_stock', 'out_of_stock', 'approaching_stock', 'expiring_soon', 'expired'])) {
            $this->tableFilters[$filter] = ['isActive' => true];
        }
    }

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
