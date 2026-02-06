<?php

namespace App\Filament\Widgets;

use App\Models\CashSession;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OtherOpenCashSessionsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $sessions = CashSession::where('status', 'open')
            ->where('user_id', '!=', auth()->id())
            ->with(['user', 'ventas' => function ($query) {
                $query->where('status', 'active');
            }])
            ->orderBy('opened_at', 'desc')
            ->get();

        if ($sessions->isEmpty()) {
            return [
                Stat::make('👥 Otras Cajas Abiertas', 'Ninguna')
                    ->description('No hay otras cajas abiertas en este momento')
                    ->descriptionIcon('heroicon-o-lock-closed')
                    ->color('gray'),
            ];
        }

        $stats = [];
        
        foreach ($sessions as $session) {
            $ventas = $session->ventas->sum('grand_total');
            $total = $session->initial_amount + $ventas;
            $userName = $session->user->name ?? 'Usuario';
            
            // Calcular tiempo de forma legible
            $minutes = round($session->opened_at->diffInMinutes(now()));
            if ($minutes < 60) {
                $timeOpen = $minutes . ' min';
            } else {
                $hours = floor($minutes / 60);
                $mins = $minutes % 60;
                $timeOpen = $hours . 'h ' . $mins . 'min';
            }
            
            $stats[] = Stat::make(
                '👤 ' . $userName,
                '$' . number_format($total, 0, ',', '.')
            )
                ->description('Caja #' . $session->id . ' • Abierta hace ' . $timeOpen . ' • Ventas: $' . number_format($ventas, 0, ',', '.'))
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('info')
                ->chart([0, $session->initial_amount, $total]);
        }

        return $stats;
    }
}
