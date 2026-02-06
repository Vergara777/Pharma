<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Columna 1: Información del Lote --}}
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
            📦 Información del Lote
        </h3>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Código de Lote</label>
                <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $lote->codigo_lote }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Estado</label>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $lote->estado === 'activo' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                    {{ $lote->estado === 'agotado' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                    {{ $lote->estado === 'bloqueado' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                    {{ $lote->estado === 'vencido' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                    {{ ucfirst($lote->estado) }}
                </span>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Producto</label>
                <p class="text-base text-gray-900 dark:text-white">{{ $lote->product->name }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Proveedor</label>
                <p class="text-base text-gray-900 dark:text-white">{{ $lote->proveedor->name ?? 'N/A' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-3 border-t border-gray-200 dark:border-gray-700">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Cantidad Inicial</label>
                    <p class="text-base text-gray-900 dark:text-white">{{ number_format($lote->cantidad_inicial, 0, ',', '.') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Cantidad Actual</label>
                    <p class="text-base font-semibold {{ $lote->cantidad_actual <= 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                        {{ number_format($lote->cantidad_actual, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Costo Unitario</label>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">${{ number_format($lote->costo_unitario, 0, ',', '.') }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-3 border-t border-gray-200 dark:border-gray-700">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Fecha de Vencimiento</label>
                    <p class="text-base text-gray-900 dark:text-white">{{ $lote->fecha_vencimiento->format('d/m/Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Fecha de Ingreso</label>
                    <p class="text-base text-gray-900 dark:text-white">{{ $lote->fecha_ingreso->format('d/m/Y') }}</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Registrado por</label>
                <p class="text-base text-gray-900 dark:text-white">{{ $lote->usuarioRegistro->name ?? 'N/A' }}</p>
            </div>

            @if($lote->notas)
            <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Notas</label>
                <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg">{{ $lote->notas }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Columna 2: Historial de Movimientos --}}
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
            📋 Historial de Movimientos
        </h3>
        
        @if($movimientos->isEmpty())
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="mt-2">No hay movimientos registrados</p>
            </div>
        @else
            <div class="space-y-3 max-h-[600px] overflow-y-auto pr-2">
                @foreach($movimientos as $movimiento)
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900/50 hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors">
                        <div class="flex items-start gap-3 mb-3">
                            <span class="text-2xl flex-shrink-0">
                                @switch($movimiento->tipo_movimiento)
                                    @case('entrada') 📥 @break
                                    @case('salida') 📤 @break
                                    @case('ajuste') ⚙️ @break
                                    @case('merma') 📉 @break
                                    @case('vencimiento') ⏰ @break
                                    @case('devolucion') ↩️ @break
                                    @case('venta') 🛒 @break
                                    @default 📦
                                @endswitch
                            </span>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-white">
                                    {{ ucfirst($movimiento->tipo_movimiento) }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $movimiento->created_at->format('d/m/Y h:i A') }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-3 mb-3">
                            <div class="text-center p-2 bg-white dark:bg-gray-800 rounded">
                                <span class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Cantidad</span>
                                <p class="font-bold text-gray-900 dark:text-white">{{ number_format($movimiento->cantidad, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-center p-2 bg-white dark:bg-gray-800 rounded">
                                <span class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Anterior</span>
                                <p class="text-gray-700 dark:text-gray-300">{{ number_format($movimiento->cantidad_anterior, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-center p-2 bg-white dark:bg-gray-800 rounded">
                                <span class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Nueva</span>
                                <p class="text-gray-700 dark:text-gray-300">{{ number_format($movimiento->cantidad_nueva, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        @if($movimiento->motivo)
                        <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                            <span class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Motivo</span>
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $movimiento->motivo }}</p>
                        </div>
                        @endif

                        @if($movimiento->usuario)
                        <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Por: </span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $movimiento->usuario->name }}</span>
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
