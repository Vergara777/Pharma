<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div 
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}'),
            buffer: '',
            lastKeyTime: Date.now(),
            
            handleKeyPress(e) {
                // Solo procesar si el input está enfocado
                if (document.activeElement !== $refs.input) return;
                
                const currentTime = Date.now();
                
                // Si pasan más de 100ms entre teclas, reiniciar buffer
                if (currentTime - this.lastKeyTime > 100) {
                    this.buffer = '';
                }
                
                this.lastKeyTime = currentTime;
                
                // Acumular caracteres (el scanner escribe muy rápido)
                if (e.key !== 'Enter' && e.key.length === 1) {
                    this.buffer += e.key;
                }
                
                // Si es Enter y hay datos en el buffer, es un escaneo
                if (e.key === 'Enter' && this.buffer.length > 3) {
                    e.preventDefault();
                    this.state = this.buffer;
                    this.buffer = '';
                    
                    // Notificación visual
                    new FilamentNotification()
                        .title('Código escaneado')
                        .body('Código de barras: ' + this.state)
                        .success()
                        .send();
                }
            }
        }" 
        class="relative"
    >
        <div class="flex gap-2 items-center">
            <div class="flex-1">
                <input
                    x-ref="input"
                    type="text"
                    x-model="state"
                    @keypress="handleKeyPress($event)"
                    placeholder="{{ $getPlaceholder() }}"
                    class="fi-input block w-full border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    {{ $attributes }}
                    autocomplete="off"
                />
            </div>
            <div class="flex items-center text-gray-400 dark:text-gray-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
            </div>
        </div>
    </div>
</x-dynamic-component>
