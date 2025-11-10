<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderItem;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        OrderItem::insert([
            [
                'order_id' => 1,
                'product_id' => 1,
                'quantity' => 1,
                'price' => 150000,
            ],
            [
                'order_id' => 1,
                'product_id' => 2,
                'quantity' => 2,
                'price' => 25000,
            ],
        ]);
    }
}
