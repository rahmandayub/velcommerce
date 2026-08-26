<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Only customers who purchased the product (paid+ order) may review it.
     */
    public function create(User $user, ?Product $product = null): bool
    {
        if ($product === null) {
            return false;
        }

        return $this->userPurchasedProduct($user, $product);
    }

    /**
     * A user may update their own review; admins may update any.
     */
    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->user_id || $user->can('manage products');
    }

    /**
     * A user may delete their own review; admins may delete any.
     */
    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->user_id || $user->can('manage products');
    }

    /**
     * Whether the user has a completed/paid order containing the product.
     */
    protected function userPurchasedProduct(User $user, Product $product): bool
    {
        return $user->orders()
            ->whereIn('status', ['paid', 'shipped', 'completed'])
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->exists();
    }
}
