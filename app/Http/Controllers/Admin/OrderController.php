<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $orders = Order::query()
            ->with(['user', 'items'])
            ->latest('id')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Order $order): array => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'user' => $order->user ? ['name' => $order->user->name, 'email' => $order->user->email] : null,
                'status' => $order->status->value,
                'payment_status' => $order->payment_status->value,
                'total' => (float) $order->total,
                'items_count' => $order->items->count(),
                'created_at' => $order->created_at->toIso8601String(),
            ]);

        return Inertia::render('admin/orders/index', [
            'orders' => $orders,
        ]);
    }

    public function show(Order $order): Response
    {
        $order->load(['user', 'address', 'items.product.images', 'items.variant']);

        /** @var OrderStatus $status */
        $status = $order->status;

        return Inertia::render('admin/orders/show', [
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
                'allowed_transitions' => array_map(fn (OrderStatus $s) => $s->value, $status->allowedTransitions()),
                'user' => $order->user ? ['name' => $order->user->name, 'email' => $order->user->email] : null,
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
                ])->all(),
            ],
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $target = OrderStatus::from($request->string('status')->toString());
        /** @var OrderStatus $current */
        $current = $order->status;

        if (! $current->canTransitionTo($target)) {
            return back()->with('error', "Cannot transition from {$current->value} to {$target->value}.");
        }

        match ($target) {
            OrderStatus::Paid => $order->markPaid($order->payment_reference ?? 'ADMIN-'.now()->format('YmdHis')),
            OrderStatus::Failed => $order->markFailed($order->payment_reference),
            OrderStatus::Shipped => $order->markShipped(),
            OrderStatus::Completed => $order->markCompleted(),
            OrderStatus::Cancelled => $order->cancel() ?: abort(422, 'Cannot cancel this order.'),
            default => null,
        };

        return back()->with('success', "Order status updated to {$target->value}.");
    }
}
