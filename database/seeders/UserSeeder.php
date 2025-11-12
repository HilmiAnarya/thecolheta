<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk membuat akun awal.
     */
    public function run(): void
    {
        // 🧑‍💼 ADMIN DEFAULT
        User::create([
            'name'              => 'Admin CakeShop',
            'email'             => 'admin@cakeshop.test',
            'password'          => Hash::make('password'),
            'phone'             => '081234567890',
            'gender'            => 'laki-laki',     // ✅ lowercase sesuai enum
            'birth_date'        => '1990-01-01',
            'role'              => 'admin',
            'email_verified_at' => now(),
        ]);

        // 👩 CUSTOMER DEFAULT
        User::create([
            'name'              => 'Test Customer',
            'email'             => 'customer@cakeshop.test',
            'password'          => Hash::make('password'),
            'phone'             => '089876543210',
            'gender'            => 'perempuan',     // ✅ lowercase sesuai enum
            'birth_date'        => '1998-07-15',
            'role'              => 'customer',
            'email_verified_at' => now(),
        ]);
    }
}
