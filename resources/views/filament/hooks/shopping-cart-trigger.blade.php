@livewire('cart-badge')
@livewire('cart-modal')

<script>
function toggleCartModal() {
    Livewire.dispatch('openCart');
}
</script>
