<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use LogicException;

class CartService
{
    public function resolve(Request $request): Cart
    {
        $user = $request->user();

        if ($user) {
            return Cart::firstOrCreate(['user_id' => $user->id]);
        }

        $sessionId = $request->session()->getId() ?? Str::uuid()->toString();

        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    /**
     * Add an item to the cart, summing quantities and capping at available stock.
     */
    public function addItem(Cart $cart, Product $product, ?ProductVariant $variant, int $quantity): CartItem
    {
        if ($product->variants()->where('is_active', true)->exists() && $variant === null) {
            throw new LogicException('A variant must be selected for this product.');
        }

        $stock = $variant?->stock ?? $product->stock;

        if ($stock <= 0) {
            throw new InsufficientStockException($product, $variant);
        }

        $existing = $cart->items()
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variant?->id)
            ->first();

        $newQuantity = min($quantity + (int) ($existing?->quantity ?? 0), $stock);

        return $cart->items()->updateOrCreate(
            [
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
            ],
            [
                'quantity' => $newQuantity,
                'price' => $variant?->effective_price ?? $product->price,
            ],
        );
    }

    /**
     * Update a line item quantity, capping at available stock.
     */
    public function updateQuantity(CartItem $cartItem, int $quantity): CartItem
    {
        $cartItem->load(['product', 'variant']);

        $stock = $cartItem->variant?->stock ?? $cartItem->product?->stock;

        if ($stock === null || $stock <= 0) {
            throw new InsufficientStockException($cartItem->product, $cartItem->variant);
        }

        $cartItem->update([
            'quantity' => min(max(1, $quantity), $stock),
        ]);

        return $cartItem;
    }

    public function removeItem(CartItem $cartItem): void
    {
        $cartItem->delete();
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }

    /**
     * Merge the guest (session) cart into the user's cart after login.
     * Line items are deduplicated by (product, variant); quantities are summed
     * and capped at the current stock. The guest cart is deleted afterwards.
     */
    public function mergeGuestCartOnLogin(User $user, string $sessionId): void
    {
        $guestCart = Cart::query()
            ->with('items')
            ->where('session_id', $sessionId)
            ->first();

        if (! $guestCart || $guestCart->items->isEmpty()) {
            $guestCart?->delete();

            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $user->id]);

        foreach ($guestCart->items as $item) {
            $product = $item->product;
            $variant = $item->variant;

            if (! $product || ! $product->is_active) {
                continue;
            }

            $stock = $variant?->stock ?? $product->stock;
            $existing = $userCart->items()
                ->where('product_id', $item->product_id)
                ->where('product_variant_id', $item->product_variant_id)
                ->first();

            $newQuantity = min((int) ($existing?->quantity ?? 0) + $item->quantity, max(0, $stock));

            if ($newQuantity <= 0) {
                continue;
            }

            $userCart->items()->updateOrCreate(
                [
                    'cart_id' => $userCart->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                ],
                [
                    'quantity' => $newQuantity,
                    'price' => $variant?->effective_price ?? $product->price,
                ],
            );
        }

        $guestCart->delete();
    }
}
