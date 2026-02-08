<div 
    x-data="{ 
        open: @entangle('isOpen'),
        init() {
            window.addEventListener('openCart', () => {
                this.open = true;
            });
        }
    }"
    x-cloak
>
    <!-- Overlay de Fondo -->
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="position: fixed; inset: 0; z-index: 99999; background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; padding: 1rem; pointer-events: auto;"
    >
        <!-- Contenedor del Modal -->
        <div 
            @click.away="open = false"
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            style="width: 100%; max-width: 42rem; background-color: white; border-radius: 1.5rem; display: flex; flex-direction: column; overflow: hidden; border: 1px solid #e5e7eb; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);"
            class="dark:bg-gray-900 dark:border-gray-800"
        >
            <!-- Header -->
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; justify-content: space-between; background-color: #f9fafb;" class="dark:bg-gray-800/50 dark:border-gray-800">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="padding: 0.5rem; background-color: #fef3c7; border-radius: 0.75rem; color: #d97706;" class="dark:bg-amber-900/30">
                        <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <div>
                        <h2 style="font-size: 1.25rem; font-weight: 800; color: #111827; margin: 0; line-height: 1.2;" class="dark:text-white">Carrito Rápido</h2>
                        <p style="font-size: 0.75rem; color: #6b7280; font-weight: 500; margin: 0;">Resumen del punto de venta</p>
                    </div>
                </div>
                <button @click="open = false" style="color: #9ca3af; border: none; background: none; cursor: pointer; padding: 0.5rem; border-radius: 0.5rem;" class="hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Content -->
            <div style="padding: 1.5rem; overflow-y: auto; max-height: 55vh; background-color: white;" class="dark:bg-gray-900">
                @if(empty($cart))
                    <div style="padding: 4rem 0; text-align: center;">
                        <div style="width: 6rem; height: 6rem; background-color: #f3f4f6; border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: #9ca3af;" class="dark:bg-gray-800">
                             <svg style="width: 3rem; height: 3rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        </div>
                        <p style="color: #6b7280; font-size: 1.125rem; font-weight: 500;" class="dark:text-gray-400">Tu carrito está vacío</p>
                    </div>
                @else
                    <table style="width: 100%; text-align: left; border-collapse: separate; border-spacing: 0 0.5rem;">
                        <thead>
                            <tr style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: #9ca3af; letter-spacing: 0.05em;">
                                <th style="padding: 0 0.5rem 0.75rem;">PRODUCTO</th>
                                <th style="padding: 0 0.5rem 0.75rem;">PRECIO</th>
                                <th style="padding: 0 0.5rem 0.75rem; width: 6rem;">CANT.</th>
                                <th style="padding: 0 0.5rem 0.75rem; text-align: right;">SUBTOTAL</th>
                                <th style="padding: 0 0.5rem 0.75rem;"></th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 0.9rem;">
                            @foreach($cart as $id => $item)
                                <tr style="background-color: #f9fafb; transition: background-color 0.2s;" class="dark:bg-gray-800/40 hover:bg-gray-50 dark:hover:bg-gray-800/60 group">
                                    <td style="padding: 0.75rem; border-radius: 0.75rem 0 0 0.75rem; display: flex; align-items: center; gap: 1rem;">
                                        <img 
                                            src="{{ $item['image'] && str_starts_with($item['image'], 'http') ? $item['image'] : url('/Images/Pharma1.jpeg') }}" 
                                            style="width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; object-cover; shadow: 0 1px 2px rgba(0,0,0,0.1);"
                                            class="dark:border-gray-700"
                                        >
                                        <div>
                                            <div style="font-weight: 700; color: #111827;" class="dark:text-white">{{ $item['name'] }}</div>
                                            <div style="font-size: 0.7rem; color: #6b7280; font-weight: 600;">{{ strtoupper($item['type'] ?? 'unidad') }} • SKU: {{ $item['sku'] }}</div>
                                            
                                            @if(isset($stockErrors[$id]))
                                                <div style="font-size: 0.9rem; color: #b91c1c; font-weight: 900; margin-top: 8px; background-color: #fee2e2; padding: 10px 14px; border-radius: 12px; border: 2px solid #ef4444; display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                                                    <span style="font-size: 1.25rem;">⚠️</span>
                                                    <span>{{ $stockErrors[$id] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="padding: 1rem 0.5rem; color: #4b5563; font-weight: 500;" class="dark:text-gray-300">
                                        ${{ number_format($item['price'], 0, ',', '.') }}
                                    </td>
                                    <td style="padding: 1rem 0.5rem;">
                                        <input 
                                            wire:key="input-{{ $id }}-{{ $item['qty'] }}"
                                            type="number" 
                                            value="{{ $item['qty'] }}"
                                            wire:change="updateQuantity('{{ $id }}', $event.target.value)"
                                            style="width: 4rem; padding: 0.35rem 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-weight: 700; text-align: center; outline: none; transition: border-color 0.2s;"
                                            class="dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:border-amber-500"
                                            min="1"
                                        >
                                    </td>
                                    <td style="padding: 1rem 0.5rem; text-align: right; font-weight: 800; color: #111827;" class="dark:text-white">
                                        ${{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                                    </td>
                                    <td style="padding: 1rem 0.75rem; text-align: right; border-radius: 0 0.75rem 0.75rem 0;">
                                        <button 
                                            wire:click="removeItem('{{ $id }}')"
                                            style="color: #9ca3af; border: none; background: none; cursor: pointer; padding: 0.4rem; border-radius: 0.4rem;"
                                            class="hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all"
                                            title="Eliminar producto"
                                        >
                                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr style="height: 0.25rem;"></tr> <!-- Spacer -->
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Footer -->
            <div style="padding: 1.5rem 2rem; background-color: #ffffff; border-top: 2px dashed #f3f4f6;" class="dark:bg-gray-900 dark:border-gray-800">
                <div style="display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 2rem;">
                    <div>
                        <span style="font-size: 0.7rem; color: #9ca3af; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.25rem;">VALOR TOTAL</span>
                        <span style="font-size: 2.25rem; font-weight: 950; color: #d97706; line-height: 1;">${{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <button 
                        wire:click="clearCart"
                        @if(empty($cart)) disabled @endif
                        style="flex: 1; height: 3.5rem; font-size: 0.9rem; font-weight: 700; color: #4b5563; background-color: #f3f4f6; border: none; border-radius: 1rem; cursor: pointer; transition: all 0.2s;"
                        class="hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed"
                    >
                        Vaciar
                    </button>
                    <button 
                        wire:click="goToCheckout"
                        @if(empty($cart)) disabled @endif
                        style="flex: 3; height: 3.5rem; font-size: 1.1rem; font-weight: 950; color: white; background-color: #f59e0b; border: none; border-radius: 1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.75rem; transition: all 0.3s; box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.4);"
                        class="hover:bg-amber-600 dark:hover:bg-amber-500 disabled:opacity-40 disabled:shadow-none disabled:cursor-not-allowed"
                    >
                        <span>IR A PAGAR</span>
                        <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </button>
                </div>

                <!-- Pantalla de Carga Global para Redirección -->
                @if($isRedirecting)
                    <div 
                        style="position: fixed; inset: 0; z-index: 999999; background-color: rgba(255, 255, 255, 0.9); backdrop-filter: blur(8px); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1.5rem;"
                    >
                        <div style="position: relative; width: 5rem; height: 5rem; display: flex; align-items: center; justify-content: center;">
                            <svg class="animate-spin" style="width: 4rem; height: 4rem; color: #f59e0b;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle style="opacity: 0.2;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path style="opacity: 0.9;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <div style="text-align: center; animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;">
                            <h3 style="font-size: 1.75rem; font-weight: 900; color: #111827; margin: 0; letter-spacing: -0.025em;">Cargando Ventas...</h3>
                            <p style="font-size: 1rem; color: #6b7280; font-weight: 700; margin-top: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Procesando pedido en tiempo real</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
