<div>
    <!-- Modal del Carrito -->
    <x-filament::modal
        id="shopping-cart-modal"
        wire:model="isOpen"
        width="2xl"
        :close-by-clicking-away="false"
    >
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-shopping-cart class="w-6 h-6" />
                <span>Carrito de Compras</span>
                @if(count($cart) > 0)
                    <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300">
                        {{ count($cart) }} {{ count($cart) === 1 ? 'producto' : 'productos' }}
                    </span>
                @endif
            </div>
        </x-slot>

        <div class="space-y-4">
            @if(empty($cart))
                <div class="text-center py-12">
                    <x-heroicon-o-shopping-cart class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                    <p class="text-gray-500 dark:text-gray-400">El carrito está vacío</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Agrega productos desde el módulo de productos</p>
                </div>
            @else
                <!-- Lista de productos -->
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($cart as $key => $item)
                        <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ $item['name'] }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    ${{ number_format($item['price'], 2) }} c/u
                                </p>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <button 
                                    wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                                >
                                    <x-heroicon-o-minus class="w-4 h-4" />
                                </button>
                                
                                <span class="w-12 text-center font-semibold">{{ $item['quantity'] }}</span>
                                
                                <button 
                                    wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                                >
                                    <x-heroicon-o-plus class="w-4 h-4" />
                                </button>
                            </div>

                            <div class="text-right min-w-[80px]">
                                <p class="font-bold text-gray-900 dark:text-gray-100">
                                    ${{ number_format($item['price'] * $item['quantity'], 2) }}
                                </p>
                            </div>

                            <button 
                                wire:click="removeItem('{{ $key }}')"
                                class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                            >
                                <x-heroicon-o-trash class="w-5 h-5" />
                            </button>
                        </div>
                    @endforeach
                </div>

                <!-- Método de pago -->
                <div class="border-t pt-4 dark:border-gray-700">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Método de Pago
                    </label>
                    <select 
                        wire:model="paymentMethodId"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    >
                        <option value="">Seleccionar método de pago</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method->id }}">{{ $method->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Total -->
                <div class="border-t pt-4 dark:border-gray-700">
                    <div class="flex justify-between items-center text-xl font-bold">
                        <span class="text-gray-900 dark:text-gray-100">Total:</span>
                        <span class="text-primary-600 dark:text-primary-400">
                            ${{ number_format($total, 2) }}
                        </span>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex gap-3 pt-4">
                    <button 
                        wire:click="clearCart"
                        class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition font-medium"
                    >
                        Vaciar Carrito
                    </button>
                    <button 
                        wire:click="finalizeSale"
                        class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition font-medium"
                    >
                        Finalizar Venta
                    </button>
                </div>
            @endif
        </div>
    </x-filament::modal>
</div>
