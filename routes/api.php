<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductPriceController;
use Illuminate\Support\Facades\Route;


Route::apiResource('products', ProductController::class);

Route::get('products/{productId}/prices', [ProductPriceController::class, 'index']);
Route::post('products/{productId}/prices', [ProductPriceController::class, 'store']);