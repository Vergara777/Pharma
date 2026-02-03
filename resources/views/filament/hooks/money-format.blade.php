<script>
document.addEventListener('livewire:initialized', () => {
    // Función para formatear números con puntos
    window.formatCurrency = function(value) {
        if (!value && value !== 0) return '';
        // Convertir a string y remover todo excepto números
        let number = value.toString().replace(/\D/g, '');
        if (!number) return '';
        // Agregar puntos como separadores de miles
        return number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    };
    
    // Función para limpiar formato
    window.unformatCurrency = function(value) {
        if (!value) return '';
        return value.toString().replace(/\./g, '');
    };
    
    // Aplicar formato a inputs monetarios
    function setupMoneyInputs() {
        // Buscar todos los inputs dentro de wrappers con prefijo $
        document.querySelectorAll('.fi-input-wrapper').forEach(wrapper => {
            const prefix = wrapper.querySelector('.fi-input-wrapper-prefix');
            if (!prefix || !prefix.textContent.includes('$')) return;
            
            const input = wrapper.querySelector('input[type="text"], input[type="number"]');
            if (!input || input.hasAttribute('data-money-formatted')) return;
            
            input.setAttribute('data-money-formatted', 'true');
            input.setAttribute('inputmode', 'numeric');
            
            // Formatear valor inicial
            if (input.value) {
                const cleaned = window.unformatCurrency(input.value);
                input.value = window.formatCurrency(cleaned);
            }
            
            // Evento input para formatear mientras escribe
            input.addEventListener('input', function(e) {
                const cursorPos = this.selectionStart;
                const oldValue = this.value;
                const oldLength = oldValue.length;
                
                // Limpiar y reformatear
                const cleaned = window.unformatCurrency(this.value);
                const formatted = window.formatCurrency(cleaned);
                
                if (formatted !== oldValue) {
                    this.value = formatted;
                    
                    // Ajustar cursor
                    const newLength = formatted.length;
                    const diff = newLength - oldLength;
                    const newPos = cursorPos + diff;
                    this.setSelectionRange(newPos, newPos);
                }
            });
            
            // Antes de enviar, limpiar el formato
            input.addEventListener('blur', function() {
                if (this.value) {
                    const cleaned = window.unformatCurrency(this.value);
                    const formatted = window.formatCurrency(cleaned);
                    this.value = formatted;
                }
            });
        });
    }
    
    // Ejecutar al cargar
    setupMoneyInputs();
    
    // Ejecutar después de actualizaciones de Livewire
    Livewire.hook('morph.updated', () => {
        setTimeout(setupMoneyInputs, 50);
    });
    
    Livewire.hook('commit', ({ component, commit, respond }) => {
        setTimeout(setupMoneyInputs, 50);
    });
});
</script>

