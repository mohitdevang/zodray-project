<?php

use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Force JSON on all API routes
Route::middleware(['force.json'])->group(function () {
    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Public item listing
    Route::get('/items', [ItemController::class, 'index']);

    // Protected routes - Add custom header support for IIS Basic Auth
    Route::middleware(['sanctum.token', 'auth:sanctum'])->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        
        // Checkout
        Route::post('/checkout', [CheckoutController::class, 'createOrder']);
        
        // Payment
        Route::post('/payment/{orderId}', [PaymentController::class, 'processPayment']);
        Route::get('/payment/{orderId}', [PaymentController::class, 'getPaymentStatus']);
        Route::put('/payment/{orderId}', [PaymentController::class, 'updatePaymentStatus']);
    });
});
