<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'address_id' => Address::factory(),
            'order_number' => 'VEL-'.now()->format('Ymd').'-'.fake()->unique()->numerify('####'),
            'status' => OrderStatus::Pending,
            'payment_status' => 'unpaid',
            'shipping_cost' => 15000,
            'subtotal' => fake()->randomFloat(2, 50, 500),
            'discount' => 0,
            'tax' => 0,
            'total' => fake()->randomFloat(2, 50, 500),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Paid,
            'payment_status' => 'paid',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Completed,
            'payment_status' => 'paid',
        ]);
    }
}
