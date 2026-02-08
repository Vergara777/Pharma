<?php

namespace App\Livewire;

use App\Services\CartService;
use Livewire\Component;

class CartBadge extends Component
{
    public $cartCount = 0;

    protected $listeners = ['cartUpdated' => 'updateCount', 'refreshCartBadge' => 'updateCount'];

    public function mount()
    {
        $this->updateCount();
    }

    public function updateCount()
    {
        $this->cartCount = CartService::getCount();
    }

    public function refreshCount()
    {
        $this->updateCount();
    }

    public function render()
    {
        return view('livewire.cart-badge');
    }
}
