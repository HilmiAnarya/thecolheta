<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Auth; // <-- 1. IMPORT FACADE AUTH

class AuthController extends Controller
{
    /**
     * 🔹 REGISTER
     * Register, lalu OTOMATIS LOGIN (membuat sesi)
     */
    public function register(Request $request)
    {
        // Validasi ini sudah cocok dengan $fillable Anda
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:laki-laki,perempuan',
            'birth_date' => 'nullable|date',
        ]);

        //
        // ! 2. PERBAIKAN: User::create Anda sudah LENGKAP
        // ! (Tidak ada '...' lagi)
        //
        $user = User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'phone'      => $validated['phone'] ?? null,
            'gender'     => $validated['gender'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'role'       => 'customer', // default untuk semua user baru
        ]);

        // ! 3. LOGIN USER SETELAH REGISTER (Membuat Sesi)
        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'user'    => $user,
            // 'token'   => $token, // <-- TOKEN DIHAPUS
            'redirect' => $user->role === 'admin' ? '/admin/dashboard' : '/profile',
        ], 201); // Kirim status 201 Created
    }

    /**
     * 🔹 LOGIN
     * Gunakan Auth::attempt() untuk membuat SESI & COOKIE
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // ! 4. PERBAIKAN: Gunakan Auth::attempt() untuk membuat Sesi
        // 'remember' bisa ditambahkan jika Anda punya checkbox "Ingat Saya"
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            // Jika login gagal
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // Jika login berhasil, regenerasi sesi
        $request->session()->regenerate();

        $user = Auth::user();

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user'    => $user, // Kembalikan data user
            // 'token'   => $token, // <-- TOKEN DIHAPUS
            'redirect' => $user->role === 'admin' ? '/admin/dashboard' : '/profile',
        ]);
    }

    /**
     * 🔹 GET CURRENT USER
     * (Disederhanakan untuk 'auth.ts' baru kita)
     */
    public function user(Request $request)
    {
        // Ini akan mengambil user dari sesi cookie
        return response()->json($request->user());
    }

    /**
     * 🔹 LOGOUT
     * Hancurkan Sesi (Session), bukan Token
     */
    public function logout(Request $request)
    {
        // ! 5. PERBAIKAN: Gunakan logout berbasis Sesi
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // HAPUS LOGIKA TOKEN LAMA
        // $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
