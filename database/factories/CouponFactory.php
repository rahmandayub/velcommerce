<?php

namespace Database\Factories;

use App\Enums\CouponType;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => Str::upper(fake()->unique()->bothify('????##')),
            'type' => CouponType::Percent,
            'value' => fake()->randomFloat(2, 5, 25),
            'min_order_amount' => 0,
            'max_discount_amount' => null,
            'usage_limit' => null,
            'usage_count' => 0,
            'per_user_limit' => 1,
            'is_active' => true,
            'starts_at' => null,
            'expires_at' => null,
        ];
    }

    public function percent(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CouponType::Percent,
        ]);
    }

    public function fixed(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CouponType::Fixed,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }

    public function notYetActive(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => now()->addDay(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withUsageLimit(int $limit = 1): static
    {
        return $this->state(fn (array $attributes) => [
            'usage_limit' => $limit,
            'per_user_limit' => $limit,
        ]);
    }
}
