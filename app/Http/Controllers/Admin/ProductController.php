<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $products = Product::query()
            ->with(['category', 'images', 'variants'])
            ->latest('id')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Product $p): array => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'sku' => $p->sku,
                'price' => (float) $p->price,
                'stock' => $p->stock,
                'total_stock' => $p->total_stock,
                'is_active' => (bool) $p->is_active,
                'is_featured' => (bool) $p->is_featured,
                'category' => $p->category ? ['name' => $p->category->name] : null,
                'image' => $p->images->first()?->url,
                'variants_count' => $p->variants->count(),
            ]);

        return Inertia::render('admin/products/index', [
            'products' => $products,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/products/form', [
            'product' => null,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $product = DB::transaction(function () use ($data, $request): Product {
            $product = Product::create([
                'name' => $data['name'],
                'slug' => $this->uniqueSlug($data['name']),
                'description' => $data['description'],
                'short_description' => $data['short_description'] ?? null,
                'price' => $data['price'],
                'compare_price' => $data['compare_price'] ?? null,
                'cost' => $data['cost'] ?? null,
                'sku' => $data['sku'],
                'barcode' => $data['barcode'] ?? null,
                'stock' => $data['stock'],
                'category_id' => $data['category_id'] ?? null,
                'is_active' => (bool) ($data['is_active'] ?? true),
                'is_featured' => (bool) ($data['is_featured'] ?? false),
                'weight' => $data['weight'] ?? null,
                'meta_title' => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
            ]);

            $this->syncImages($product, $request);
            $this->syncVariants($product, $data['variants'] ?? []);

            return $product;
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created.');
    }

    public function edit(Product $product): Response
    {
        $product->load(['images', 'variants', 'category']);

        return Inertia::render('admin/products/form', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'short_description' => $product->short_description,
                'price' => (float) $product->price,
                'compare_price' => $product->compare_price !== null ? (float) $product->compare_price : null,
                'cost' => $product->cost !== null ? (float) $product->cost : null,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'stock' => $product->stock,
                'category_id' => $product->category_id,
                'is_active' => (bool) $product->is_active,
                'is_featured' => (bool) $product->is_featured,
                'weight' => $product->weight,
                'meta_title' => $product->meta_title,
                'meta_description' => $product->meta_description,
                'images' => $product->images->sortBy('sort_order')->map(fn (ProductImage $img): array => [
                    'id' => $img->id,
                    'url' => $img->url,
                    'is_primary' => (bool) $img->is_primary,
                    'sort_order' => $img->sort_order,
                ])->values()->all(),
                'variants' => $product->variants->map(fn ($v): array => [
                    'id' => $v->id,
                    'sku' => $v->sku,
                    'name' => $v->name,
                    'price' => $v->price !== null ? (float) $v->price : null,
                    'stock' => $v->stock,
                    'attributes' => $v->attributes,
                    'is_active' => (bool) $v->is_active,
                ])->all(),
            ],
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($product, $data, $request): void {
            $product->update([
                'name' => $data['name'],
                'description' => $data['description'],
                'short_description' => $data['short_description'] ?? null,
                'price' => $data['price'],
                'compare_price' => $data['compare_price'] ?? null,
                'cost' => $data['cost'] ?? null,
                'sku' => $data['sku'],
                'barcode' => $data['barcode'] ?? null,
                'stock' => $data['stock'],
                'category_id' => $data['category_id'] ?? null,
                'is_active' => (bool) ($data['is_active'] ?? $product->is_active),
                'is_featured' => (bool) ($data['is_featured'] ?? $product->is_featured),
                'weight' => $data['weight'] ?? null,
                'meta_title' => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
            ]);

            if ($request->hasFile('images')) {
                $this->syncImages($product, $request);
            }

            if (array_key_exists('variants', $data)) {
                $this->syncVariants($product, $data['variants'] ?? []);
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $paths = $product->images()->pluck('path')->all();

        DB::transaction(function () use ($product): void {
            $product->delete();
        });

        foreach ($paths as $path) {
            Storage::disk('public')->delete($path);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted.');
    }

    public function storeImage(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'images' => ['required', 'array', 'max:5'],
            'images.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $this->syncImages($product, $request);

        return back()->with('success', 'Images uploaded.');
    }

    public function destroyImage(Product $product, ProductImage $image): RedirectResponse
    {
        if ((int) $image->product_id !== (int) $product->id) {
            abort(404);
        }

        $path = $image->path;
        $image->delete();
        Storage::disk('public')->delete($path);

        // Ensure at least one image remains primary if any left.
        if ($product->images()->where('is_primary', true)->doesntExist()) {
            $first = $product->images()->orderBy('sort_order')->first();
            $first?->update(['is_primary' => true]);
        }

        return back()->with('success', 'Image removed.');
    }

    /**
     * @param  array<int, array<string, mixed>>  $variantsData
     */
    private function syncVariants(Product $product, array $variantsData): void
    {
        if (empty($variantsData)) {
            return;
        }

        $keepIds = [];

        foreach ($variantsData as $row) {
            $variant = $product->variants()->updateOrCreate(
                ['id' => $row['id'] ?? null],
                [
                    'sku' => $row['sku'],
                    'name' => $row['name'] ?? null,
                    'price' => $row['price'] ?? null,
                    'stock' => $row['stock'],
                    'attributes' => $row['attributes'] ?? null,
                    'is_active' => (bool) ($row['is_active'] ?? true),
                ],
            );

            $keepIds[] = $variant->id;
        }

        // Remove variants that were not in the payload when editing.
        // Only prune if the payload explicitly included ids (edit mode).
        $hasIds = collect($variantsData)->contains(fn ($r) => isset($r['id']));

        if ($hasIds) {
            $product->variants()->whereNotIn('id', $keepIds)->delete();
        }
    }

    private function syncImages(Product $product, Request $request): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $files = $request->file('images');
        $existingCount = $product->images()->count();
        $isFirst = $existingCount === 0;

        $imageService = app(ImageService::class);

        foreach ((array) $files as $index => $file) {
            $path = $imageService->storeOptimized($file, 'products');

            $product->images()->create([
                'path' => $path,
                'alt' => $product->name,
                'is_primary' => $isFirst && $index === 0,
                'sort_order' => $existingCount + $index,
            ]);
        }
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    private function categoryOptions(): array
    {
        return Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($c): array => ['id' => $c->id, 'name' => $c->name])
            ->all();
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $base.'-'.Str::lower(Str::random(4)).'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
