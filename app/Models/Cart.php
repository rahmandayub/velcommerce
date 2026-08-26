<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<CartItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public static function forUserOrSession(?User $user, ?string $sessionId): ?self
    {
        if ($user) {
            return static::firstOrCreate(['user_id' => $user->id]);
        }

        if ($sessionId) {
            return static::firstOrCreate(['session_id' => $sessionId]);
        }

        return null;
    }

    public function getTotalQuantityAttribute(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    public function getSubtotalAttribute(): string
    {
        return number_format(
            $this->items->sum(fn (CartItem $item) => (float) $item->price * $item->quantity),
            2,
            '.',
            ''
        );
    }
}
