<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage; // <-- Import Storage

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'product_variant_id' => $this->product_variant_id,

            //
            // ! INI ADALAH PERBAIKANNYA
            //
            // Kita cek 'variant' dulu. Jika 'variant' ada,
            // baru kita ambil datanya.
            //
            'size' => $this->whenLoaded('variant', $this->variant->size),
            'price' => $this->whenLoaded('variant', (float) $this->variant->price),
            'stock_available' => $this->whenLoaded('variant', (int) $this->variant->stock),

            // Kalkulasi subtotal
            'subtotal' => $this->whenLoaded('variant', (float) $this->variant->price * $this->quantity),

            //
            // ! INI PERBAIKAN UNTUK RELASI BERSARANG ('product')
            //
            // Kita cek 'variant' dulu, LALU kita cek 'product' di DALAM 'variant'.
            //
            'product_id' => $this->whenLoaded('variant',
                // 'product' adalah relasi DI DALAM 'variant'
                fn() => $this->variant->product ? $this->variant->product->id : null
            ),
            'product_name' => $this->whenLoaded('variant',
                fn() => $this->variant->product ? $this->variant->product->name : 'Produk Dihapus'
            ),
            'product_image_url' => $this->whenLoaded('variant',
                fn() => $this->variant->product && $this->variant->product->image_url
                    ? 'http://localhost:8000' . Storage::url($this->variant->product->image_url)
                    : null
            ),
        ];
    }
}
