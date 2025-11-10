<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Admin login routes (public)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'adminLogin']);
    Route::post('/logout', [AuthController::class, 'adminLogout'])->name('logout');
});

// Protected admin routes (require authentication)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('orders', OrderController::class);
    Route::get('/orders-export', [OrderController::class, 'export'])->name('orders.export');
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::put('/orders/{id}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.updatePaymentStatus');
});
