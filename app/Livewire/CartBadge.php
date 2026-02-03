<?php

namespace App\Livewire;

use Livewire\Component;

class CartBadge extends Component
{
    public $cartCount = 0;

    protected $listeners = ['cartUpdated' => 'updateCount'];

    public function mount()
    {
        $this->updateCount();
    }

    public function updateCount()
    {
        $this->cartCount = count(session()->get('cart', []));
    }

    public function render()
    {
        return view('livewire.cart-badge');
    }
}
