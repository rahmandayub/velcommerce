<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    /**
     * Paginated approved reviews for a product.
     */
    public function index(Request $request, Product $product): Response
    {
        $reviews = $product->reviews()
            ->approved()
            ->with('user')
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Review $review): array => [
                'id' => $review->id,
                'rating' => $review->rating,
                'title' => $review->title,
                'body' => $review->body,
                'created_at' => $review->created_at->toIso8601String(),
                'user' => [
                    'name' => $review->user?->name,
                ],
            ]);

        return Inertia::render('products/reviews', [
            'product' => [
                'id' => $product->id,
                'slug' => $product->slug,
                'name' => $product->name,
            ],
            'reviews' => $reviews,
        ]);
    }

    /**
     * Store a review for a product (only verified buyers may review).
     */
    public function store(StoreReviewRequest $request): RedirectResponse
    {
        $product = Product::query()->findOrFail($request->integer('product_id'));
        $user = $request->user();

        Gate::authorize('create', [Review::class, $product]);

        // One review per user per product.
        if ($user->reviews()->where('product_id', $product->id)->exists()) {
            return back()->withErrors([
                'review' => 'Anda sudah memberikan ulasan untuk produk ini.',
            ]);
        }

        $user->reviews()->create([
            'product_id' => $product->id,
            'order_id' => null,
            'rating' => $request->integer('rating'),
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'is_approved' => true,
        ]);

        return back()->with('review_success', 'Ulasan berhasil dikirim.');
    }

    public function update(UpdateReviewRequest $request, Review $review): RedirectResponse
    {
        Gate::authorize('update', $review);

        $review->update($request->validated());

        return back()->with('review_success', 'Ulasan diperbarui.');
    }

    public function destroy(Request $request, Review $review): RedirectResponse
    {
        Gate::authorize('delete', $review);

        $review->delete();

        return back();
    }
}
