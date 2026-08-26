<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGateway;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $orders = $request->user()->orders()
            ->with(['items'])
            ->latest()
            ->paginate(12)
            ->through(fn (Order $order): array => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status->value,
                'payment_status' => $order->payment_status->value,
                'total' => (float) $order->total,
                'subtotal' => (float) $order->subtotal,
                'shipping_cost' => (float) $order->shipping_cost,
                'created_at' => $order->created_at?->toIso8601String(),
                'paid_at' => $order->paid_at?->toIso8601String(),
                'shipped_at' => $order->shipped_at?->toIso8601String(),
                'completed_at' => $order->completed_at?->toIso8601String(),
                'cancelled_at' => $order->cancelled_at?->toIso8601String(),
                'items_count' => $order->items->count(),
            ]);

        return Inertia::render('orders/index', [
            'orders' => $orders,
        ]);
    }

    public function show(Request $request, string $orderNumber): Response
    {
        $order = Order::query()
            ->where('order_number', $orderNumber)
            ->with(['items.product.images', 'items.variant', 'address'])
            ->firstOrFail();

        Gate::authorize('view', $order);

        /** @var OrderStatus $status */
        $status = $order->status;

        return Inertia::render('orders/show', [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $status->value,
                'payment_status' => $order->payment_status->value,
                'payment_method' => $order->payment_method,
                'payment_reference' => $order->payment_reference,
                'subtotal' => (float) $order->subtotal,
                'shipping_cost' => (float) $order->shipping_cost,
                'discount' => (float) $order->discount,
                'tax' => (float) $order->tax,
                'total' => (float) $order->total,
                'notes' => $order->notes,
                'created_at' => $order->created_at->toIso8601String(),
                'paid_at' => $order->paid_at?->toIso8601String(),
                'shipped_at' => $order->shipped_at?->toIso8601String(),
                'completed_at' => $order->completed_at?->toIso8601String(),
                'cancelled_at' => $order->cancelled_at?->toIso8601String(),
                'can_cancel' => $status->canTransitionTo(OrderStatus::Cancelled),
                'allowed_transitions' => array_map(fn (OrderStatus $s) => $s->value, $status->allowedTransitions()),
                'address' => $order->address ? [
                    'label' => $order->address->label,
                    'recipient_name' => $order->address->recipient_name,
                    'phone' => $order->address->phone,
                    'full_address' => $order->address->full_address,
                ] : null,
                'items' => $order->items->map(fn ($item): array => [
                    'id' => $item->id,
                    'product_name' => $item->product_name,
                    'variant_name' => $item->variant_name,
                    'sku' => $item->sku,
                    'price' => (float) $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => (float) $item->subtotal,
                    'attributes' => $item->attributes,
                    'image' => $item->product?->images->first()?->url,
                ])->all(),
            ],
        ]);
    }

    public function payment(Request $request, string $orderNumber): Response
    {
        $order = Order::query()->where('order_number', $orderNumber)->firstOrFail();

        Gate::authorize('view', $order);

        return Inertia::render('orders/payment', [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status->value,
                'payment_status' => $order->payment_status->value,
                'total' => (float) $order->total,
            ],
        ]);
    }

    public function mockCallback(Request $request, string $orderNumber, PaymentGateway $gateway): RedirectResponse
    {
        $order = Order::query()->where('order_number', $orderNumber)->firstOrFail();

        Gate::authorize('view', $order);

        if (! app()->environment(['local', 'testing', 'staging'])) {
            abort(404);
        }

        $gateway->handleCallback($request, $order);

        return redirect()->route('orders.show', ['order' => $order->order_number]);
    }

    public function cancel(Request $request, string $orderNumber): RedirectResponse
    {
        $order = Order::query()->where('order_number', $orderNumber)->firstOrFail();

        Gate::authorize('view', $order);

        if (! $order->cancel()) {
            return back()->with('error', 'Order cannot be cancelled at this stage.');
        }

        return redirect()->route('orders.show', ['order' => $order->order_number])
            ->with('success', 'Order cancelled. Stock has been restored.');
    }
}
