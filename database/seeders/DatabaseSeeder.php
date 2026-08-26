<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
        ]);

        // Seed fallback test user if not exists (keep original starter kit behavior)
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Categories: 5 top-level
        $categories = Category::factory()->count(5)->create();

        // Create a couple of child categories for demonstration
        Category::factory()->count(3)->create([
            'parent_id' => $categories->random()->id,
        ]);

        // Products: 20, distributed across categories, with random variants
        Product::factory()
            ->count(20)
            ->recycle($categories)
            ->create()
            ->each(function (Product $product): void {
                if (fake()->boolean(40)) {
                    ProductVariant::factory()
                        ->count(fake()->numberBetween(2, 4))
                        ->create([
                            'product_id' => $product->id,
                        ]);
                }
            });
    }
}
