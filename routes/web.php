<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

use App\Http\Controllers\POSController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.show');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::post('/pos/add', [POSController::class, 'addToCart'])->name('pos.add');
    Route::delete('/pos/cart/item/{index}', [POSController::class, 'removeCartItem'])->name('pos.cart.item.destroy');
    Route::post('/pos/cart/cancel', [POSController::class, 'cancelCart'])->name('pos.cart.cancel');
    Route::get('/pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');
    Route::post('/pos/checkout', [POSController::class, 'placeOrder'])->name('pos.place');
    Route::post('/pos/apply-promo', [POSController::class, 'applyPromo'])->name('pos.apply-promo');
    Route::get('/pos/receipt/{id}', [POSController::class, 'receipt'])->name('pos.receipt');

    // Payment endpoints (KHQR)
    Route::get('/pos/payment/khqr/create/{order}', [PaymentController::class, 'createKHQRPayment'])->name('payment.khqr.create');
    Route::get('/pos/payment/khqr/status/{payment}', [PaymentController::class, 'checkKHQRPayment'])->name('payment.khqr.status');
    Route::post('/pos/payment/verify/{payment}', [PaymentController::class, 'verifyPayment'])->name('payment.verify');
    Route::get('/pos/checkout/confirm', [PaymentController::class, 'confirm'])->name('pos.payment.confirm');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::view('/project-overview', 'project-overview')->name('project.overview');

    Route::middleware('role:admin,manager')->group(function () {
        // Product CRUD
        Route::resource('products', ProductController::class)->except(['show']);

        // Orders (admin)
        Route::resource('orders', OrderController::class)->only(['index', 'show']);

        // Promos
        Route::resource('promos', PromoController::class);

        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/products', [ReportController::class, 'products'])->name('reports.products');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    });

    Route::get('/api/promo/validate', [PromoController::class, 'validate'])->name('promo.validate');
    Route::post('/payment/process', [PaymentMethodController::class, 'process'])->name('payment.process');
});
