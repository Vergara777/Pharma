
        <div class="flex items-center gap-3 px-6 py-4 mb-4 border-b border-gray-100 dark:border-white/5 overflow-hidden">
            <x-filament-panels::avatar.user size="lg" :user="auth()->user()" class="flex-shrink-0" />
            
            <div x-show="$store.sidebar.isOpen" class="flex flex-col truncate transition-all duration-300">
                <span class="font-bold text-sm text-gray-900 dark:text-white truncate">
                    {{ auth()->user()->name }}
                </span>
                
                {{-- Lógica dinámica para el cargo --}}
                <span class="text-xs text-gray-500 truncate">
                    @if(auth()->user()->id === 1) {{-- O usa la lógica de roles que tengas --}}
                        Administrador
                    @else
                        Farmacéutico
                    @endif
                </span>
            </div>
        </div>
    