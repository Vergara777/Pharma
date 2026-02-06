<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
            📦 Información del Lote
        </h3>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Código de Lote</label>
                <p class="text-base font-semibold text-gray-900 dark:text-white"><?php echo e($lote->codigo_lote); ?></p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Estado</label>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    <?php echo e($lote->estado === 'activo' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : ''); ?>

                    <?php echo e($lote->estado === 'agotado' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : ''); ?>

                    <?php echo e($lote->estado === 'bloqueado' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : ''); ?>

                    <?php echo e($lote->estado === 'vencido' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : ''); ?>">
                    <?php echo e(ucfirst($lote->estado)); ?>

                </span>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Producto</label>
                <p class="text-base text-gray-900 dark:text-white"><?php echo e($lote->product->name); ?></p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Proveedor</label>
                <p class="text-base text-gray-900 dark:text-white"><?php echo e($lote->proveedor->name ?? 'N/A'); ?></p>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-3 border-t border-gray-200 dark:border-gray-700">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Cantidad Inicial</label>
                    <p class="text-base text-gray-900 dark:text-white"><?php echo e(number_format($lote->cantidad_inicial, 0, ',', '.')); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Cantidad Actual</label>
                    <p class="text-base font-semibold <?php echo e($lote->cantidad_actual <= 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'); ?>">
                        <?php echo e(number_format($lote->cantidad_actual, 0, ',', '.')); ?>

                    </p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Costo Unitario</label>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">$<?php echo e(number_format($lote->costo_unitario, 0, ',', '.')); ?></p>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-3 border-t border-gray-200 dark:border-gray-700">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Fecha de Vencimiento</label>
                    <p class="text-base text-gray-900 dark:text-white"><?php echo e($lote->fecha_vencimiento->format('d/m/Y')); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Fecha de Ingreso</label>
                    <p class="text-base text-gray-900 dark:text-white"><?php echo e($lote->fecha_ingreso->format('d/m/Y')); ?></p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Registrado por</label>
                <p class="text-base text-gray-900 dark:text-white"><?php echo e($lote->usuarioRegistro->name ?? 'N/A'); ?></p>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lote->notas): ?>
            <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Notas</label>
                <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg"><?php echo e($lote->notas); ?></p>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
            📋 Historial de Movimientos
        </h3>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($movimientos->isEmpty()): ?>
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="mt-2">No hay movimientos registrados</p>
            </div>
        <?php else: ?>
            <div class="space-y-3 max-h-[600px] overflow-y-auto pr-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $movimientos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movimiento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900/50 hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors">
                        <div class="flex items-start gap-3 mb-3">
                            <span class="text-2xl flex-shrink-0">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php switch($movimiento->tipo_movimiento):
                                    case ('entrada'): ?> 📥 <?php break; ?>
                                    <?php case ('salida'): ?> 📤 <?php break; ?>
                                    <?php case ('ajuste'): ?> ⚙️ <?php break; ?>
                                    <?php case ('merma'): ?> 📉 <?php break; ?>
                                    <?php case ('vencimiento'): ?> ⏰ <?php break; ?>
                                    <?php case ('devolucion'): ?> ↩️ <?php break; ?>
                                    <?php case ('venta'): ?> 🛒 <?php break; ?>
                                    <?php default: ?> 📦
                                <?php endswitch; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-white">
                                    <?php echo e(ucfirst($movimiento->tipo_movimiento)); ?>

                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <?php echo e($movimiento->created_at->format('d/m/Y h:i A')); ?>

                                </p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-3 mb-3">
                            <div class="text-center p-2 bg-white dark:bg-gray-800 rounded">
                                <span class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Cantidad</span>
                                <p class="font-bold text-gray-900 dark:text-white"><?php echo e(number_format($movimiento->cantidad, 0, ',', '.')); ?></p>
                            </div>
                            <div class="text-center p-2 bg-white dark:bg-gray-800 rounded">
                                <span class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Anterior</span>
                                <p class="text-gray-700 dark:text-gray-300"><?php echo e(number_format($movimiento->cantidad_anterior, 0, ',', '.')); ?></p>
                            </div>
                            <div class="text-center p-2 bg-white dark:bg-gray-800 rounded">
                                <span class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Nueva</span>
                                <p class="text-gray-700 dark:text-gray-300"><?php echo e(number_format($movimiento->cantidad_nueva, 0, ',', '.')); ?></p>
                            </div>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($movimiento->motivo): ?>
                        <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                            <span class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Motivo</span>
                            <p class="text-sm text-gray-700 dark:text-gray-300"><?php echo e($movimiento->motivo); ?></p>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($movimiento->usuario): ?>
                        <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Por: </span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e($movimiento->usuario->name); ?></span>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Pharma\resources\views/filament/resources/lotes/detalles-completos.blade.php ENDPATH**/ ?>