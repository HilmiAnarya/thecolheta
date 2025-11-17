<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use app\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::with('category')->paginate(10));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image_url' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        return response()->json($product->load('category'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $product->update($validated);
        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }

    public function toggleStatus(Product $product)
    {
        // 1. Balik nilainya (true menjadi false, false menjadi true)
        $product->is_active = !$product->is_active;

        // 2. Simpan perubahan ke database
        $product->save();

        // 3. Kembalikan data produk yang sudah diperbarui
        return new ProductResource($product);
    }
}
