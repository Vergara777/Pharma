<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php
        $lote = $this->record;
        $movimientos = $lote->movimientos()->with('usuario')->latest()->get();
    ?>

    <div class="space-y-6">
        
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header flex items-center gap-x-3 overflow-hidden px-6 py-4">
                <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    📦 Información del Lote
                </h3>
            </div>

            <div class="fi-section-content-ctn border-t border-gray-200 dark:border-white/10">
                <div class="fi-section-content p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Código de Lote</label>
                                <p class="text-lg font-bold text-gray-900 dark:text-white"><?php echo e($lote->codigo_lote); ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Estado</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    <?php echo e($lote->estado === 'activo' ? 'bg-green-100 text-green-800' : ''); ?>

                                    <?php echo e($lote->estado === 'agotado' ? 'bg-red-100 text-red-800' : ''); ?>

                                    <?php echo e($lote->estado === 'bloqueado' ? 'bg-yellow-100 text-yellow-800' : ''); ?>

                                    <?php echo e($lote->estado === 'vencido' ? 'bg-red-100 text-red-800' : ''); ?>">
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

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Cantidad Inicial</label>
                                    <p class="text-base text-gray-900 dark:text-white"><?php echo e(number_format($lote->cantidad_inicial, 0, ',', '.')); ?></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Cantidad Actual</label>
                                    <p class="text-base font-semibold <?php echo e($lote->cantidad_actual <= 0 ? 'text-red-600' : 'text-green-600'); ?>">
                                        <?php echo e(number_format($lote->cantidad_actual, 0, ',', '.')); ?>

                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Costo Unitario</label>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">$<?php echo e(number_format($lote->costo_unitario, 0, ',', '.')); ?></p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Fecha Vencimiento</label>
                                    <p class="text-base text-gray-900 dark:text-white"><?php echo e($lote->fecha_vencimiento->format('d/m/Y')); ?></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Fecha Ingreso</label>
                                    <p class="text-base text-gray-900 dark:text-white"><?php echo e($lote->fecha_ingreso->format('d/m/Y')); ?></p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Registrado por</label>
                                <p class="text-base text-gray-900 dark:text-white"><?php echo e($lote->usuarioRegistro->name ?? 'Sistema'); ?></p>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lote->notas): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Notas</label>
                                <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 p-3 rounded-lg"><?php echo e($lote->notas); ?></p>
                            </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header flex items-center gap-x-3 overflow-hidden px-6 py-4">
                <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    📋 Historial de Movimientos (<?php echo e($movimientos->count()); ?>)
                </h3>
            </div>

            <div class="fi-section-content-ctn border-t border-gray-200 dark:border-white/10">
                <div class="fi-section-content p-6">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($movimientos->isEmpty()): ?>
                        <div class="text-center py-12 text-gray-500">
                            <p class="text-lg">No hay movimientos registrados</p>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $movimientos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="rounded-lg border p-4 bg-gray-50 hover:shadow-md transition-all">
                                    <div class="flex items-start gap-3 mb-3">
                                        <span class="text-3xl">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mov->tipo_movimiento === 'entrada'): ?> 📥
                                            <?php elseif($mov->tipo_movimiento === 'salida'): ?> 📤
                                            <?php elseif($mov->tipo_movimiento === 'ajuste'): ?> ⚙️
                                            <?php elseif($mov->tipo_movimiento === 'merma'): ?> 📉
                                            <?php else: ?> 📦
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </span>
                                        <div>
                                            <h4 class="font-bold text-lg"><?php echo e(ucfirst($mov->tipo_movimiento)); ?></h4>
                                            <p class="text-xs text-gray-500"><?php echo e($mov->created_at->format('d/m/Y h:i A')); ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-2 mb-3">
                                        <div class="flex justify-between p-2 bg-white rounded">
                                            <span class="text-xs text-gray-500">Cantidad</span>
                                            <p class="font-bold"><?php echo e(number_format($mov->cantidad, 0, ',', '.')); ?></p>
                                        </div>
                                        <div class="flex justify-between p-2 bg-white rounded">
                                            <span class="text-xs text-gray-500">Anterior</span>
                                            <p><?php echo e(number_format($mov->cantidad_anterior, 0, ',', '.')); ?></p>
                                        </div>
                                        <div class="flex justify-between p-2 bg-white rounded">
                                            <span class="text-xs text-gray-500">Nueva</span>
                                            <p class="font-semibold"><?php echo e(number_format($mov->cantidad_nueva, 0, ',', '.')); ?></p>
                                        </div>
                                    </div>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mov->motivo): ?>
                                    <div class="pt-2 border-t">
                                        <span class="block text-xs font-medium text-gray-500 mb-1">Motivo</span>
                                        <p class="text-sm"><?php echo e($mov->motivo); ?></p>
                                    </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mov->usuario): ?>
                                    <div class="pt-2 border-t mt-2">
                                        <span class="text-xs text-gray-500">Por: </span>
                                        <span class="text-sm font-medium"><?php echo e($mov->usuario->name); ?></span>
                                    </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php /**PATH C:\Pharma\resources\views/filament/resources/lotes/pages/view-lote.blade.php ENDPATH**/ ?>