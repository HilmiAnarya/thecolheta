<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartItemResource; // <-- Gunakan Resource kita
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Tampilkan semua item di keranjang user yang sedang login.
     * (GET /api/cart)
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // 1. Ambil item-nya dulu
        $cartItems = CartItem::where('user_id', $user->id)->get();

        // 2. PAKSA load relasi yang kita butuhkan
        $cartItems->load('variant.product');

        // 3. Kembalikan resource
        return CartItemResource::collection($cartItems);
    }

    /**
     * Tambah/update item ke keranjang.
     * (POST /api/cart/add)
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();
        $variantId = $validated['variant_id'];
        $quantity = $validated['quantity'];

        // Cek stok varian
        $variant = ProductVariant::find($variantId);

        // Cek apakah item ini SUDAH ADA di keranjang user?
        $cartItem = CartItem::where('user_id', $user->id)
                            ->where('product_variant_id', $variantId)
                            ->first();

        if ($cartItem) {
            // --- JIKA SUDAH ADA: Update quantity ---
            $newQuantity = $cartItem->quantity + $quantity;

            // Cek stok lagi
            if ($variant->stock < $newQuantity) {
                 return response()->json(['message' => 'Stok tidak mencukupi untuk jumlah total'], 400);
            }
            $cartItem->quantity = $newQuantity;
            $cartItem->save();

            return new CartItemResource($cartItem->load('variant.product'));

        } else {
            // --- JIKA BELUM ADA: Buat item baru ---

            // Cek stok awal
            if ($variant->stock < $quantity) {
                return response()->json(['message' => 'Stok tidak mencukupi'], 400);
            }

            $cartItem = CartItem::create([
                'user_id' => $user->id,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
            ]);

            // Kembalikan data item yang baru dibuat dengan status 201 (Created)
            return (new CartItemResource($cartItem->load('variant.product')))
                   ->response()
                   ->setStatusCode(201);
        }
    }

    /**
     * Hapus item dari keranjang.
     * (DELETE /api/cart/remove/{cartItem})
     */
    public function remove(Request $request, CartItem $cartItem)
    {
        // ! Keamanan: Pastikan user hanya bisa menghapus item miliknya sendiri
        if ($request->user()->id !== $cartItem->user_id) {
            return response()->json(['message' => 'Tidak diizinkan'], 403);
        }

        $cartItem->delete();

        // Kirim 204 No Content (standar untuk delete)
        return response()->noContent();
    }

    public function updateQuantity(Request $request, CartItem $cartItem)
    {
        // 1. Validasi
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1', // Kuantitas baru
        ]);

        // 2. Keamanan: Pastikan user ini pemilik item
        if ($request->user()->id !== $cartItem->user_id) {
            return response()->json(['message' => 'Tidak diizinkan'], 403);
        }

        // 3. Cek Stok
        // Muat relasi 'variant' jika belum ada
        $cartItem->load('variant');
        $variant = $cartItem->variant;

        if ($variant->stock < $validated['quantity']) {
            return response()->json(['message' => 'Stok tidak mencukupi'], 400);
        }

        // 4. Update kuantitas
        $cartItem->quantity = $validated['quantity'];
        $cartItem->save();

        // 5. Kembalikan data item yang sudah di-update
        return new CartItemResource($cartItem->load('variant.product'));
    }
}
