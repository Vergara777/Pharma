<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Ventas;
use App\Models\VentaItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryProfitWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Valor total del inventario (costo)
        $totalInventoryCost = Product::active()
            ->sum(\DB::raw('cost * stock'));

        // Valor total del inventario (precio de venta)
        $totalInventoryValue = Product::active()
            ->sum(\DB::raw('price * stock'));

        // Ganancia potencial del inventario
        $potentialProfit = $totalInventoryValue - $totalInventoryCost;
        
        // Calcular margen de ganancia
        $profitMargin = $totalInventoryCost > 0 ? (($potentialProfit / $totalInventoryCost) * 100) : 0;

        // Ganancias reales (ventas realizadas) - usando items
        $realProfit = VentaItem::whereHas('venta', function ($query) {
                $query->where('status', 'active');
            })
            ->get()
            ->sum(function ($item) {
                $product = $item->product;
                if (!$product) return 0;
                
                $costPerUnit = $product->cost ?? 0;
                $profit = ($item->unit_price - $costPerUnit) * $item->qty;
                return $profit;
            });

        // Ganancias del mes actual
        $monthlyProfit = VentaItem::whereHas('venta', function ($query) {
                $query->where('status', 'active')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
            })
            ->get()
            ->sum(function ($item) {
                $product = $item->product;
                if (!$product) return 0;
                
                $costPerUnit = $product->cost ?? 0;
                $profit = ($item->unit_price - $costPerUnit) * $item->qty;
                return $profit;
            });

        // Ganancias de hoy
        $todayProfit = VentaItem::whereHas('venta', function ($query) {
                $query->where('status', 'active')
                    ->whereDate('created_at', today());
            })
            ->get()
            ->sum(function ($item) {
                $product = $item->product;
                if (!$product) return 0;
                
                $costPerUnit = $product->cost ?? 0;
                $profit = ($item->unit_price - $costPerUnit) * $item->qty;
                return $profit;
            });

        return [
            Stat::make('Valor Inventario (Costo)', '$' . number_format($totalInventoryCost, 0, ',', '.'))
                ->description('Inversión total en inventario')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('info'),

            Stat::make('Valor Inventario (Venta)', '$' . number_format($totalInventoryValue, 0, ',', '.'))
                ->description('Valor potencial de venta')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Ganancia Potencial', '$' . number_format($potentialProfit, 0, ',', '.'))
                ->description(number_format($profitMargin, 1) . '% de margen')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('warning'),

            Stat::make('Ganancias Totales', '$' . number_format($realProfit, 0, ',', '.'))
                ->description('Ganancias acumuladas')
                ->descriptionIcon('heroicon-o-trophy')
                ->color('success'),

            Stat::make('Ganancias del Mes', '$' . number_format($monthlyProfit, 0, ',', '.'))
                ->description(now()->format('F Y'))
                ->descriptionIcon('heroicon-o-calendar')
                ->color('success'),

            Stat::make('Ganancias de Hoy', '$' . number_format($todayProfit, 0, ',', '.'))
                ->description(now()->format('d/m/Y'))
                ->descriptionIcon('heroicon-o-clock')
                ->color('success'),
        ];
    }
}
