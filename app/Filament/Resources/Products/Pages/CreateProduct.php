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

        // Notificación de stock excedido
        if ($product->stock > $product->max_stock) {
            $excess = $product->stock - $product->max_stock;
            Notification::make()
                ->title('Stock Excedido')
                ->body("El producto '{$product->name}' excede el stock máximo por {$excess} unidades. Stock actual: {$product->stock}, Máximo: {$product->max_stock}")
                ->info()
                ->icon('heroicon-o-information-circle')
                ->duration(6000)
                ->send();
        }
        // Notificación de stock agotado
        elseif ($product->stock == 0) {
            Notification::make()
                ->title('Producto Creado Sin Stock')
                ->body("El producto '{$product->name}' fue creado sin stock disponible.")
                ->warning()
                ->icon('heroicon-o-exclamation-triangle')
                ->duration(6000)
                ->send();
        }
        // Notificación de stock bajo
        elseif ($product->stock <= $product->min_stock) {
            Notification::make()
                ->title('Producto Creado con Stock Bajo')
                ->body("El producto '{$product->name}' fue creado con stock bajo ({$product->stock} unidades). Mínimo requerido: {$product->min_stock}")
                ->warning()
                ->icon('heroicon-o-exclamation-triangle')
                ->duration(6000)
                ->send();
        }

        // Notificación de producto vencido
        if ($product->expires_at && $product->expires_at->isPast()) {
            $daysExpired = now()->diffInDays($product->expires_at);
            Notification::make()
                ->title('Producto Creado Vencido')
                ->body("El producto '{$product->name}' fue creado con fecha de vencimiento pasada ({$daysExpired} días vencido).")
                ->danger()
                ->icon('heroicon-o-calendar-x')
                ->duration(8000)
                ->send();
        }
        // Notificación de producto próximo a vencer
        elseif ($product->expires_at) {
            $daysUntilExpiration = now()->diffInDays($product->expires_at, false);
            $alertDays = \Illuminate\Support\Facades\Cache::get('settings.expiration_alert_days', 30);

            if ($daysUntilExpiration >= 0 && $daysUntilExpiration <= $alertDays) {
                Notification::make()
                    ->title('Producto Próximo a Vencer')
                    ->body("El producto '{$product->name}' vence en {$daysUntilExpiration} días.")
                    ->warning()
                    ->icon('heroicon-o-calendar')
                    ->duration(6000)
                    ->send();
            }
        }
    }
}
