<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected function getStats(): array
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalSuppliers = Supplier::count();
        
        // Obtener configuración de moneda
        $currency = Cache::get('settings.currency', 'COP');
        $locale = Cache::get('settings.locale', 'es_CO');
        
        // Calcular valor del inventario de forma segura
        $totalInventoryValue = Product::query()
            ->selectRaw('SUM(price * stock) as total')
            ->value('total') ?? 0;
        
        // Calcular costo total del inventario
        $totalInventoryCost = Product::query()
            ->selectRaw('SUM(cost * stock) as total')
            ->value('total') ?? 0;
        
        // Calcular ganancia potencial del inventario
        $potentialProfit = $totalInventoryValue - $totalInventoryCost;
        $profitMargin = $totalInventoryCost > 0 ? (($potentialProfit / $totalInventoryCost) * 100) : 0;
        
        // Contar productos con stock bajo
        $lowStockCount = Product::query()
            ->whereColumn('stock', '<=', 'min_stock')
            ->where('stock', '>', 0)
            ->count();
        
        // Contar productos sin stock
        $outOfStockCount = Product::where('stock', 0)->count();

        // Productos próximos a vencer
        $expirationAlertDays = Cache::get('settings.expiration_alert_days', 30);
        $expiringProducts = Product::query()
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '<=', now()->addDays($expirationAlertDays))
            ->whereDate('expires_at', '>=', now())
            ->count();

        // Productos vencidos
        $expiredProducts = Product::query()
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '<', now())
            ->count();

        return [
            Stat::make('Total Productos', number_format($totalProducts))
                ->description('Productos registrados')
                ->descriptionIcon('heroicon-o-cube')
                ->color('primary')
                ->url(route('filament.admin.resources.products.index')),
            
            Stat::make('Stock Bajo', number_format($lowStockCount))
                ->description('Requieren reabastecimiento')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('warning')
                ->url(route('filament.admin.resources.products.index') . '?filter=low_stock'),
            
            Stat::make('Sin Stock', number_format($outOfStockCount))
                ->description('Productos agotados')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger')
                ->url(route('filament.admin.resources.products.index') . '?filter=out_of_stock'),
            
            Stat::make('Próximos a Vencer', number_format($expiringProducts))
                ->description("Vencen en {$expirationAlertDays} días o menos")
                ->descriptionIcon('heroicon-o-calendar')
                ->color($expiringProducts > 0 ? 'warning' : 'success')
                ->url(route('filament.admin.resources.products.index') . '?filter=expiring_soon'),
            
            Stat::make('Vencidos', number_format($expiredProducts))
                ->description('Productos vencidos')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color($expiredProducts > 0 ? 'danger' : 'success')
                ->url(route('filament.admin.resources.products.index') . '?filter=expired'),
        ];
    }
}
