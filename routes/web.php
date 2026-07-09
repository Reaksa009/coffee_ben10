<?php

use App\Http\Controllers\AuthController;
// Route::get('/', function () {
//     return view('welcome');
// });

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CashierShiftController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShopSettingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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
    Route::post('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::post('/pos/add', [POSController::class, 'addToCart'])->name('pos.add');
    Route::delete('/pos/cart/item/{index}', [POSController::class, 'removeCartItem'])->name('pos.cart.item.destroy');
    Route::post('/pos/cart/cancel', [POSController::class, 'cancelCart'])->name('pos.cart.cancel');
    Route::get('/pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');
    Route::post('/pos/checkout', [POSController::class, 'placeOrder'])->name('pos.place');
    Route::post('/pos/apply-promo', [POSController::class, 'applyPromo'])->name('pos.apply-promo');
    Route::get('/pos/customers/lookup', [POSController::class, 'lookupCustomer'])->name('pos.customers.lookup');
    Route::get('/pos/receipt/{id}', [POSController::class, 'receipt'])->name('pos.receipt');
    Route::get('/products/{product}/image', [ProductController::class, 'image'])->name('products.image');

    // Payment endpoints (KHQR)
    Route::get('/pos/payment/khqr/create/{order}', [PaymentController::class, 'createKHQRPayment'])->name('payment.khqr.create');
    Route::get('/pos/payment/khqr/status/{payment}', [PaymentController::class, 'checkKHQRPayment'])->name('payment.khqr.status');
    Route::post('/pos/payment/verify/{payment}', [PaymentController::class, 'verifyPayment'])->name('payment.verify');
    Route::get('/pos/checkout/confirm', [PaymentController::class, 'confirm'])->name('pos.payment.confirm');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::view('/project-overview', 'project-overview')->name('project.overview');
    Route::get('/cashier-shifts', [CashierShiftController::class, 'index'])->name('cashier-shifts.index');
    Route::post('/cashier-shifts', [CashierShiftController::class, 'store'])->name('cashier-shifts.store');
    Route::put('/cashier-shifts/{cashierShift}/close', [CashierShiftController::class, 'close'])->name('cashier-shifts.close');

    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.role.update');
        Route::patch('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.password.update');
    });

    Route::middleware('role:admin,manager')->group(function () {
        // Product CRUD
        Route::resource('products', ProductController::class)->except(['show', 'destroy']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])
            ->middleware('role:admin')
            ->name('products.destroy');

        // Category CRUD
        Route::resource('categories', CategoryController::class)->except(['show', 'destroy']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
            ->middleware('role:admin')
            ->name('categories.destroy');

        // Ingredient inventory and recipes
        Route::resource('inventory', InventoryController::class)->except(['create', 'show', 'edit']);

        // Orders (admin)
        Route::resource('orders', OrderController::class)->only(['index', 'show']);

        // Customers and loyalty points
        Route::resource('customers', CustomerController::class)->only(['index', 'show']);

        // Promos
        Route::resource('promos', PromoController::class)->except(['destroy']);
        Route::delete('/promos/{promo}', [PromoController::class, 'destroy'])
            ->middleware('role:admin')
            ->name('promos.destroy');

        // Supplier and purchase management
        Route::resource('suppliers', SupplierController::class)->except(['create', 'show', 'edit']);
        Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);

        // Shop settings, activity logs, and backups
        Route::get('/shop-settings', [ShopSettingController::class, 'edit'])->name('shop-settings.edit');
        Route::put('/shop-settings', [ShopSettingController::class, 'update'])->name('shop-settings.update');
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
        Route::get('/backup/export/{type}', [BackupController::class, 'export'])->name('backup.export');

        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/products', [ReportController::class, 'products'])->name('reports.products');
        Route::get('/reports/daily-close', [ReportController::class, 'dailyClose'])->name('reports.daily-close');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    });

    Route::get('/api/promo/validate', [PromoController::class, 'validate'])->name('promo.validate');
    Route::post('/payment/process', [PaymentMethodController::class, 'process'])->name('payment.process');
});

Route::get('/debug-products', function () {
    if (request('code') !== 'debug123') {
        abort(403);
    }
    
    // Clear any previous test product
    App\Models\Product::where('name', 'Persisted Product')->delete();
    
    $product = App\Models\Product::create([
        'name' => 'Persisted Product',
        'price' => 1.5,
        'stock' => 100,
    ]);
    
    $retrieved = App\Models\Product::find($product->id);
    
    return [
        'created_attributes' => $product->getAttributes(),
        'retrieved_attributes' => $retrieved ? $retrieved->getAttributes() : null,
        'retrieved_stock_field' => $retrieved ? $retrieved->stock : null,
    ];
});
