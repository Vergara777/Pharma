<script>
document.addEventListener('DOMContentLoaded', function() {
    function stripNonDigits(value) {
        return (value ?? '').toString().replace(/\D/g, '');
    }

    function formatCurrency(value) {
        const digits = stripNonDigits(value);

        if (!digits) return '';

        return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function cleanCurrency(value) {
        return stripNonDigits(value);
    }

    function normalizeAsNumericTextInput(input) {
        if (input.type === 'number') {
            input.type = 'text';
        }

        input.inputMode = 'numeric';
    }

    function applyCurrencyFormat(input) {
        let cursorPosition = input.selectionStart ?? input.value.length;
        const oldValue = input.value;
        const oldLength = oldValue.length;
        const formatted = formatCurrency(oldValue);
        if (formatted !== oldValue) {
            input.value = formatted;

            const newLength = formatted.length;
            const diff = newLength - oldLength;
            cursorPosition = Math.max(0, cursorPosition + diff);
            try {
                input.setSelectionRange(cursorPosition, cursorPosition);
            } catch (e) {
                // Ignorar errores de selección de cursor
            }
        }
    }

    function processInput(input) {
        if (!input || input.dataset.numericBehaviorInitialized === 'true') return;

        const isCurrencyInput = input.dataset.currencyInput === 'true';
        const hasZeroDefault = input.dataset.zeroDefault === 'true';

        if (!isCurrencyInput && !hasZeroDefault) return;

        input.dataset.numericBehaviorInitialized = 'true';
        normalizeAsNumericTextInput(input);

        input.addEventListener('focus', function() {
            if (this.value === '0') {
                this.value = '';
            }
        });

        input.addEventListener('blur', function() {
            if (!this.value || this.value.trim() === '') {
                if (hasZeroDefault) {
                    this.value = '0';
                }

                return;
            }

            if (isCurrencyInput) {
                applyCurrencyFormat(this);
            } else {
                this.value = stripNonDigits(this.value);
            }
        });

        input.addEventListener('input', function() {
            if (!this.value || this.value.trim() === '') {
                return;
            }

            if (isCurrencyInput) {
                applyCurrencyFormat(this);
            } else {
                this.value = stripNonDigits(this.value);
            }
        });
        const form = input.closest('form');
        if (form && !form.dataset.currencyFormListener) {
            form.dataset.currencyFormListener = 'true';
            form.addEventListener('submit', function() {
                const currencyInputs = form.querySelectorAll('input[data-currency-input="true"]');
                currencyInputs.forEach(currencyInput => {
                    currencyInput.value = cleanCurrency(currencyInput.value);
                });
            });
        }

        if ((!input.value || input.value.trim() === '') && hasZeroDefault) {
            input.value = '0';
        }

        if (isCurrencyInput && input.value && input.value !== '0') {
            setTimeout(() => applyCurrencyFormat(input), 50);
        }
    }

    function findAndProcessInputs() {
        const inputs = document.querySelectorAll('input[data-zero-default="true"], input[data-currency-input="true"]');
        inputs.forEach(processInput);
    }

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
    setTimeout(findAndProcessInputs, 300);
    setTimeout(findAndProcessInputs, 800);
    setTimeout(findAndProcessInputs, 1500);
    document.addEventListener('livewire:load', findAndProcessInputs);
    document.addEventListener('livewire:update', findAndProcessInputs);
    document.addEventListener('alpine:init', findAndProcessInputs);
});
</script>
