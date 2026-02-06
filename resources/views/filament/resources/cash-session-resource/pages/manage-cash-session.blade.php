<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Información de la Sesión en Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span>Monto Inicial</span>
                    </div>
                </x-slot>
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                    ${{ number_format($record->initial_amount, 0, ',', '.') }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Apertura: {{ $record->opened_at->format('d/m/Y h:i A') }}
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <span>Ventas del Día</span>
                    </div>
                </x-slot>
                <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                    ${{ number_format($record->theoretical_amount - $record->initial_amount, 0, ',', '.') }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    {{ $record->ventas()->count() }} transacciones
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span>Monto Teórico</span>
                    </div>
                </x-slot>
                <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                    ${{ number_format($record->theoretical_amount, 0, ',', '.') }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Inicial + Ventas
                </div>
            </x-filament::section>
        </div>

        {{-- Formulario de Cierre --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <span>Cierre de Caja</span>
                </div>
            </x-slot>

            <x-slot name="description">
                Ingresa el monto contado físicamente y las observaciones del cierre
            </x-slot>
            
            <form wire:submit="closeSession" class="space-y-6">
                {{ $this->form }}

                @if($record->difference !== null)
                    <x-filament::section
                        :color="$record->difference > 0 ? 'success' : ($record->difference < 0 ? 'danger' : 'gray')"
                    >
                        <div class="flex items-center gap-4">
                            <div class="flex items-center justify-center w-12 h-12 rounded-full {{ $record->difference > 0 ? 'bg-success-100 dark:bg-success-900/30' : ($record->difference < 0 ? 'bg-danger-100 dark:bg-danger-900/30' : 'bg-gray-100 dark:bg-gray-700') }}">
                                @if($record->difference > 0)
                                    <svg class="w-6 h-6 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                @elseif($record->difference < 0)
                                    <svg class="w-6 h-6 text-danger-600 dark:text-danger-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                @else
                                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="text-2xl font-bold {{ $record->difference > 0 ? 'text-success-600 dark:text-success-400' : ($record->difference < 0 ? 'text-danger-600 dark:text-danger-400' : 'text-gray-600 dark:text-gray-400') }}">
                                    Diferencia: ${{ number_format($record->difference, 0, ',', '.') }}
                                </div>
                                <div class="text-sm {{ $record->difference > 0 ? 'text-success-600 dark:text-success-400' : ($record->difference < 0 ? 'text-danger-600 dark:text-danger-400' : 'text-gray-600 dark:text-gray-400') }} mt-1">
                                    @if($record->difference > 0)
                                        Hay más dinero del esperado (sobrante)
                                    @elseif($record->difference < 0)
                                        Hay menos dinero del esperado (faltante)
                                    @else
                                        La caja cuadra perfectamente
                                    @endif
                                </div>
                            </div>
                        </div>
                    </x-filament::section>
                @endif

                <div class="flex justify-end gap-3">
                    <x-filament::button
                        color="gray"
                        tag="a"
                        href="{{ route('filament.admin.resources.cash-sessions.index') }}"
                    >
                        Cancelar
                    </x-filament::button>
                    
                    <x-filament::button
                        type="submit"
                        color="danger"
                    >
                        Cerrar Caja
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>
    </div>
</x-filament-panels::page>
