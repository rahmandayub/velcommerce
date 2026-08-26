<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
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
            'label' => fake()->randomElement(['Rumah', 'Kantor']),
            'recipient_name' => fake()->name(),
            'phone' => '0812'.fake()->numerify('########'),
            'street' => fake()->streetAddress(),
            'village' => fake()->optional()->citySuffix(),
            'district' => fake()->city(),
            'city' => fake()->city(),
            'province' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'is_default' => false,
        ];
    }
}
