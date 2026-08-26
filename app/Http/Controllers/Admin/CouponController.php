<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CouponController extends Controller
{
    public function index(Request $request): Response
    {
        $coupons = Coupon::query()
            ->latest('id')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Coupon $coupon): array => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'type' => $coupon->type->value,
                'value' => (float) $coupon->value,
                'min_order_amount' => (float) $coupon->min_order_amount,
                'max_discount_amount' => $coupon->max_discount_amount !== null ? (float) $coupon->max_discount_amount : null,
                'usage_limit' => $coupon->usage_limit,
                'usage_count' => $coupon->usage_count,
                'per_user_limit' => $coupon->per_user_limit,
                'is_active' => (bool) $coupon->is_active,
                'starts_at' => $coupon->starts_at?->toIso8601String(),
                'expires_at' => $coupon->expires_at?->toIso8601String(),
                'created_at' => $coupon->created_at->toIso8601String(),
            ]);

        return Inertia::render('admin/coupons/index', [
            'coupons' => $coupons,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/coupons/form', [
            'coupon' => null,
        ]);
    }

    public function store(StoreCouponRequest $request): RedirectResponse
    {
        Coupon::create($request->validated());

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Kupon dibuat.');
    }

    public function edit(Coupon $coupon): Response
    {
        return Inertia::render('admin/coupons/form', [
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'type' => $coupon->type->value,
                'value' => (float) $coupon->value,
                'min_order_amount' => (float) $coupon->min_order_amount,
                'max_discount_amount' => $coupon->max_discount_amount !== null ? (float) $coupon->max_discount_amount : null,
                'usage_limit' => $coupon->usage_limit,
                'per_user_limit' => $coupon->per_user_limit,
                'is_active' => (bool) $coupon->is_active,
                'starts_at' => $coupon->starts_at?->toIso8601String(),
                'expires_at' => $coupon->expires_at?->toIso8601String(),
            ],
        ]);
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update($request->validated());

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Kupon diperbarui.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Kupon dihapus.');
    }

    public function toggle(Request $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update(['is_active' => ! $coupon->is_active]);

        return back()->with('success', 'Status kupon diperbarui.');
    }
}
