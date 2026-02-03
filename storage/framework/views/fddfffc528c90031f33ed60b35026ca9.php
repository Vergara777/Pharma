<div x-data="{
    buffer: '',
    lastKeyTime: Date.now(),
    isScanning: false,
    
    init() {
        this.setupBarcodeScanner();
    },
    
    setupBarcodeScanner() {
        document.addEventListener('keypress', (e) => {
            // Ignorar si estamos en un input/textarea
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                return;
            }
            
            const currentTime = Date.now();
            
            // Si pasan más de 100ms entre teclas, reiniciar buffer
            if (currentTime - this.lastKeyTime > 100) {
                this.buffer = '';
                this.isScanning = false;
            }
            
            this.lastKeyTime = currentTime;
            this.isScanning = true;
            
            // Si es Enter y hay datos en el buffer, es un escaneo completo
            if (e.key === 'Enter' && this.buffer.length > 3) {
                e.preventDefault();
                this.handleBarcodeScanned(this.buffer);
                this.buffer = '';
                this.isScanning = false;
                return;
            }
            
            // Acumular caracteres
            if (e.key !== 'Enter') {
                this.buffer += e.key;
            }
        });
    },
    
    async handleBarcodeScanned(barcode) {
        console.log('Código escaneado:', barcode);
        
        // Buscar el producto por SKU
        try {
            const response = await fetch(`/admin/api/products/search-by-sku/${barcode}`);
            const data = await response.json();
            
            if (data.success && data.product) {
                // Mostrar modal para agregar al carrito
                this.showAddToCartModal(data.product);
            } else {
                // Producto no encontrado
                new FilamentNotification()
                    .title('Producto no encontrado')
                    .body(`No se encontró ningún producto con el código: ${barcode}`)
                    .danger()
                    .send();
            }
        } catch (error) {
            console.error('Error al buscar producto:', error);
            new FilamentNotification()
                .title('Error')
                .body('Ocurrió un error al buscar el producto')
                .danger()
                .send();
        }
    },
    
    showAddToCartModal(product) {
        // Verificar stock
        if (product.stock <= 0) {
            new FilamentNotification()
                .title('Sin stock')
                .body(`${product.name} no tiene stock disponible`)
                .danger()
                .send();
            return;
        }
        
        // Usar el sistema de modales de Filament
        $wire.dispatch('open-modal', {
            id: 'add-to-cart-barcode',
            product: product
        });
        
        // Alternativa: Agregar directamente con cantidad 1
        this.addToCart(product, 1);
    },
    
    async addToCart(product, quantity) {
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
                Livewire.dispatch('cartUpdated');
                
                new FilamentNotification()
                    .title('Producto agregado')
                    .body(`${product.name} x${quantity} agregado al carrito`)
                    .success()
                    .send();
            } else {
                new FilamentNotification()
                    .title('Error')
                    .body(data.message || 'No se pudo agregar el producto')
                    .danger()
                    .send();
            }
        } catch (error) {
            console.error('Error al agregar al carrito:', error);
        }
    }
}" class="hidden">
    <!-- Este componente solo escucha eventos de teclado -->
</div>

<script>
    // Asegurar que FilamentNotification esté disponible
    if (typeof FilamentNotification === 'undefined') {
        window.FilamentNotification = class {
            constructor() {
                this._title = '';
                this._body = '';
                this._status = 'info';
            }
            
            title(text) {
                this._title = text;
                return this;
            }
            
            body(text) {
                this._body = text;
                return this;
            }
            
            success() {
                this._status = 'success';
                return this;
            }
            
            danger() {
                this._status = 'danger';
                return this;
            }
            
            warning() {
                this._status = 'warning';
                return this;
            }
            
            send() {
                // Usar el sistema de notificaciones de Filament
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: {
                        message: this._body,
                        type: this._status
                    }
                }));
            }
        };
    }
</script>
<?php /**PATH C:\Pharma\resources\views/filament/hooks/barcode-scanner-listener.blade.php ENDPATH**/ ?>