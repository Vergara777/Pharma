<div class="fi-ta-section space-y-6">
    <!-- Información General -->
    <div class="fi-ta-section">
        <div class="fi-ta-section-header">
            <h3 class="fi-ta-section-heading">Información General</h3>
        </div>
        <div class="fi-ta-section-content grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="fi-form-field">
                <label class="fi-form-field-label">Código de Lote</label>
                <div class="fi-form-input-container">
                    <input type="text" value="<?php echo e($lote->codigo_lote); ?>" disabled class="fi-form-input" />
                </div>
            </div>
            <div class="fi-form-field">
                <label class="fi-form-field-label">Producto</label>
                <div class="fi-form-input-container">
                    <input type="text" value="<?php echo e($lote->product->name); ?>" disabled class="fi-form-input" />
                </div>
            </div>
        </div>
    </div>

    <!-- Información Comercial -->
    <div class="fi-ta-section">
        <div class="fi-ta-section-header">
            <h3 class="fi-ta-section-heading">Información Comercial</h3>
        </div>
        <div class="fi-ta-section-content grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="fi-form-field">
                <label class="fi-form-field-label">Costo Unitario</label>
                <div class="fi-form-input-container">
                    <input type="text" value="$<?php echo e(number_format($lote->costo_unitario, 0, ',', '.')); ?>" disabled class="fi-form-input" />
                </div>
            </div>
            <div class="fi-form-field">
                <label class="fi-form-field-label">Precio Venta Sugerido</label>
                <div class="fi-form-input-container">
                    <input type="text" value="$<?php echo e(number_format($lote->precio_venta_sugerido, 0, ',', '.')); ?>" disabled class="fi-form-input" />
                </div>
            </div>
        </div>
    </div>

    <!-- Stock y Fechas -->
    <div class="fi-ta-section">
        <div class="fi-ta-section-header">
            <h3 class="fi-ta-section-heading">Stock y Fechas</h3>
        </div>
        <div class="fi-ta-section-content grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="fi-form-field">
                <label class="fi-form-field-label">Stock Actual</label>
                <div class="fi-form-input-container">
                    <input type="text" value="<?php echo e($lote->cantidad_actual); ?> unidades" disabled class="fi-form-input" />
                </div>
            </div>
            <div class="fi-form-field">
                <label class="fi-form-field-label">Fecha Vencimiento</label>
                <div class="fi-form-input-container">
                    <input type="text" value="<?php echo e($lote->fecha_vencimiento ? $lote->fecha_vencimiento->format('d/m/Y') : 'Sin fecha'); ?>" disabled class="fi-form-input" />
                </div>
            </div>
        </div>
    </div>

    <!-- Información Adicional -->
    <div class="fi-ta-section">
        <div class="fi-ta-section-header">
            <h3 class="fi-ta-section-heading">Información Adicional</h3>
        </div>
        <div class="fi-ta-section-content grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="fi-form-field">
                <label class="fi-form-field-label">Proveedor</label>
                <div class="fi-form-input-container">
                    <input type="text" value="<?php echo e($lote->proveedor->name ?? 'Sin proveedor'); ?>" disabled class="fi-form-input" />
                </div>
            </div>
            <div class="fi-form-field">
                <label class="fi-form-field-label">Ubicación Física</label>
                <div class="fi-form-input-container">
                    <input type="text" value="<?php echo e($lote->ubicacion_fisica ?? 'Sin ubicación'); ?>" disabled class="fi-form-input" />
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Pharma\resources\views/filament/resources/lotes/lote-view-simple.blade.php ENDPATH**/ ?>