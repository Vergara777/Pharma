<div class="fi-topbar-item-cart" x-data="{ 
    openCart() {
        Livewire.dispatch('openCart');
        console.log('Event dispatched');
    }
}">
    <button 
        type="button"
        @click="openCart()"
        class="fi-icon-btn relative inline-flex items-center justify-center rounded-lg outline-none transition duration-75 hover:bg-gray-50 focus-visible:bg-gray-50 dark:hover:bg-white/5 dark:focus-visible:bg-white/5 fi-color-gray fi-icon-btn-size-md h-9 w-9"
        title="Carrito de compras"
    >
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        
        @if($cartCount > 0)
            <span class="absolute -top-1 -right-1 flex items-center justify-center min-w-[1.25rem] h-5 px-1 text-xs font-bold text-white bg-danger-600 rounded-full">
                {{ $cartCount }}
            </span>
        @endif
    </button>
</div>
