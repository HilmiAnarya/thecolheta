<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::create([
            'user_id' => 2, // user 'Test Customer'
            'name' => 'Test Customer',
            'email' => 'customer@cakeshop.test',
            'phone' => '089876543210',
        ]);
    }
}
