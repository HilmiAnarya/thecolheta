<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
// Kita tidak perlu 'use App\Http\Resources\CategoryResource;'

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Kita gunakan $this->whenLoaded untuk 'variants'
        $variants = $this->whenLoaded('variants');

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'description' => $this->description,

            'starting_price' => $this->whenLoaded('variants',
                fn() => (float) $this->variants->min('price'),
                null
            ),

            'image_url' => $this->image_url
                           ? 'http://localhost:8000' . Storage::url($this->image_url)
                           : null,

            'is_active' => (bool) $this->is_active,

            //
            // ! INI PERBAIKANNYA
            // ! Kita kembalikan ke cara 'manual' (tanpa CategoryResource)
            //
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),

            'variants' => ProductVariantResource::collection($variants),

            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
