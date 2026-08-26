<?php

use App\Enums\CouponType;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Product;
use App\Models\User;
use App\Services\CheckoutService;
use App\Services\CouponService;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    foreach (['manage products', 'manage orders', 'manage users', 'view analytics', 'manage categories'] as $name) {
        Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
    }

    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
    Role::findByName('admin')
        ->syncPermissions(Permission::all());

    $this->user = User::factory()->create();
    $this->user->assignRole('customer');

    $this->address = Address::create([
        'user_id' => $this->user->id,
        'label' => 'Rumah',
        'recipient_name' => $this->user->name,
        'phone' => '08123456789',
        'street' => 'Jl. Test',
        'district' => 'Kec. Test',
        'city' => 'Jakarta',
        'province' => 'DKI Jakarta',
        'postal_code' => '12345',
    ]);

    $this->product = Product::factory()->create(['price' => 100000, 'stock' => 10]);
    $this->cart = Cart::create(['user_id' => $this->user->id]);
    CartItem::create([
        'cart_id' => $this->cart->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
        'price' => $this->product->price,
    ]);
});

test('percent coupon calculates discount capped by max', function (): void {
    $coupon = Coupon::factory()->percent()->create([
        'value' => 10,
        'min_order_amount' => 0,
        'max_discount_amount' => 5000,
    ]);

    expect($coupon->calculateDiscount(100000))->toBe(5000.0);
});

test('fixed coupon returns value capped at subtotal', function (): void {
    $coupon = Coupon::factory()->fixed()->create(['value' => 20000]);

    expect($coupon->calculateDiscount(100000))->toBe(20000.0);
    expect($coupon->calculateDiscount(10000))->toBe(10000.0);
});

test('validateForCheckout throws for expired coupon', function (): void {
    $coupon = Coupon::factory()->expired()->create();

    $this->expectException(ValidationException::class);
    app(CouponService::class)->validateForCheckout($coupon->code, $this->user, 100000);
});

test('validateForCheckout throws when minimum not met', function (): void {
    $coupon = Coupon::factory()->percent()->create([
        'value' => 10,
        'min_order_amount' => 500000,
    ]);

    $this->expectException(ValidationException::class);
    app(CouponService::class)->validateForCheckout($coupon->code, $this->user, 100000);
});

test('checkout applies coupon discount and increments usage atomically', function (): void {
    $coupon = Coupon::factory()->percent()->create([
        'value' => 10,
        'min_order_amount' => 0,
    ]);

    $order = app(CheckoutService::class)->placeOrder(
        $this->user,
        $this->address,
        $this->cart,
        ['coupon_code' => $coupon->code],
    );

    expect((float) $order->discount)->toBe(10000.0);
    expect((float) $order->coupon_discount)->toBe(10000.0);
    expect($order->coupon_code)->toBe($coupon->code);
    expect($coupon->fresh()->usage_count)->toBe(1);
    expect(
        CouponUsage::where('order_id', $order->id)->count(),
    )->toBe(1);
});

test('per-user limit prevents reusing coupon on a second order', function (): void {
    $coupon = Coupon::factory()->percent()->create([
        'value' => 10,
        'min_order_amount' => 0,
        'per_user_limit' => 1,
    ]);

    // First order succeeds.
    app(CheckoutService::class)->placeOrder(
        $this->user,
        $this->address,
        $this->cart,
        ['coupon_code' => $coupon->code],
    );

    // Re-add to cart and try again — should throw.
    CartItem::create([
        'cart_id' => $this->cart->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
        'price' => $this->product->price,
    ]);

    $this->expectException(ValidationException::class);
    app(CheckoutService::class)->placeOrder(
        $this->user,
        $this->address,
        $this->cart,
        ['coupon_code' => $coupon->code],
    );
});

test('guest cannot apply a coupon', function (): void {
    $coupon = Coupon::factory()->percent()->create(['value' => 10]);

    $response = $this->post(route('coupons.apply'), ['coupon_code' => $coupon->code]);
    $response->assertRedirect(route('login'));
});

test('authenticated user can apply and remove a coupon from session', function (): void {
    $coupon = Coupon::factory()->percent()->create(['value' => 10]);

    $response = $this->actingAs($this->user)
        ->post(route('coupons.apply'), ['coupon_code' => $coupon->code]);

    $response->assertSessionHas('coupon_code', $coupon->code);

    $this->actingAs($this->user)->delete(route('coupons.remove'));
    expect(Session::get('coupon_code'))->toBeNull();
});

test('applying an invalid coupon returns an error', function (): void {
    $response = $this->actingAs($this->user)
        ->post(route('coupons.apply'), ['coupon_code' => 'NOPE']);

    $response->assertSessionMissing('coupon_code');
    $response->assertSessionHasErrors('coupon_code');
});

test('non-admin cannot create coupons', function (): void {
    $response = $this->actingAs($this->user)->post(route('admin.coupons.store'), [
        'code' => 'TEST123',
        'type' => 'percent',
        'value' => 10,
    ]);

    $response->assertForbidden();
    expect(Coupon::where('code', 'TEST123')->exists())->toBeFalse();
});

test('admin can create and toggle a coupon', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->post(route('admin.coupons.store'), [
        'code' => 'ADMIN10',
        'type' => 'percent',
        'value' => 10,
        'min_order_amount' => 0,
    ]);

    $response->assertRedirect(route('admin.coupons.index'));
    $coupon = Coupon::where('code', 'ADMIN10')->firstOrFail();
    expect($coupon->type)->toBe(CouponType::Percent);

    $this->actingAs($admin)->post(route('admin.coupons.toggle', $coupon));
    expect($coupon->fresh()->is_active)->toBeFalse();
});
