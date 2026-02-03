
        <div class="flex items-center gap-3 px-6 py-4 mb-4 border-b border-gray-100 dark:border-white/5 overflow-hidden">
            
            <?php if (isset($component)) { $__componentOriginalceea4679a368984135244eacf4aafeca = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalceea4679a368984135244eacf4aafeca = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.avatar.user','data' => ['size' => 'lg','user' => auth()->user(),'class' => 'flex-shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::avatar.user'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'lg','user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(auth()->user()),'class' => 'flex-shrink-0']); ?>
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
            
            
            <div x-show="$store.sidebar.isOpen" class="flex flex-col truncate transition-all duration-300">
                <span class="font-bold text-sm text-gray-900 dark:text-white truncate">
                    <?php echo e(auth()->user()->name); ?>

                </span>
                <span class="text-xs text-gray-500 truncate">
                    Farmacéutico
                </span>
            </div>
        </div>
    <?php /**PATH C:\Pharma\storage\framework\views/20f4dc229d8344f9a7ac31e27dd2e43d.blade.php ENDPATH**/ ?>