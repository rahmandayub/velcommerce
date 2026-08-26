<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;

test('core tables exist', function (): void {
    $tables = [
        'users',
        'categories',
        'products',
        'product_variants',
        'product_images',
        'addresses',
        'carts',
        'cart_items',
        'orders',
        'order_items',
        'roles',
        'permissions',
        'model_has_roles',
        'model_has_permissions',
        'role_has_permissions',
        'reviews',
        'wishlists',
        'coupons',
        'coupon_usages',
    ];

    foreach ($tables as $table) {
        expect(Schema::hasTable($table))->toBeTrue("Table {$table} should exist");
    }
});

test('reviews table has expected columns', function (): void {
    expect(Schema::hasColumns('reviews', [
        'id', 'user_id', 'product_id', 'order_id', 'rating',
        'title', 'body', 'is_approved',
    ]))->toBeTrue();
    expect(Schema::hasColumn('reviews', 'order_id'))->toBeTrue();
});

test('wishlists table has expected columns', function (): void {
    expect(Schema::hasColumns('wishlists', [
        'id', 'user_id', 'product_id',
    ]))->toBeTrue();
});

test('coupons table has expected columns', function (): void {
    expect(Schema::hasColumns('coupons', [
        'id', 'code', 'type', 'value', 'min_order_amount',
        'max_discount_amount', 'usage_limit', 'usage_count',
        'per_user_limit', 'is_active', 'starts_at', 'expires_at',
    ]))->toBeTrue();
});

test('coupon_usages table has expected columns', function (): void {
    expect(Schema::hasColumns('coupon_usages', [
        'id', 'coupon_id', 'user_id', 'order_id', 'discount_amount',
    ]))->toBeTrue();
});

test('orders table has coupon columns', function (): void {
    expect(Schema::hasColumns('orders', [
        'coupon_id', 'coupon_code', 'coupon_discount',
    ]))->toBeTrue();
});

test('categories table has expected columns', function (): void {
    expect(Schema::hasColumns('categories', ['id', 'name', 'slug', 'parent_id', 'is_active', 'sort_order']))->toBeTrue();
    expect(Schema::hasColumn('categories', 'description'))->toBeTrue();
});

test('products table has expected columns', function (): void {
    expect(Schema::hasColumns('products', [
        'id',
        'category_id',
        'name',
        'slug',
        'price',
        'sku',
        'stock',
        'is_active',
    ]))->toBeTrue();
});

test('product variants table has expected columns', function (): void {
    expect(Schema::hasColumns('product_variants', [
        'id',
        'product_id',
        'sku',
        'price',
        'stock',
        'attributes',
    ]))->toBeTrue();
});

test('carts table supports guest and user', function (): void {
    $cart = Cart::create(['session_id' => 'test-session-123']);
    expect($cart->session_id)->toBe('test-session-123');
    expect($cart->user_id)->toBeNull();

    // user cart
    $user = User::factory()->create();
    $cart2 = Cart::create(['user_id' => $user->id]);
    expect($cart2->user_id)->toBe($user->id);
});

test('cart items unique constraint prevents duplicate product variant in same cart', function (): void {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id]);
    $variant = ProductVariant::factory()->create(['product_id' => $product->id]);
    $cart = Cart::create(['session_id' => 'sess-'.uniqid()]);

    CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'product_variant_id' => $variant->id,
        'quantity' => 1,
        'price' => $variant->price ?? $product->price,
    ]);

    expect(fn () => CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'product_variant_id' => $variant->id,
        'quantity' => 2,
        'price' => $variant->price ?? $product->price,
    ]))->toThrow(QueryException::class);
});

test('cart items with null variant are handled via application logic', function (): void {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id]);
    $cart = Cart::create(['session_id' => 'sess-'.uniqid()]);

    CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'product_variant_id' => null,
        'quantity' => 1,
        'price' => $product->price,
    ]);

    // In SQLite/MySQL nulls are distinct for unique, so DB won't prevent duplicate nulls.
    // Application layer should handle this via validation/upsert.
    // Here we just verify that first item was created.
    expect($cart->items()->count())->toBe(1);
});

test('orders and order items can be created with snapshot data', function (): void {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id]);

    $order = Order::create([
        'user_id' => $user->id,
        'order_number' => 'VEL-'.now()->format('Ymd').'-TEST',
        'status' => 'pending',
        'payment_status' => 'unpaid',
        'subtotal' => '100.00',
        'total' => '100.00',
    ]);

    expect($order->order_number)->toStartWith('VEL-');

    $item = OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'price' => $product->price,
        'quantity' => 2,
        'subtotal' => number_format((float) $product->price * 2, 2, '.', ''),
    ]);

    expect($item->product_name)->toBe($product->name);
    expect((int) $item->quantity)->toBe(2);
});

test('category self-referential parent relationship works', function (): void {
    $parent = Category::factory()->create();
    $child = Category::factory()->create(['parent_id' => $parent->id]);

    expect($child->parent->id)->toBe($parent->id);
    expect($parent->children->contains($child))->toBeTrue();
});

test('product factory creates valid product', function (): void {
    $product = Product::factory()->create();

    expect($product->slug)->not->toBeEmpty();
    expect($product->sku)->not->toBeEmpty();
    expect((float) $product->price)->toBeGreaterThan(0);
});

test('foreign key cascade on category delete nulls product category', function (): void {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id]);

    $category->delete();

    expect($product->fresh()->category_id)->toBeNull();
});
