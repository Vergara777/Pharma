<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    public function show($id)
    {
        $product = Product::findOrFail($id);
        
        if (!$product->image) {
            abort(404);
        }
        
        // Si es una URL externa, redirigir
        if (str_starts_with($product->image, 'http')) {
            return redirect($product->image);
        }
        
        // Si es un archivo local, servirlo
        if (Storage::disk('local')->exists($product->image)) {
            return Storage::disk('local')->response($product->image);
        }
        
        abort(404);
    }
}
