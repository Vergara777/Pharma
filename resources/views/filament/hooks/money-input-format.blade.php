<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para formatear números con puntos de miles
    function formatMoney(input) {
        let value = input.value.replace(/\D/g, '');
        if (value) {
            value = parseInt(value, 10).toLocaleString('es-CO');
            input.value = value;
        }
    }

    // Aplicar a todos los inputs con data-money-format
    document.addEventListener('input', function(e) {
        if (e.target.matches('[data-money-format]')) {
            formatMoney(e.target);
        }
    });

    // También aplicar cuando se carga el modal
    document.addEventListener('livewire:load', function() {
        setTimeout(() => {
            document.querySelectorAll('[data-money-format]').forEach(input => {
                if (input.value) {
                    formatMoney(input);
                }
            });
        }, 100);
    });
});
</script>
