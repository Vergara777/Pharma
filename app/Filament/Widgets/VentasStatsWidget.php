<?php

namespace App\Filament\Widgets;

use App\Models\Ventas;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VentasStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Ventas de hoy
        $ventasHoy = Ventas::whereDate('created_at', today())
            ->where('status', 'active')
            ->count();
        
        $totalHoy = Ventas::whereDate('created_at', today())
            ->where('status', 'active')
            ->sum('grand_total');
        
        // Ventas del mes
        $ventasMes = Ventas::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'active')
            ->count();
        
        $totalMes = Ventas::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'active')
            ->sum('grand_total');
        
        // Ventas totales
        $ventasTotales = Ventas::where('status', 'active')->count();
        $totalGeneral = Ventas::where('status', 'active')->sum('grand_total');
        
        // Calcular tendencia (comparar con ayer)
        $ventasAyer = Ventas::whereDate('created_at', today()->subDay())
            ->where('status', 'active')
            ->count();
        
        return [
            Stat::make('Ventas Hoy', $ventasHoy)
                ->description($ventasHoy > $ventasAyer ? 'Aumento respecto a ayer' : 'Disminución respecto a ayer')
                ->descriptionIcon($ventasHoy > $ventasAyer ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart([7, 4, 6, 8, 5, 9, $ventasHoy])
                ->color($ventasHoy > $ventasAyer ? 'success' : 'danger'),
            
            Stat::make('Ingresos Hoy', '$' . number_format($totalHoy, 0, ',', '.'))
                ->description('Total vendido hoy')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
            
            Stat::make('Ventas del Mes', $ventasMes)
                ->description('Total: $' . number_format($totalMes, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
            
            Stat::make('Ventas Totales', $ventasTotales)
                ->description('Ingresos: $' . number_format($totalGeneral, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('warning'),
        ];
    }
}
