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
            ->withAvg('reviews', 'rating')
            ->withCount(['reviews' => fn ($q) => $q->approved()])
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
                'average_rating' => round((float) ($product->reviews_avg_rating ?? 0), 2),
                'reviews_count' => (int) $product->reviews_count,
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

        $wishlistIds = $request->user()
            ? $request->user()->wishlists()->pluck('product_id')->all()
            : [];

        return Inertia::render('products/index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $filters,
            'wishlistIds' => $wishlistIds,
        ]);
    }

    public function show(string $slug): Response
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with(['images', 'category', 'variants' => fn ($q) => $q->where('is_active', true)])
            ->withAvg('reviews', 'rating')
            ->withCount(['reviews' => fn ($q) => $q->approved()])
            ->firstOrFail();

        $user = request()->user();
        $userReview = null;
        $canReview = false;

        if ($user !== null) {
            $userReview = $product->reviews()->where('user_id', $user->id)->first();
            $canReview = $userReview === null
                && $user->orders()
                    ->whereIn('status', ['paid', 'shipped', 'completed'])
                    ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
                    ->exists();
        }

        $reviews = $product->reviews()
            ->approved()
            ->with('user')
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($review): array => [
                'id' => $review->id,
                'rating' => $review->rating,
                'title' => $review->title,
                'body' => $review->body,
                'created_at' => $review->created_at->toIso8601String(),
                'user' => [
                    'name' => $review->user?->name,
                ],
            ]);

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
                'average_rating' => round((float) ($product->reviews_avg_rating ?? 0), 2),
                'reviews_count' => (int) $product->reviews_count,
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
                'reviews' => $reviews,
                'can_review' => $canReview,
                'user_review' => $userReview ? [
                    'id' => $userReview->id,
                    'rating' => $userReview->rating,
                    'title' => $userReview->title,
                    'body' => $userReview->body,
                ] : null,
            ],
        ]);
    }
}
