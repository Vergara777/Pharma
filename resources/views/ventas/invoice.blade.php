<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $venta->invoice_number }}</title>
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
            <h1>{{ setting('pharmacy_name', config('app.name')) }}</h1>
            @if(setting('pharmacy_address'))
            <p>{{ setting('pharmacy_address') }}</p>
            @endif
            @if(setting('pharmacy_phone'))
            <p>Tel: {{ setting('pharmacy_phone') }}</p>
            @endif
            @if(setting('pharmacy_email'))
            <p>Email: {{ setting('pharmacy_email') }}</p>
            @endif
        </div>
        
        <!-- Invoice Info -->
        <div class="invoice-info">
            <p><strong>Factura:</strong> {{ $venta->invoice_number }}</p>
            <p><strong>Fecha:</strong> {{ $venta->created_at->format('d/m/Y h:i A') }}</p>
            <p><strong>Vendedor:</strong> {{ $venta->user->name ?? 'N/A' }}</p>
        </div>
        
        <!-- Customer Info -->
        @if($venta->customer_name)
        <div class="customer-info">
            <h3>CLIENTE</h3>
            <p><strong>Nombre:</strong> {{ $venta->customer_name }}</p>
            @if($venta->invoice_document)
            <p><strong>Documento:</strong> {{ $venta->invoice_document }}</p>
            @endif
            @if($venta->invoice_address)
            <p><strong>Dirección:</strong> {{ $venta->invoice_address }}</p>
            @endif
            @if($venta->customer_phone)
            <p><strong>Teléfono:</strong> {{ $venta->customer_phone }}</p>
            @endif
            @if($venta->customer_email)
            <p><strong>Email:</strong> {{ $venta->customer_email }}</p>
            @endif
        </div>
        @endif
        
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
                    @if($venta->items->isNotEmpty())
                        @foreach($venta->items as $item)
                            <tr>
                                <td class="item-name">{{ $item->product ? $item->product->name : 'Producto eliminado' }}</td>
                                <td class="item-qty">{{ $item->qty }}</td>
                                <td class="item-price">${{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" style="font-size: 10px; color: #666; padding-left: 5px;">
                                    @ ${{ number_format($item->unit_price, 0, ',', '.') }} c/u
                                </td>
                            </tr>
                        @endforeach
                    @else
                        {{-- Fallback para ventas antiguas --}}
                        @if($venta->product)
                            <tr>
                                <td class="item-name">{{ $venta->product->name }}</td>
                                <td class="item-qty">{{ $venta->qty }}</td>
                                <td class="item-price">${{ number_format($venta->unit_price * $venta->qty, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" style="font-size: 10px; color: #666; padding-left: 5px;">
                                    @ ${{ number_format($venta->unit_price, 0, ',', '.') }} c/u
                                </td>
                            </tr>
                        @endif
                    @endif
                </tbody>
            </table>
        </div>
        
        <!-- Totals -->
        <div class="totals">
            <p><span>Subtotal:</span> <span>${{ number_format($venta->subtotal, 0, ',', '.') }}</span></p>
            @if($venta->discount_amount > 0)
            <p><span>Descuento:</span> <span>-${{ number_format($venta->discount_amount, 0, ',', '.') }}</span></p>
            @endif
            @if($venta->tax_amount > 0)
            <p><span>IVA ({{ $venta->tax_rate }}%):</span> <span>${{ number_format($venta->tax_amount, 0, ',', '.') }}</span></p>
            @endif
            <p class="total-line"><span>TOTAL:</span> <span>${{ number_format($venta->grand_total, 0, ',', '.') }}</span></p>
        </div>
        
        <!-- Payment Info -->
        <div class="payment-info">
            <p><strong>Método de Pago:</strong> <span>{{ $venta->paymentMethod->name }}</span></p>
            @if($venta->payment_reference)
            <p><strong>Referencia:</strong> <span>{{ $venta->payment_reference }}</span></p>
            @endif
            @if($venta->amount_received > 0)
            <p><strong>Recibido:</strong> <span>${{ number_format($venta->amount_received, 0, ',', '.') }}</span></p>
            <p><strong>Cambio:</strong> <span>${{ number_format($venta->change_amount, 0, ',', '.') }}</span></p>
            @endif
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>¡Gracias por su compra!</p>
            <p>Esta es su factura de venta</p>
            <p>{{ now()->format('d/m/Y h:i:s A') }}</p>
        </div>
    </div>
    
    <script>
        // Auto-print cuando se carga la página (opcional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
