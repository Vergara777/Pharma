<div 
    class="fi-topbar-item-cart" 
    x-data="{ 
        openCart() {
            $wire.dispatch('openCart');
        }
    }"
    style="display: flex; align-items: center;"
>
    <button 
        type="button"
        @click="Livewire.dispatch('openCart')"
        class="fi-icon-btn relative inline-flex items-center justify-center rounded-lg outline-none transition duration-75 hover:bg-gray-50 focus-visible:bg-gray-50 dark:hover:bg-white/5 dark:focus-visible:bg-white/5 fi-color-gray fi-icon-btn-size-md h-9 w-9"
        style="position: relative; height: 2.25rem; width: 2.25rem; display: inline-flex; items-center; justify-content: center; border-radius: 0.5rem; border: none; background: transparent; cursor: pointer;"
        title="Carrito de compras"
    >
        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        
        @if($cartCount > 0)
            <span 
                style="position: absolute; top: 0; right: 0; transform: translate(45%, -25%); display: flex; align-items: center; justify-content: center; min-width: 18px; height: 18px; padding: 0 4px; font-size: 0.7rem; font-weight: 800; color: white; background-color: #ef4444; border-radius: 0.5rem; line-height: 1; z-index: 10;"
            >
                {{ $cartCount }}
            </span>
        @endif
    </button>
</div>
