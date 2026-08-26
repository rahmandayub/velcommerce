<?php

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderPlacedNotification;
use App\Notifications\OrderStatusUpdatedNotification;
use App\Notifications\PaymentStatusNotification;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

    foreach (['manage products', 'manage orders', 'manage users', 'view analytics', 'manage categories'] as $name) {
        Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
    }

    $adminRole = Role::findByName('admin');
    $adminRole->syncPermissions(Permission::all());
});

test('order placed dispatches OrderPlacedNotification', function (): void {
    Notification::fake();

    $user = User::factory()->create();
    $user->assignRole('customer');
    $address = Address::create([
        'user_id' => $user->id,
        'label' => 'Rumah',
        'recipient_name' => $user->name,
        'phone' => '08123456789',
        'street' => 'Jl. Test',
        'district' => 'Kec. Test',
        'city' => 'Jakarta',
        'province' => 'DKI Jakarta',
        'postal_code' => '12345',
    ]);
    $product = Product::factory()->create(['price' => 50000, 'stock' => 10]);
    $cart = Cart::create(['user_id' => $user->id]);
    CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => $product->price,
    ]);

    $response = $this->actingAs($user)->post(route('checkout.store'), [
        'address_id' => $address->id,
    ]);

    $response->assertRedirect();
    Notification::assertSentTo($user, OrderPlacedNotification::class);
});

test('mock callback paid dispatches PaymentStatusNotification', function (): void {
    Notification::fake();

    $user = User::factory()->create();
    $user->assignRole('customer');
    $order = Order::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->post(
        route('orders.mock-callback', $order->order_number),
        ['outcome' => 'paid'],
    );

    $response->assertRedirect(route('orders.show', $order->order_number));
    Notification::assertSentTo($user, PaymentStatusNotification::class, function ($n): bool {
        return $n->outcome === 'paid';
    });
});

test('mock callback failed dispatches PaymentStatusNotification as failed', function (): void {
    Notification::fake();

    $user = User::factory()->create();
    $user->assignRole('customer');
    $order = Order::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)->post(
        route('orders.mock-callback', $order->order_number),
        ['outcome' => 'failed'],
    );

    Notification::assertSentTo($user, PaymentStatusNotification::class, function ($n): bool {
        return $n->outcome === 'failed';
    });
});

test('admin shipping an order dispatches OrderStatusUpdatedNotification', function (): void {
    Notification::fake();

    $customer = User::factory()->create();
    $customer->assignRole('customer');
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $order = Order::factory()->create([
        'user_id' => $customer->id,
        'status' => 'paid',
        'payment_status' => 'paid',
    ]);

    $response = $this->actingAs($admin)->post(
        route('admin.orders.status', $order),
        ['status' => 'shipped'],
    );

    $response->assertRedirect();
    Notification::assertSentTo(
        $customer,
        OrderStatusUpdatedNotification::class,
    );
});

test('customer cancel dispatches OrderStatusUpdatedNotification as cancelled', function (): void {
    Notification::fake();

    $user = User::factory()->create();
    $user->assignRole('customer');
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($user)->post(
        route('orders.cancel', $order->order_number),
    );

    $response->assertRedirect(route('orders.show', $order->order_number));
    Notification::assertSentTo(
        $user,
        OrderStatusUpdatedNotification::class,
    );
});
