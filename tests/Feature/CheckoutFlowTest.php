<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    foreach (['manage products', 'manage orders', 'manage users', 'view analytics', 'manage categories'] as $name) {
        Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
    }

    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
    Role::findByName('admin')->syncPermissions(Permission::all());

    $this->user = User::factory()->create();
    $this->user->assignRole('customer');

    $this->address = Address::create([
        'user_id' => $this->user->id,
        'label' => 'Rumah',
        'recipient_name' => $this->user->name,
        'phone' => '08123456789',
        'street' => 'Jl. Test 123',
        'district' => 'Kec. Test',
        'city' => 'Jakarta',
        'province' => 'DKI Jakarta',
        'postal_code' => '12345',
    ]);

    $this->product = Product::factory()->create(['price' => 100000, 'stock' => 10, 'is_active' => true]);
    $this->cart = Cart::create(['user_id' => $this->user->id]);
    CartItem::create([
        'cart_id' => $this->cart->id,
        'product_id' => $this->product->id,
        'quantity' => 2,
        'price' => $this->product->price,
    ]);
});

test('guest is redirected to login when accessing checkout address', function (): void {
    $response = $this->get(route('checkout.address'));
    $response->assertRedirect(route('login'));
});

test('unverified user is redirected to verification notice when accessing checkout', function (): void {
    $unverified = User::factory()->unverified()->create();
    $unverified->assignRole('customer');

    $response = $this->actingAs($unverified)->get(route('checkout.address'));
    $response->assertRedirect(route('verification.notice'));
});

test('authenticated verified customer can view checkout address page', function (): void {
    $response = $this->actingAs($this->user)->get(route('checkout.address'));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('checkout/address'));
});

test('checkout confirm redirects to cart when cart is empty', function (): void {
    $this->cart->items()->delete();

    $response = $this->actingAs($this->user)->get(route('checkout.confirm'));
    $response->assertRedirect(route('cart.index'));
});

test('customer can complete checkout happy path and order appears in history', function (): void {
    Notification::fake();

    $response = $this->actingAs($this->user)->post(route('checkout.store'), [
        'address_id' => $this->address->id,
    ]);

    $order = Order::where('user_id', $this->user->id)->latest('id')->firstOrFail();

    $response->assertRedirect(route('orders.payment', ['order' => $order->order_number]));

    expect($order->status)->toBe(OrderStatus::Pending);
    expect($order->payment_status)->toBe(PaymentStatus::Unpaid);
    expect((float) $order->subtotal)->toBe(200000.0);
    expect((float) $order->total)->toBeGreaterThan(200000.0); // includes shipping
    expect($order->items)->toHaveCount(1);
    expect($this->cart->fresh()->items)->toHaveCount(0);
    expect((int) $this->product->fresh()->stock)->toBe(8);

    // Order appears in history
    $history = $this->actingAs($this->user)->get(route('orders.index'));
    $history->assertOk();
});

test('checkout fails with insufficient stock and returns validation error', function (): void {
    // Set stock to 1 but cart has 2
    $this->product->update(['stock' => 1]);

    $response = $this->actingAs($this->user)->post(route('checkout.store'), [
        'address_id' => $this->address->id,
    ]);

    $response->assertSessionHasErrors('cart');
    expect(Order::where('user_id', $this->user->id)->count())->toBe(0);
    // Stock unchanged
    expect((int) $this->product->fresh()->stock)->toBe(1);
});

test('checkout rejects invalid coupon from session', function (): void {
    // Simulate applied coupon that is expired
    $coupon = Coupon::factory()->expired()->create(['code' => 'EXPIRED10']);

    $response = $this->actingAs($this->user)
        ->withSession(['coupon_code' => $coupon->code])
        ->post(route('checkout.store'), [
            'address_id' => $this->address->id,
        ]);

    $response->assertSessionHasErrors('coupon_code');
    expect(Order::where('user_id', $this->user->id)->count())->toBe(0);
});

test('checkout applies valid coupon discount from session', function (): void {
    $coupon = Coupon::factory()->percent()->create([
        'value' => 10,
        'min_order_amount' => 0,
    ]);

    $response = $this->actingAs($this->user)
        ->withSession(['coupon_code' => $coupon->code])
        ->post(route('checkout.store'), [
            'address_id' => $this->address->id,
        ]);

    $order = Order::where('user_id', $this->user->id)->latest('id')->firstOrFail();

    $response->assertRedirect(route('orders.payment', ['order' => $order->order_number]));
    expect((float) $order->discount)->toBe(20000.0);
    expect($order->coupon_code)->toBe($coupon->code);
});

test('mock callback transitions order to paid', function (): void {
    $order = $this->actingAs($this->user)->post(route('checkout.store'), [
        'address_id' => $this->address->id,
    ]);

    $created = Order::where('user_id', $this->user->id)->latest('id')->firstOrFail();
    expect($created->payment_status)->toBe(PaymentStatus::Unpaid);

    $response = $this->actingAs($this->user)->post(route('orders.mock-callback', ['order' => $created->order_number]), [
        'outcome' => 'paid',
    ]);

    $response->assertRedirect(route('orders.show', ['order' => $created->order_number]));
    expect($created->fresh()->payment_status)->toBe(PaymentStatus::Paid);
});
