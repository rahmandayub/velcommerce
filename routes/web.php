<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

// ---------------------------------------------------------------------------
// Public catalog
// ---------------------------------------------------------------------------
Route::get('products', [CatalogController::class, 'index'])->name('products.index');
Route::get('products/{slug}', [CatalogController::class, 'show'])->name('products.show');

// ---------------------------------------------------------------------------
// Cart — accessible to guests (session_id) and authenticated users.
// ---------------------------------------------------------------------------
Route::get('cart', [CartController::class, 'index'])->name('cart.index');
Route::post('cart/items', [CartController::class, 'store'])->name('cart.items.store');
Route::patch('cart/items/{cartItem}', [CartController::class, 'update'])->name('cart.items.update');
Route::delete('cart/items/{cartItem}', [CartController::class, 'destroy'])->name('cart.items.destroy');

// ---------------------------------------------------------------------------
// Authenticated: addresses, checkout, orders
// ---------------------------------------------------------------------------
Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');

    // Addresses (also used from checkout)
    Route::get('addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::post('addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::patch('addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');

    // Checkout — 3 steps
    Route::get('checkout/address', [CheckoutController::class, 'address'])->name('checkout.address');
    Route::get('checkout/confirm', [CheckoutController::class, 'confirm'])->name('checkout.confirm');
    Route::post('checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Orders
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('orders/{order}/payment', [OrderController::class, 'payment'])->name('orders.payment');
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

// Mock-gateway callback — only in local/testing/staging
if (app()->environment(['local', 'testing', 'staging'])) {
    Route::post('orders/{order}/mock-callback', [OrderController::class, 'mockCallback'])
        ->middleware(['auth', 'verified'])
        ->name('orders.mock-callback');
}

// ---------------------------------------------------------------------------
// Admin (role:admin)
// ---------------------------------------------------------------------------
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', fn () => inertia('dashboard'))->name('dashboard');

    // Products CRUD — prefixed resource-ish
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('products/{product}/images', [ProductController::class, 'storeImage'])->name('products.images.store');
    Route::delete('products/{product}/images/{image}', [ProductController::class, 'destroyImage'])->name('products.images.destroy');

    // Orders management
    Route::get('orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.status');
});

require __DIR__.'/settings.php';
