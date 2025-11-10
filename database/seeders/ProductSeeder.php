<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::insert([
            [
                'category_id' => 1,
                'name' => 'Chocolate Birthday Cake',
                'description' => 'Kue coklat lembut dengan krim lezat.',
                'price' => 150000,
                'stock' => 10,
                'image_url' => 'images/products/choco_cake.jpg',
            ],
            [
                'category_id' => 2,
                'name' => 'Vanilla Cupcake',
                'description' => 'Cupcake vanilla dengan topping buttercream.',
                'price' => 25000,
                'stock' => 20,
                'image_url' => 'images/products/vanilla_cupcake.jpg',
            ],
            [
                'category_id' => 2,
                'name' => 'Strawberry Cupcake',
                'description' => 'Cupcake rasa stroberi segar.',
                'price' => 27000,
                'stock' => 15,
                'image_url' => 'images/products/strawberry_cupcake.jpg',
            ],
        ]);
    }
}
