<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Jalankan seeder kategori dan subkategori.
     */
    public function run(): void
    {
        // Kategori utama
        $cakes = Category::create([
            'name' => 'Cakes',
            'description' => 'Berbagai pilihan kue lezat untuk setiap kesempatan.'
        ]);

        $hampers = Category::create([
            'name' => 'Hampers',
            'description' => 'Paket hadiah spesial untuk orang terkasih.'
        ]);

        $dessertBox = Category::create([
            'name' => 'Dessert Box',
            'description' => 'Kue dalam box yang manis dan elegan.'
        ]);

        $cookies = Category::create([
            'name' => 'Cookies',
            'description' => 'Aneka kue kering dan cookies renyah.'
        ]);

        // Subkategori di bawah Cakes
        Category::insert([
            [
                'name' => 'Dry Cake',
                'description' => 'Kue kering premium cocok untuk hampers dan hadiah.',
                'parent_id' => $cakes->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Birthday Cake',
                'description' => 'Kue ulang tahun spesial untuk berbagai tema.',
                'parent_id' => $cakes->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Matcha Series',
                'description' => 'Kue dengan cita rasa teh hijau khas Jepang.',
                'parent_id' => $cakes->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Subkategori di bawah Hampers
        Category::insert([
            [
                'name' => 'Christmas Hampers',
                'description' => 'Paket hampers edisi Natal yang elegan.',
                'parent_id' => $hampers->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Eid Hampers',
                'description' => 'Paket spesial lebaran untuk keluarga dan teman.',
                'parent_id' => $hampers->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
