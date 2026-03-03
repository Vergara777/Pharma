<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test-backend', function () {
    return response()->json([
        'status' => 'Conectado',
        'message' => 'El backend está vivo',
        'php_version' => PHP_VERSION
    ]);
});
