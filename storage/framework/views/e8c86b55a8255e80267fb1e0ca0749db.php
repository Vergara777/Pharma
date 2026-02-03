<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura <?php echo e($venta->invoice_number); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: #fff;
        }
        
        .ticket {
            width: 80mm;
            margin: 0 auto;
            padding: 10mm;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 11px;
            margin: 2px 0;
        }
        
        .invoice-info {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
        }
        
        .invoice-info p {
            margin: 3px 0;
        }
        
        .invoice-info strong {
            display: inline-block;
            width: 100px;
        }
        
        .customer-info {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
        }
        
        .customer-info h3 {
            font-size: 13px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .customer-info p {
            margin: 2px 0;
            font-size: 11px;
        }
        
        .items {
            margin-bottom: 10px;
        }
        
        .items table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .items th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding: 5px 0;
            font-size: 11px;
        }
        
        .items td {
            padding: 5px 0;
            font-size: 11px;
        }
        
        .items .item-name {
            width: 50%;
        }
        
        .items .item-qty {
            width: 15%;
            text-align: center;
        }
        
        .items .item-price {
            width: 35%;
            text-align: right;
        }
        
        .totals {
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-bottom: 10px;
        }
        
        .totals p {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        
        .totals .total-line {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        
        .payment-info {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
        }
        
        .payment-info p {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px dashed #000;
            font-size: 11px;
        }
        
        .footer p {
            margin: 3px 0;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .ticket {
                width: 80mm;
                margin: 0;
                padding: 5mm;
            }
            
            .no-print {
                display: none;
            }
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #f59e0b;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .print-button:hover {
            background: #d97706;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">Imprimir Factura</button>
    
    <div class="ticket">
        <!-- Header -->
        <div class="header">
            <h1><?php echo e($venta->empresa_name); ?></h1>
            <p>NIT: <?php echo e($venta->empresa_nit); ?></p>
            <p>Dirección: <?php echo e($venta->empresa_address); ?></p>
            <p>Tel: <?php echo e($venta->empresa_phone); ?></p>
            <p>Email: [EMAIL_ADDRESS]</p>
        </div>
        
        <!-- Invoice Info -->
        <div class="invoice-info">
            <p><strong>Factura:</strong> <?php echo e($venta->invoice_number); ?></p>
            <p><strong>Fecha:</strong> <?php echo e($venta->created_at->format('d/m/Y H:i')); ?></p>
            <p><strong>Vendedor:</strong> <?php echo e($venta->user_name); ?></p>
        </div>
        
        <!-- Customer Info -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($venta->customer_name): ?>
        <div class="customer-info">
            <h3>CLIENTE</h3>
            <p><strong>Nombre:</strong> <?php echo e($venta->customer_name); ?></p>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($venta->invoice_document): ?>
            <p><strong>Documento:</strong> <?php echo e($venta->invoice_document); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($venta->invoice_address): ?>
            <p><strong>Dirección:</strong> <?php echo e($venta->invoice_address); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($venta->customer_phone): ?>
            <p><strong>Teléfono:</strong> <?php echo e($venta->customer_phone); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($venta->customer_email): ?>
            <p><strong>Email:</strong> <?php echo e($venta->customer_email); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        <!-- Items -->
        <div class="items">
            <table>
                <thead>
                    <tr>
                        <th class="item-name">Producto</th>
                        <th class="item-qty">Cant.</th>
                        <th class="item-price">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($venta->items->isNotEmpty()): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $venta->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <tr>
                                <td class="item-name"><?php echo e($item->product ? $item->product->name : 'Producto eliminado'); ?></td>
                                <td class="item-qty"><?php echo e($item->qty); ?></td>
                                <td class="item-price">$<?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" style="font-size: 10px; color: #666; padding-left: 5px;">
                                    @ $<?php echo e(number_format($item->unit_price, 0, ',', '.')); ?> c/u
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <?php else: ?>
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($venta->product): ?>
                            <tr>
                                <td class="item-name"><?php echo e($venta->product->name); ?></td>
                                <td class="item-qty"><?php echo e($venta->qty); ?></td>
                                <td class="item-price">$<?php echo e(number_format($venta->unit_price * $venta->qty, 0, ',', '.')); ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" style="font-size: 10px; color: #666; padding-left: 5px;">
                                    @ $<?php echo e(number_format($venta->unit_price, 0, ',', '.')); ?> c/u
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Totals -->
        <div class="totals">
            <p><span>Subtotal:</span> <span>$<?php echo e(number_format($venta->subtotal, 0, ',', '.')); ?></span></p>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($venta->discount_amount > 0): ?>
            <p><span>Descuento:</span> <span>-$<?php echo e(number_format($venta->discount_amount, 0, ',', '.')); ?></span></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($venta->tax_amount > 0): ?>
            <p><span>IVA (<?php echo e($venta->tax_rate); ?>%):</span> <span>$<?php echo e(number_format($venta->tax_amount, 0, ',', '.')); ?></span></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <p class="total-line"><span>TOTAL:</span> <span>$<?php echo e(number_format($venta->grand_total, 0, ',', '.')); ?></span></p>
        </div>
        
        <!-- Payment Info -->
        <div class="payment-info">
            <p><strong>Método de Pago:</strong> <span><?php echo e($venta->paymentMethod->name); ?></span></p>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($venta->payment_reference): ?>
            <p><strong>Referencia:</strong> <span><?php echo e($venta->payment_reference); ?></span></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($venta->amount_received > 0): ?>
            <p><strong>Recibido:</strong> <span>$<?php echo e(number_format($venta->amount_received, 0, ',', '.')); ?></span></p>
            <p><strong>Cambio:</strong> <span>$<?php echo e(number_format($venta->change_amount, 0, ',', '.')); ?></span></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>¡Gracias por su compra!</p>
            <p>Esta es su factura de venta</p>
            <p><?php echo e(now()->format('d/m/Y H:i:s')); ?></p>
        </div>
    </div>
    
    <script>
        // Auto-print cuando se carga la página (opcional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
<?php /**PATH C:\Pharma\resources\views/ventas/invoice.blade.php ENDPATH**/ ?>