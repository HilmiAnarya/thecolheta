<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        Payment::create([
            'order_id' => 1,
            'payment_date' => now(),
            'payment_status' => 'paid',
            'transaction_id' => 'TRX-TEST-001',
            'amount' => 200000,
            'payment_proof' => 'images/payments/test-proof.jpg',
        ]);
    }
}
