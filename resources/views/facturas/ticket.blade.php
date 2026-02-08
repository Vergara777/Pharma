<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $factura->invoice_number }}</title>
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
            padding: 20px;
            max-width: 400px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .info-section {
            margin-bottom: 15px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .info-label {
            font-weight: bold;
        }
        
        .cliente-section {
            margin-bottom: 15px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        
        .cliente-section h3 {
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .productos-section {
            margin-bottom: 15px;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
        }
        
        .producto-item {
            margin-bottom: 8px;
        }
        
        .producto-header {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .producto-detalle {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #555;
        }
        
        .totales-section {
            margin-bottom: 15px;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .total-final {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #f59e0b;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }
        
        .print-button:hover {
            background-color: #d97706;
        }
        
        @media print {
            .print-button {
                display: none;
            }
            
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">Imprimir Factura</button>
    
    <div class="header">
        <h1>{{ config('app.name', 'Pharma') }}</h1>
        <div>---------------------------------------</div>
    </div>
    
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Factura:</span>
            <span>{{ $factura->invoice_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha:</span>
            <span>{{ $factura->fecha_emision->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Vendedor:</span>
            <span>{{ $factura->user->name }}</span>
        </div>
    </div>
    
    <div class="cliente-section">
        <h3>CLIENTE</h3>
        <div class="info-row">
            <span class="info-label">Nombre:</span>
            <span>{{ $factura->cliente->name }}</span>
        </div>
        @if($factura->cliente->document)
        <div class="info-row">
            <span class="info-label">Documento:</span>
            <span>{{ $factura->cliente->document }}</span>
        </div>
        @endif
        @if($factura->cliente->address)
        <div class="info-row">
            <span class="info-label">Dirección:</span>
            <span>{{ $factura->cliente->address }}</span>
        </div>
        @endif
        @if($factura->cliente->phone)
        <div class="info-row">
            <span class="info-label">Teléfono:</span>
            <span>{{ $factura->cliente->phone }}</span>
        </div>
        @endif
        @if($factura->cliente->email)
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span>{{ $factura->cliente->email }}</span>
        </div>
        @endif
    </div>
    
    <div class="productos-section">
        <div style="text-align: center; margin-bottom: 10px;">
            <strong>---------------------------------------</strong>
        </div>
        <div class="producto-header">
            <span>Producto</span>
            <span>Cant.</span>
            <span>Total</span>
        </div>
        
        @foreach($factura->items as $item)
        <div class="producto-item">
            <div class="producto-header">
                <span>{{ $item->product->name }}</span>
                <span>{{ $item->quantity }}</span>
                <span>${{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="producto-detalle">
                <span>@ ${{ number_format($item->price, 0, ',', '.') }} c/u</span>
            </div>
        </div>
        @endforeach
        
        <div style="text-align: center; margin-top: 10px;">
            <strong>---------------------------------------</strong>
        </div>
    </div>
    
    <div class="totales-section">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>${{ number_format($factura->subtotal, 0, ',', '.') }}</span>
        </div>
        @if($factura->tax > 0)
        <div class="total-row">
            <span>IVA (19%):</span>
            <span>${{ number_format($factura->tax, 0, ',', '.') }}</span>
        </div>
        @endif
        @if($factura->discount > 0)
        <div class="total-row">
            <span>Descuento:</span>
            <span>-${{ number_format($factura->discount, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="total-row total-final">
            <span>TOTAL:</span>
            <span>${{ number_format($factura->total, 0, ',', '.') }}</span>
        </div>
    </div>
    
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Método de Pago:</span>
            <span>
                @switch($factura->payment_method)
                    @case('cash') Efectivo @break
                    @case('card') Tarjeta @break
                    @case('transfer') Transferencia @break
                    @case('check') Cheque @break
                    @default {{ $factura->payment_method }}
                @endswitch
            </span>
        </div>
        @if($factura->notes)
        <div class="info-row">
            <span class="info-label">Notas:</span>
            <span>{{ $factura->notes }}</span>
        </div>
        @endif
    </div>
    
    <div class="footer">
        <div style="margin-bottom: 10px;">
            <strong>---------------------------------------</strong>
        </div>
        <div>¡Gracias por su compra!</div>
        <div style="margin-top: 5px;">
            Esta es su factura de venta
        </div>
        <div style="margin-top: 5px; font-size: 10px;">
            {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>
</body>
</html>
