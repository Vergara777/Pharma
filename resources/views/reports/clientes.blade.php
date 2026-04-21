<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Clientes</title>
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
        .status-active { color: #10b981; font-weight: bold; }
        .status-inactive { color: #ef4444; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $farmacia }}</h1>
        <p>Reporte General de Clientes - {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Documento</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Dirección</th>
                <th>Estado</th>
                <th>Fecha Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clientes as $cliente)
            <tr>
                <td>{{ $cliente->name }}</td>
                <td>{{ $cliente->document ?? 'N/A' }}</td>
                <td>{{ $cliente->phone ?? 'N/A' }}</td>
                <td>{{ $cliente->email ?? 'N/A' }}</td>
                <td>{{ $cliente->address ?? 'N/A' }}</td>
                <td>
                    <span class="{{ $cliente->is_active ? 'status-active' : 'status-inactive' }}">
                        {{ $cliente->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td>{{ $cliente->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generado por {{ config('app.name') }} - Página <span class="page-number"></span>
    </div>
</body>
</html>
