<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGateway;
use App\Models\Order;
use App\Notifications\PaymentStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class MockGateway implements PaymentGateway
{
    /**
     * Mock payment: redirect straight to the local mock payment page.
     */
    public function initiate(Order $order): RedirectResponse
    {
        return Redirect::route('orders.payment', ['order' => $order->order_number]);
    }

    /**
     * Mock callback: apply the requested outcome (paid|failed) to the order.
     */
    public function handleCallback(Request $request, Order $order): void
    {
        $outcome = $request->string('outcome', 'paid')->toString();

        $payload = array_merge(
            $request->except(['_token', '_method']),
            ['gateway' => 'mock', 'received_at' => now()->toIso8601String()],
        );

        $reference = 'MOCK-'.Str::upper(Str::random(10));

        $order->forceFill(['payment_payload' => $payload]);

        if ($outcome === 'paid') {
            $order->markPaid($reference);
            $order->user->notify(new PaymentStatusNotification($order, 'paid'));

            return;
        }

        $order->markFailed($reference);
        $order->user->notify(new PaymentStatusNotification($order, 'failed'));
    }
}
