<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentIcon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar observers
        \App\Models\Lote::observe(\App\Observers\LoteObserver::class);
        
        // Restaurar inventario cuando el usuario cierre sesión
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Logout::class,
            function ($event) {
                $this->restoreCartInventory();
            }
        );
    }

    /**
     * Restaurar el inventario de productos en el carrito
     */
    protected function restoreCartInventory(): void
    {
        $cart = session()->get('cart', []);
        
        if (!empty($cart)) {
            foreach ($cart as $item) {
                $product = \App\Models\Product::find($item['product_id']);
                if ($product) {
                    $product->update(['stock' => $product->stock + $item['quantity']]);
                }
            }
            
            session()->forget('cart');
        }
    }
}
