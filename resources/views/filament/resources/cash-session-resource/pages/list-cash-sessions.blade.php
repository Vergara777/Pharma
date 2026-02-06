<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Caja Actual --}}
        @if($openSession)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🔓</span>
                    <span>Caja Abierta</span>
                </div>
            </x-slot>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="text-sm text-blue-600 dark:text-blue-400">Monto Inicial</div>
                    <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                        ${{ number_format($openSession->initial_amount, 0, ',', '.') }}
                    </div>
                </div>
                
                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                    <div class="text-sm text-green-600 dark:text-green-400">Ventas del Día</div>
                    <div class="text-2xl font-bold text-green-700 dark:text-green-300">
                        ${{ number_format($openSession->ventas()->sum('grand_total'), 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                        {{ $openSession->ventas()->count() }} transacciones
                    </div>
                </div>
                
                <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border border-purple-200 dark:border-purple-800">
                    <div class="text-sm text-purple-600 dark:text-purple-400">Total Esperado</div>
                    <div class="text-2xl font-bold text-purple-700 dark:text-purple-300">
                        ${{ number_format($openSession->calculateTheoreticalAmount(), 0, ',', '.') }}
                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Apertura</div>
                    <div class="text-lg font-bold text-gray-700 dark:text-gray-300">
                        {{ $openSession->opened_at->format('d/m/Y') }}
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">
                        {{ $openSession->opened_at->format('H:i') }}
                    </div>
                </div>
            </div>
        </x-filament::section>
        @endif

        {{-- Historial de Cajas --}}
        <x-filament::section>
            <x-slot name="heading">
                Historial de Cajas
            </x-slot>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($this->getSessions() as $session)
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition-shadow">
                    {{-- Header --}}
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Caja #{{ $session->id }}</div>
                            <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $session->user->name }}</div>
                        </div>
                        <div>
                            @if($session->status === 'open')
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-success-100 dark:bg-success-900/20 text-success-700 dark:text-success-400 rounded-full text-xs font-medium">
                                    🔓 Abierta
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs font-medium">
                                    🔒 Cerrada
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Fechas --}}
                    <div class="space-y-1 mb-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Apertura:</span>
                            <span class="font-medium">{{ $session->opened_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($session->closed_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Cierre:</span>
                            <span class="font-medium">{{ $session->closed_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- Montos --}}
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Inicial:</span>
                            <span class="font-bold text-blue-600 dark:text-blue-400">
                                ${{ number_format($session->initial_amount, 0, ',', '.') }}
                            </span>
                        </div>
                        
                        @if($session->theoretical_amount)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Teórico:</span>
                            <span class="font-bold text-green-600 dark:text-green-400">
                                ${{ number_format($session->theoretical_amount, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif
                        
                        @if($session->counted_amount)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Contado:</span>
                            <span class="font-bold text-purple-600 dark:text-purple-400">
                                ${{ number_format($session->counted_amount, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif
                        
                        @if($session->difference !== null)
                        <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-700">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Diferencia:</span>
                            <span class="font-bold text-lg {{ $session->difference > 0 ? 'text-success-600' : ($session->difference < 0 ? 'text-danger-600' : 'text-gray-600') }}">
                                ${{ number_format($session->difference, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif
                    </div>

                    {{-- Acciones --}}
                    <div class="flex gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <x-filament::button
                            wire:click="viewDetails({{ $session->id }})"
                            color="info"
                            size="sm"
                            class="flex-1"
                        >
                            <x-slot name="icon">
                                heroicon-o-eye
                            </x-slot>
                            Ver Detalles
                        </x-filament::button>
                        
                        @if($session->status === 'open')
                        <x-filament::button
                            tag="a"
                            href="{{ route('filament.admin.resources.cash-sessions.close', $session) }}"
                            color="danger"
                            size="sm"
                        >
                            <x-slot name="icon">
                                heroicon-o-lock-closed
                            </x-slot>
                            Cerrar
                        </x-filament::button>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12 text-gray-500 dark:text-gray-400">
                    No hay cajas registradas
                </div>
                @endforelse
            </div>

            {{-- Paginación --}}
            @if($this->getSessions()->hasPages())
            <div class="mt-6">
                {{ $this->getSessions()->links() }}
            </div>
            @endif
        </x-filament::section>
    </div>

    {{-- Modal de Detalles --}}
    <x-filament::modal id="view-details" width="4xl">
        @if($selectedSession)
        <x-slot name="heading">
            Detalles de Caja #{{ $selectedSession->id }}
        </x-slot>

        @include('filament.resources.cash-session-resource.modals.view-details', ['record' => $selectedSession])
        @endif
    </x-filament::modal>
</x-filament-panels::page>
