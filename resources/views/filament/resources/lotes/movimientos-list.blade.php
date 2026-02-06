<div class="space-y-3">
    @forelse($getState() as $movimiento)
        @php
            $configs = [
                'entrada' => ['bg' => 'bg-green-50 dark:bg-green-950/30', 'border' => 'border-green-200 dark:border-green-800', 'badge' => 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300', 'label' => 'Entrada'],
                'salida' => ['bg' => 'bg-red-50 dark:bg-red-950/30', 'border' => 'border-red-200 dark:border-red-800', 'badge' => 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300', 'label' => 'Salida'],
                'venta' => ['bg' => 'bg-blue-50 dark:bg-blue-950/30', 'border' => 'border-blue-200 dark:border-blue-800', 'badge' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300', 'label' => 'Venta'],
                'devolucion' => ['bg' => 'bg-yellow-50 dark:bg-yellow-950/30', 'border' => 'border-yellow-200 dark:border-yellow-800', 'badge' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300', 'label' => 'Devolución'],
                'ajuste' => ['bg' => 'bg-purple-50 dark:bg-purple-950/30', 'border' => 'border-purple-200 dark:border-purple-800', 'badge' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300', 'label' => 'Ajuste'],
                'merma' => ['bg' => 'bg-orange-50 dark:bg-orange-950/30', 'border' => 'border-orange-200 dark:border-orange-800', 'badge' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-300', 'label' => 'Merma'],
                'vencimiento' => ['bg' => 'bg-red-50 dark:bg-red-950/30', 'border' => 'border-red-200 dark:border-red-800', 'badge' => 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300', 'label' => 'Vencimiento'],
            ];
            $config = $configs[$movimiento->tipo_movimiento] ?? ['bg' => 'bg-gray-50 dark:bg-gray-950/30', 'border' => 'border-gray-200 dark:border-gray-800', 'badge' => 'bg-gray-100 text-gray-700 dark:bg-gray-900/50 dark:text-gray-300', 'label' => ucfirst($movimiento->tipo_movimiento)];
        @endphp
        
        <div class="{{ $config['bg'] }} rounded-lg border {{ $config['border'] }} p-3">
            <div class="flex items-start justify-between mb-2">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold {{ $config['badge'] }}">
                        {{ $config['label'] }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $movimiento->created_at->format('d/m/Y h:i A') }}
                    </span>
                </div>
                <div class="text-right">
                    @if(in_array($movimiento->tipo_movimiento, ['entrada', 'devolucion']))
                        <span class="text-lg font-bold text-green-600 dark:text-green-400">+{{ $movimiento->cantidad }}</span>
                    @elseif(in_array($movimiento->tipo_movimiento, ['salida', 'venta', 'merma', 'vencimiento']))
                        <span class="text-lg font-bold text-red-600 dark:text-red-400">-{{ $movimiento->cantidad }}</span>
                    @else
                        <span class="text-lg font-bold text-gray-600 dark:text-gray-400">{{ $movimiento->cantidad }}</span>
                    @endif
                    <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">unidades</span>
                </div>
            </div>
            
            <div class="grid grid-cols-3 gap-3 text-sm">
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Stock Anterior:</span>
                    <span class="font-semibold text-gray-900 dark:text-gray-100 ml-1">{{ $movimiento->cantidad_anterior }}</span>
                </div>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Stock Nuevo:</span>
                    <span class="font-semibold text-gray-900 dark:text-gray-100 ml-1">{{ $movimiento->cantidad_nueva }}</span>
                </div>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Usuario:</span>
                    <span class="font-semibold text-gray-900 dark:text-gray-100 ml-1">{{ $movimiento->usuario->name ?? 'Sistema' }}</span>
                </div>
            </div>
            
            @if($movimiento->motivo)
                <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Motivo:</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $movimiento->motivo }}</p>
                </div>
            @endif
        </div>
    @empty
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            <p class="text-sm">No hay movimientos registrados</p>
        </div>
    @endforelse
</div>
