<?php

namespace App\Filament\Resources\Ventas\Pages;

use App\Filament\Resources\Ventas\VentasResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVenta extends CreateRecord
{
    protected static string $resource = VentasResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Guardar los productos temporalmente
        $this->productsToCreate = $data['products'] ?? [];
        unset($data['products']);
        
        // Guardar info del usuario
        $data['user_id'] = auth()->id();
        $data['user_name'] = auth()->user()->name;
        $data['user_role'] = auth()->user()->role;
        
        // Generar número de factura si se solicitó
        if (!empty($data['generate_invoice']) && !empty($data['customer_name'])) {
            $data['invoice_number'] = 'FAC-' . date('Ymd') . '-' . str_pad(
                \App\Models\Ventas::whereDate('created_at', today())->count() + 1, 
                4, 
                '0', 
                STR_PAD_LEFT
            );
            
            $data['invoice_name'] = $data['customer_name'];
            $data['invoice_phone'] = $data['customer_phone'] ?? null;
            $data['invoice_email'] = $data['customer_email'] ?? null;
        }
        
        unset($data['generate_invoice']);
        
        // Limpiar product_id y qty ya que ahora usamos items
        $data['product_id'] = null;
        $data['qty'] = null;
        $data['unit_price'] = null;
        
        $data['discount_percent'] = 0;
        $data['discount_amount'] = 0;
        $data['tax_rate'] = 0;
        $data['tax_amount'] = 0;
        $data['amount_received'] = $data['amount_received'] ?? 0;
        $data['change_amount'] = $data['change_amount'] ?? 0;
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Crear los items de la venta
        if (!empty($this->productsToCreate)) {
            foreach ($this->productsToCreate as $productData) {
                $product = \App\Models\Product::find($productData['product_id']);
                
                if (!$product) continue;
                
                // Verificar stock
                if ($product->stock < $productData['qty']) {
                    \Filament\Notifications\Notification::make()
                        ->warning()
                        ->title('Stock insuficiente')
                        ->body("El producto {$product->name} no tiene stock suficiente")
                        ->send();
                    continue;
                }
                
                $subtotal = $productData['unit_price'] * $productData['qty'];
                
                // Crear el item
                \App\Models\VentaItem::create([
                    'venta_id' => $this->record->id,
                    'product_id' => $productData['product_id'],
                    'qty' => $productData['qty'],
                    'unit_price' => $productData['unit_price'],
                    'subtotal' => $subtotal,
                ]);
                
                // Descontar stock
                $product->decrement('stock', $productData['qty']);
            }
        }
        
        $invoiceMessage = $this->record->invoice_number 
            ? " - Factura: {$this->record->invoice_number}" 
            : '';
        
        $productCount = count($this->productsToCreate ?? []);
        
        \Filament\Notifications\Notification::make()
            ->success()
            ->title('Venta registrada')
            ->body("Se registraron {$productCount} producto(s) correctamente" . $invoiceMessage)
            ->send();
    }

    protected array $productsToCreate = [];

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
