<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Filament\Notifications\Notification;

class TestNotificationCommand extends Command
{
    protected $signature = 'test:notification';
    protected $description = 'Enviar notificación de prueba';

    public function handle()
    {
        $user = \App\Models\User::first();
        
        if (!$user) {
            $this->error('No hay usuarios en la base de datos');
            return;
        }

        // Notificación de prueba
        Notification::make()
            ->title('🧪 Notificación de Prueba')
            ->body('Esta es una notificación de prueba para verificar que el sistema funciona correctamente.')
            ->success()
            ->icon('heroicon-o-check-circle')
            ->duration(5000)
            ->sendToDatabase($user);

        $this->info("Notificación enviada a: {$user->name}");
        $this->info("Total notificaciones: " . $user->notifications()->count());
        
        return 0;
    }
}
