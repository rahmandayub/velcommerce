<?php

use App\Models\Product;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Storage::fake('public');

    foreach (['manage products', 'manage orders', 'manage users', 'view analytics', 'manage categories'] as $name) {
        Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
    }

    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
    Role::findByName('admin')->syncPermissions(Permission::all());
});

test('image service resizes large image to max 1200 and converts to webp', function (): void {
    // Create a 2000x2000 fake image
    $file = UploadedFile::fake()->image('large.jpg', 2000, 2000);

    $service = app(ImageService::class);
    $path = $service->storeOptimized($file, 'products');

    Storage::disk('public')->assertExists($path);

    // Extension should be webp when gd supports it, otherwise jpg
    $expectedExtension = function_exists('imagewebp') ? 'webp' : 'jpg';
    expect($path)->toEndWith('.'.$expectedExtension);

    // Verify dimensions are <=1200
    $fullPath = Storage::disk('public')->path($path);
    $size = getimagesize($fullPath);
    expect($size)->not->toBeFalse();
    expect($size[0])->toBeLessThanOrEqual(1200);
    expect($size[1])->toBeLessThanOrEqual(1200);
});

test('image service does not upscale small images', function (): void {
    $file = UploadedFile::fake()->image('small.jpg', 400, 400);

    $service = app(ImageService::class);
    $path = $service->storeOptimized($file, 'products');

    $fullPath = Storage::disk('public')->path($path);
    $size = getimagesize($fullPath);
    expect($size[0])->toBeLessThanOrEqual(400);
    expect($size[1])->toBeLessThanOrEqual(400);
});

test('admin can upload product images via syncImages and they are optimized', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $product = Product::factory()->create();

    $file = UploadedFile::fake()->image('test-image.jpg', 1600, 1200);

    $response = $this->actingAs($admin)->post(route('admin.products.images.store', $product), [
        'images' => [$file],
    ]);

    $response->assertRedirect();
    $product->refresh();
    expect($product->images)->toHaveCount(1);

    $image = $product->images->first();
    expect(Storage::disk('public')->exists($image->path))->toBeTrue();

    $fullPath = Storage::disk('public')->path($image->path);
    $size = getimagesize($fullPath);
    expect($size[0])->toBeLessThanOrEqual(1200);
});
