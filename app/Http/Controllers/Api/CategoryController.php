<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    // Menampilkan semua kategori beserta subkategori dan produk
    public function index()
    {
        return response()->json(Category::with('children', 'products')->get());
    }

    // Menyimpan kategori baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id', // Memastikan parent_id ada
        ]);

        $category = Category::create($validated);
        return response()->json($category, 201);
    }

    // Menampilkan kategori berdasarkan id
    public function show(Category $category)
    {
        return response()->json($category->load('children', 'products'));
    }

    // Mengupdate kategori berdasarkan id
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id', // Memastikan parent_id ada
        ]);

        $category->update($validated);
        return response()->json($category);
    }

    // Menghapus kategori berdasarkan id
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->noContent();
    }
}
