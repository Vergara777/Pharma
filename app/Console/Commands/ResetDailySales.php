<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ResetDailySales extends Command
{
    protected $signature = 'sales:reset-daily';
    protected $description = 'Resetear el contador de ventas diarias (se ejecuta automáticamente a medianoche)';

    public function handle()
    {
        // Limpiar caché de estadísticas diarias
        Cache::forget('daily_sales_count');
        Cache::forget('daily_sales_total');
        
        $this->info('✅ Contador de ventas diarias reseteado correctamente');
        $this->info('📅 Fecha actual: ' . now()->format('d/m/Y H:i:s'));
        
        return Command::SUCCESS;
    }
}
