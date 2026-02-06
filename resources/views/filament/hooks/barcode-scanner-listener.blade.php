<script>
(function() {
    let buffer = '';
    let lastKeyTime = Date.now();
    let isScanning = false;
    
    console.log('Barcode scanner initialized');
    
    document.addEventListener('keypress', (e) => {
        // Solo ignorar si estamos en un input de formulario (no el buscador de tabla)
        const isFormInput = e.target.tagName === 'INPUT' && 
                           e.target.type !== 'search' && 
                           !e.target.classList.contains('fi-input-search') &&
                           !e.target.closest('.fi-ta-search');
        
        const isTextarea = e.target.tagName === 'TEXTAREA';
        
        if (isFormInput || isTextarea) {
            console.log('Ignoring keypress in form input/textarea');
            return;
        }
        
        console.log('Key pressed:', e.key, 'Target:', e.target.tagName);
        
        const currentTime = Date.now();
        
        // Si pasan más de 100ms entre teclas, reiniciar buffer
        if (currentTime - lastKeyTime > 100) {
            if (buffer.length > 0) {
                console.log('Buffer timeout, resetting. Old buffer:', buffer);
            }
            buffer = '';
            isScanning = false;
        }
        
        lastKeyTime = currentTime;
        isScanning = true;
        
        // Si es Enter y hay datos en el buffer, es un escaneo completo
        if (e.key === 'Enter' && buffer.length > 3) {
            e.preventDefault();
            console.log('Barcode detected:', buffer);
            handleBarcodeScanned(buffer);
            buffer = '';
            isScanning = false;
            return;
        }
        
        // Acumular caracteres
        if (e.key !== 'Enter') {
            buffer += e.key;
            console.log('Buffer:', buffer);
        }
    });
    
    async function handleBarcodeScanned(barcode) {
        console.log('=== BARCODE SCANNED ===');
        console.log('Código escaneado:', barcode);
        
        try {
            const response = await fetch('/admin/api/products/search-by-sku/' + barcode);
            console.log('Response status:', response.status);
            const data = await response.json();
            console.log('Response data:', data);
            
            if (data.success && data.product) {
                console.log('Producto encontrado:', data.product);
                showAddToCartConfirmation(data.product);
            } else {
                console.log('Producto NO encontrado');
                showNotification('Producto no encontrado', 'No se encontró ningún producto con el código: ' + barcode, 'warning');
            }
        } catch (error) {
            console.error('Error al buscar producto:', error);
            showNotification('Error', 'Error al buscar producto: ' + error.message, 'danger');
        }
    }
    
    function showAddToCartConfirmation(product) {
        console.log('=== MOSTRANDO MODAL ===');
        console.log('Product:', product);
        
        // Verificar stock
        if (product.stock <= 0) {
            showNotification('Sin Stock', product.name + ' no tiene stock disponible', 'danger');
            return;
        }
        
        const isDark = document.documentElement.classList.contains('dark');
        
        // Agregar estilos de animación
        if (!document.getElementById('barcode-modal-styles')) {
            const styles = document.createElement('style');
            styles.id = 'barcode-modal-styles';
            styles.textContent = `
                @keyframes barcodeModalFadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                @keyframes barcodeModalSlideIn {
                    from { transform: scale(0.95); opacity: 0; }
                    to { transform: scale(1); opacity: 1; }
                }
                @keyframes barcodeModalFadeOut {
                    from { opacity: 1; }
                    to { opacity: 0; }
                }
            `;
            document.head.appendChild(styles);
        }
        
        // Crear overlay
        const overlay = document.createElement('div');
        overlay.id = 'barcode-modal-overlay';
        overlay.style.cssText = `
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            z-index: 999999 !important;
            background-color: rgba(0, 0, 0, 0.4) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 1rem !important;
            animation: barcodeModalFadeIn 0.15s ease-out !important;
        `;
        
        // Crear modal - diseño limpio y simple
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: relative !important;
            width: 100% !important;
            max-width: 600px !important;
            background-color: ${isDark ? 'rgb(31, 41, 55)' : 'white'} !important;
            border-radius: 1rem !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
            animation: barcodeModalSlideIn 0.2s ease-out !important;
            padding: 2rem !important;
        `;
        
        modal.innerHTML = `
            <button type="button" data-action="close" style="position: absolute; top: 1rem; right: 1rem; width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; background: none; border: none; cursor: pointer; color: ${isDark ? 'rgb(156, 163, 175)' : 'rgb(107, 114, 128)'}; transition: color 0.2s;">
                <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            <h2 style="font-size: 1.5rem; font-weight: 700; color: ${isDark ? 'white' : 'rgb(17, 24, 39)'}; margin: 0 0 0.5rem 0;">Agregar al Carrito</h2>
            <p style="font-size: 1rem; color: ${isDark ? 'rgb(156, 163, 175)' : 'rgb(107, 114, 128)'}; margin: 0 0 1.5rem 0;">¿Cuántas unidades de ${product.name} deseas agregar?</p>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: ${isDark ? 'white' : 'rgb(17, 24, 39)'}; margin-bottom: 0.5rem;">
                    Cantidad<span style="color: rgb(239, 68, 68);">*</span>
                </label>
                <input 
                    type="number" 
                    id="barcode-quantity-input" 
                    min="1" 
                    max="${product.stock}" 
                    value="1" 
                    style="width: 100%; padding: 0.75rem 1rem; font-size: 1rem; border: 2px solid ${isDark ? 'rgb(55, 65, 81)' : 'rgb(229, 231, 235)'}; border-radius: 0.5rem; background-color: ${isDark ? 'rgb(17, 24, 39)' : 'white'}; color: ${isDark ? 'white' : 'rgb(17, 24, 39)'}; outline: none; transition: border-color 0.2s;"
                    onfocus="this.style.borderColor='rgb(34, 197, 94)'"
                    onblur="this.style.borderColor='${isDark ? 'rgb(55, 65, 81)' : 'rgb(229, 231, 235)'}'"
                />
            </div>
            
            <div style="display: flex; gap: 0.75rem; margin-top: 2rem;">
                <button type="button" data-action="cancel" style="flex: 1; padding: 0.75rem 1.5rem; background-color: ${isDark ? 'rgb(55, 65, 81)' : 'white'}; color: ${isDark ? 'rgb(209, 213, 219)' : 'rgb(55, 65, 81)'}; border: 1px solid ${isDark ? 'rgb(75, 85, 99)' : 'rgb(209, 213, 219)'}; border-radius: 0.5rem; font-weight: 600; font-size: 1rem; cursor: pointer; transition: all 0.2s;">
                    Cancelar
                </button>
                <button type="button" data-action="accept" style="flex: 1; padding: 0.75rem 1.5rem; background-color: rgb(34, 197, 94); color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 1rem; cursor: pointer; transition: all 0.2s;">
                    Agregar al Carrito
                </button>
            </div>
        `;
        
        overlay.appendChild(modal);
        document.body.appendChild(overlay);
        
        console.log('Modal agregado al DOM');
        
        // Focus en el input
        setTimeout(() => {
            const input = document.getElementById('barcode-quantity-input');
            if (input) {
                input.focus();
                input.select();
            }
        }, 100);
        
        // Event listeners
        const acceptBtn = modal.querySelector('[data-action="accept"]');
        const cancelBtn = modal.querySelector('[data-action="cancel"]');
        const closeBtn = modal.querySelector('[data-action="close"]');
        const quantityInput = document.getElementById('barcode-quantity-input');
        
        const closeModal = () => {
            overlay.style.animation = 'barcodeModalFadeOut 0.15s ease-out';
            setTimeout(() => overlay.remove(), 150);
        };
        
        acceptBtn.addEventListener('click', () => {
            const quantity = parseInt(quantityInput.value) || 1;
            if (quantity > 0 && quantity <= product.stock) {
                addToCart(product, quantity);
                closeModal();
            } else {
                showNotification('Cantidad inválida', 'Por favor ingresa una cantidad válida', 'warning');
            }
        });
        
        cancelBtn.addEventListener('click', closeModal);
        closeBtn.addEventListener('click', closeModal);
        
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeModal();
        });
        
        // Enter para aceptar
        quantityInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                acceptBtn.click();
            }
        });
        
        // Hover effects
        closeBtn.addEventListener('mouseenter', () => {
            closeBtn.style.color = isDark ? 'white' : 'rgb(17, 24, 39)';
        });
        closeBtn.addEventListener('mouseleave', () => {
            closeBtn.style.color = isDark ? 'rgb(156, 163, 175)' : 'rgb(107, 114, 128)';
        });
        
        cancelBtn.addEventListener('mouseenter', () => {
            cancelBtn.style.backgroundColor = isDark ? 'rgb(75, 85, 99)' : 'rgb(249, 250, 251)';
        });
        cancelBtn.addEventListener('mouseleave', () => {
            cancelBtn.style.backgroundColor = isDark ? 'rgb(55, 65, 81)' : 'white';
        });
        
        acceptBtn.addEventListener('mouseenter', () => {
            acceptBtn.style.backgroundColor = 'rgb(22, 163, 74)';
        });
        acceptBtn.addEventListener('mouseleave', () => {
            acceptBtn.style.backgroundColor = 'rgb(34, 197, 94)';
        });
    }
    
    async function addToCart(product, quantity) {
        try {
            const response = await fetch('/admin/api/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    product_id: product.id,
                    quantity: quantity
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Actualizar el carrito
                window.dispatchEvent(new CustomEvent('cartUpdated'));
                if (typeof Livewire !== 'undefined') {
                    Livewire.dispatch('cartUpdated');
                }
                
                // Mostrar notificación bonita
                showNotification(
                    'Producto agregado', 
                    product.name + ' x' + quantity + ' agregado al carrito', 
                    'success'
                );
            } else {
                showNotification('Error', data.message || 'No se pudo agregar el producto', 'danger');
            }
        } catch (error) {
            console.error('Error al agregar al carrito:', error);
            showNotification('Error', 'Error al agregar al carrito', 'danger');
        }
    }
    
    function showNotification(title, message, type = 'success') {
        // Usar el sistema de notificaciones de Filament si está disponible
        if (typeof Filament !== 'undefined' && Filament.notifications) {
            Filament.notifications.send({
                title: title,
                body: message,
                status: type,
                duration: 3000
            });
            return;
        }
        
        // Fallback: crear notificación personalizada
        const isDark = document.documentElement.classList.contains('dark');
        const colors = {
            success: { bg: 'rgb(34, 197, 94)', border: 'rgb(22, 163, 74)', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
            danger: { bg: 'rgb(239, 68, 68)', border: 'rgb(220, 38, 38)', icon: 'M6 18L18 6M6 6l12 12' },
            warning: { bg: 'rgb(245, 158, 11)', border: 'rgb(217, 119, 6)', icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' }
        };
        
        const color = colors[type] || colors.success;
        
        // Agregar estilos si no existen
        if (!document.getElementById('notification-styles')) {
            const styles = document.createElement('style');
            styles.id = 'notification-styles';
            styles.textContent = `
                @keyframes notificationSlideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes notificationSlideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
            document.head.appendChild(styles);
        }
        
        // Crear notificación
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed !important;
            top: 1rem !important;
            right: 1rem !important;
            z-index: 999999 !important;
            min-width: 300px !important;
            max-width: 400px !important;
            background-color: ${isDark ? 'rgb(31, 41, 55)' : 'white'} !important;
            border-left: 4px solid ${color.bg} !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            padding: 1rem !important;
            animation: notificationSlideIn 0.3s ease-out !important;
            display: flex !important;
            gap: 0.75rem !important;
        `;
        
        notification.innerHTML = `
            <div style="flex-shrink: 0; width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; background-color: ${color.bg}; border-radius: 50%;">
                <svg style="width: 1.25rem; height: 1.25rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${color.icon}" />
                </svg>
            </div>
            <div style="flex: 1;">
                <h4 style="font-size: 0.875rem; font-weight: 600; color: ${isDark ? 'white' : 'rgb(17, 24, 39)'}; margin: 0 0 0.25rem 0;">${title}</h4>
                <p style="font-size: 0.875rem; color: ${isDark ? 'rgb(156, 163, 175)' : 'rgb(107, 114, 128)'}; margin: 0;">${message}</p>
            </div>
            <button type="button" style="flex-shrink: 0; width: 1.5rem; height: 1.5rem; display: flex; align-items: center; justify-content: center; background: none; border: none; cursor: pointer; color: ${isDark ? 'rgb(156, 163, 175)' : 'rgb(107, 114, 128)'}; transition: color 0.2s;">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        `;
        
        document.body.appendChild(notification);
        
        // Cerrar al hacer clic en X
        const closeBtn = notification.querySelector('button');
        closeBtn.addEventListener('click', () => {
            notification.style.animation = 'notificationSlideOut 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        });
        
        // Auto cerrar después de 3 segundos
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.animation = 'notificationSlideOut 0.3s ease-out';
                setTimeout(() => notification.remove(), 300);
            }
        }, 3000);
    }
})();
</script>
