<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplyCouponRequest;
use App\Models\User;
use App\Services\CartService;
use App\Services\CouponService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CouponController extends Controller
{
    public function __construct(private readonly CouponService $couponService) {}

    /**
     * Validate and store the coupon code in the session for the checkout preview.
     */
    public function apply(ApplyCouponRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $subtotal = $this->resolveSessionSubtotal($request);

        try {
            $coupon = $this->couponService->validateForCheckout(
                $request->string('coupon_code')->toString(),
                $user,
                $subtotal
            );
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->with('coupon_error', $e->errors()['coupon_code'][0] ?? 'Kupon tidak valid.');
        }

        session()->put('coupon_code', $coupon->code);

        return back()->with('coupon_success', "Kupon {$coupon->code} diterapkan.");
    }

    /**
     * Remove the applied coupon from the session.
     */
    public function remove(Request $request): RedirectResponse
    {
        session()->forget('coupon_code');

        return back()->with('coupon_removed', true);
    }

    /**
     * Resolve the current cart subtotal for coupon validation.
     */
    private function resolveSessionSubtotal(Request $request): float
    {
        $cart = app(CartService::class)->resolve($request);
        $cart->load('items');

        return (float) $cart->subtotal;
    }
}
