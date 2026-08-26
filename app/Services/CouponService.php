<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CouponService
{
    /**
     * Find a coupon by (normalized) code or null.
     */
    public function findByCode(?string $code): ?Coupon
    {
        if (blank($code)) {
            return null;
        }

        $normalized = Str::upper(trim($code));

        return Coupon::where('code', $normalized)->first();
    }

    /**
     * Validate the coupon for the given user and cart subtotal.
     *
     * @throws ValidationException
     */
    public function validateForCheckout(?string $code, User $user, float $subtotal): Coupon
    {
        $coupon = $this->findByCode($code);

        if ($coupon === null) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Kupon tidak ditemukan.',
            ]);
        }

        if (! $coupon->isCurrentlyValid()) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Kupon tidak aktif atau sudah kedaluwarsa.',
            ]);
        }

        if (! $coupon->hasUsagesRemaining()) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Kupon sudah mencapai batas penggunaan.',
            ]);
        }

        if (! $coupon->canBeUsedBy($user)) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Anda sudah menggunakan kupon ini.',
            ]);
        }

        if (! $coupon->meetsMinimum($subtotal)) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Minimum pembelian untuk kupon belum terpenuhi.',
            ]);
        }

        return $coupon;
    }
}
