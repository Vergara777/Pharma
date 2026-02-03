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
        
        // Enviar notificaciones al cargar la página
        LowStockNotification::send();
    }

    protected function getHeaderActions(): array
    {
        $actions = [];
        
        // Solo admins pueden crear
        if (auth()->user()->role === 'admin') {
            $actions[] = CreateAction::make()
                ->icon('heroicon-o-plus-circle');
        }

        // Si hay un filtro activo, agregar botón para limpiar
        if (request()->query('filter')) {
            $actions[] = Action::make('clear_filter')
                ->label('Ver Todos los Productos')
                ->icon('heroicon-o-x-mark')
                ->color('gray')
                ->url(route('filament.admin.resources.products.index'));
        }

        return $actions;
    }

    protected function getTableQuery(): ?Builder
    {
        $query = parent::getTableQuery();
        $filter = request()->query('filter');
        
        if ($filter === 'low_stock') {
            return $query->whereColumn('stock', '<=', 'stock_minimum')->where('stock', '>', 0);
        }
        
        if ($filter === 'out_of_stock') {
            return $query->where('stock', 0);
        }
        
        if ($filter === 'expiring_soon') {
            $alertDays = \Illuminate\Support\Facades\Cache::get('settings.expiration_alert_days', 30);
            return $query->whereNotNull('expiration_date')
                ->whereDate('expiration_date', '<=', now()->addDays($alertDays))
                ->whereDate('expiration_date', '>=', now());
        }
        
        if ($filter === 'expired') {
            return $query->whereNotNull('expiration_date')
                ->whereDate('expiration_date', '<', now());
        }
        
        return $query;
    }
}
