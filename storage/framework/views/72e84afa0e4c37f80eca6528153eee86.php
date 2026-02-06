<?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('cart-badge');

$key = null;
$__componentSlots = [];

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1394343961-0', $key);

$__html = app('livewire')->mount($__name, $__params, $key, $__componentSlots);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
<?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('cart-modal');

$key = null;
$__componentSlots = [];

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1394343961-1', $key);

$__html = app('livewire')->mount($__name, $__params, $key, $__componentSlots);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

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
<?php /**PATH C:\Pharma\resources\views/filament/hooks/shopping-cart-trigger.blade.php ENDPATH**/ ?>