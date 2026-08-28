<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'home')->name('home');

Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('robots.txt', [SitemapController::class, 'robots'])->name('robots');

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

    // Wishlist
    Route::get('wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('wishlist/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

    // Reviews
    Route::get('products/{product}/reviews', [ReviewController::class, 'index'])->name('products.reviews.index');
    Route::post('reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::patch('reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Coupons (apply/remove from session during checkout)
    Route::post('coupons/apply', [CouponController::class, 'apply'])->name('coupons.apply');
    Route::delete('coupons', [CouponController::class, 'remove'])->name('coupons.remove');
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
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

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

    // Coupons
    Route::get('coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
    Route::get('coupons/create', [AdminCouponController::class, 'create'])->name('coupons.create');
    Route::post('coupons', [AdminCouponController::class, 'store'])->name('coupons.store');
    Route::get('coupons/{coupon}/edit', [AdminCouponController::class, 'edit'])->name('coupons.edit');
    Route::put('coupons/{coupon}', [AdminCouponController::class, 'update'])->name('coupons.update');
    Route::delete('coupons/{coupon}', [AdminCouponController::class, 'destroy'])->name('coupons.destroy');
    Route::post('coupons/{coupon}/toggle', [AdminCouponController::class, 'toggle'])->name('coupons.toggle');

    // Reviews moderation
    Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::delete('reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
});

require __DIR__.'/settings.php';
