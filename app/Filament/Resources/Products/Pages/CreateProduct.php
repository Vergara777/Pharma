<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function afterCreate(): void
    {
        $product = $this->record;

        // Notificación de stock agotado
        if ($product->stock == 0) {
            Notification::make()
                ->title('⚠️ Producto Creado Sin Stock')
                ->body("El producto '{$product->name}' fue creado sin stock disponible.")
                ->warning()
                ->icon('heroicon-o-exclamation-triangle')
                ->iconColor('warning')
                ->duration(6000)
                ->send();
        }
        // Notificación de stock bajo
        elseif ($product->stock <= $product->stock_minimum) {
            Notification::make()
                ->title('⚠️ Producto Creado con Stock Bajo')
                ->body("El producto '{$product->name}' fue creado con stock bajo ({$product->stock} unidades). Mínimo requerido: {$product->stock_minimum}")
                ->warning()
                ->icon('heroicon-o-exclamation-triangle')
                ->iconColor('warning')
                ->duration(6000)
                ->send();
        }

        // Notificación de producto vencido
        if ($product->expiration_date && $product->expiration_date->isPast()) {
            $daysExpired = now()->diffInDays($product->expiration_date);
            Notification::make()
                ->title('🚫 Producto Creado Vencido')
                ->body("El producto '{$product->name}' fue creado con fecha de vencimiento pasada ({$daysExpired} días vencido).")
                ->danger()
                ->icon('heroicon-o-calendar-x')
                ->iconColor('danger')
                ->duration(8000)
                ->send();
        }
        // Notificación de producto próximo a vencer
        elseif ($product->expiration_date) {
            $daysUntilExpiration = now()->diffInDays($product->expiration_date, false);
            $alertDays = \Illuminate\Support\Facades\Cache::get('settings.expiration_alert_days', 30);

            if ($daysUntilExpiration >= 0 && $daysUntilExpiration <= $alertDays) {
                Notification::make()
                    ->title('📅 Producto Próximo a Vencer')
                    ->body("El producto '{$product->name}' vence en {$daysUntilExpiration} días.")
                    ->warning()
                    ->icon('heroicon-o-calendar')
                    ->iconColor('warning')
                    ->duration(6000)
                    ->send();
            }
        }
    }
}
