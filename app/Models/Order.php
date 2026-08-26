<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LogicException;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'address_id',
        'order_number',
        'status',
        'payment_status',
        'payment_method',
        'payment_reference',
        'payment_payload',
        'shipping_method',
        'shipping_cost',
        'subtotal',
        'discount',
        'tax',
        'total',
        'notes',
        'paid_at',
        'shipped_at',
        'completed_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
            'shipping_cost' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'payment_payload' => 'array',
            'paid_at' => 'datetime',
            'shipped_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order): void {
            if (empty($order->order_number)) {
                $order->order_number = 'VEL-'.now()->format('Ymd').'-'.Str::upper(Str::random(4));
            }
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Address, $this>
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isPending(): bool
    {
        return $this->status === OrderStatus::Pending;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === PaymentStatus::Paid;
    }

    public function markPaid(?string $paymentReference = null): void
    {
        $this->ensureCanTransitionTo(OrderStatus::Paid);

        $this->forceFill([
            'status' => OrderStatus::Paid,
            'payment_status' => PaymentStatus::Paid,
            'payment_reference' => $paymentReference ?? $this->payment_reference,
            'paid_at' => now(),
        ])->save();
    }

    public function markFailed(?string $paymentReference = null): void
    {
        $this->ensureCanTransitionTo(OrderStatus::Failed);

        $this->forceFill([
            'status' => OrderStatus::Failed,
            'payment_status' => PaymentStatus::Failed,
            'payment_reference' => $paymentReference ?? $this->payment_reference,
        ])->save();
    }

    public function markShipped(): void
    {
        $this->ensureCanTransitionTo(OrderStatus::Shipped);

        $this->forceFill([
            'status' => OrderStatus::Shipped,
            'shipped_at' => now(),
        ])->save();
    }

    public function markCompleted(): void
    {
        $this->ensureCanTransitionTo(OrderStatus::Completed);

        $this->forceFill([
            'status' => OrderStatus::Completed,
            'completed_at' => now(),
        ])->save();
    }

    /**
     * Cancel the order and restore stock (only before shipping).
     */
    public function cancel(): bool
    {
        if (! $this->status->canTransitionTo(OrderStatus::Cancelled)) {
            return false;
        }

        return DB::transaction(function (): bool {
            foreach ($this->items()->with(['variant', 'product'])->get() as $item) {
                if ($item->variant !== null) {
                    ProductVariant::query()
                        ->whereKey($item->variant->getKey())
                        ->increment('stock', $item->quantity);
                } elseif ($item->product !== null) {
                    Product::query()
                        ->whereKey($item->product->getKey())
                        ->increment('stock', $item->quantity);
                }
            }

            $this->forceFill([
                'status' => OrderStatus::Cancelled,
                'cancelled_at' => now(),
            ])->save();

            return true;
        });
    }

    private function ensureCanTransitionTo(OrderStatus $target): void
    {
        if (! $this->status->canTransitionTo($target)) {
            throw new LogicException(sprintf(
                'Order %s cannot transition from %s to %s.',
                $this->order_number,
                $this->status->value,
                $target->value,
            ));
        }
    }
}
