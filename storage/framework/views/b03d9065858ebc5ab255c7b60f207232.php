<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para formatear números con puntos de miles
    function formatCurrency(value) {
        if (!value) return '';
        // Remover todo excepto números
        let number = value.toString().replace(/\D/g, '');
        if (!number) return '';
        // Formatear con puntos de miles
        return number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // Función para limpiar formato (remover puntos)
    function cleanCurrency(value) {
        return value.replace(/\./g, '');
    }

    // Función para aplicar formato a un input
    function applyFormat(input) {
        if (!input || input.type !== 'text') return;
        
        let cursorPosition = input.selectionStart;
        let oldValue = input.value;
        let oldLength = oldValue.length;
        
        // Formatear el valor
        let formatted = formatCurrency(oldValue);
        
        if (formatted !== oldValue) {
            input.value = formatted;
            
            // Ajustar posición del cursor
            let newLength = formatted.length;
            let diff = newLength - oldLength;
            cursorPosition = Math.max(0, cursorPosition + diff);
            
            try {
                input.setSelectionRange(cursorPosition, cursorPosition);
            } catch (e) {
                // Ignorar errores de selección
            }
        }
    }

    // Función para procesar inputs
    function processInput(input) {
        // Evitar agregar el listener múltiples veces
        if (input.dataset.currencyFormatted) return;
        
        input.dataset.currencyFormatted = 'true';
        
        // Limpiar el "0" cuando el usuario hace click/focus
        input.addEventListener('focus', function(e) {
            if (this.value === '0' || this.value === '') {
                this.value = '';
            }
        });
        
        // Si el usuario deja el campo vacío, poner "0"
        input.addEventListener('blur', function(e) {
            if (!this.value || this.value.trim() === '') {
                this.value = '0';
            } else {
                applyFormat(this);
            }
        });
        
        // Formatear al escribir
        input.addEventListener('input', function(e) {
            // Si el usuario borra todo, dejar vacío (no poner 0 automáticamente)
            if (!this.value || this.value.trim() === '') {
                this.value = '';
                return;
            }
            applyFormat(this);
        });
        
        // Limpiar formato antes de enviar el formulario
        const form = input.closest('form');
        if (form && !form.dataset.currencyFormListener) {
            form.dataset.currencyFormListener = 'true';
            form.addEventListener('submit', function(e) {
                const priceInputs = form.querySelectorAll('input[data-currency-formatted="true"]');
                priceInputs.forEach(inp => {
                    inp.value = cleanCurrency(inp.value);
                });
            });
        }
        
        // Formatear al cargar si ya tiene valor y no es "0"
        if (input.value && input.value !== '0') {
            setTimeout(() => applyFormat(input), 100);
        }
    }

    // Función para buscar y procesar inputs de precio
    function findAndProcessInputs() {
        // Buscar inputs por diferentes selectores
        const selectors = [
            'input[id*="cost"]',
            'input[id*="price"]',
            'input[id*="price_unit"]',
            'input[id*="price_package"]',
            'input[wire\\:model*="cost"]',
            'input[wire\\:model*="price"]'
        ];
        
        selectors.forEach(selector => {
            const inputs = document.querySelectorAll(selector);
            inputs.forEach(input => {
                if (input.type === 'text' || input.type === 'number') {
                    // Cambiar tipo a text si es number para permitir puntos
                    if (input.type === 'number') {
                        input.type = 'text';
                        input.inputMode = 'numeric';
                    }
                    processInput(input);
                }
            });
        });
    }

    // Observar cambios en el DOM para inputs dinámicos
    const observer = new MutationObserver(function(mutations) {
        findAndProcessInputs();
    });

    // Iniciar observador
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Aplicar formato inicial
    findAndProcessInputs();
    
    // Reintentar después de que Livewire/Alpine se inicialicen
    setTimeout(findAndProcessInputs, 500);
    setTimeout(findAndProcessInputs, 1000);
    setTimeout(findAndProcessInputs, 2000);
    
    // Escuchar eventos de Livewire
    document.addEventListener('livewire:load', findAndProcessInputs);
    document.addEventListener('livewire:update', findAndProcessInputs);
    
    // Escuchar eventos de Alpine
    document.addEventListener('alpine:init', findAndProcessInputs);
});
</script>
<?php /**PATH C:\Pharma\resources\views/filament/hooks/currency-format.blade.php ENDPATH**/ ?>