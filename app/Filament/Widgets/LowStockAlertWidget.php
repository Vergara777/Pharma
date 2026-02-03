<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\Widget;
use Filament\Notifications\Notification;

class LowStockAlertWidget extends Widget
{
    protected static ?int $sort = 0; // Primero que todos
    
    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.widgets.low-stock-alert';

    public function mount(): void
    {
        $this->checkLowStock();
    }

    protected function checkLowStock(): void
    {
        $lowStockProducts = Product::query()
            ->whereColumn('stock', '<=', 'stock_minimum')
            ->where('stock', '>', 0)
            ->count();

        $outOfStockProducts = Product::where('stock', 0)->count();

        // Productos próximos a vencer
        $expirationAlertDays = \Illuminate\Support\Facades\Cache::get('settings.expiration_alert_days', 30);
        $expiringProducts = Product::query()
            ->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<=', now()->addDays($expirationAlertDays))
            ->whereDate('expiration_date', '>=', now())
            ->count();

        // Productos vencidos
        $expiredProducts = Product::query()
            ->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<', now())
            ->count();

        // Notificación de stock
        if ($lowStockProducts > 0 || $outOfStockProducts > 0) {
            $message = [];
            
            if ($outOfStockProducts > 0) {
                $message[] = "{$outOfStockProducts} producto(s) sin stock";
            }
            
            if ($lowStockProducts > 0) {
                $message[] = "{$lowStockProducts} producto(s) con stock bajo";
            }

            Notification::make()
                ->warning()
                ->title('⚠️ Alerta de Inventario')
                ->body(implode(' y ', $message))
                ->persistent()
                ->send();
        }

        // Notificación de productos vencidos
        if ($expiredProducts > 0 && \Illuminate\Support\Facades\Cache::get('settings.expiration_alert', true)) {
            Notification::make()
                ->danger()
                ->title('🚨 Productos Vencidos')
                ->body("{$expiredProducts} producto(s) ya están vencidos")
                ->persistent()
                ->send();
        }

        // Notificación de productos próximos a vencer
        if ($expiringProducts > 0 && \Illuminate\Support\Facades\Cache::get('settings.expiration_alert', true)) {
            Notification::make()
                ->warning()
                ->title('📅 Productos Próximos a Vencer')
                ->body("{$expiringProducts} producto(s) vencen en los próximos {$expirationAlertDays} días")
                ->persistent()
                ->send();
        }
    }
}
