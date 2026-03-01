<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductPriceController;
use Illuminate\Support\Facades\Route;

// Rutas públicas de auth
Route::post('login', [AuthController::class, 'login']);

// Rutas protegidas por JWT
Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::apiResource('products', ProductController::class);

    Route::get('products/{productId}/prices', [ProductPriceController::class, 'index']);
    Route::post('products/{productId}/prices', [ProductPriceController::class, 'store']);
});
