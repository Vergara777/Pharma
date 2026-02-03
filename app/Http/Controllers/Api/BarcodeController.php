<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BarcodeController extends Controller
{
    /**
     * Buscar producto por SKU (código de barras)
     */
    public function searchBySku($sku)
    {
        try {
            $product = Product::where('sku', $sku)
                ->with(['category', 'supplier'])
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'category' => $product->category?->name,
                    'image' => $product->image,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error al buscar producto por SKU: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar el producto'
            ], 500);
        }
    }

    /**
     * Agregar producto al carrito
     */
    public function addToCart(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1'
            ]);

            $product = Product::findOrFail($validated['product_id']);

            // Verificar stock disponible
            if ($product->stock < $validated['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => "Solo hay {$product->stock} unidades disponibles"
                ], 400);
            }

            $cart = session()->get('cart', []);
            $cartKey = $product->id;

            if (isset($cart[$cartKey])) {
                // Verificar stock para cantidad adicional
                $newQuantity = $cart[$cartKey]['quantity'] + $validated['quantity'];
                if ($product->stock < $validated['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No hay suficiente stock para agregar más unidades'
                    ], 400);
                }
                $cart[$cartKey]['quantity'] += $validated['quantity'];
            } else {
                $cart[$cartKey] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $validated['quantity'],
                ];
            }

            // Descontar del inventario
            $product->update(['stock' => $product->stock - $validated['quantity']]);

            session()->put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Producto agregado al carrito',
                'cart' => $cart
            ]);
        } catch (\Exception $e) {
            Log::error('Error al agregar al carrito: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar el producto al carrito'
            ], 500);
        }
    }
}
