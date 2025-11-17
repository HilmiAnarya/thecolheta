<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Ini adalah resource baru untuk data varian
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'size' => $this->size,
            'price' => (float) $this->price,
            'stock' => (int) $this->stock,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
