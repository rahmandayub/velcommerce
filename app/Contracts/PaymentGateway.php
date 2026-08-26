<?php

namespace App\Contracts;

use App\Models\Order;
use Illuminate\Http\Request;

interface PaymentGateway
{
    /**
     * Initiate a payment for the given order and return the redirect target.
     */
    public function initiate(Order $order): mixed;

    /**
     * Handle the gateway callback/webhook for the given order.
     */
    public function handleCallback(Request $request, Order $order): void;
}
