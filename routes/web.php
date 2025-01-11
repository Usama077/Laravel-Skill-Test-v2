<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;

Route::get('/', [ProductController::class, 'index']);
Route::post('/save-product', [ProductController::class, 'store']);
Route::get('/get-products', [ProductController::class, 'listProducts']);
Route::put('/update-product/{index}', [ProductController::class, 'update']);
