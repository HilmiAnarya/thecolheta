<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        return response()->json(Address::with('customer')->paginate(10));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'recipient_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'street' => 'required|string',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
        ]);

        $address = Address::create($validated);
        return response()->json($address, 201);
    }

    public function show(Address $address)
    {
        return response()->json($address->load('customer'));
    }

    public function update(Request $request, Address $address)
    {
        $validated = $request->validate([
            'recipient_name' => 'sometimes|string|max:100',
            'phone' => 'sometimes|string|max:20',
            'street' => 'sometimes|string',
            'city' => 'sometimes|string|max:100',
            'province' => 'sometimes|string|max:100',
            'postal_code' => 'sometimes|string|max:10',
        ]);

        $address->update($validated);
        return response()->json($address);
    }

    public function destroy(Address $address)
    {
        $address->delete();
        return response()->noContent();
    }
}
