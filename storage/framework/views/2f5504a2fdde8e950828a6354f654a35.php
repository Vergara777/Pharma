
            <div class="flex items-center gap-3 px-6 py-4 mb-4 border-b border-gray-100 dark:border-white/5">
                <?php if (isset($component)) { $__componentOriginalceea4679a368984135244eacf4aafeca = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalceea4679a368984135244eacf4aafeca = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.avatar.user','data' => ['size' => 'lg','user' => auth()->user()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::avatar.user'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'lg','user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(auth()->user())]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalceea4679a368984135244eacf4aafeca)): ?>
<?php $attributes = $__attributesOriginalceea4679a368984135244eacf4aafeca; ?>
<?php unset($__attributesOriginalceea4679a368984135244eacf4aafeca); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalceea4679a368984135244eacf4aafeca)): ?>
<?php $component = $__componentOriginalceea4679a368984135244eacf4aafeca; ?>
<?php unset($__componentOriginalceea4679a368984135244eacf4aafeca); ?>
<?php endif; ?>
                <div class="flex flex-col">
                    <span class="font-bold text-sm text-gray-900 dark:text-white">
                        <?php echo e(auth()->user()->name); ?>

                    </span>
                    <span class="text-xs text-gray-500">
                        Farmacéutico
                    </span>
                </div>
            </div>
        <?php /**PATH C:\Pharma\storage\framework\views/5a9a988db2dc26ed40c1be4e0e1aefb2.blade.php ENDPATH**/ ?>