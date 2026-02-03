<div>
    @if($isOpen)
        <div 
            wire:click="close"
            style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 99999; background-color: rgba(0, 0, 0, 0.6); display: flex; align-items: center; justify-content: center; padding: 1rem;"
        >
            <div 
                wire:click.stop
                style="position: relative; width: 100%; max-width: 56rem; max-height: 90vh; background-color: white; border-radius: 0.75rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; display: flex; flex-direction: column;"
                class="dark:bg-gray-900"
            >
                <!-- Header fijo -->
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 1.5rem; border-bottom: 1px solid #e5e7eb;" class="dark:border-gray-800">
                    <h2 style="font-size: 1.25rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; color: #111827;" class="dark:text-white">
                        <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Carrito de Compras
                        @if(count($cart) > 0)
                            <span style="background-color: #f59e0b; color: white; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                                {{ count($cart) }}
                            </span>
                        @endif
                    </h2>
                    <button 
                        wire:click="close"
                        style="padding: 0.5rem; border-radius: 0.5rem; color: #6b7280; transition: all 0.15s;"
                        class="hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800"
                    >
                        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Contenido scrolleable -->
                <div style="flex: 1; overflow-y: auto; padding: 1.5rem;">
                    @if(empty($cart))
                        <div style="text-align: center; padding: 3rem 0;">
                            <svg style="width: 4rem; height: 4rem; margin: 0 auto 1rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p style="font-size: 1.125rem; color: #6b7280; margin-bottom: 1rem;" class="dark:text-gray-400">El carrito está vacío</p>
                            <p style="font-size: 0.875rem; color: #9ca3af; margin-bottom: 1.5rem;" class="dark:text-gray-500">Agrega productos desde la lista</p>
                            <a 
                                href="{{ route('filament.admin.resources.products.index') }}"
                                style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background-color: #f59e0b; color: white; border-radius: 0.5rem; font-weight: 600; text-decoration: none; transition: all 0.15s;"
                                class="hover:bg-amber-600"
                            >
                                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                Ir a Productos
                            </a>
                        </div>
                    @else
                        <!-- Lista de productos -->
                        <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem;">
                            @foreach($cart as $key => $item)
                                <div style="display: flex; gap: 1rem; padding: 1rem; background-color: #f9fafb; border-radius: 0.75rem; border: 1px solid #e5e7eb;" class="dark:bg-gray-800 dark:border-gray-700">
                                    <!-- Imagen del producto -->
                                    <div style="flex-shrink: 0;">
                                        @if(!empty($item['image']))
                                            @if(str_starts_with($item['image'], 'http'))
                                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" style="width: 5rem; height: 5rem; object-fit: cover; border-radius: 0.5rem;">
                                            @else
                                                <img src="{{ Storage::disk('local')->url($item['image']) }}" alt="{{ $item['name'] }}" style="width: 5rem; height: 5rem; object-fit: cover; border-radius: 0.5rem;">
                                            @endif
                                        @else
                                            <div style="width: 5rem; height: 5rem; background-color: #e5e7eb; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;" class="dark:bg-gray-700">
                                                <svg style="width: 2rem; height: 2rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Información del producto -->
                                    <div style="flex: 1; min-width: 0;">
                                        <h4 style="font-weight: 600; font-size: 1rem; color: #111827; margin-bottom: 0.25rem;" class="dark:text-white">
                                            {{ $item['name'] }}
                                        </h4>
                                        @if(!empty($item['description']))
                                            <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" class="dark:text-gray-400">
                                                {{ $item['description'] }}
                                            </p>
                                        @endif
                                        <p style="font-size: 0.875rem; color: #6b7280;" class="dark:text-gray-400">
                                            ${{ number_format($item['price'], 0, ',', '.') }} c/u
                                        </p>
                                        @if(isset($item['stock_available']))
                                            <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;" class="dark:text-gray-500">
                                                Stock disponible: {{ $item['stock_available'] }}
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Controles de cantidad y precio -->
                                    <div style="display: flex; flex-direction: column; align-items: flex-end; justify-content: space-between;">
                                        <p style="font-weight: 700; font-size: 1.125rem; color: #111827;" class="dark:text-white">
                                            ${{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                        </p>
                                        
                                        <!-- Controles de cantidad -->
                                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem;">
                                            <button 
                                                wire:click="decreaseQuantity({{ $item['product_id'] }})"
                                                style="width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; background-color: #f3f4f6; border-radius: 0.375rem; color: #374151; font-weight: 600; transition: all 0.15s;"
                                                class="hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                                            >
                                                -
                                            </button>
                                            <span style="min-width: 2rem; text-align: center; font-weight: 600; color: #111827;" class="dark:text-white">
                                                {{ $item['quantity'] }}
                                            </span>
                                            <button 
                                                wire:click="increaseQuantity({{ $item['product_id'] }})"
                                                style="width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; background-color: #f59e0b; border-radius: 0.375rem; color: white; font-weight: 600; transition: all 0.15s;"
                                                class="hover:bg-amber-600"
                                            >
                                                +
                                            </button>
                                        </div>

                                        <!-- Botón eliminar -->
                                        <button 
                                            wire:click="removeItem({{ $item['product_id'] }})"
                                            style="margin-top: 0.5rem; padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #ef4444; background-color: #fee2e2; border-radius: 0.375rem; transition: all 0.15s;"
                                            class="hover:bg-red-200 dark:bg-red-900 dark:text-red-200 dark:hover:bg-red-800"
                                        >
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Total -->
                        <div style="border-top: 2px solid #e5e7eb; padding-top: 1rem; margin-bottom: 1.5rem;" class="dark:border-gray-700">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 1.5rem; font-weight: 700; color: #111827;" class="dark:text-white">Total:</span>
                                <span style="font-size: 1.875rem; font-weight: 700; color: #f59e0b;">
                                    ${{ number_format($total, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Form de pago -->
                        <form 
                            action="{{ route('cart.finalize') }}" 
                            method="POST" 
                            style="display: flex; flex-direction: column; gap: 1rem;"
                            x-data="{
                                submitForm(event) {
                                    // Copiar valores de Livewire a los campos hidden antes de enviar
                                    event.target.querySelector('[name=amount_received]').value = @this.amountReceived;
                                    event.target.querySelector('[name=payment_reference]').value = @this.paymentReference;
                                    event.target.querySelector('[name=customer_name]').value = @this.customerName;
                                    event.target.querySelector('[name=customer_phone]').value = @this.customerPhone;
                                    event.target.querySelector('[name=customer_email]').value = @this.customerEmail;
                                    event.target.querySelector('[name=customer_document]').value = @this.customerDocument;
                                    event.target.querySelector('[name=customer_address]').value = @this.customerAddress;
                                    event.target.querySelector('[name=generate_invoice]').value = @this.generateInvoice ? '1' : '0';
                                }
                            }"
                            @submit="submitForm"
                        >
                            @csrf
                            
                            <!-- Campos hidden para enviar al backend -->
                            <input type="hidden" name="amount_received" value="">
                            <input type="hidden" name="payment_reference" value="">
                            <input type="hidden" name="customer_name" value="">
                            <input type="hidden" name="customer_phone" value="">
                            <input type="hidden" name="customer_email" value="">
                            <input type="hidden" name="customer_document" value="">
                            <input type="hidden" name="customer_address" value="">
                            <input type="hidden" name="generate_invoice" value="">
                            
                            <!-- Método de pago -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;" class="dark:text-gray-300">
                                    Método de Pago *
                                </label>
                                <select 
                                    name="payment_method_id" 
                                    wire:model.live="selectedPaymentMethod"
                                    required 
                                    style="width: 100%; padding: 0.75rem; font-size: 1rem; border: 2px solid #d1d5db; border-radius: 0.5rem; transition: all 0.15s;"
                                    class="dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
                                >
                                    <option value="">Seleccionar método de pago</option>
                                    @foreach($paymentMethods as $method)
                                        <option value="{{ $method->name }}">{{ $method->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Campos para Efectivo -->
                            @if($selectedPaymentMethod === 'Efectivo')
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; padding: 1rem; background-color: #fef3c7; border-radius: 0.5rem; border: 2px solid #fbbf24;" class="dark:bg-yellow-900 dark:border-yellow-700">
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #92400e; margin-bottom: 0.5rem;" class="dark:text-yellow-200">
                                            Monto Recibido *
                                        </label>
                                        <input 
                                            type="text" 
                                            x-data="{ 
                                                value: @entangle('amountReceived').live,
                                                formatted: '',
                                                format() {
                                                    let num = this.value.toString().replace(/\D/g, '');
                                                    this.value = parseInt(num) || 0;
                                                    this.formatted = new Intl.NumberFormat('es-CO').format(this.value);
                                                }
                                            }"
                                            x-init="format()"
                                            x-model="formatted"
                                            @input="
                                                let num = $event.target.value.replace(/\D/g, '');
                                                value = parseInt(num) || 0;
                                                format();
                                            "
                                            required
                                            style="width: 100%; padding: 0.75rem; font-size: 1rem; border: 2px solid #fbbf24; border-radius: 0.5rem; background-color: white;"
                                            class="dark:bg-gray-800 dark:border-yellow-600 dark:text-white"
                                            placeholder="0"
                                        >
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #92400e; margin-bottom: 0.5rem;" class="dark:text-yellow-200">
                                            Cambio
                                        </label>
                                        <div style="width: 100%; padding: 0.75rem; font-size: 1.125rem; font-weight: 700; border: 2px solid #fbbf24; border-radius: 0.5rem; background-color: white; color: {{ $change >= 0 ? '#059669' : '#dc2626' }};" class="dark:bg-gray-800">
                                            ${{ number_format($change, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Campo de referencia para otros métodos de pago -->
                            @if($selectedPaymentMethod && $selectedPaymentMethod !== 'Efectivo')
                                <div style="padding: 1rem; background-color: #f0fdf4; border-radius: 0.5rem; border: 2px solid #10b981;" class="dark:bg-green-900 dark:border-green-700">
                                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #065f46; margin-bottom: 0.5rem;" class="dark:text-green-200">
                                        Número de Referencia / Autorización
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model="paymentReference"
                                        style="width: 100%; padding: 0.75rem; font-size: 1rem; border: 2px solid #10b981; border-radius: 0.5rem; background-color: white;"
                                        class="dark:bg-gray-800 dark:border-green-600 dark:text-white"
                                        placeholder="Ej: 123456789"
                                    >
                                    <p style="font-size: 0.75rem; color: #065f46; margin-top: 0.5rem;" class="dark:text-green-300">
                                        Ingrese el número de referencia o autorización de la transacción
                                    </p>
                                </div>
                            @endif

                            <!-- Toggle para capturar datos del cliente -->
                            <div style="padding: 1rem; background-color: #f9fafb; border-radius: 0.5rem; border: 1px solid #e5e7eb;" class="dark:bg-gray-800 dark:border-gray-700">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <span style="font-size: 0.875rem; font-weight: 500; color: #374151;" class="dark:text-gray-300">
                                        Registrar datos del cliente
                                    </span>
                                    <button 
                                        type="button"
                                        wire:click="$toggle('captureCustomerData')"
                                        style="position: relative; display: inline-flex; height: 1.5rem; width: 2.75rem; align-items: center; border-radius: 9999px; transition: background-color 0.2s; outline: none; background-color: {{ $captureCustomerData ? '#f59e0b' : '#d1d5db' }};"
                                        class="{{ $captureCustomerData ? '' : 'dark:bg-gray-700' }}"
                                    >
                                        <span 
                                            style="display: inline-block; height: 1rem; width: 1rem; border-radius: 9999px; background-color: white; transition: transform 0.2s; transform: translateX({{ $captureCustomerData ? '1.5rem' : '0.25rem' }});"
                                        ></span>
                                    </button>
                                </div>
                            </div>

                            <!-- Campos del cliente (solo si el toggle está activado) -->
                            @if($captureCustomerData)
                                <div style="display: grid; grid-template-columns: 1fr; gap: 1rem; padding: 1rem; background-color: #f9fafb; border-radius: 0.5rem; border: 1px solid #e5e7eb;" class="dark:bg-gray-800 dark:border-gray-700">
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;" class="dark:text-gray-300">
                                            Nombre del Cliente
                                        </label>
                                        <input 
                                            type="text" 
                                            wire:model="customerName"
                                            style="width: 100%; padding: 0.75rem; font-size: 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background-color: white;"
                                            class="dark:bg-gray-900 dark:border-gray-600 dark:text-white"
                                            placeholder="Ej: Juan Pérez"
                                        >
                                    </div>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;" class="dark:text-gray-300">
                                                Teléfono
                                            </label>
                                            <input 
                                                type="tel" 
                                                wire:model="customerPhone"
                                                style="width: 100%; padding: 0.75rem; font-size: 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background-color: white;"
                                                class="dark:bg-gray-900 dark:border-gray-600 dark:text-white"
                                                placeholder="Ej: 3001234567"
                                            >
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;" class="dark:text-gray-300">
                                                Documento / NIT
                                            </label>
                                            <input 
                                                type="text" 
                                                wire:model="customerDocument"
                                                style="width: 100%; padding: 0.75rem; font-size: 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background-color: white;"
                                                class="dark:bg-gray-900 dark:border-gray-600 dark:text-white"
                                                placeholder="CC, NIT, etc."
                                            >
                                        </div>
                                    </div>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;" class="dark:text-gray-300">
                                                Email
                                            </label>
                                            <input 
                                                type="email" 
                                                wire:model="customerEmail"
                                                style="width: 100%; padding: 0.75rem; font-size: 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background-color: white;"
                                                class="dark:bg-gray-900 dark:border-gray-600 dark:text-white"
                                                placeholder="Ej: cliente@email.com"
                                            >
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;" class="dark:text-gray-300">
                                                Dirección
                                            </label>
                                            <input 
                                                type="text" 
                                                wire:model="customerAddress"
                                                style="width: 100%; padding: 0.75rem; font-size: 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background-color: white;"
                                                class="dark:bg-gray-900 dark:border-gray-600 dark:text-white"
                                                placeholder="Dirección completa"
                                            >
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Toggle para generar factura -->
                            <div style="padding: 1rem; background-color: #fef3c7; border-radius: 0.5rem; border: 1px solid #fbbf24;" class="dark:bg-yellow-900 dark:border-yellow-700">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div>
                                        <span style="font-size: 0.875rem; font-weight: 600; color: #92400e; display: block;" class="dark:text-yellow-200">
                                            Generar Factura / Ticket
                                        </span>
                                        <span style="font-size: 0.75rem; color: #b45309; margin-top: 0.25rem; display: block;" class="dark:text-yellow-300">
                                            Se usarán los datos del cliente capturados arriba
                                        </span>
                                    </div>
                                    <button 
                                        type="button"
                                        wire:click="$toggle('generateInvoice')"
                                        style="position: relative; display: inline-flex; height: 1.5rem; width: 2.75rem; align-items: center; border-radius: 9999px; transition: background-color 0.2s; outline: none; background-color: {{ $generateInvoice ? '#f59e0b' : '#d1d5db' }};"
                                        class="{{ $generateInvoice ? '' : 'dark:bg-gray-700' }}"
                                    >
                                        <span 
                                            style="display: inline-block; height: 1rem; width: 1rem; border-radius: 9999px; background-color: white; transition: transform 0.2s; transform: translateX({{ $generateInvoice ? '1.5rem' : '0.25rem' }});"
                                        ></span>
                                    </button>
                                </div>
                                @if($generateInvoice && !$captureCustomerData)
                                    <div style="margin-top: 0.75rem; padding: 0.75rem; background-color: #fef3c7; border-left: 3px solid #f59e0b; border-radius: 0.25rem;">
                                        <p style="font-size: 0.75rem; color: #92400e; font-weight: 500;" class="dark:text-yellow-200">
                                            Activa "Registrar datos del cliente" para incluir información en la factura
                                        </p>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Botones de acción -->
                            <div style="display: flex; gap: 0.75rem; padding-top: 0.5rem;">
                                <button 
                                    type="button"
                                    wire:click="clearCart"
                                    style="flex: 1; padding: 0.875rem 1rem; background-color: #f3f4f6; color: #374151; text-align: center; border-radius: 0.5rem; font-weight: 600; font-size: 1rem; border: none; cursor: pointer; transition: all 0.15s;"
                                    class="dark:bg-gray-800 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-700"
                                >
                                    Vaciar Carrito
                                </button>
                                <button 
                                    type="submit" 
                                    style="flex: 2; padding: 0.875rem 1rem; background-color: #f59e0b; color: white; border-radius: 0.5rem; font-weight: 600; font-size: 1rem; border: none; cursor: pointer; transition: all 0.15s; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);"
                                    class="hover:bg-amber-600"
                                    @if($selectedPaymentMethod === 'Efectivo' && $amountReceived < $total) disabled style="opacity: 0.5; cursor: not-allowed;" @endif
                                >
                                    Finalizar Venta
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
