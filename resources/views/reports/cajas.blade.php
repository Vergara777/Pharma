<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Cajas</title>
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
        .diff-positive { color: #10b981; }
        .diff-negative { color: #ef4444; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $farmacia }}</h1>
        <p>Reporte de Sesiones de Caja - {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Apertura</th>
                <th>Cierre</th>
                <th>Inicial</th>
                <th>Teórico</th>
                <th>Contado</th>
                <th>Diferencia</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sessions as $session)
            <tr>
                <td>#{{ $session->id }}</td>
                <td>{{ $session->user->name ?? 'N/A' }}</td>
                <td>{{ $session->opened_at->format('d/m H:i') }}</td>
                <td>{{ $session->closed_at ? $session->closed_at->format('d/m H:i') : '-' }}</td>
                <td>${{ number_format($session->initial_amount, 0, ',', '.') }}</td>
                <td>${{ number_format($session->theoretical_amount ?? 0, 0, ',', '.') }}</td>
                <td>${{ number_format($session->counted_amount ?? 0, 0, ',', '.') }}</td>
                <td class="{{ $session->difference > 0 ? 'diff-positive' : ($session->difference < 0 ? 'diff-negative' : '') }}">
                    ${{ number_format($session->difference ?? 0, 0, ',', '.') }}
                </td>
                <td>{{ $session->status === 'open' ? 'Abierta' : 'Cerrada' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generado por {{ config('app.name') }} - Página <span class="page-number"></span>
    </div>
</body>
</html>
