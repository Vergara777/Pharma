<?php

namespace App\Livewire;

use App\Services\CartService;
use Livewire\Component;
use App\Models\Product;
use Filament\Notifications\Notification;

class CartModal extends Component
{
    public $isOpen = false;
    public $cart = [];
    public $total = 0;
    public $stockErrors = [];
    public $isRedirecting = false; // Nueva propiedad

    protected $listeners = [
        'cartUpdated' => 'refreshCart', 
        'openCart' => 'open',
        'refreshCart' => 'refreshCart'
    ];

    public function mount()
    {
        $this->refreshCart();
    }

    public function refreshCart()
    {
        $this->cart = CartService::getCart();
        $this->total = CartService::getTotal();
    }

    public function open()
    {
        $this->refreshCart();
        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
        $this->stockErrors = [];
        $this->isRedirecting = false;
    }

    public function updateQuantity($id, $qty)
    {
        $qty = (int)$qty;
        if ($qty < 1) $qty = 1;

        // Limpiar error anterior para este item
        unset($this->stockErrors[$id]);

        // Validar stock antes de actualizar
        $cart = \App\Services\CartService::getCart();
        if (isset($cart[$id])) {
            $product = \App\Models\Product::find($cart[$id]['product_id']);
            $type = $cart[$id]['type'] ?? 'unit';
            
            // Calcular unidades reales (si es paquete, multiplicar por unids/paquete)
            $requestedUnits = $qty;
            if ($type === 'package') {
                $requestedUnits *= ($product->units_per_package ?: 1);
            }

            if ($product && $product->stock < $requestedUnits) {
                $maxPossible = $type === 'package' 
                    ? floor($product->stock / ($product->units_per_package ?: 1))
                    : $product->stock;

                // Guardar error localmente
                $this->stockErrors[$id] = "Máx. disponible: {$maxPossible}";

                // Ajustar al máximo permitido automáticamente
                \App\Services\CartService::updateQty($id, (int)$maxPossible);
                $this->refreshCart(); 
                return;
            }
        }

        \App\Services\CartService::updateQty($id, $qty);
        $this->refreshCart();
        $this->dispatch('cartUpdated');
        $this->dispatch('refreshFormFromCart'); // Notificar a CreateVenta si está abierta
    }

    public function removeItem($id)
    {
        \App\Services\CartService::remove($id);
        $this->refreshCart();
        $this->dispatch('cartUpdated');
        
        Notification::make()
            ->title('Producto eliminado')
            ->success()
            ->send();
    }

    public function clearCart()
    {
        \App\Services\CartService::clear();
        $this->refreshCart();
        $this->dispatch('cartUpdated');
        
        Notification::make()
            ->title('Carrito vaciado')
            ->success()
            ->send();
    }

    public function goToCheckout()
    {
        $this->isRedirecting = true;
        return $this->redirect(route('filament.admin.resources.ventas.create'), navigate: true);
    }

    public function render()
    {
        return view('livewire.cart-modal');
    }
}
