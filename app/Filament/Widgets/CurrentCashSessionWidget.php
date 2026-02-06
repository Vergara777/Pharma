<?php

namespace App\Filament\Widgets;

use App\Models\CashSession;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CurrentCashSessionWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $openSession = CashSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if (!$openSession) {
            return [
                Stat::make('Estado de Caja', '🔒 Cerrada')
                    ->description('No hay caja abierta actualmente')
                    ->descriptionIcon('heroicon-o-lock-closed')
                    ->color('gray'),
            ];
        }

        // Sumar todas las ventas activas de esta sesión de caja
        $ventasHoy = $openSession->ventas()
            ->where('status', 'active')
            ->sum('grand_total');
        $transacciones = $openSession->ventas()
            ->where('status', 'active')
            ->count();
        $totalEsperado = $openSession->initial_amount + $ventasHoy;

        return [
            Stat::make('Monto Inicial', '$' . number_format($openSession->initial_amount, 0, ',', '.'))
                ->description('Apertura: ' . $openSession->opened_at->format('d/m/Y h:i A'))
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('primary'),
                
            Stat::make('Ventas del Día', '$' . number_format($ventasHoy, 0, ',', '.'))
                ->description($transacciones . ' transacciones realizadas')
                ->descriptionIcon('heroicon-o-shopping-cart')
                ->color('success')
                ->chart(array_fill(0, 7, $ventasHoy / 7)),
                
            Stat::make('Total Esperado', '$' . number_format($totalEsperado, 0, ',', '.'))
                ->description('Inicial + Ventas')
                ->descriptionIcon('heroicon-o-calculator')
                ->color('info'),
                
            Stat::make('Estado', '🔓 Abierta')
                ->description('Caja #' . $openSession->id)
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
