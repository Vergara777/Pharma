<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Filament\Notifications\Notification;

class CheckOpenCashSession
{
    public function handle(Request $request, Closure $next): Response
    {
        // Solo verificar en la ruta de logout
        if ($request->is('admin/logout') || $request->routeIs('filament.admin.auth.logout')) {
            $user = auth()->user();
            
            if ($user) {
                $openSession = \App\Models\CashSession::where('user_id', $user->id)
                    ->where('status', 'open')
                    ->first();
                
                \Log::info('Logout attempt', [
                    'user' => $user->name,
                    'role' => $user->role,
                    'has_open_session' => $openSession ? 'yes' : 'no',
                    'session_id' => $openSession?->id
                ]);
                
                // Si es trabajador y tiene caja abierta, bloquear logout
                if ($openSession && $user->role !== 'admin') {
                    Notification::make()
                        ->danger()
                        ->title('No puedes cerrar sesión')
                        ->body('Debes cerrar tu caja antes de cerrar sesión. Ve a Cajas para cerrarla.')
                        ->persistent()
                        ->send();
                    
                    return redirect()->route('filament.admin.resources.cash-sessions.index');
                }
                
                // Si es admin con caja abierta, advertir pero permitir
                if ($openSession && $user->role === 'admin') {
                    Notification::make()
                        ->warning()
                        ->title('Tienes una caja abierta')
                        ->body('Como administrador puedes cerrar sesión, pero recuerda cerrar tu caja después.')
                        ->send();
                }
            }
        }
        
        return $next($request);
    }
}
