<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin CakeShop',
            'email' => 'admin@cakeshop.test',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Test Customer',
            'email' => 'customer@cakeshop.test',
            'password' => Hash::make('password'),
            'phone' => '089876543210',
            'role' => 'customer',
        ]);
    }
}
