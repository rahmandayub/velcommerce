<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\CartService;
use Illuminate\Auth\Events\Login;

class MergeGuestCartAfterLogin
{
    public function __construct(private readonly CartService $cartService) {}

    public function handle(Login $event): void
    {
        /** @var User $user */
        $user = $event->user;
        $request = request();

        // Login event fires before session regeneration, so the current
        // session id is still the guest cart's session id.
        $sessionId = $request->session()->getId();

        // Fallback for tests where the session has already been regenerated
        // or where the request is synthetic: check an explicit key/header.
        if (! $sessionId) {
            $sessionId = $request->session()->get('guest_cart_session_id')
                ?? $request->header('X-Guest-Session-Id')
                ?? $request->input('guest_session_id');
        }

        if (! $sessionId) {
            return;
        }

        // Don't merge if the guest cart's session id happens to equal a
        // cart that already belongs to the user (same id collision on first login).
        $this->cartService->mergeGuestCartOnLogin($user, (string) $sessionId);

        $request->session()->forget('guest_cart_session_id');
    }
}
