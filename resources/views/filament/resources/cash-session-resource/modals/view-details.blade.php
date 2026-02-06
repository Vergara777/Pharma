<div class="space-y-6">
    {{-- Información General --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
            <div class="text-sm text-gray-500 dark:text-gray-400">Cajero</div>
            <div class="text-lg font-semibold">{{ $record->user->name }}</div>
        </div>
        
        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
            <div class="text-sm text-gray-500 dark:text-gray-400">Estado</div>
            <div class="text-lg font-semibold">
                @if($record->status === 'open')
                    <span class="text-success-600">🔓 Abierta</span>
                @else
                    <span class="text-gray-600">🔒 Cerrada</span>
                @endif
            </div>
        </div>
        
        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
            <div class="text-sm text-gray-500 dark:text-gray-400">Fecha de Apertura</div>
            <div class="text-lg font-semibold">{{ $record->opened_at->format('d/m/Y H:i') }}</div>
        </div>
        
        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
            <div class="text-sm text-gray-500 dark:text-gray-400">Fecha de Cierre</div>
            <div class="text-lg font-semibold">
                {{ $record->closed_at ? $record->closed_at->format('d/m/Y H:i') : 'Aún abierta' }}
            </div>
        </div>
    </div>

    {{-- Montos --}}
    <div class="border-t pt-4">
        <h3 class="text-lg font-semibold mb-4">Montos</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="text-sm text-blue-600 dark:text-blue-400">Monto Inicial</div>
                <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                    ${{ number_format($record->initial_amount, 0, ',', '.') }}
                </div>
            </div>
            
            @if($record->theoretical_amount)
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                <div class="text-sm text-green-600 dark:text-green-400">Monto Teórico</div>
                <div class="text-2xl font-bold text-green-700 dark:text-green-300">
                    ${{ number_format($record->theoretical_amount, 0, ',', '.') }}
                </div>
                <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                    Inicial + Ventas
                </div>
            </div>
            @endif
            
            @if($record->counted_amount)
            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border border-purple-200 dark:border-purple-800">
                <div class="text-sm text-purple-600 dark:text-purple-400">Monto Contado</div>
                <div class="text-2xl font-bold text-purple-700 dark:text-purple-300">
                    ${{ number_format($record->counted_amount, 0, ',', '.') }}
                </div>
            </div>
            @endif
            
            @if($record->difference !== null)
            <div class="bg-{{ $record->difference > 0 ? 'green' : ($record->difference < 0 ? 'red' : 'gray') }}-50 dark:bg-{{ $record->difference > 0 ? 'green' : ($record->difference < 0 ? 'red' : 'gray') }}-900/20 p-4 rounded-lg border border-{{ $record->difference > 0 ? 'green' : ($record->difference < 0 ? 'red' : 'gray') }}-200 dark:border-{{ $record->difference > 0 ? 'green' : ($record->difference < 0 ? 'red' : 'gray') }}-800">
                <div class="text-sm text-{{ $record->difference > 0 ? 'green' : ($record->difference < 0 ? 'red' : 'gray') }}-600 dark:text-{{ $record->difference > 0 ? 'green' : ($record->difference < 0 ? 'red' : 'gray') }}-400">
                    Diferencia
                </div>
                <div class="text-2xl font-bold text-{{ $record->difference > 0 ? 'green' : ($record->difference < 0 ? 'red' : 'gray') }}-700 dark:text-{{ $record->difference > 0 ? 'green' : ($record->difference < 0 ? 'red' : 'gray') }}-300">
                    ${{ number_format($record->difference, 0, ',', '.') }}
                </div>
                <div class="text-xs text-{{ $record->difference > 0 ? 'green' : ($record->difference < 0 ? 'red' : 'gray') }}-600 dark:text-{{ $record->difference > 0 ? 'green' : ($record->difference < 0 ? 'red' : 'gray') }}-400 mt-1">
                    @if($record->difference > 0)
                        Sobrante
                    @elseif($record->difference < 0)
                        Faltante
                    @else
                        Cuadra perfecto
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Ventas --}}
    <div class="border-t pt-4">
        <h3 class="text-lg font-semibold mb-4">Ventas Realizadas</h3>
        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total de Transacciones</div>
                    <div class="text-2xl font-bold">{{ $record->ventas()->count() }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Vendido</div>
                    <div class="text-2xl font-bold text-success-600">
                        ${{ number_format($record->ventas()->sum('grand_total'), 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Notas --}}
    @if($record->notes)
    <div class="border-t pt-4">
        <h3 class="text-lg font-semibold mb-2">Notas del Cierre</h3>
        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
            <p class="text-gray-700 dark:text-gray-300">{{ $record->notes }}</p>
        </div>
    </div>
    @endif
</div>
