<div style="position: relative; z-index: 40;">
    <button 
        type="button"
        onclick="toggleCartModal()"
        class="fi-icon-btn relative flex items-center justify-center rounded-lg outline-none transition duration-75 hover:bg-gray-50 focus-visible:bg-gray-50 dark:hover:bg-white/5 dark:focus-visible:bg-white/5 fi-color-gray fi-icon-btn-size-md gap-1.5 px-2 py-2"
        title="Carrito de compras"
    >
        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cartCount > 0): ?>
            <span style="position: absolute; top: -4px; right: -4px; display: flex; align-items: center; justify-content: center; min-width: 1.25rem; height: 1.25rem; padding: 0 0.25rem; font-size: 0.75rem; font-weight: 700; color: white; background-color: rgb(239 68 68); border-radius: 9999px; z-index: 50;">
                <?php echo e($cartCount); ?>

            </span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </button>
</div>
<?php /**PATH C:\Pharma\resources\views/livewire/cart-badge.blade.php ENDPATH**/ ?>