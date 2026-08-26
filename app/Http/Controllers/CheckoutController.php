<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\PlaceOrderRequest;
use App\Models\User;
use App\Notifications\OrderPlacedNotification;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\CouponService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly CheckoutService $checkoutService,
        private readonly CouponService $couponService,
    ) {}

    public function address(Request $request): Response
    {
        $cart = $this->cartService->resolve($request);
        $cart->load(['items.product.images', 'items.variant']);

        if ($cart->items->isEmpty()) {
            return Inertia::render('checkout/empty');
        }

        $addresses = $request->user()->addresses()->latest()->get();
        $couponPreview = $this->resolveCouponPreview($request);

        return Inertia::render('checkout/address', [
            'addresses' => $addresses->map(fn ($a): array => [
                'id' => $a->id,
                'label' => $a->label,
                'recipient_name' => $a->recipient_name,
                'phone' => $a->phone,
                'full_address' => $a->full_address,
                'street' => $a->street,
                'village' => $a->village,
                'district' => $a->district,
                'city' => $a->city,
                'province' => $a->province,
                'postal_code' => $a->postal_code,
                'is_default' => (bool) $a->is_default,
            ])->all(),
            'cart' => [
                'items' => $cart->items->map(fn ($item): array => [
                    'id' => $item->id,
                    'product_name' => $item->product?->name,
                    'variant_name' => $item->variant?->name,
                    'price' => (float) $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => (float) $item->subtotal,
                    'image' => $item->product?->images->first()?->url,
                ])->values()->all(),
                'subtotal' => (float) $cart->subtotal,
                'shipping_cost' => (float) config('shop.shipping_cost', 15000),
            ],
            'coupon' => $couponPreview,
        ]);
    }

    public function confirm(Request $request): Response|RedirectResponse
    {
        $cart = $this->cartService->resolve($request);
        $cart->load(['items.product.images', 'items.variant']);

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $addresses = $request->user()->addresses()->latest()->get();

        if ($addresses->isEmpty()) {
            return redirect()->route('checkout.address');
        }

        $default = $addresses->firstWhere('is_default', true);
        $selectedAddressId = $request->query('address_id')
            ? (int) $request->query('address_id')
            : ($default !== null ? $default->id : $addresses->first()->id);

        $selectedAddress = $addresses->firstWhere('id', $selectedAddressId) ?? $addresses->first();

        $subtotal = (float) $cart->subtotal;
        $shippingCost = (float) config('shop.shipping_cost', 15000);
        $couponPreview = $this->resolveCouponPreview($request);
        $discount = $couponPreview['discount'] ?? 0;
        $total = max(0, $subtotal + $shippingCost - $discount);

        return Inertia::render('checkout/confirm', [
            'address' => [
                'id' => $selectedAddress->id,
                'label' => $selectedAddress->label,
                'recipient_name' => $selectedAddress->recipient_name,
                'phone' => $selectedAddress->phone,
                'full_address' => $selectedAddress->full_address,
            ],
            'cart' => [
                'items' => $cart->items->map(fn ($item): array => [
                    'id' => $item->id,
                    'product_name' => $item->product?->name,
                    'variant_name' => $item->variant?->name,
                    'price' => (float) $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => (float) $item->subtotal,
                    'image' => $item->product?->images->first()?->url,
                ])->values()->all(),
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'total' => $total,
            ],
            'coupon' => $couponPreview,
        ]);
    }

    public function store(PlaceOrderRequest $request): RedirectResponse
    {
        $cart = $this->cartService->resolve($request);
        $address = $request->user()->addresses()->findOrFail($request->integer('address_id'));

        $data = $request->validated();
        $data['coupon_code'] = session('coupon_code');

        try {
            $order = $this->checkoutService->placeOrder(
                $request->user(),
                $address,
                $cart,
                $data,
            );
        } catch (InsufficientStockException $e) {
            throw ValidationException::withMessages([
                'cart' => $e->getMessage(),
            ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        session()->forget('coupon_code');

        $order->user->notify(new OrderPlacedNotification($order));

        return redirect()->route('orders.payment', ['order' => $order->order_number]);
    }

    /**
     * Resolve the applied coupon from the session and compute its discount
     * against the current cart subtotal (without incrementing usage).
     *
     * @return array{code: string, type: string, value: float, discount: float}|null
     */
    private function resolveCouponPreview(Request $request): ?array
    {
        $code = session('coupon_code');

        if (blank($code)) {
            return null;
        }

        /** @var User $user */
        $user = $request->user();
        $subtotal = (float) $this->cartService->resolve($request)->load('items')->subtotal;

        try {
            $coupon = $this->couponService->validateForCheckout($code, $user, $subtotal);
        } catch (ValidationException) {
            session()->forget('coupon_code');

            return null;
        }

        return [
            'code' => $coupon->code,
            'type' => $coupon->type->value,
            'value' => (float) $coupon->value,
            'discount' => $coupon->calculateDiscount($subtotal),
        ];
    }
}
