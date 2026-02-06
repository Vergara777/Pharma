<div class="space-y-4">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($movimientos->isEmpty()): ?>
        <p class="text-sm text-gray-500 dark:text-gray-400">No hay movimientos registrados</p>
    <?php else: ?>
        <!-- Resumen -->
        <div class="space-y-2">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Total Movimientos</p>
                <p class="text-base text-gray-900 dark:text-white"><?php echo e($movimientos->count()); ?></p>
            </div>
            
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Total Entradas</p>
                <p class="text-base text-gray-900 dark:text-white">+<?php echo e($movimientos->whereIn('tipo_movimiento', ['entrada', 'devolucion'])->sum('cantidad')); ?></p>
            </div>
            
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Total Salidas</p>
                <p class="text-base text-gray-900 dark:text-white">-<?php echo e($movimientos->whereIn('tipo_movimiento', ['salida', 'venta', 'merma', 'vencimiento'])->sum('cantidad')); ?></p>
            </div>
        </div>

        <!-- Historial -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $movimientos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movimiento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <div class="space-y-2">
                    <div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($movimiento->tipo_movimiento, ['entrada', 'devolucion'])): ?>
                            <p class="text-base text-gray-900 dark:text-white">+<?php echo e($movimiento->cantidad); ?> unidades</p>
                        <?php elseif(in_array($movimiento->tipo_movimiento, ['salida', 'venta', 'merma', 'vencimiento'])): ?>
                            <p class="text-base text-gray-900 dark:text-white">-<?php echo e($movimiento->cantidad); ?> unidades</p>
                        <?php else: ?>
                            <p class="text-base text-gray-900 dark:text-white"><?php echo e($movimiento->cantidad); ?> unidades</p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo e($movimiento->created_at->format('d/m/Y h:i A')); ?></p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo e(ucfirst($movimiento->tipo_movimiento)); ?></p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Stock Anterior: <?php echo e($movimiento->cantidad_anterior); ?></p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Stock Nuevo: <?php echo e($movimiento->cantidad_nueva); ?></p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Usuario: <?php echo e($movimiento->usuario->name ?? 'Sistema'); ?></p>
                    </div>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($movimiento->motivo): ?>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Motivo:</p>
                            <p class="text-sm text-gray-900 dark:text-white"><?php echo e($movimiento->motivo); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$loop->last): ?>
                        <div class="border-t border-gray-200 dark:border-gray-700 mt-4"></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Pharma\resources\views/filament/resources/lotes/movimientos-table.blade.php ENDPATH**/ ?>