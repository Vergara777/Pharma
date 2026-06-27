<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Ventas</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4F46E5; padding-bottom: 10px; }
        .header h1 { color: #4F46E5; margin: 0; font-size: 20px; }
        .header p { margin: 5px 0 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #4F46E5; color: white; padding: 8px; text-align: left; border: 1px solid #ddd; }
        td { padding: 6px; border: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
        .total-row { background-color: #eee; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $farmacia }}</h1>
        <p>Reporte General de Ventas - {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>N° Factura</th>
                <th>Producto</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($ventas as $venta)
            <tr>
                <td>{{ $venta->invoice_number }}</td>
                <td>{{ $venta->product_id }}</td>
                <td>{{ $venta->customer_name ?? 'Cliente General' }}</td>
                <td>{{ $venta->user_name }}</td>
                <td>{{ $venta->status === 'active' ? 'Activa' : ($venta->status === 'cancelled' ? 'Cancelada' : 'Devuelta') }}</td>
                <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                <td>${{ number_format($venta->grand_total, 0, ',', '.') }}</td>
            </tr>
            @php if($venta->status === 'active') $grandTotal += $venta->grand_total; @endphp
            @endforeach
            <tr class="total-row">
                <td colspan="5" style="text-align: right;">TOTAL VENTAS ACTIVAS:</td>
                <td>${{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Generado por {{ config('app.name') }} - Página <span class="page-number"></span>
    </div>
</body>
</html>
