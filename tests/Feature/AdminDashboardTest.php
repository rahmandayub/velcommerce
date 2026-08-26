<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

    foreach (['manage products', 'manage orders', 'manage users', 'view analytics', 'manage categories'] as $name) {
        Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
    }

    Role::findByName('admin')->syncPermissions(Permission::all());
});

test('guest is redirected to login when accessing admin dashboard', function (): void {
    $response = $this->get('/admin');
    $response->assertRedirect(route('login'));
});

test('customer cannot access admin dashboard', function (): void {
    $user = User::factory()->create();
    $user->assignRole('customer');

    $response = $this->actingAs($user)->get('/admin');
    $response->assertForbidden();
});

test('admin can access dashboard and sees chart and KPI props', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $product = Product::factory()->create();
    $order = Order::factory()->paid()->create(['total' => 100000]);
    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'quantity' => 2,
        'subtotal' => 100000,
    ]);

    $response = $this->actingAs($admin)->get('/admin');
    $response->assertOk();
    $response->assertInertia(function ($page): void {
        $page->component('admin/dashboard');
        $page->has('range');
        $page->has('kpis');
        $page->has('chartData');
        $page->has('topProducts');
        $page->has('lowStock');
    });
});

test('admin dashboard accepts the range param', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get('/admin?range=7d');
    $response->assertOk();
    $response->assertInertia(function ($page): void {
        $page->where('range', '7d');
    });
});

test('admin dashboard top products are sorted by quantity descending', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $productA = Product::factory()->create();
    $productB = Product::factory()->create();

    $order1 = Order::factory()->paid()->create();
    $order2 = Order::factory()->paid()->create();

    OrderItem::factory()->create([
        'order_id' => $order1->id,
        'product_id' => $productA->id,
        'product_name' => $productA->name,
        'quantity' => 10,
        'subtotal' => 100000,
    ]);
    OrderItem::factory()->create([
        'order_id' => $order2->id,
        'product_id' => $productB->id,
        'product_name' => $productB->name,
        'quantity' => 2,
        'subtotal' => 20000,
    ]);

    $response = $this->actingAs($admin)->get('/admin');
    $response->assertInertia(function ($page) use ($productA): void {
        $page->where('topProducts.0.product_id', $productA->id);
    });
});
