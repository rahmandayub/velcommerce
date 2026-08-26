<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAddressRequest;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AddressController extends Controller
{
    public function index(Request $request): Response
    {
        $addresses = $request->user()->addresses()->latest()->get();

        return Inertia::render('addresses/index', [
            'addresses' => $addresses,
        ]);
    }

    public function store(StoreAddressRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        // First address becomes default automatically.
        if ($request->user()->addresses()->count() === 0) {
            $data['is_default'] = true;
        }

        if (! empty($data['is_default'])) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address = Address::create($data);

        // If request came from checkout, redirect back to checkout address step.
        if ($request->header('Referer') && str_contains($request->header('Referer'), 'checkout')) {
            return redirect()->route('checkout.address');
        }

        return back();
    }

    public function update(StoreAddressRequest $request, Address $address): RedirectResponse
    {
        if ((int) $address->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        $data = $request->validated();

        if (! empty($data['is_default'])) {
            $request->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($data);

        return back();
    }

    public function destroy(Request $request, Address $address): RedirectResponse
    {
        if ((int) $address->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        $address->delete();

        return back();
    }
}
