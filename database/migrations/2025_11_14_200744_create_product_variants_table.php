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
        // Ini adalah tabel BARU Anda untuk menyimpan harga, stok, dan ukuran
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            // 'Jembatan' ke tabel products
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete(); // Jika produk dihapus, varian ikut terhapus

            // Kolom untuk nama varian (ukuran Anda).
            // Kita gunakan string() agar fleksibel.
            $table->string('size')->nullable(); // e.g., "20 x 20", "30 x 40"

            // price dan stock sekarang ada di sini
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
