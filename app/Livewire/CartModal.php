<?php

namespace App\Livewire;

use App\Models\PaymentMethod;
use App\Models\Product;
use Livewire\Component;

class CartModal extends Component
{
    public $isOpen = false;
    public $cart = [];
    public $total = 0;
    public $selectedPaymentMethod = '';
    public $amountReceived = 0;
    public $change = 0;
    public $customerName = '';
    public $customerPhone = '';
    public $customerEmail = '';
    public $customerDocument = '';
    public $customerAddress = '';
    public $paymentReference = '';
    public $generateInvoice = false;
    public $invoiceType = 'none'; // 'none', 'with_data', 'without_data'

    protected $listeners = ['cartUpdated' => 'refreshCart', 'openCart' => 'open'];

    public function mount()
    {
        $this->refreshCart();
    }

    public function refreshCart()
    {
        $this->cart = session()->get('cart', []);
        
        // Solo enriquecer si hay productos en el carrito
        if (!empty($this->cart)) {
            // Obtener todos los IDs de productos de una sola vez
            $productIds = array_column($this->cart, 'product_id');
            
            // Hacer una sola consulta para todos los productos
            $products = Product::whereIn('id', $productIds)
                ->select('id', 'image', 'description', 'stock')
                ->get()
                ->keyBy('id');
            
            // Enriquecer el carrito con la información
            foreach ($this->cart as $key => $item) {
                $product = $products->get($item['product_id']);
                if ($product) {
                    $this->cart[$key]['image'] = $product->image;
                    $this->cart[$key]['description'] = $product->description;
                    $this->cart[$key]['stock_available'] = $product->stock;
                }
            }
        }
        
        $this->total = collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $this->calculateChange();
    }

    public function updatedSelectedPaymentMethod()
    {
        // Resetear campos cuando cambia el método de pago
        $this->amountReceived = 0;
        $this->change = 0;
        $this->paymentReference = '';
    }

    public function updatedInvoiceType()
    {
        // Si cambia a 'none', limpiar datos del cliente
        if ($this->invoiceType === 'none') {
            $this->customerName = '';
            $this->customerPhone = '';
            $this->customerEmail = '';
            $this->customerDocument = '';
            $this->customerAddress = '';
            $this->generateInvoice = false;
        } else {
            $this->generateInvoice = true;
        }
    }

    public function updatedAmountReceived()
    {
        $this->calculateChange();
    }

    public function calculateTotal()
    {
        $this->total = collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $this->calculateChange();
    }

    public function calculateChange()
    {
        if ($this->amountReceived > 0 && $this->total > 0) {
            $this->change = $this->amountReceived - $this->total;
        } else {
            $this->change = 0;
        }
    }

    public function increaseQuantity($productId)
    {
        $product = Product::find($productId);
        
        if (!$product || $product->stock < 1) {
            \Filament\Notifications\Notification::make()
                ->title('Stock insuficiente')
                ->body('No hay más unidades disponibles')
                ->danger()
                ->duration(3000)
                ->send();
            return;
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
            $this->cart[$productId]['stock_available']--;
            
            // Descontar del inventario
            $product->update(['stock' => $product->stock - 1]);
            
            session()->put('cart', $this->cart);
            $this->calculateTotal();
            $this->dispatch('cartUpdated');
            $this->dispatch('refreshProducts');
        }
    }

    public function decreaseQuantity($productId)
    {
        if (isset($this->cart[$productId]) && $this->cart[$productId]['quantity'] > 1) {
            $this->cart[$productId]['quantity']--;
            $this->cart[$productId]['stock_available']++;
            
            // Restaurar al inventario
            $product = Product::find($productId);
            if ($product) {
                $product->update(['stock' => $product->stock + 1]);
            }
            
            session()->put('cart', $this->cart);
            $this->calculateTotal();
            $this->dispatch('cartUpdated');
            $this->dispatch('refreshProducts');
        } else {
            // Si la cantidad es 1, remover el producto
            $this->removeItem($productId);
        }
    }

    public function removeItem($productId)
    {
        if (isset($this->cart[$productId])) {
            // Restaurar el stock
            $product = Product::find($productId);
            if ($product) {
                $product->update(['stock' => $product->stock + $this->cart[$productId]['quantity']]);
            }
            
            unset($this->cart[$productId]);
            session()->put('cart', $this->cart);
            $this->calculateTotal();
            $this->dispatch('cartUpdated');
            $this->dispatch('refreshProducts');
            
            \Filament\Notifications\Notification::make()
                ->title('Producto eliminado')
                ->body('Inventario restaurado')
                ->success()
                ->duration(2000)
                ->send();
        }
    }

    public function open()
    {
        \Log::info('CartModal open() called');
        $this->refreshCart();
        $this->isOpen = true;
        \Log::info('isOpen set to true', ['isOpen' => $this->isOpen]);
    }

    public function close()
    {
        $this->isOpen = false;
    }

    public function clearCart()
    {
        if (empty($this->cart)) {
            $this->isOpen = false;
            return;
        }
        
        // Obtener todos los IDs de productos
        $productIds = array_column($this->cart, 'product_id');
        
        // Hacer una sola consulta para todos los productos
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        
        // Restaurar el inventario de todos los productos
        foreach ($this->cart as $item) {
            $product = $products->get($item['product_id']);
            if ($product) {
                $product->update(['stock' => $product->stock + $item['quantity']]);
            }
        }
        
        session()->forget('cart');
        $this->cart = [];
        $this->total = 0;
        
        // Disparar eventos para actualizar todo
        $this->dispatch('cartUpdated');
        $this->dispatch('refreshProducts');
        
        // Cerrar el modal
        $this->isOpen = false;
        
        \Filament\Notifications\Notification::make()
            ->title('Carrito vaciado')
            ->body('Inventario restaurado correctamente')
            ->success()
            ->duration(3000)
            ->send();
    }

    public function render()
    {
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        
        return view('livewire.cart-modal', [
            'paymentMethods' => $paymentMethods,
        ]);
    }
}
