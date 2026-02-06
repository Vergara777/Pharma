@livewire('cart-badge')
@livewire('cart-modal')

<style>
/* Asegurar que el botón del carrito tenga el tamaño correcto */
.fi-topbar-item-cart {
    display: flex;
    align-items: center;
    justify-content: center;
}

.fi-topbar-item-cart svg {
    width: 1.25rem !important;
    height: 1.25rem !important;
}
</style>

<script>
function toggleCartModal() {
    console.log('Dispatching openCart event');
    // Intentar ambos métodos
    if (window.Livewire) {
        window.Livewire.dispatch('openCart');
    }
    // También intentar con el método global
    if (typeof Livewire !== 'undefined') {
        Livewire.dispatch('openCart');
    }
}
</script>
