<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductVariant; // <-- 1. Import ProductVariant
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // <-- 2. Import DB untuk Transaction

class ProductController extends Controller
{
    /**
     * Tampilkan semua produk, sekarang memuat relasi 'variants'.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants']);

        //
        // ! INI ADALAH LOGIKA BARU YANG DIPERBAIKI
        //

        // 1. Cek request SPESIFIK dulu (Rekomendasi / Add-On)
        if ($request->filled('limit')) {
            // Ini untuk "You May Also Like"
            $products = $query->latest()->take($request->input('limit'))->get();
            return ProductResource::collection($products);

        } elseif ($request->filled('category_name')) {
            // Ini untuk "Add On" di Halaman Keranjang
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category_name);
            });

        } else {
            // 2. Jika ini request UMUM (dari /products atau /admin)

            // Terapkan filter kategori (jika ada)
            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            // Sembunyikan "Add On" HANYA JIKA BUKAN dari admin
            if ($request->input('include_addons') !== 'true') {
                $query->whereHas('category', function ($q) {
                    $q->where('name', '!=', 'Add On'); // Sesuaikan nama kategori
                });
            }
        }

        // 3. Terapkan sorting (HANYA untuk request umum, bukan 'limit')
        $sort = $request->input('sort', 'latest');
        if ($sort === 'price_asc') {
            $query->orderByRaw(
                '(SELECT MIN(price) FROM product_variants WHERE product_id = products.id) ASC'
            );
        } else if ($sort === 'price_desc') {
            $query->orderByRaw(
                '(SELECT MIN(price) FROM product_variants WHERE product_id = products.id) DESC'
            );
        } else {
            $query->latest();
        }

        // 4. Ambil hasil dengan paginasi
        // (Ini hanya akan berjalan untuk request umum)
        $products = $query->paginate(10)->withQueryString();

        return ProductResource::collection($products);
    }

    /**
     * Tampilkan satu produk, memuat relasi 'variants'.
     */
    public function show(Product $product)
    {
        return new ProductResource($product->load(['category', 'variants']));
    }

    /**
     * Simpan produk BARU dan semua variannya.
     */
    public function store(Request $request)
    {
        // 1. Validasi data produk utama
        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'boolean',

            // 2. Validasi array 'variants'
            'variants' => 'required|array|min:1', // Harus ada min 1 varian
            'variants.*.size' => 'nullable|string|max:100',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imageUrl = $request->file('image')->store('products', 'public');
        }

        // Mulai Database Transaction
        DB::beginTransaction();
        try {
            // 3. Buat Produk Utama
            $product = Product::create([
                'category_id' => $validatedData['category_id'],
                'name' => $validatedData['name'],
                'description' => $validatedData['description'] ?? null,
                'image_url' => $imageUrl,
                'is_active' => $validatedData['is_active'] ?? true,
            ]);

            // 4. Loop dan buat Varian-variannya
            foreach ($validatedData['variants'] as $variantData) {
                $product->variants()->create([
                    'size' => $variantData['size'],
                    'price' => $variantData['price'],
                    'stock' => $variantData['stock'],
                ]);
            }

            // 5. Jika semua berhasil, commit ke database
            DB::commit();

            // Muat relasi varian sebelum mengembalikannya
            return new ProductResource($product->load('variants'));

        } catch (\Exception $e) {
            // 6. Jika ada error, batalkan semua
            DB::rollBack();
            // Hapus gambar yang mungkin sudah ter-upload jika terjadi error
            if ($imageUrl) {
                Storage::disk('public')->delete($imageUrl);
            }
            // Kirim response error
            return response()->json(['message' => 'Gagal menyimpan produk: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update produk dan varian-variannya.
     */
    public function update(Request $request, Product $product)
    {
        // 1. Validasi data produk utama
        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // 'image' adalah file baru, 'image_url' tidak
            'is_active' => 'boolean',

            // 2. Validasi array 'variants'
            'variants' => 'required|array|min:1',
            'variants.*.id' => 'nullable|exists:product_variants,id', // ID untuk varian yang ada
            'variants.*.size' => 'nullable|string|max:100',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
        ]);

        $productData = $request->except(['_method', 'image', 'variants']);
        $productData['is_active'] = $request->input('is_active', $product->is_active);

        // 3. Logika untuk update/mengganti file
        if ($request->hasFile('image')) {
            if ($product->image_url) {
                Storage::disk('public')->delete($product->image_url);
            }
            $productData['image_url'] = $request->file('image')->store('products', 'public');
        }

        // Mulai Database Transaction
        DB::beginTransaction();
        try {
            // 4. Update Produk Utama
            $product->update($productData);

            // 5. Update/Create/Delete Varian
            $incomingVariantIds = [];

            foreach ($validatedData['variants'] as $variantData) {
                if (isset($variantData['id'])) {
                    // Jika ada ID -> Update Varian yang ada
                    $variant = ProductVariant::find($variantData['id']);
                    if ($variant) {
                        $variant->update($variantData);
                        $incomingVariantIds[] = $variant->id;
                    }
                } else {
                    // Jika tidak ada ID -> Buat Varian baru
                    $newVariant = $product->variants()->create($variantData);
                    $incomingVariantIds[] = $newVariant->id;
                }
            }

            // 6. Hapus varian lama yang tidak ada di request baru
            $product->variants()->whereNotIn('id', $incomingVariantIds)->delete();

            // 7. Commit
            DB::commit();

            return new ProductResource($product->load(['category', 'variants']));

        } catch (\Exception $e) {
            // 8. Rollback jika gagal
            DB::rollBack();
            return response()->json(['message' => 'Gagal mengupdate produk: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Hapus produk (dan variannya akan terhapus otomatis by cascade).
     */
    public function destroy(Product $product)
    {
        // Hapus gambar terkait
        if ($product->image_url) {
            Storage::disk('public')->delete($product->image_url);
        }

        $product->delete(); // cascadeOnDelete akan menghapus varian
        return response()->json(['message' => 'Produk berhasil dihapus']);
    }

    /**
     * Toggle status aktif (ini tetap sama).
     */
    public function toggleStatus(Product $product)
    {
        $product->is_active = !$product->is_active;
        $product->save();
        return new ProductResource($product->load('variants'));
    }
}
