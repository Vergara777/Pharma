<?php

namespace App\Filament\Resources\Ventas\Pages;

use App\Filament\Resources\Ventas\VentasResource;
use App\Models\VentaItem;
use App\Models\Product;
use App\Models\Ventas;
use App\Services\CartService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateVenta extends CreateRecord
{
    protected static string $resource = VentasResource::class;

    protected $listeners = ['cartUpdated' => 'refreshFormFromCart'];

    public function mount(): void
    {
        // Verificar si hay una caja abierta antes de permitir crear ventas
        $activeSession = \App\Models\CashSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();
        
        if (!$activeSession) {
            Notification::make()
                ->danger()
                ->title('No hay caja abierta')
                ->body('Debes abrir una caja antes de realizar ventas. Redirigiendo...')
                ->persistent()
                ->send();
            
            $this->redirect(\App\Filament\Resources\CashSessionResource\Pages\ListCashSessions::getUrl());
            return;
        }

        parent::mount();
    }

    public function refreshFormFromCart(): void
    {
        $cartData = \App\Filament\Resources\Ventas\Schemas\VentaForm::getCartFormData();
        
        // Actualizamos quirúrgicamente las claves del carrito sin tocar el resto (cliente, pago, etc.)
        foreach ($cartData as $key => $value) {
            $this->data[$key] = $value;
        }
    }

    protected function afterFill(): void
    {
        // Cargar el carrito automáticamente al entrar a la página
        \App\Filament\Resources\Ventas\Schemas\VentaForm::loadCartIntoForm(fn($key, $value) => $this->form->fill([$key => $value]));
        
        // El anterior loadCartIntoForm usa $set, pero aquí estamos en un Livewire component context.
        // Necesitamos adaptar la carga.
        $cart = CartService::getCart();
        if (!empty($cart)) {
            $products = [];
            $total = 0;
            foreach ($cart as $id => $item) {
                $subtotal = $item['price'] * $item['qty'];
                $total += $subtotal;
                $products[] = [
                    'cart_id' => $id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'product_sku' => $item['sku'],
                    'product_image' => $item['image'],
                    'qty' => $item['qty'],
                    'type' => $item['type'],
                    'unit_price' => $item['price'],
                    'unit_price_display' => number_format($item['price'], 0, ',', '.'),
                    'subtotal_item_display' => number_format($subtotal, 0, ',', '.')
                ];
            }
            $this->form->fill([
                'products' => $products,
                'grand_total' => $total,
                'grand_total_display' => number_format($total, 0, ',', '.')
            ]);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Verificar nuevamente que hay una caja abierta
        $activeSession = \App\Models\CashSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();
        
        if (!$activeSession) {
            Notification::make()
                ->danger()
                ->title('Error: No hay caja abierta')
                ->body('No se puede crear la venta sin una caja abierta.')
                ->persistent()
                ->send();
            
            $this->halt();
        }

        $this->productsToCreate = $data['products'] ?? [];
        unset($data['products']);
        
        $data['user_id'] = auth()->id();
        $data['user_name'] = auth()->user()->name;
        $data['user_role'] = auth()->user()->role;

        // 1. Procesar venta anónima PRIMERO
        if (!empty($data['anonymous_invoice'])) {
            $data['customer_name'] = 'CONSUMIDOR FINAL';
            $data['invoice_document'] = '222222222222';
            $data['cliente_id'] = null; // No asociar cliente en ventas anónimas
        } else {
            // 2. Crear o actualizar cliente si no es anónimo
            if (!empty($data['customer_name']) && $data['customer_name'] !== 'CONSUMIDOR FINAL') {
                // Si no hay cliente_id seleccionado, intentar crear uno nuevo
                if (empty($data['cliente_id'])) {
                    // Buscar si ya existe un cliente con ese documento o nombre
                    $existingCliente = null;
                    if (!empty($data['invoice_document']) && $data['invoice_document'] !== '222222222222') {
                        $existingCliente = \App\Models\Cliente::where('document', $data['invoice_document'])->first();
                    }
                    
                    if (!$existingCliente && !empty($data['customer_name'])) {
                        $existingCliente = \App\Models\Cliente::where('name', $data['customer_name'])->first();
                    }

                    if ($existingCliente) {
                        // Cliente existe, usarlo
                        $data['cliente_id'] = $existingCliente->id;
                    } else {
                        // Crear nuevo cliente
                        $newCliente = \App\Models\Cliente::create([
                            'name' => $data['customer_name'],
                            'document' => $data['invoice_document'] ?? null,
                            'phone' => $data['customer_phone'] ?? null,
                            'email' => $data['customer_email'] ?? null,
                            'address' => $data['invoice_address'] ?? null,
                            'is_active' => true,
                        ]);
                        
                        $data['cliente_id'] = $newCliente->id;
                        
                        Notification::make()
                            ->success()
                            ->title('Cliente registrado')
                            ->body("El cliente {$newCliente->name} fue registrado automáticamente")
                            ->send();
                    }
                }
            }
        }

        // Si el nombre viene del form (aunque esté disabled lo forzamos con dehydrated)
        $customerName = $data['customer_name'] ?? 'CONSUMIDOR FINAL';

        // 3. Generación de Factura / Ticket
        if (!empty($data['generate_invoice'])) {
            $datePrefix = date('Ymd');
            $count = Ventas::where('invoice_number', 'like', "FAC-{$datePrefix}-%")->count();
            
            $data['invoice_number'] = 'FAC-' . $datePrefix . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            $data['invoice_name'] = $customerName;
            $data['invoice_document'] = $data['invoice_document'] ?? '222222222222';
            $data['invoice_phone'] = $data['customer_phone'] ?? null;
            $data['invoice_email'] = $data['customer_email'] ?? null;
            $data['customer_name'] = $customerName; // Asegurar que customer_name se guarde
        }
        
        // 4. Limpieza y normalización de campos financieros
        unset($data['generate_invoice']);
        unset($data['anonymous_invoice']);
        
        if (isset($data['amount_received'])) {
            $data['amount_received'] = (float) str_replace(['.', ','], '', $data['amount_received']);
        } else {
            // Para métodos no efectivos (tarjeta/transferencia), el monto recibido es igual al total
            $data['amount_received'] = $data['grand_total'] ?? 0;
        }

        // Asegurar que change_amount y status nunca sean null
        $data['change_amount'] = $data['change_amount'] ?? 0;
        $data['status'] = $data['status'] ?? 'active';

        // Intentar asignar a la sesión de caja activa del usuario
        $activeSession = \App\Models\CashSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();
        
        if ($activeSession) {
            $data['cash_session_id'] = $activeSession->id;
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        if (!empty($this->productsToCreate)) {
            $subtotalVenta = 0;
            
            foreach ($this->productsToCreate as $productData) {
                $product = Product::find($productData['product_id']);
                if (!$product) continue;
                
                $qtyToDecrement = (int)$productData['qty'];
                if ($productData['type'] === 'package') {
                    $qtyToDecrement *= ($product->units_per_package ?: 1);
                }

                // Verificar stock real
                if ($product->stock < $qtyToDecrement) {
                    Notification::make()
                        ->warning()
                        ->title('Stock insuficiente')
                        ->body("El producto {$product->name} no tiene stock suficiente para esta cantidad")
                        ->send();
                }
                
                $subtotal = $productData['unit_price'] * $productData['qty'];
                $subtotalVenta += $subtotal;
                
                VentaItem::create([
                    'venta_id' => $this->record->id,
                    'product_id' => $productData['product_id'],
                    'qty' => $productData['qty'],
                    'unit_price' => $productData['unit_price'],
                    'subtotal' => $subtotal,
                ]);
                
                // STOCK YA FUE DECREMENTADO POR EL CARRITO EN TIEMPO REAL
            }

            // Crear factura automáticamente si el toggle estaba activado
            if (!empty($this->data['generate_invoice']) && !empty($this->record->cliente_id)) {
                // El total de la venta ya incluye todo (IVA, descuentos, etc.)
                // No necesitamos recalcular, solo usar el grand_total de la venta
                $totalVenta = $this->record->grand_total;
                
                // Calcular el subtotal sin IVA (si el total incluye IVA del 19%)
                // subtotal = total / 1.19
                $subtotalSinIva = round($totalVenta / 1.19);
                $taxCalculado = $totalVenta - $subtotalSinIva;

                $factura = \App\Models\Factura::create([
                    'invoice_number' => $this->record->invoice_number ?? \App\Models\Factura::generateInvoiceNumber(),
                    'cliente_id' => $this->record->cliente_id,
                    'user_id' => auth()->id(),
                    'fecha_emision' => now(),
                    'fecha_vencimiento' => now()->addDays(30),
                    'subtotal' => $subtotalSinIva,
                    'tax' => $taxCalculado,
                    'discount' => 0,
                    'total' => $totalVenta,
                    'status' => 'paid', // Ya está pagada porque viene de una venta
                    'payment_method' => $this->getPaymentMethodName($this->record->payment_method_id),
                    'notes' => 'Factura generada automáticamente desde venta #' . $this->record->id,
                ]);

                // Copiar los items de la venta a la factura
                foreach ($this->productsToCreate as $productData) {
                    \App\Models\FacturaItem::create([
                        'factura_id' => $factura->id,
                        'product_id' => $productData['product_id'],
                        'quantity' => $productData['qty'],
                        'price' => $productData['unit_price'],
                        'subtotal' => $productData['unit_price'] * $productData['qty'],
                    ]);
                }

                // Actualizar la venta con el ID de la factura
                $this->record->update(['factura_id' => $factura->id]);

                Notification::make()
                    ->success()
                    ->title('Factura generada')
                    ->body("Factura {$factura->invoice_number} creada automáticamente")
                    ->send();
            }
        }
        
        // Vaciar el carrito sin devolver el stock (porque la venta fue exitosa)
        CartService::clear(restoreStock: false);
        
        $invoiceMessage = $this->record->invoice_number 
            ? " - Factura: {$this->record->invoice_number}" 
            : '';
        
        Notification::make()
            ->success()
            ->title('Venta registrada')
            ->body("Venta #{$this->record->id} completada exitosamente" . $invoiceMessage)
            ->send();
    }

    protected function getPaymentMethodName($paymentMethodId): string
    {
        $method = \App\Models\PaymentMethod::find($paymentMethodId);
        if (!$method) return 'cash';
        
        return match($method->code) {
            'cash' => 'cash',
            'card' => 'card',
            'transfer' => 'transfer',
            default => 'cash',
        };
    }

    protected array $productsToCreate = [];

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
