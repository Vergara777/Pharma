<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class PersistTablePreferences
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Guardar preferencias de tabla si vienen en la petición
        if ($request->has('tableColumnToggles') && auth()->check()) {
            $userId = auth()->id();
            $table = $request->input('table');
            $toggles = $request->input('tableColumnToggles');
            
            Cache::forever("table_preferences.{$userId}.{$table}.columns", $toggles);
        }
        
        if ($request->has('perPage') && auth()->check()) {
            $userId = auth()->id();
            $table = $request->input('table');
            $perPage = $request->input('perPage');
            
            Cache::forever("table_preferences.{$userId}.{$table}.perPage", $perPage);
        }
        
        return $next($request);
    }
}
