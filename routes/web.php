<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\BarcodeController;
use App\Http\Controllers\LoteDocumentController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [DashboardController::class, 'index']);

// Rutas API para código de barras
Route::middleware(['auth'])->prefix('admin/api')->group(function () {
    Route::get('/products/search-by-sku/{sku}', [BarcodeController::class, 'searchBySku']);
    Route::post('/cart/add', [BarcodeController::class, 'addToCart']);
    Route::get('/products/{id}/image', [\App\Http\Controllers\ProductImageController::class, 'show'])->name('product.image');
});

// Rutas del carrito
Route::middleware(['auth'])->group(function () {
    Route::post('/admin/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/admin/cart/finalize', [CartController::class, 'finalize'])->name('cart.finalize');
    Route::get('/admin/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/admin/ventas/{venta}/invoice', [CartController::class, 'showInvoice'])->name('ventas.invoice');
    
    // Ruta para ver documentos de lotes
    Route::get('/admin/lotes/{lote}/documento', [LoteDocumentController::class, 'show'])->name('lotes.documento');
    
    // Ruta para ver ticket de factura
    Route::get('/admin/facturas/{factura}/ticket', function (\App\Models\Factura $factura) {
        return view('facturas.ticket', compact('factura'));
    })->name('facturas.ticket');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
Route::get('/Roles', function () {
    return Inertia::render('Roles/Index');
})->name('roles.index');
// Route::get('/', function () {
//     return redirect('/admin');
// });