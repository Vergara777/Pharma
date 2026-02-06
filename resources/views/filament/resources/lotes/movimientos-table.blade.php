<div class="space-y-4">
    @if($movimientos->isEmpty())
        <p class="text-sm text-gray-500 dark:text-gray-400">No hay movimientos registrados</p>
    @else
        <!-- Resumen -->
        <div class="space-y-2">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Total Movimientos</p>
                <p class="text-base text-gray-900 dark:text-white">{{ $movimientos->count() }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Total Entradas</p>
                <p class="text-base text-gray-900 dark:text-white">+{{ $movimientos->whereIn('tipo_movimiento', ['entrada', 'devolucion'])->sum('cantidad') }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Total Salidas</p>
                <p class="text-base text-gray-900 dark:text-white">-{{ $movimientos->whereIn('tipo_movimiento', ['salida', 'venta', 'merma', 'vencimiento'])->sum('cantidad') }}</p>
            </div>
        </div>

        <!-- Historial -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-4">
            @foreach($movimientos as $movimiento)
                <div class="space-y-2">
                    <div>
                        @if(in_array($movimiento->tipo_movimiento, ['entrada', 'devolucion']))
                            <p class="text-base text-gray-900 dark:text-white">+{{ $movimiento->cantidad }} unidades</p>
                        @elseif(in_array($movimiento->tipo_movimiento, ['salida', 'venta', 'merma', 'vencimiento']))
                            <p class="text-base text-gray-900 dark:text-white">-{{ $movimiento->cantidad }} unidades</p>
                        @else
                            <p class="text-base text-gray-900 dark:text-white">{{ $movimiento->cantidad }} unidades</p>
                        @endif
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $movimiento->created_at->format('d/m/Y h:i A') }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($movimiento->tipo_movimiento) }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Stock Anterior: {{ $movimiento->cantidad_anterior }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Stock Nuevo: {{ $movimiento->cantidad_nueva }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Usuario: {{ $movimiento->usuario->name ?? 'Sistema' }}</p>
                    </div>
                    
                    @if($movimiento->motivo)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Motivo:</p>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $movimiento->motivo }}</p>
                        </div>
                    @endif
                    
                    @if(!$loop->last)
                        <div class="border-t border-gray-200 dark:border-gray-700 mt-4"></div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
