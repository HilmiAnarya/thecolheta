<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 🧭 AUTH
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 🪶 PUBLIC DATA (tanpa login)
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
//Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

// 🔐 AUTHENTICATED USERS (semua user login)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// 🧍 CUSTOMER ROUTES
Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {
    //Route::apiResource('orders', OrderController::class);
    //Route::apiResource('payments', PaymentController::class)->only(['index', 'store', 'show']);
});

// 🧑‍💼 ADMIN ROUTES
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);
    //Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    //Route::apiResource('payments', PaymentController::class)->except(['store', 'index', 'show']);
});
