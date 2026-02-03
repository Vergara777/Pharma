<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Ventas;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);
        
        $product = Product::find($productId);
        
        if (!$product) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }
        
        // Verificar stock disponible
        if ($product->stock < $quantity) {
            return response()->json(['error' => 'Stock insuficiente'], 400);
        }
        
        $cart = session()->get('cart', []);
        $cartKey = $productId;
        
        if (isset($cart[$cartKey])) {
            // Si ya existe en el carrito, verificar que hay stock para la cantidad adicional
            if ($product->stock < $quantity) {
                return response()->json(['error' => 'Stock insuficiente para agregar más unidades'], 400);
            }
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
            ];
        }
        
        // Descontar del inventario usando update directo
        $product->update(['stock' => $product->stock - $quantity]);
        
        session()->put('cart', $cart);
        
        return response()->json(['success' => true, 'cart' => $cart]);
    }

    public function finalize(Request $request)
    {
        $cart = session()->get('cart', []);
        $paymentMethodName = $request->input('payment_method_id');
        $amountReceived = $request->input('amount_received', 0);
        $paymentReference = $request->input('payment_reference');
        $customerName = $request->input('customer_name');
        $customerPhone = $request->input('customer_phone');
        $customerEmail = $request->input('customer_email');
        $customerDocument = $request->input('customer_document');
        $customerAddress = $request->input('customer_address');
        $generateInvoice = $request->input('generate_invoice');

        if (empty($cart) || !$paymentMethodName) {
            return redirect()->back()->with('error', 'Complete todos los campos');
        }

        // Validar que si se solicita factura, haya datos del cliente
        if ($generateInvoice && empty($customerName)) {
            \Filament\Notifications\Notification::make()
                ->title('Datos incompletos')
                ->body('Debe capturar los datos del cliente para generar la factura')
                ->danger()
                ->duration(5000)
                ->send();
            return redirect()->back();
        }

        try {
            // Buscar el payment method por nombre
            $paymentMethod = \App\Models\PaymentMethod::where('name', $paymentMethodName)->first();
            
            if (!$paymentMethod) {
                return redirect()->back()->with('error', 'Método de pago no válido');
            }

            $grandTotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
            $changeAmount = $amountReceived > 0 ? $amountReceived - $grandTotal : 0;
            
            // Generar número de factura si se solicitó
            $invoiceNumber = null;
            if ($generateInvoice) {
                $invoiceNumber = 'FAC-' . date('Ymd') . '-' . str_pad(Ventas::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
            }

            // Crear UNA sola venta
            $venta = Ventas::create([
                'product_id' => null,
                'qty' => null,
                'unit_price' => null,
                'total' => $grandTotal,
                'subtotal' => $grandTotal,
                'grand_total' => $grandTotal,
                'payment_method_id' => $paymentMethod->id,
                'payment_reference' => $paymentReference,
                'amount_received' => $amountReceived,
                'change_amount' => $changeAmount,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'customer_email' => $customerEmail,
                'invoice_number' => $invoiceNumber,
                'invoice_name' => $generateInvoice ? $customerName : null,
                'invoice_document' => $generateInvoice ? $customerDocument : null,
                'invoice_address' => $generateInvoice ? $customerAddress : null,
                'invoice_phone' => $generateInvoice ? $customerPhone : null,
                'invoice_email' => $generateInvoice ? $customerEmail : null,
                'user_id' => auth()->id(),
                'user_role' => auth()->user()->role ?? null,
                'user_name' => auth()->user()->name,
                'status' => 'active',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'tax_rate' => 0,
                'tax_amount' => 0,
            ]);

            // Crear los items de la venta
            foreach ($cart as $item) {
                \App\Models\VentaItem::create([
                    'venta_id' => $venta->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            // Limpiar carrito sin restaurar inventario (la venta se completó)
            session()->forget('cart');

            \Filament\Notifications\Notification::make()
                ->title('Venta completada')
                ->body('La venta se registró exitosamente' . ($invoiceNumber ? " - Factura: {$invoiceNumber}" : '') . ($customerName ? " para {$customerName}" : ''))
                ->success()
                ->duration(5000)
                ->send();

            return redirect()->back();

        } catch (\Exception $e) {
            \Log::error('Error al finalizar venta:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al procesar la venta: ' . $e->getMessage());
        }
    }

    public function clear()
    {
        $cart = session()->get('cart', []);
        
        // Restaurar el inventario de todos los productos en el carrito
        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $product->update(['stock' => $product->stock + $item['quantity']]);
            }
        }
        
        session()->forget('cart');
        return redirect()->back()->with('success', 'Carrito vaciado e inventario restaurado');
    }

    public function showInvoice(Ventas $venta)
    {
        // Verificar que la venta tenga factura
        if (!$venta->invoice_number) {
            abort(404, 'Esta venta no tiene factura generada');
        }

        return view('ventas.invoice', compact('venta'));
    }
}
