<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only(['q', 'category', 'min_price', 'max_price', 'sort']);
        $perPage = (int) config('shop.products_per_page', 12);

        $products = Product::query()
            ->filtered($filters)
            ->with(['images', 'category', 'variants'])
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn (Product $product): array => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->price,
                'compare_price' => $product->compare_price !== null ? (float) $product->compare_price : null,
                'short_description' => $product->short_description,
                'is_featured' => (bool) $product->is_featured,
                'stock' => $product->stock,
                'total_stock' => $product->total_stock,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                    'slug' => $product->category->slug,
                ] : null,
                'image' => $product->images->first()?->url,
                'images' => $product->images->map(fn ($img): array => [
                    'id' => $img->id,
                    'url' => $img->url,
                    'is_primary' => (bool) $img->is_primary,
                ])->all(),
            ]);

        $categories = Category::query()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Category $cat): array => [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'children' => $cat->children->map(fn (Category $child): array => [
                    'id' => $child->id,
                    'name' => $child->name,
                    'slug' => $child->slug,
                ])->all(),
            ]);

        return Inertia::render('products/index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $filters,
        ]);
    }

    public function show(string $slug): Response
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with(['images', 'category', 'variants' => fn ($q) => $q->where('is_active', true)])
            ->firstOrFail();

        return Inertia::render('products/show', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'short_description' => $product->short_description,
                'price' => (float) $product->price,
                'compare_price' => $product->compare_price !== null ? (float) $product->compare_price : null,
                'sku' => $product->sku,
                'stock' => $product->stock,
                'total_stock' => $product->total_stock,
                'is_active' => (bool) $product->is_active,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                    'slug' => $product->category->slug,
                ] : null,
                'images' => $product->images->sortBy('sort_order')->map(fn ($img): array => [
                    'id' => $img->id,
                    'url' => $img->url,
                    'is_primary' => (bool) $img->is_primary,
                    'alt' => $img->alt,
                ])->values()->all(),
                'variants' => $product->variants->map(fn ($v): array => [
                    'id' => $v->id,
                    'sku' => $v->sku,
                    'name' => $v->name,
                    'price' => $v->price !== null ? (float) $v->price : null,
                    'effective_price' => (float) $v->effective_price,
                    'stock' => $v->stock,
                    'attributes' => $v->attributes,
                    'is_active' => (bool) $v->is_active,
                ])->all(),
            ],
        ]);
    }
}
