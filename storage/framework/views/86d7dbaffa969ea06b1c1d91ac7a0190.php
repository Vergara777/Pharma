<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">Imagen</th>
                <th scope="col" class="px-6 py-3">Insumo</th>
                <th scope="col" class="px-6 py-3 text-center">Cant.</th>
                <th scope="col" class="px-6 py-3 text-right">Precio</th>
                <th scope="col" class="px-6 py-3 text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($getRecord()->items->count() > 0): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $getRecord()->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="p-4">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->product && $item->product->image): ?>
                                <img src="<?php echo e(str_starts_with($item->product->image, 'http') ? $item->product->image : \Storage::disk('local')->url($item->product->image)); ?>" 
                                     alt="<?php echo e($item->product->name); ?>" 
                                     class="w-12 h-12 object-cover rounded shadow-sm">
                            <?php else: ?>
                                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            <div class="flex flex-col">
                                <span class="text-base font-semibold"><?php echo e($item->product ? $item->product->name : 'Producto Eliminado'); ?></span>
                                <span class="text-xs text-gray-500"><?php echo e($item->product ? $item->product->sku : '-'); ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-primary-100 text-primary-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-primary-900 dark:text-primary-300">
                                <?php echo e($item->qty); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            $<?php echo e(number_format($item->unit_price, 0, ',', '.')); ?>

                        </td>
                        <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                            $<?php echo e(number_format($item->qty * $item->unit_price, 0, ',', '.')); ?>

                        </td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php else: ?>
                
                <?php if($getRecord()->product): ?>
                    <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                        <td class="p-4">
                            <?php if($getRecord()->product->image): ?>
                                <img src="<?php echo e(str_starts_with($getRecord()->product->image, 'http') ? $getRecord()->product->image : \Storage::disk('local')->url($getRecord()->product->image)); ?>" class="w-12 h-12 object-cover rounded">
                            <?php else: ?>
                                <div class="w-12 h-12 bg-gray-100 rounded"></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                             <div class="flex flex-col">
                                <span class="text-base font-semibold"><?php echo e($getRecord()->product->name); ?></span>
                                <span class="text-xs text-gray-500"><?php echo e($getRecord()->product->sku); ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center"><?php echo e($getRecord()->qty); ?></td>
                        <td class="px-6 py-4 text-right">$<?php echo e(number_format($getRecord()->unit_price, 0, ',', '.')); ?></td>
                        <td class="px-6 py-4 text-right font-bold">$<?php echo e(number_format($getRecord()->qty * $getRecord()->unit_price, 0, ',', '.')); ?></td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>
</div>
<?php /**PATH C:\Pharma\resources\views/filament/resources/ventas/infolists/items-list.blade.php ENDPATH**/ ?>