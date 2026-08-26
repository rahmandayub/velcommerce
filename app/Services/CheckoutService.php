<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\InsufficientStockException;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    /**
     * Create an order from the cart, validating stock under row locks.
     *
     * @throws InsufficientStockException
     * @throws ValidationException
     */
    public function placeOrder(User $user, Address $address, Cart $cart, array $data = []): Order
    {
        $cart->load(['items.product', 'items.variant']);

        if ($cart->items->isEmpty()) {
            throw new \LogicException('Cart is empty.');
        }

        return DB::transaction(function () use ($user, $address, $cart, $data): Order {
            $subtotal = 0;
            $itemsData = [];

            foreach ($cart->items as $cartItem) {
                $product = Product::query()
                    ->whereKey($cartItem->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $variant = null;

                if ($cartItem->product_variant_id !== null) {
                    $variant = ProductVariant::query()
                        ->whereKey($cartItem->product_variant_id)
                        ->lockForUpdate()
                        ->firstOrFail();
                }

                $availableStock = $variant?->stock ?? $product->stock;

                if ($availableStock < $cartItem->quantity) {
                    throw new InsufficientStockException($product, $variant);
                }

                $price = $variant?->effective_price ?? $product->price;
                $lineSubtotal = (float) $price * $cartItem->quantity;
                $subtotal += $lineSubtotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'product_name' => $product->name,
                    'variant_name' => $variant?->name,
                    'sku' => $variant?->sku ?? $product->sku,
                    'price' => $price,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => number_format($lineSubtotal, 2, '.', ''),
                    'attributes' => $variant?->attributes,
                ];
            }

            $shippingCost = (float) config('shop.shipping_cost', 15000);

            // Resolve and validate the coupon under a row lock so usage limits
            // and per-user limits are enforced atomically with order creation.
            $coupon = null;
            $discount = 0;

            if (! empty($data['coupon_code'])) {
                /** @var Coupon|null $coupon */
                $coupon = Coupon::query()
                    ->where('code', Str::upper(trim((string) $data['coupon_code'])))
                    ->lockForUpdate()
                    ->first();

                if ($coupon === null) {
                    throw ValidationException::withMessages([
                        'coupon_code' => 'Kupon tidak ditemukan.',
                    ]);
                }

                if (! $coupon->isCurrentlyValid()
                    || ! $coupon->hasUsagesRemaining()
                    || ! $coupon->canBeUsedBy($user)
                    || ! $coupon->meetsMinimum($subtotal)) {
                    throw ValidationException::withMessages([
                        'coupon_code' => 'Kupon tidak dapat digunakan untuk pesanan ini.',
                    ]);
                }

                $discount = $coupon->calculateDiscount($subtotal);
                $coupon->increment('usage_count');
            }

            $tax = 0;
            $total = $subtotal + $shippingCost - $discount + $tax;

            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $address->id,
                'status' => OrderStatus::Pending,
                'payment_status' => PaymentStatus::Unpaid,
                'payment_method' => $data['payment_method'] ?? 'mock',
                'shipping_method' => $data['shipping_method'] ?? null,
                'shipping_cost' => number_format($shippingCost, 2, '.', ''),
                'subtotal' => number_format($subtotal, 2, '.', ''),
                'discount' => number_format($discount, 2, '.', ''),
                'coupon_id' => $coupon?->id,
                'coupon_code' => $coupon?->code,
                'coupon_discount' => number_format($discount, 2, '.', ''),
                'tax' => number_format($tax, 2, '.', ''),
                'total' => number_format($total, 2, '.', ''),
                'notes' => $data['notes'] ?? null,
            ]);

            if ($coupon !== null) {
                $coupon->usages()->create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'discount_amount' => number_format($discount, 2, '.', ''),
                ]);
            }

            foreach ($itemsData as $row) {
                $order->items()->create($row);
            }

            // Decrement stock.
            foreach ($cart->items as $cartItem) {
                if ($cartItem->product_variant_id !== null) {
                    ProductVariant::query()
                        ->whereKey($cartItem->product_variant_id)
                        ->decrement('stock', $cartItem->quantity);
                } else {
                    Product::query()
                        ->whereKey($cartItem->product_id)
                        ->decrement('stock', $cartItem->quantity);
                }
            }

            $cart->items()->delete();

            return $order->load(['items', 'address']);
        });
    }
}
