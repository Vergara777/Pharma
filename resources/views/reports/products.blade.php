<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; padding: 20px 0; border-bottom: 2px solid #2563eb; margin-bottom: 15px; }
        .header img { height: 60px; margin-bottom: 8px; }
        .header h1 { font-size: 20px; color: #2563eb; font-weight: bold; }
        .header p { font-size: 11px; color: #666; }
        .meta { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 10px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead tr { background-color: #2563eb; color: white; }
        thead th { padding: 6px 4px; text-align: left; font-size: 9px; }
        tbody tr:nth-child(even) { background-color: #f1f5f9; }
        tbody tr:hover { background-color: #dbeafe; }
        tbody td { padding: 5px 4px; border-bottom: 1px solid #e2e8f0; font-size: 9px; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 8px; font-weight: bold; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        @if(file_exists(public_path('logo.png')))
            <img src="{{ public_path('logo.png') }}" alt="Logo">
        @endif
        <h1>{{ $farmacia }}</h1>
        <p>Reporte de Inventario de Productos</p>
    </div>

    <div class="meta">
        <span>Fecha: {{ now()->format('d/m/Y H:i') }}</span>
        <span>Total productos: {{ $products->count() }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Proveedor</th>
                <th>Precio</th>
                <th>Costo</th>
                <th>Stock</th>
                <th>Mín</th>
                <th>Máx</th>
                <th>Unidad</th>
                <th>Presentación</th>
                <th>Uds/Pres</th>
                <th>P. Unidad</th>
                <th>P. Paquete</th>
                <th>Estante</th>
                <th>Vencimiento</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->sku }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category?->name ?? 'N/A' }}</td>
                <td>{{ $product->supplier?->name ?? 'N/A' }}</td>
                <td>${{ number_format($product->price, 0, ',', '.') }}</td>
                <td>${{ number_format($product->cost, 0, ',', '.') }}</td>
                <td>
                    @php
                        $stock = $product->stock;
                        $min = $product->min_stock ?? 5;
                        $max = $product->max_stock ?? 100;
                        $badgeClass = $stock == 0 ? 'badge-danger' : ($stock <= $min ? 'badge-danger' : ($stock <= $min + 10 ? 'badge-warning' : ($stock > $max ? 'badge-info' : 'badge-success')));
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $stock }}</span>
                </td>
                <td>{{ $product->min_stock }}</td>
                <td>{{ $product->max_stock }}</td>
                <td>{{ $product->unit_name }}</td>
                <td>{{ $product->package_name ?? 'N/A' }}</td>
                <td>{{ $product->units_per_package }}</td>
                <td>${{ number_format($product->price_unit ?? 0, 0, ',', '.') }}</td>
                <td>${{ number_format($product->price_package ?? 0, 0, ',', '.') }}</td>
                <td>{{ collect([$product->shelf, $product->row, $product->position])->filter()->join('-') ?: 'N/A' }}</td>
                <td>
                    @if($product->expires_at)
                        @php
                            $days = now()->diffInDays($product->expires_at, false);
                            $expClass = $days < 0 ? 'badge-danger' : ($days <= 30 ? 'badge-warning' : 'badge-success');
                            $expLabel = $days < 0 ? 'Vencido' : $product->expires_at->format('d/m/Y');
                        @endphp
                        <span class="badge {{ $expClass }}">{{ $expLabel }}</span>
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    <span class="badge {{ $product->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                        {{ $product->status === 'active' ? 'Activo' : 'Retirado' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generado el {{ now()->format('d/m/Y \a \l\a\s H:i:s') }} — Sistema Pharma</p>
    </div>
</body>
</html>
