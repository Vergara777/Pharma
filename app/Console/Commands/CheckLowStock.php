<?php

namespace App\Console\Commands;

use App\Notifications\LowStockNotification;
use Illuminate\Console\Command;

class CheckLowStock extends Command
{
    protected $signature = 'stock:check';
    protected $description = 'Verificar productos con stock bajo y enviar notificaciones';

    public function handle()
    {
        $this->info('Verificando stock de productos...');
        
        LowStockNotification::send();
        
        $this->info('Notificaciones enviadas correctamente');
    }
}
