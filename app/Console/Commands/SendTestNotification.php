<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Console\Command;

class SendTestNotification extends Command
{
    protected $signature = 'notification:test {user_id=1}';
    protected $description = 'Enviar una notificación de prueba a un usuario';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);

        if (!$user) {
            $this->error("Usuario con ID {$userId} no encontrado");
            return 1;
        }

        $user->notify(new LowStockNotification(
            title: 'Notificación de Prueba',
            body: 'Esta es una notificación de prueba enviada manualmente',
            type: 'info',
            icon: 'heroicon-o-information-circle'
        ));

        $this->info("Notificación enviada exitosamente a: {$user->name}");
        $this->info("Total de notificaciones del usuario: " . $user->notifications()->count());
        $this->info("Notificaciones no leídas: " . $user->unreadNotifications()->count());

        return 0;
    }
}
