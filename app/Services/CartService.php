<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'shopping_cart';

    public static function getCart(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public static function add(Product $product, int $qty = 1, string $type = 'unit', bool $updateStock = true): void
    {
        $cart = self::getCart();
        $id = $product->id . '_' . $type;

        if (isset($cart[$id])) {
            $cart[$id]['qty'] += $qty;
        } else {
            $price = $type === 'package' ? $product->price_package : $product->price_unit;
            if (!$price) {
                $price = $product->price;
            }
            
            $cart[$id] = [
                'id' => $id,
                'product_id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'image' => $product->image,
                'qty' => $qty,
                'price' => $price,
                'type' => $type,
                'package_name' => $product->package_name ?? 'Paquete',
                'unit_name' => $product->unit_name ?? 'Unidad',
                'units_per_package' => $product->units_per_package ?? 1,
            ];
        }

        // Real-time stock decrement
        if ($updateStock) {
            $units = $qty;
            if ($type === 'package') {
                $units *= ($product->units_per_package ?: 1);
            }
            $product->decrement('stock', $units);
        }

        Session::put(self::SESSION_KEY, $cart);
    }

    public static function updateQty(string $id, int $qty): void
    {
        $cart = self::getCart();
        if (isset($cart[$id])) {
            $product = Product::find($cart[$id]['product_id']);
            if ($product) {
                $oldQty = (int)$cart[$id]['qty'];
                $diff = $qty - $oldQty;
                
                $units = $diff;
                if ($cart[$id]['type'] === 'package') {
                    $units *= ($product->units_per_package ?: 1);
                }
                
                // Adjust stock based on difference
                if ($units > 0) {
                    $product->decrement('stock', $units);
                } elseif ($units < 0) {
                    $product->increment('stock', abs($units));
                }
            }

            if ($qty <= 0) {
                unset($cart[$id]);
            } else {
                $cart[$id]['qty'] = $qty;
            }
            Session::put(self::SESSION_KEY, $cart);
        }
    }

    public static function remove(string $id, bool $restoreStock = true): void
    {
        $cart = self::getCart();
        if (isset($cart[$id])) {
            if ($restoreStock) {
                $product = Product::find($cart[$id]['product_id']);
                if ($product) {
                    $units = (int)$cart[$id]['qty'];
                    if ($cart[$id]['type'] === 'package') {
                        $units *= ($product->units_per_package ?: 1);
                    }
                    $product->increment('stock', $units);
                }
            }
            unset($cart[$id]);
            Session::put(self::SESSION_KEY, $cart);
        }
    }

    public static function clear(bool $restoreStock = true): void
    {
        if ($restoreStock) {
            $cart = self::getCart();
            foreach ($cart as $item) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $units = (int)$item['qty'];
                    if (($item['type'] ?? 'unit') === 'package') {
                        $units *= ($product->units_per_package ?: 1);
                    }
                    $product->increment('stock', $units);
                }
            }
        }
        Session::forget(self::SESSION_KEY);
    }

    public static function getTotal(): float
    {
        $total = 0;
        foreach (self::getCart() as $item) {
            $total += (float)$item['price'] * (int)$item['qty'];
        }
        return $total;
    }

    public static function getCount(): int
    {
        return count(self::getCart());
    }
}
