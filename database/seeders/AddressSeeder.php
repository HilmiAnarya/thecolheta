<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        Address::create([
            'customer_id' => 1,
            'recipient_name' => 'Test Customer',
            'phone' => '089876543210',
            'street' => 'Jl. Mawar No. 10',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'postal_code' => '12345',
        ]);
    }
}
