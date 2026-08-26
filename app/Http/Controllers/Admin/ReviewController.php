<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    public function index(Request $request): Response
    {
        $reviews = Review::query()
            ->with(['user', 'product'])
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Review $review): array => [
                'id' => $review->id,
                'rating' => $review->rating,
                'title' => $review->title,
                'body' => $review->body,
                'is_approved' => (bool) $review->is_approved,
                'created_at' => $review->created_at->toIso8601String(),
                'user' => [
                    'name' => $review->user?->name,
                ],
                'product' => [
                    'id' => $review->product?->id,
                    'name' => $review->product?->name,
                ],
            ]);

        return Inertia::render('admin/reviews/index', [
            'reviews' => $reviews,
        ]);
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Ulasan dihapus.');
    }
}
