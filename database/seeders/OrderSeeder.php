<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        Order::create([
            'customer_id' => 1,
            'address_id' => 1,
            'order_date' => now(),
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
            'total_price' => 200000,
            'tracking_number' => null,
            'notes' => 'Tolong kirim sebelum jam 10 pagi.',
        ]);
    }
}
