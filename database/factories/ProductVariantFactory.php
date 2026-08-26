<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $colors = ['Merah', 'Biru', 'Hitam', 'Putih', 'Hijau', 'Kuning'];
        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];

        $color = fake()->randomElement($colors);
        $size = fake()->randomElement($sizes);

        return [
            'product_id' => Product::factory(),
            'sku' => strtoupper(Str::random(8)).'-'.fake()->unique()->numberBetween(1000, 9999),
            'name' => "{$color} - {$size}",
            'price' => fake()->optional(0.5)->randomFloat(2, 10, 500),
            'stock' => fake()->numberBetween(0, 50),
            'attributes' => [
                'color' => $color,
                'size' => $size,
            ],
            'image' => null,
            'is_active' => true,
        ];
    }
}
