<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use LogicException;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService) {}

    public function index(Request $request): Response
    {
        $cart = $this->cartService->resolve($request);
        $cart->load(['items.product.images', 'items.variant']);

        return Inertia::render('cart/index', [
            'items' => $cart->items->map(fn (CartItem $item): array => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product?->name,
                'product_slug' => $item->product?->slug,
                'variant_id' => $item->product_variant_id,
                'variant_name' => $item->variant?->name,
                'variant_attributes' => $item->variant?->attributes,
                'price' => (float) $item->price,
                'quantity' => $item->quantity,
                'subtotal' => (float) $item->subtotal,
                'stock' => $item->variant !== null ? $item->variant->stock : ($item->product?->stock ?? 0),
                'image' => $item->product?->images->first()?->url,
                'is_active' => $item->product !== null && $item->product->is_active,
            ])->values()->all(),
            'subtotal' => (float) $cart->subtotal,
            'count' => $cart->getTotalQuantityAttribute(),
        ]);
    }

    public function store(AddToCartRequest $request): RedirectResponse
    {
        $cart = $this->cartService->resolve($request);
        $product = Product::query()->findOrFail($request->integer('product_id'));
        $variant = $request->filled('variant_id')
            ? ProductVariant::query()->findOrFail($request->integer('variant_id'))
            : null;

        try {
            $this->cartService->addItem($cart, $product, $variant, $request->integer('quantity', 1));
        } catch (InsufficientStockException $e) {
            throw ValidationException::withMessages([
                'quantity' => $e->getMessage(),
            ]);
        } catch (LogicException $e) {
            throw ValidationException::withMessages([
                'variant_id' => $e->getMessage(),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Added to cart.']);

        return back();
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem): RedirectResponse
    {
        // Ownership check: ensure cart item belongs to the requester's cart.
        $cart = $this->cartService->resolve($request);

        if ((int) $cartItem->cart_id !== (int) $cart->id) {
            abort(404);
        }

        try {
            $this->cartService->updateQuantity($cartItem, $request->integer('quantity'));
        } catch (InsufficientStockException $e) {
            throw ValidationException::withMessages([
                'quantity' => $e->getMessage(),
            ]);
        }

        return back();
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        $cart = $this->cartService->resolve($request);

        if ((int) $cartItem->cart_id !== (int) $cart->id) {
            abort(404);
        }

        $this->cartService->removeItem($cartItem);

        return back();
    }
}
