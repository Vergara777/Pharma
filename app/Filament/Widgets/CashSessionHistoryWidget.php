<?php

namespace App\Filament\Widgets;

use App\Models\CashSession;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CashSessionHistoryWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $sessions = CashSession::where('user_id', auth()->id())
            ->where('status', 'closed')
            ->orderBy('closed_at', 'desc')
            ->limit(6)
            ->get();

        if ($sessions->isEmpty()) {
            return [
                Stat::make('Historial', 'Sin cajas cerradas')
                    ->description('Aún no has cerrado ninguna caja')
                    ->descriptionIcon('heroicon-o-archive-box')
                    ->color('gray'),
            ];
        }

        $stats = [];

        foreach ($sessions as $session) {
            $diferencia = $session->difference ?? 0;
            $color = $diferencia > 0 ? 'success' : ($diferencia < 0 ? 'danger' : 'info');
            $icon = $diferencia > 0 ? 'heroicon-o-arrow-trending-up' : ($diferencia < 0 ? 'heroicon-o-arrow-trending-down' : 'heroicon-o-minus');
            $diferenciaText = $diferencia > 0 ? 'Sobrante' : ($diferencia < 0 ? 'Faltante' : 'Cuadrada');

            $stats[] = Stat::make(
                'Caja #' . $session->id . ' • ' . $session->closed_at->format('d/m/Y'),
                '$' . number_format($session->counted_amount ?? 0, 0, ',', '.')
            )
                ->description($diferenciaText . ': $' . number_format(abs($diferencia), 0, ',', '.') . ' • ' . $session->closed_at->format('h:i A'))
                ->descriptionIcon($icon)
                ->color($color);
        }

        return $stats;
    }
    
    protected function getColumns(): int
    {
        return 3; // 3 tarjetas por fila
    }
}
