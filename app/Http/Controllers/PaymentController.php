<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        return response()->json(Payment::with('order')->paginate(10));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_date' => 'nullable|date',
            'payment_status' => 'required|in:unpaid,paid,failed',
            'transaction_id' => 'nullable|string|max:100',
            'amount' => 'required|numeric|min:0',
            'payment_proof' => 'nullable|string|max:255',
        ]);

        $payment = Payment::create($validated);
        return response()->json($payment, 201);
    }

    public function show(Payment $payment)
    {
        return response()->json($payment->load('order'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'payment_status' => 'sometimes|in:unpaid,paid,failed',
            'transaction_id' => 'nullable|string|max:100',
            'payment_proof' => 'nullable|string|max:255',
        ]);

        $payment->update($validated);
        return response()->json($payment);
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return response()->noContent();
    }
}
