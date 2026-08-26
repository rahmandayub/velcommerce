<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /** @var string $name */
        $name = fake()->unique()->words(2, true);

        return [
            'name' => Str::title((string) $name),
            'slug' => Str::slug((string) $name).'-'.Str::lower(Str::random(4)),
            'description' => fake()->sentence(),
            'parent_id' => null,
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
            'image' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
