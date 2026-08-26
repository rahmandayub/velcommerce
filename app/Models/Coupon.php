<?php

namespace App\Models;

use App\Enums\CouponType;
use Database\Factories\CouponFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $code
 * @property CouponType $type
 * @property string $value
 * @property string $min_order_amount
 * @property string|null $max_discount_amount
 * @property int|null $usage_limit
 * @property int $usage_count
 * @property int|null $per_user_limit
 * @property bool $is_active
 * @property Carbon|null $starts_at
 * @property Carbon|null $expires_at
 */
class Coupon extends Model
{
    /** @use HasFactory<CouponFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit',
        'usage_count',
        'per_user_limit',
        'is_active',
        'starts_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => CouponType::class,
            'value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'max_discount_amount' => 'decimal:2',
            'usage_count' => 'integer',
            'usage_limit' => 'integer',
            'per_user_limit' => 'integer',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Coupon $coupon): void {
            $coupon->code = Str::upper(trim((string) $coupon->code));
        });
    }

    /**
     * @return HasMany<CouponUsage, $this>
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Whether the coupon is currently active and within its valid date window.
     */
    public function isCurrentlyValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();

        if ($this->starts_at !== null && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->expires_at !== null && $now->gt($this->expires_at)) {
            return false;
        }

        return true;
    }

    /**
     * Whether the coupon has not hit its global usage limit.
     */
    public function hasUsagesRemaining(): bool
    {
        return $this->usage_limit === null || $this->usage_count < $this->usage_limit;
    }

    /**
     * Whether the given user can still redeem this coupon based on their usage.
     */
    public function canBeUsedBy(User $user): bool
    {
        if ($this->per_user_limit === null) {
            return true;
        }

        $used = $this->usages()->where('user_id', $user->id)->count();

        return $used < $this->per_user_limit;
    }

    /**
     * Whether the subtotal meets the minimum order amount.
     */
    public function meetsMinimum(float $subtotal): bool
    {
        return $subtotal >= (float) $this->min_order_amount;
    }

    /**
     * Compute the discount amount for the given subtotal.
     *
     * @return float Discount capped at the subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0.0;
        }

        $discount = match ($this->type) {
            CouponType::Percent => $subtotal * ((float) $this->value / 100),
            CouponType::Fixed => (float) $this->value,
        };

        if ($this->max_discount_amount !== null && $discount > (float) $this->max_discount_amount) {
            $discount = (float) $this->max_discount_amount;
        }

        return min($discount, $subtotal);
    }

    /**
     * @param  Builder<Coupon>  $query
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
