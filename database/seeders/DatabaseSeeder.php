<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        // Categories: 5 top-level
        $categories = Category::factory()->count(5)->create();

        // Create a couple of child categories for demonstration
        Category::factory()->count(3)->create([
            'parent_id' => $categories->random()->id,
        ]);

        // Ensure customer has an address for checkout demo
        $customer = User::where('email', 'customer@velcommerce.test')->first();
        if ($customer && $customer->addresses()->count() === 0) {
            Address::create([
                'user_id' => $customer->id,
                'label' => 'Rumah',
                'recipient_name' => $customer->name,
                'phone' => '0812'.fake()->numerify('########'),
                'street' => fake()->streetAddress(),
                'village' => fake()->citySuffix(),
                'district' => fake()->city(),
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => fake()->postcode(),
                'is_default' => true,
            ]);
        }

        // Products: 20, distributed across categories, with random variants + placeholder images
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

                // Create 1–2 placeholder images per product on the public disk
                $count = fake()->numberBetween(1, 2);
                for ($i = 0; $i < $count; $i++) {
                    $path = "products/seed-{$product->id}-{$i}.jpg";
                    // Create a tiny placeholder if not exists (1x1 transparent png saved as jpg)
                    if (! Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->put($path, base64_decode(
                            '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAAQABADAREAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwCwAA8A/9k='
                        ));
                    }

                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => $path,
                        'alt' => $product->name,
                        'is_primary' => $i === 0,
                        'sort_order' => $i,
                    ]);
                }
            });
    }
}
