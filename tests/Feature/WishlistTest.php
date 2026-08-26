<?php

use App\Models\Product;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
});

test('guest cannot add to wishlist', function (): void {
    $product = Product::factory()->create();

    $response = $this->post(route('wishlist.store'), ['product_id' => $product->id]);
    $response->assertRedirect(route('login'));
});

test('user can wishlist a product', function (): void {
    $product = Product::factory()->create();
    $user = User::factory()->create();
    $user->assignRole('customer');

    $response = $this->actingAs($user)->post(route('wishlist.store'), [
        'product_id' => $product->id,
    ]);

    $response->assertRedirect();
    expect($user->wishlists()->where('product_id', $product->id)->exists())->toBeTrue();
});

test('wishlist toggle is idempotent — second post removes', function (): void {
    $product = Product::factory()->create();
    $user = User::factory()->create();
    $user->assignRole('customer');

    $this->actingAs($user)->post(route('wishlist.store'), ['product_id' => $product->id]);
    expect($user->wishlists()->where('product_id', $product->id)->exists())->toBeTrue();

    $this->actingAs($user)->post(route('wishlist.store'), ['product_id' => $product->id]);
    expect($user->wishlists()->where('product_id', $product->id)->exists())->toBeFalse();
});

test('user can remove an item via the destroy route', function (): void {
    $product = Product::factory()->create();
    $user = User::factory()->create();
    $user->assignRole('customer');

    $wishlist = $user->wishlists()->create(['product_id' => $product->id]);

    $response = $this->actingAs($user)->delete(route('wishlist.destroy', $wishlist));
    $response->assertRedirect();
    expect($user->wishlists()->where('product_id', $product->id)->exists())->toBeFalse();
});

test('user cannot delete another user wishlist entry', function (): void {
    $product = Product::factory()->create();
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $owner->assignRole('customer');
    $other->assignRole('customer');

    $wishlist = $owner->wishlists()->create(['product_id' => $product->id]);

    $response = $this->actingAs($other)->delete(route('wishlist.destroy', $wishlist));
    $response->assertNotFound();
});

test('wishlist index pages and includes product image', function (): void {
    $product = Product::factory()->create();
    $user = User::factory()->create();
    $user->assignRole('customer');
    $user->wishlists()->create(['product_id' => $product->id]);

    $response = $this->actingAs($user)->get(route('wishlist.index'));
    $response->assertOk();
    $response->assertInertia(function ($page): void {
        $page->component('wishlist/index');
        $page->has('items');
    });
});

test('wishlistCount is present in Inertia shared props', function (): void {
    $product = Product::factory()->create();
    $user = User::factory()->create();
    $user->assignRole('customer');
    $user->wishlists()->create(['product_id' => $product->id]);

    $response = $this->actingAs($user)->get('/');
    $response->assertInertia(function ($page): void {
        $page->where('wishlistCount', 1);
    });
});
