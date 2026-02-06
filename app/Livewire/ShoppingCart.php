<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Ventas;
use App\Models\PaymentMethod;
use Filament\Notifications\Notification;
use Livewire\Component;

class ShoppingCart extends Component
{
    public $isOpen = false;
    public $cart = [];
    public $paymentMethodId = null;

    protected $listeners = ['addToCart', 'openCart'];

    public function mount()
    {
        $this->cart = session()->get('cart', []);
    }

    public function addToCart($productId, $quantity = 1)
    {
        $product = Product::find($productId);
        
        if (!$product) {
            Notification::make()
                ->title('Producto no encontrado')
                ->danger()
                ->send();
            return;
        }

        if ($product->stock < $quantity) {
            Notification::make()
                ->title('Stock insuficiente')
                ->body("Solo hay {$product->stock} unidades disponibles")
                ->warning()
                ->send();
            return;
        }

        $cartKey = $productId;
        
        if (isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['quantity'] += $quantity;
        } else {
            $this->cart[$cartKey] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $this->cart);

        Notification::make()
            ->title('Producto agregado')
            ->body("{$product->name} agregado al carrito")
            ->success()
            ->send();
    }

    public function openCart()
    {
        $this->isOpen = true;
    }

    public function updateQuantity($cartKey, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeItem($cartKey);
            return;
        }

        $product = Product::find($this->cart[$cartKey]['product_id']);
        
        if ($product->stock < $quantity) {
            Notification::make()
                ->title('Stock insuficiente')
                ->body("Solo hay {$product->stock} unidades disponibles")
                ->warning()
                ->send();
            return;
        }

        $this->cart[$cartKey]['quantity'] = $quantity;
        session()->put('cart', $this->cart);
    }

    public function removeItem($cartKey)
    {
        unset($this->cart[$cartKey]);
        session()->put('cart', $this->cart);

        Notification::make()
            ->title('Producto eliminado')
            ->success()
            ->send();
    }

    public function clearCart()
    {
        $this->cart = [];
        session()->forget('cart');
    }

    public function getTotal()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function finalizeSale()
    {
        if (empty($this->cart)) {
            Notification::make()
                ->title('Carrito vacío')
                ->body('Agrega productos antes de finalizar la venta')
                ->warning()
                ->send();
            return;
        }

        if (!$this->paymentMethodId) {
            Notification::make()
                ->title('Método de pago requerido')
                ->body('Selecciona un método de pago')
                ->warning()
                ->send();
            return;
        }

        // Verificar si hay una caja abierta
        $openSession = \App\Models\CashSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();
        
        if (!$openSession) {
            Notification::make()
                ->title('Caja Cerrada')
                ->body('Debes abrir una caja antes de realizar ventas')
                ->danger()
                ->duration(5000)
                ->send();
            return;
        }

        try {
            foreach ($this->cart as $item) {
                $product = Product::find($item['product_id']);
                
                if ($product->stock < $item['quantity']) {
                    Notification::make()
                        ->title('Stock insuficiente')
                        ->body("No hay suficiente stock de {$product->name}")
                        ->danger()
                        ->send();
                    return;
                }

                // Crear venta asociada a la sesión de caja
                $ventaData = [
                    'product_id' => $item['product_id'],
                    'qty' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total' => $item['price'] * $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                    'grand_total' => $item['price'] * $item['quantity'],
                    'payment_method_id' => $this->paymentMethodId,
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()->name,
                    'cash_session_id' => $openSession->id,
                    'status' => 'active',
                ];
                
                Ventas::create($ventaData);

                // Reducir stock
                $product->decrement('stock', $item['quantity']);
            }

            $total = $this->getTotal();

            Notification::make()
                ->title('Venta finalizada')
                ->body("Venta por $" . number_format($total, 2) . " completada exitosamente")
                ->success()
                ->duration(5000)
                ->send();

            $this->clearCart();
            $this->isOpen = false;
            $this->paymentMethodId = null;

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al procesar la venta')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        
        return view('livewire.shopping-cart', [
            'total' => $this->getTotal(),
            'paymentMethods' => $paymentMethods,
        ]);
    }
}
