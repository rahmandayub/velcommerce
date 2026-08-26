<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user() ? $request->user()->loadMissing('roles', 'permissions') : null,
            ],
            'flash' => [
                'success' => fn (): ?string => $request->session()->get('success'),
                'error' => fn (): ?string => $request->session()->get('error'),
                'status' => fn (): ?string => $request->session()->get('status'),
            ],
            'cartCount' => fn (): int => $this->resolveCartCount($request),
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }

    private function resolveCartCount(Request $request): int
    {
        $user = $request->user();

        if ($user) {
            $cart = Cart::where('user_id', $user->id)->first();

            if (! $cart) {
                return 0;
            }

            return (int) $cart->items()->sum('quantity');
        }

        $sessionId = $request->session()->getId();

        if (! $sessionId) {
            return 0;
        }

        $cart = Cart::where('session_id', $sessionId)->first();

        if (! $cart) {
            return 0;
        }

        return (int) $cart->items()->sum('quantity');
    }
}
