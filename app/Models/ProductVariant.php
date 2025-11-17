<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    // Nama tabel Anda (Laravel biasanya bisa menebak, tapi ini lebih aman)
    protected $table = 'product_variants';
    protected $primaryKey = 'id';

    protected $fillable = [
        'product_id',
        'size',
        'price',
        'stock',
    ];

    /**
     * RELASI BARU
     * Mendefinisikan bahwa satu Varian dimiliki oleh SATU Produk.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
