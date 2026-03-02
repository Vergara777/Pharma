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
        // El sistema de notificaciones globales ya maneja los toasts.
        // Este widget puede usarse para mostrar información visual en el dashboard
        // si se requiere, pero no debe disparar notificaciones duplicadas.
    }

    protected function checkLowStock(): void
    {
        // Verificar si las alertas están activadas
        $lowStockAlertEnabled = setting('low_stock_alert', true);
        $expirationAlertEnabled = setting('expiration_alert', true);
        
        // Si ambas están desactivadas, no mostrar nada
        if (!$lowStockAlertEnabled && !$expirationAlertEnabled) {
            return;
        }

        // Notificación de stock (solo si está activada)
        if ($lowStockAlertEnabled) {
            $lowStockProducts = Product::query()
                ->whereColumn('stock', '<=', 'min_stock')
                ->where('stock', '>', 0)
                ->count();

            $outOfStockProducts = Product::where('stock', 0)->count();

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
                    ->title('Alerta de Inventario')
                    ->body(implode(' y ', $message))
                    ->persistent()
                    ->send();
            }
        }

        // Notificaciones de vencimiento (solo si está activada)
        if ($expirationAlertEnabled) {
            $expirationAlertDays = setting('expiration_alert_days', 30);
            
            $expiringProducts = Product::query()
                ->whereNotNull('expires_at')
                ->whereDate('expires_at', '<=', now()->addDays($expirationAlertDays))
                ->whereDate('expires_at', '>=', now())
                ->count();

            $expiredProducts = Product::query()
                ->whereNotNull('expires_at')
                ->whereDate('expires_at', '<', now())
                ->count();

            // Notificación de productos vencidos
            if ($expiredProducts > 0) {
                Notification::make()
                    ->danger()
                    ->title('Productos Vencidos')
                    ->body("{$expiredProducts} producto(s) ya están vencidos")
                    ->persistent()
                    ->send();
            }

            // Notificación de productos próximos a vencer
            if ($expiringProducts > 0) {
                Notification::make()
                    ->warning()
                    ->title('Productos Próximos a Vencer')
                    ->body("{$expiringProducts} producto(s) vencen en los próximos {$expirationAlertDays} días")
                    ->persistent()
                    ->send();
            }
        }
    }
}
