<div>
    <x-filament::modal
        id="shopping-cart-modal"
        :visible="$isOpen"
        width="4xl"
    >
        <x-slot name="heading">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-br from-amber-500 to-orange-500">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold">Carrito de Compras</h2>
                    @if(count($cart) > 0)
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ count($cart) }} {{ count($cart) === 1 ? 'producto' : 'productos' }}</p>
                    @endif
                </div>
            </div>
        </x-slot>

        <x-slot name="footerActions">
            @if(!empty($cart))
                <x-filament::button
                    wire:click="close"
                    color="gray"
                    outlined
                >
                    Cerrar
                </x-filament::button>
            @endif
        </x-slot>

        @if(empty($cart))
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                    <x-heroicon-o-shopping-cart class="w-8 h-8 text-gray-400" />
                </div>
                <p class="text-lg font-medium mb-2">El carrito está vacío</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Agrega productos desde la lista</p>
                <x-filament::button
                    tag="a"
                    href="{{ route('filament.admin.resources.products.index') }}"
                    color="warning"
                    icon="heroicon-o-cube"
                >
                    Ir a Productos
                </x-filament::button>
            </div>
        @else
            <div class="space-y-4">
                <!-- Lista de productos -->
                <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                    @foreach($cart as $key => $item)
                        <div class="flex gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <!-- Imagen -->
                            <div class="flex-shrink-0">
                                @if(!empty($item['image']))
                                    @if(str_starts_with($item['image'], 'http'))
                                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-16 h-16 object-cover rounded-lg">
                                    @else
                                        <img src="{{ Storage::disk('local')->url($item['image']) }}" alt="{{ $item['name'] }}" class="w-16 h-16 object-cover rounded-lg">
                                    @endif
                                @else
                                    <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                        <x-heroicon-o-photo class="w-8 h-8 text-gray-400" />
                                    </div>
                                @endif
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold mb-1">{{ $item['name'] }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">${{ number_format($item['price'], 0, ',', '.') }} c/u</p>
                                @if(isset($item['stock_available']))
                                    <p class="text-xs text-gray-500 mt-1">Stock: {{ $item['stock_available'] }}</p>
                                @endif
                            </div>

                            <!-- Controles -->
                            <div class="flex flex-col items-end justify-between">
                                <p class="text-lg font-bold">
                                    ${{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                </p>
                                
                                <div class="flex items-center gap-2 mt-2">
                                    <x-filament::icon-button
                                        icon="heroicon-m-minus"
                                        wire:click="decreaseQuantity({{ $item['product_id'] }})"
                                        color="gray"
                                        size="sm"
                                    />
                                    <span class="w-8 text-center font-semibold">{{ $item['quantity'] }}</span>
                                    <x-filament::icon-button
                                        icon="heroicon-m-plus"
                                        wire:click="increaseQuantity({{ $item['product_id'] }})"
                                        color="warning"
                                        size="sm"
                                    />
                                </div>

                                <x-filament::link
                                    wire:click="removeItem({{ $item['product_id'] }})"
                                    color="danger"
                                    size="sm"
                                    class="mt-2"
                                >
                                    Eliminar
                                </x-filament::link>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Total -->
                <div class="p-4 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-lg border-2 border-amber-200 dark:border-amber-800">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold">Total:</span>
                        <span class="text-3xl font-bold text-amber-600 dark:text-amber-400">
                            ${{ number_format($total, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Formulario -->
                <form 
                    action="{{ route('cart.finalize') }}" 
                    method="POST" 
                    class="space-y-4"
                    x-data="{
                        submitForm(event) {
                            event.target.querySelector('[name=amount_received]').value = @this.amountReceived;
                            event.target.querySelector('[name=payment_reference]').value = @this.paymentReference;
                            event.target.querySelector('[name=customer_name]').value = @this.customerName;
                            event.target.querySelector('[name=customer_phone]').value = @this.customerPhone;
                            event.target.querySelector('[name=customer_email]').value = @this.customerEmail;
                            event.target.querySelector('[name=customer_document]').value = @this.customerDocument;
                            event.target.querySelector('[name=customer_address]').value = @this.customerAddress;
                            event.target.querySelector('[name=generate_invoice]').value = @this.generateInvoice ? '1' : '0';
                            event.target.querySelector('[name=invoice_type]').value = @this.invoiceType;
                        }
                    }"
                    @submit="submitForm"
                >
                    @csrf
                    
                    <input type="hidden" name="amount_received" value="">
                    <input type="hidden" name="payment_reference" value="">
                    <input type="hidden" name="customer_name" value="">
                    <input type="hidden" name="customer_phone" value="">
                    <input type="hidden" name="customer_email" value="">
                    <input type="hidden" name="customer_document" value="">
                    <input type="hidden" name="customer_address" value="">
                    <input type="hidden" name="generate_invoice" value="">
                    <input type="hidden" name="invoice_type" value="">

                    <!-- Método de pago -->
                    <div>
                        <label class="text-sm font-medium mb-2 block">Método de Pago *</label>
                        <select 
                            wire:model.live="selectedPaymentMethod"
                            name="payment_method_id"
                            required
                            class="fi-input block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">Seleccionar método de pago</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->name }}">{{ $method->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Efectivo -->
                    @if($selectedPaymentMethod === 'Efectivo')
                        <div class="grid grid-cols-2 gap-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-300 dark:border-green-700">
                            <div>
                                <label class="text-sm font-medium text-green-800 dark:text-green-200 mb-2 block">Monto Recibido *</label>
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
                                    @input="let num = $event.target.value.replace(/\D/g, ''); value = parseInt(num) || 0; format();"
                                    required
                                    class="fi-input block w-full border-green-300 dark:border-green-600 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="0"
                                >
                            </div>
                            <div>
                                <label class="text-sm font-medium text-green-800 dark:text-green-200 mb-2 block">Cambio</label>
                                <div class="fi-input block w-full border-green-300 dark:border-green-600 rounded-lg shadow-sm px-3 py-2 font-bold {{ $change >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    ${{ number_format($change, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Referencia -->
                    @if($selectedPaymentMethod && $selectedPaymentMethod !== 'Efectivo')
                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-300 dark:border-blue-700">
                            <label class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2 block">Número de Referencia</label>
                            <input 
                                type="text"
                                wire:model="paymentReference"
                                placeholder="Ej: 123456789"
                                class="fi-input block w-full border-blue-300 dark:border-blue-600 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            />
                            <p class="text-xs text-blue-700 dark:text-blue-300 mt-2">Ingrese el número de referencia o autorización</p>
                        </div>
                    @endif

                    <!-- Opciones de factura -->
                    <div>
                        <label class="text-sm font-semibold mb-3 block">Factura / Ticket</label>
                        <div class="space-y-2">
                            <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition {{ $invoiceType === 'none' ? 'border-warning-500 bg-warning-50 dark:bg-warning-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                                <input type="radio" wire:model.live="invoiceType" value="none" class="text-warning-600 focus:ring-warning-500">
                                <span class="ml-3 text-sm font-medium">No generar factura</span>
                            </label>
                            <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition {{ $invoiceType === 'without_data' ? 'border-warning-500 bg-warning-50 dark:bg-warning-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                                <input type="radio" wire:model.live="invoiceType" value="without_data" class="text-warning-600 focus:ring-warning-500">
                                <span class="ml-3 text-sm font-medium">Ticket sin datos del cliente</span>
                            </label>
                            <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition {{ $invoiceType === 'with_data' ? 'border-warning-500 bg-warning-50 dark:bg-warning-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                                <input type="radio" wire:model.live="invoiceType" value="with_data" class="text-warning-600 focus:ring-warning-500">
                                <span class="ml-3 text-sm font-medium">Factura con datos del cliente</span>
                            </label>
                        </div>
                    </div>

                    <!-- Datos del cliente -->
                    @if($invoiceType === 'with_data')
                        <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-300 dark:border-purple-700 space-y-3">
                            <h3 class="font-semibold text-purple-900 dark:text-purple-200">Datos del Cliente</h3>
                            
                            <div>
                                <label class="text-sm font-medium mb-1 block">Nombre Completo *</label>
                                <input 
                                    type="text"
                                    wire:model="customerName"
                                    placeholder="Ej: Juan Pérez"
                                    required
                                    class="fi-input block w-full border-purple-300 dark:border-purple-600 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white"
                                />
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-sm font-medium mb-1 block">Documento / NIT</label>
                                    <input 
                                        type="text"
                                        wire:model="customerDocument"
                                        placeholder="CC, NIT, etc."
                                        class="fi-input block w-full border-purple-300 dark:border-purple-600 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white"
                                    />
                                </div>
                                <div>
                                    <label class="text-sm font-medium mb-1 block">Teléfono</label>
                                    <input 
                                        type="tel"
                                        wire:model="customerPhone"
                                        placeholder="3001234567"
                                        class="fi-input block w-full border-purple-300 dark:border-purple-600 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white"
                                    />
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-sm font-medium mb-1 block">Email</label>
                                    <input 
                                        type="email"
                                        wire:model="customerEmail"
                                        placeholder="cliente@email.com"
                                        class="fi-input block w-full border-purple-300 dark:border-purple-600 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white"
                                    />
                                </div>
                                <div>
                                    <label class="text-sm font-medium mb-1 block">Dirección</label>
                                    <input 
                                        type="text"
                                        wire:model="customerAddress"
                                        placeholder="Dirección completa"
                                        class="fi-input block w-full border-purple-300 dark:border-purple-600 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white"
                                    />
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Botones -->
                    <div class="flex gap-3 pt-2">
                        <x-filament::button
                            type="button"
                            wire:click="clearCart"
                            color="gray"
                            outlined
                            class="flex-1"
                        >
                            Vaciar Carrito
                        </x-filament::button>
                        <x-filament::button
                            type="submit"
                            color="warning"
                            class="flex-1"
                        >
                            Finalizar Venta
                        </x-filament::button>
                    </div>
                </form>
            </div>
        @endif
    </x-filament::modal>
</div>
