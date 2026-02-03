<?php

namespace App\Notifications;

use App\Models\Product;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class LowStockNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $body,
        public string $type = 'warning',
        public ?string $icon = null,
        public ?array $actions = null
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title($this->title)
            ->body($this->body)
            ->{$this->type}()
            ->icon($this->icon)
            ->getDatabaseMessage();
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'type' => $this->type,
            'icon' => $this->icon,
            'actions' => $this->actions ?? [],
        ];
    }

    public static function send(): void
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        if ($admins->isEmpty()) {
            return;
        }

        $lowStockProducts = Product::query()
            ->whereColumn('stock', '<=', 'stock_minimum')
            ->where('stock', '>', 0)
            ->count();

        $outOfStockProducts = Product::where('stock', 0)->count();

        // Productos próximos a vencer
        $expirationAlertDays = Cache::get('settings.expiration_alert_days', 30);
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

            foreach ($admins as $admin) {
                $admin->notify(new self(
                    title: 'Alerta de Inventario',
                    body: implode(' y ', $message),
                    type: 'warning',
                    icon: 'heroicon-o-exclamation-triangle'
                ));
            }
        }

        // Notificación de productos vencidos
        if ($expiredProducts > 0 && Cache::get('settings.expiration_alert', true)) {
            foreach ($admins as $admin) {
                $admin->notify(new self(
                    title: 'Productos Vencidos',
                    body: "{$expiredProducts} producto(s) ya están vencidos",
                    type: 'danger',
                    icon: 'heroicon-o-x-circle'
                ));
            }
        }

        // Notificación de productos próximos a vencer
        if ($expiringProducts > 0 && Cache::get('settings.expiration_alert', true)) {
            foreach ($admins as $admin) {
                $admin->notify(new self(
                    title: 'Productos Próximos a Vencer',
                    body: "{$expiringProducts} producto(s) vencen en los próximos {$expirationAlertDays} días",
                    type: 'warning',
                    icon: 'heroicon-o-calendar'
                ));
            }
        }
    }

    public static function checkProduct(Product $product): void
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        if ($admins->isEmpty()) {
            return;
        }

        if ($product->stock == 0) {
            foreach ($admins as $admin) {
                $admin->notify(new self(
                    title: 'Producto Sin Stock',
                    body: "El producto '{$product->name}' se ha agotado",
                    type: 'danger',
                    icon: 'heroicon-o-x-circle'
                ));
            }
        } elseif ($product->stock <= $product->stock_minimum) {
            foreach ($admins as $admin) {
                $admin->notify(new self(
                    title: 'Stock Bajo',
                    body: "El producto '{$product->name}' tiene stock bajo ({$product->stock} unidades)",
                    type: 'warning',
                    icon: 'heroicon-o-exclamation-triangle'
                ));
            }
        }

        // Verificar vencimiento
        if ($product->expiration_date && Cache::get('settings.expiration_alert', true)) {
            $daysUntilExpiration = now()->diffInDays($product->expiration_date, false);
            $alertDays = Cache::get('settings.expiration_alert_days', 30);

            if ($daysUntilExpiration < 0) {
                // Producto vencido
                foreach ($admins as $admin) {
                    $admin->notify(new self(
                        title: 'Producto Vencido',
                        body: "El producto '{$product->name}' está vencido desde hace " . round(abs($daysUntilExpiration)) . " días",
                        type: 'danger',
                        icon: 'heroicon-o-x-circle'
                    ));
                }
            } elseif ($daysUntilExpiration >= 0 && $daysUntilExpiration <= $alertDays) {
                // Producto próximo a vencer
                foreach ($admins as $admin) {
                    $admin->notify(new self(
                        title: 'Producto Próximo a Vencer',
                        body: "El producto '{$product->name}' vence en " . round($daysUntilExpiration) . " días",
                        type: 'warning',
                        icon: 'heroicon-o-calendar'
                    ));
                }
            }
        }
    }
}
