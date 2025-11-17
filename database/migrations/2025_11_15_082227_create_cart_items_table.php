<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('cart_items', function (Blueprint $table) {
        $table->id();

        // 1. Siapa pemilik keranjang ini?
        $table->foreignId('user_id')
              ->constrained('users')
              ->cascadeOnDelete();

        // 2. Barang apa yang dia beli?
        // ! PENTING: Kita terhubung ke 'product_variants',
        // ! bukan 'products', karena varian-lah yang punya harga & stok.
        $table->foreignId('product_variant_id')
              ->constrained('product_variants')
              ->cascadeOnDelete();

        // 3. Berapa banyak?
        $table->integer('quantity')->default(1);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
