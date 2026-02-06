<?php

namespace App\Filament\Widgets;

use App\Models\CashSession;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CashSessionWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $isAdmin = auth()->user()->role === 'admin';
        
        // Obtener caja abierta del usuario actual
        $openSession = CashSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        // Si es admin, también obtener todas las cajas abiertas de otros usuarios
        $allOpenSessions = null;
        if ($isAdmin) {
            $allOpenSessions = CashSession::where('status', 'open')
                ->where('user_id', '!=', auth()->id())
                ->with('user')
                ->get();
        }

        // Obtener última caja cerrada
        $lastClosedSession = CashSession::where('user_id', auth()->id())
            ->where('status', 'closed')
            ->orderBy('closed_at', 'desc')
            ->first();

        $stats = [];

        // Tarjeta de Caja Abierta del usuario actual
        if ($openSession) {
            $ventasHoy = $openSession->ventas()
                ->where('status', 'active')
                ->sum('grand_total');
            $totalEsperado = $openSession->initial_amount + $ventasHoy;
            
            $stats[] = Stat::make('🔓 Mi Caja Abierta', '$' . number_format($totalEsperado, 0, ',', '.'))
                ->description('Inicial: $' . number_format($openSession->initial_amount, 0, ',', '.') . ' + Ventas: $' . number_format($ventasHoy, 0, ',', '.'))
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success')
                ->chart([0, $openSession->initial_amount, $totalEsperado])
                ->url(route('filament.admin.resources.cash-sessions.index'));
        } else {
            $stats[] = Stat::make('🔒 Mi Caja Cerrada', 'Sin caja abierta')
                ->description('Abre una caja para comenzar a vender')
                ->descriptionIcon('heroicon-o-lock-closed')
                ->color('gray')
                ->url(route('filament.admin.resources.cash-sessions.index'));
        }

        // Tarjeta de otras cajas abiertas (solo para admin)
        if ($isAdmin && $allOpenSessions && $allOpenSessions->count() > 0) {
            $totalOtrasCajas = 0;
            $detalles = [];
            
            foreach ($allOpenSessions as $session) {
                $ventas = $session->ventas()->where('status', 'active')->sum('grand_total');
                $totalOtrasCajas += $session->initial_amount + $ventas;
                
                // Calcular tiempo de forma legible
                $minutes = round($session->opened_at->diffInMinutes(now()));
                if ($minutes < 60) {
                    $timeOpen = $minutes . ' min';
                } else {
                    $hours = floor($minutes / 60);
                    $mins = $minutes % 60;
                    $timeOpen = $hours . 'h ' . $mins . 'min';
                }
                
                $userName = $session->user->name ?? 'Usuario';
                $detalles[] = $userName . ' (Caja #' . $session->id . ' • ' . $timeOpen . ' • Ventas: $' . number_format($ventas, 0, ',', '.') . ')';
            }
            
            $descripcion = count($detalles) > 1 
                ? $detalles[0] . ' | ' . $detalles[1] . (count($detalles) > 2 ? ' y ' . (count($detalles) - 2) . ' más' : '')
                : $detalles[0];
            
            $stats[] = Stat::make('👥 Otras Cajas Abiertas (' . count($detalles) . ')', '$' . number_format($totalOtrasCajas, 0, ',', '.'))
                ->description($descripcion)
                ->descriptionIcon('heroicon-o-users')
                ->color('info')
                ->url(route('filament.admin.resources.cash-sessions.index'));
        }

        // Tarjeta de Última Caja Cerrada
        if ($lastClosedSession) {
            $diferencia = $lastClosedSession->difference ?? 0;
            $color = $diferencia > 0 ? 'success' : ($diferencia < 0 ? 'danger' : 'info');
            $icon = $diferencia > 0 ? 'heroicon-o-arrow-trending-up' : ($diferencia < 0 ? 'heroicon-o-arrow-trending-down' : 'heroicon-o-minus');
            
            $stats[] = Stat::make('Última Caja Cerrada', '$' . number_format($lastClosedSession->counted_amount ?? 0, 0, ',', '.'))
                ->description('Diferencia: $' . number_format($diferencia, 0, ',', '.') . ' • ' . $lastClosedSession->closed_at->format('d/m/Y h:i A'))
                ->descriptionIcon($icon)
                ->color($color)
                ->url(route('filament.admin.resources.cash-sessions.index'));
        }

        return $stats;
    }
}
