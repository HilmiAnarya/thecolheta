<?php

use Illuminate\Support\Facades\Route;
// Import SEMUA controller API Anda
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;

Route::get('/', function () {
    return view('welcome');
});

//
// ! INI ADALAH SEMUA RUTE API "STATEFUL" (YANG BUTUH LOGIN)
// ! Kita letakkan di 'web.php' agar otomatis dapat Sesi & Cookie
//
Route::prefix('api')->group(function () {

    // Auth (Publik, tapi stateful)
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Rute yang dilindungi (perlu login)
    Route::middleware('auth')->group(function () {

        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // Keranjang (Cart)
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'index']);
            Route::post('/add', [CartController::class, 'add']);
            Route::delete('/remove/{cartItem}', [CartController::class, 'remove']);
            Route::post('/update/{cartItem}', [CartController::class, 'updateQuantity']);
        });

        // Admin
        Route::middleware('role:admin')->group(function () {
            Route::apiResource('products', ProductController::class)->except(['index', 'show']);
            Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
            Route::post('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus']);
        });
    });
});
