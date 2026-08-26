<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
});

function createBuyerWithPaidOrderFor(Product $product, User $user): Order
{
    $order = Order::factory()->paid()->create(['user_id' => $user->id]);
    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
    ]);

    return $order;
}

test('guest cannot create a review', function (): void {
    $product = Product::factory()->create();

    $response = $this->post(route('reviews.store'), [
        'product_id' => $product->id,
        'rating' => 5,
    ]);

    $response->assertRedirect(route('login'));
});

test('non-buyer cannot review', function (): void {
    $product = Product::factory()->create();
    $user = User::factory()->create();
    $user->assignRole('customer');

    $response = $this->actingAs($user)->post(route('reviews.store'), [
        'product_id' => $product->id,
        'rating' => 5,
    ]);

    $response->assertForbidden();
});

test('buyer can create a review', function (): void {
    $product = Product::factory()->create();
    $user = User::factory()->create();
    $user->assignRole('customer');
    createBuyerWithPaidOrderFor($product, $user);

    $response = $this->actingAs($user)->post(route('reviews.store'), [
        'product_id' => $product->id,
        'rating' => 5,
        'title' => 'Sangat bagus',
        'body' => 'Pengiriman cepat, barang sesuai foto.',
    ]);

    $response->assertRedirect();
    expect($product->reviews()->where('user_id', $user->id)->exists())->toBeTrue();
});

test('user cannot review the same product twice', function (): void {
    $product = Product::factory()->create();
    $user = User::factory()->create();
    $user->assignRole('customer');
    createBuyerWithPaidOrderFor($product, $user);

    Review::factory()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'rating' => 4,
    ]);

    $response = $this->actingAs($user)->post(route('reviews.store'), [
        'product_id' => $product->id,
        'rating' => 5,
    ]);

    $response->assertSessionHasErrors('review');
});

test('user cannot update another user review', function (): void {
    $product = Product::factory()->create();
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $owner->assignRole('customer');
    $other->assignRole('customer');

    $review = Review::factory()->create([
        'user_id' => $owner->id,
        'product_id' => $product->id,
    ]);

    $response = $this->actingAs($other)->patch(route('reviews.update', $review), [
        'rating' => 5,
        'title' => 'Hacked',
    ]);

    $response->assertForbidden();
});

test('admin can delete any review', function (): void {
    $product = Product::factory()->create();
    $user1 = User::factory()->create();
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $review = Review::factory()->create([
        'user_id' => $user1->id,
        'product_id' => $product->id,
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.reviews.destroy', $review));
    $response->assertRedirect();
    expect(Review::whereKey($review->id)->exists())->toBeFalse();
});

test('rating validation rejects out-of-range values', function (): void {
    $product = Product::factory()->create();
    $user = User::factory()->create();
    $user->assignRole('customer');
    createBuyerWithPaidOrderFor($product, $user);

    $response = $this->actingAs($user)->post(route('reviews.store'), [
        'product_id' => $product->id,
        'rating' => 10,
    ]);

    $response->assertSessionHasErrors('rating');
});

test('product detail lists rating aggregates and can_review flag', function (): void {
    $product = Product::factory()->create();
    $buyer = User::factory()->create();
    $buyer->assignRole('customer');
    createBuyerWithPaidOrderFor($product, $buyer);

    Review::factory()->create([
        'product_id' => $product->id,
        'user_id' => $buyer->id,
        'rating' => 4,
    ]);

    $response = $this->actingAs($buyer)->get(route('products.show', $product->slug));
    $response->assertOk();
    $response->assertInertia(function ($page): void {
        $page->component('products/show');
        $page->has('product');
        $page->where('product.average_rating', 4);
        $page->where('product.reviews_count', 1);
        $page->where('product.can_review', false);
    });
});
