<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /** @var string $name */
        $name = fake()->words(3, true);
        $price = fake()->randomFloat(2, 10, 500);

        return [
            'category_id' => Category::factory(),
            'name' => Str::title((string) $name),
            'slug' => Str::slug((string) $name).'-'.Str::lower(Str::random(4)),
            'description' => fake()->paragraphs(3, true),
            'short_description' => fake()->sentence(),
            'price' => $price,
            'compare_price' => fake()->optional()->randomFloat(2, $price + 10, $price + 100),
            'cost' => fake()->optional()->randomFloat(2, 5, $price - 5),
            'sku' => strtoupper(Str::random(8)).'-'.fake()->unique()->numberBetween(1000, 9999),
            'barcode' => fake()->optional()->ean13(),
            'stock' => fake()->numberBetween(0, 100),
            'is_active' => true,
            'is_featured' => fake()->boolean(20),
            'weight' => fake()->optional()->numberBetween(100, 5000),
            'dimensions' => fake()->optional()->randomElement([
                ['length' => 10, 'width' => 20, 'height' => 5],
                ['length' => 15, 'width' => 15, 'height' => 10],
            ]),
            'meta_title' => null,
            'meta_description' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
