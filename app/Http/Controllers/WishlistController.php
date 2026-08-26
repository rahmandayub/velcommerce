<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWishlistRequest;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WishlistController extends Controller
{
    public function index(Request $request): Response
    {
        $items = $request->user()
            ->wishlists()
            ->with(['product.images', 'product.category'])
            ->latest()
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Wishlist $wishlist): array => [
                'id' => $wishlist->id,
                'product' => [
                    'id' => $wishlist->product->id,
                    'name' => $wishlist->product->name,
                    'slug' => $wishlist->product->slug,
                    'price' => (float) $wishlist->product->price,
                    'compare_price' => $wishlist->product->compare_price !== null ? (float) $wishlist->product->compare_price : null,
                    'stock' => $wishlist->product->stock,
                    'is_active' => (bool) $wishlist->product->is_active,
                    'category' => $wishlist->product->category ? [
                        'id' => $wishlist->product->category->id,
                        'name' => $wishlist->product->category->name,
                    ] : null,
                    'image' => $wishlist->product->images->first()?->url,
                ],
            ]);

        return Inertia::render('wishlist/index', [
            'items' => $items,
        ]);
    }

    /**
     * Toggle a product in the user's wishlist (idempotent).
     */
    public function store(StoreWishlistRequest $request): RedirectResponse
    {
        /** @var Product $product */
        $product = Product::query()->findOrFail($request->integer('product_id'));

        $existing = $request->user()
            ->wishlists()
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return back()->with('wishlist', ['type' => 'removed', 'product_id' => $product->id]);
        }

        $request->user()->wishlists()->create([
            'product_id' => $product->id,
        ]);

        return back()->with('wishlist', ['type' => 'added', 'product_id' => $product->id]);
    }

    public function destroy(Request $request, Wishlist $wishlist): RedirectResponse
    {
        if ((int) $wishlist->user_id !== (int) $request->user()->id) {
            abort(404);
        }

        $wishlist->delete();

        return back();
    }
}
