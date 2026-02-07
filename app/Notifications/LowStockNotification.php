<?php

namespace App\Notifications;

use App\Models\Product;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Actions\Action;
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
        $notification = FilamentNotification::make()
            ->title($this->title)
            ->body($this->body)
            ->{$this->type}()
            ->icon($this->icon);

        if ($this->actions) {
            $notification->actions($this->actions);
        }

        return $notification->getDatabaseMessage();
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
        // Verificar si las alertas están activadas
        $lowStockAlertEnabled = \App\Models\Setting::get('low_stock_alert', true);
        $expirationAlertEnabled = \App\Models\Setting::get('expiration_alert', true);
        
        // Si ambas están desactivadas, no hacer nada
        if (!$lowStockAlertEnabled && !$expirationAlertEnabled) {
            return;
        }

        $admins = User::where('role', 'admin')->get();

        if ($admins->isEmpty()) {
            return;
        }

        $hasAlerts = false;
        $messageParts = [];
        $actions = [];

        // Base URL para productos
        $productsUrl = route('filament.admin.resources.products.index');

        // 1. Recopilar información de stock
        if ($lowStockAlertEnabled) {
            $outOfStockCount = Product::where('stock', 0)->count();
            $lowStockCount = Product::query()
                ->whereColumn('stock', '<=', 'min_stock')
                ->where('stock', '>', 0)
                ->count();
            
            // Productos por agotar (Amarillo)
            $approachingStockCount = Product::query()
                ->whereRaw('stock > min_stock AND stock <= (min_stock + 10)')
                ->count();

            if ($outOfStockCount > 0) {
                $hasAlerts = true;
                $messageParts[] = "{$outOfStockCount} sin stock";
                $actions[] = Action::make('view_out_of_stock')
                    ->label('Sin Stock')
                    ->url($productsUrl . '?filter=out_of_stock')
                    ->button()
                    ->color('danger');
            }

            if ($lowStockCount > 0) {
                $hasAlerts = true;
                $messageParts[] = "{$lowStockCount} stock bajo";
                $actions[] = Action::make('view_low_stock')
                    ->label('Stock Bajo')
                    ->url($productsUrl . '?filter=low_stock')
                    ->button()
                    ->color('warning');
            }

            if ($approachingStockCount > 0) {
                $hasAlerts = true;
                $messageParts[] = "{$approachingStockCount} por agotar";
                $actions[] = Action::make('view_approaching')
                    ->label('Por Agotar')
                    ->url($productsUrl . '?filter=approaching_stock')
                    ->button()
                    ->color('warning');
            }
        }

        // 2. Recopilar información de vencimientos
        if ($expirationAlertEnabled) {
            $expirationAlertDays = \App\Models\Setting::get('expiration_alert_days', 30);
            
            $expiringCount = Product::query()
                ->whereNotNull('expires_at')
                ->whereDate('expires_at', '<=', now()->addDays($expirationAlertDays))
                ->whereDate('expires_at', '>=', now())
                ->count();

            $expiredCount = Product::query()
                ->whereNotNull('expires_at')
                ->whereDate('expires_at', '<', now())
                ->count();

            if ($expiredCount > 0) {
                $hasAlerts = true;
                $messageParts[] = "{$expiredCount} vencidos";
                $actions[] = Action::make('view_expired')
                    ->label('Vencidos')
                    ->url($productsUrl . '?filter=expired')
                    ->button()
                    ->color('danger');
            }

            if ($expiringCount > 0) {
                $hasAlerts = true;
                $messageParts[] = "{$expiringCount} por vencer";
                $actions[] = Action::make('view_expiring')
                    ->label('Por Vencer')
                    ->url($productsUrl . '?filter=expiring_soon')
                    ->button()
                    ->color('warning');
            }
        }

        // Si no hay alertas, salir
        if (!$hasAlerts) {
            return;
        }

        // --- LÓGICA DE ENVÍO A BASE DE DATOS (CAMPANA) ---
        $dbCacheKey = 'db_notif_sent_v5_' . auth()->id();
        if (!\Illuminate\Support\Facades\Cache::has($dbCacheKey)) {
            foreach ($admins as $admin) {
                $admin->notify(new self(
                    title: 'Inventario Crítico',
                    body: implode(', ', $messageParts),
                    type: 'danger',
                    icon: 'heroicon-o-exclamation-circle',
                    actions: $actions
                ));
            }
            \Illuminate\Support\Facades\Cache::put($dbCacheKey, true, now()->addHour());
        }

        // --- LÓGICA DE ALERTA VISUAL (TOAST) ---
        if (auth()->user()?->role === 'admin') {
            FilamentNotification::make()
                ->title('⚠️ Alertas de Inventario')
                ->body('Se detectaron problemas: ' . implode(', ', $messageParts))
                ->danger()
                ->icon('heroicon-o-bell-alert')
                ->duration(30000)
                ->actions($actions) // Aquí salen todos los botones
                ->send();
        }
    }

    public static function checkProduct(Product $product): void
    {
        // Verificar si las alertas están activadas
        $lowStockAlertEnabled = \App\Models\Setting::get('low_stock_alert', true);
        $expirationAlertEnabled = \App\Models\Setting::get('expiration_alert', true);

        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        if ($admins->isEmpty()) {
            return;
        }

        // Alertas de stock (solo si está activada)
        if ($lowStockAlertEnabled) {
            if ($product->stock == 0) {
                foreach ($admins as $admin) {
                    $admin->notify(new self(
                        title: 'Producto Sin Stock',
                        body: "El producto '{$product->name}' se ha agotado",
                        type: 'danger',
                        icon: 'heroicon-o-x-circle'
                    ));
                }
            } elseif ($product->stock <= $product->min_stock) {
                foreach ($admins as $admin) {
                    $admin->notify(new self(
                        title: 'Stock Bajo',
                        body: "El producto '{$product->name}' tiene stock bajo ({$product->stock} unidades)",
                        type: 'warning',
                        icon: 'heroicon-o-exclamation-triangle'
                    ));
                }
            }
        }

        // Verificar vencimiento (solo si está activada)
        if ($expirationAlertEnabled && $product->expires_at) {
            $daysUntilExpiration = now()->diffInDays($product->expires_at, false);
            $alertDays = \App\Models\Setting::get('expiration_alert_days', 30);

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
