<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * 🔹 Admin melihat semua user
     */
    public function index()
    {
        $users = User::latest()->paginate(10);
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * 🔹 Admin menambahkan user baru secara manual
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6',
            'phone'      => 'nullable|string|max:20',
            'gender'     => 'nullable|in:laki-laki,perempuan',
            'birth_date' => 'nullable|date',
            'role'       => 'required|in:admin,customer',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * 🔹 Show specific user (Admin or self)
     */
    public function show(User $user, Request $request)
    {
        $authUser = $request->user();

        if ($authUser->role !== 'admin' && $authUser->id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * 🔹 Update user profile (customer update dirinya, admin bisa update siapa pun)
     */
    public function update(Request $request, User $user)
    {
        $authUser = $request->user();

        // Validasi role: hanya admin yang boleh ubah user lain
        if ($authUser->role !== 'admin' && $authUser->id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name'       => 'sometimes|string|max:100',
            'email'      => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')->ignore($user->id)
            ],
            'phone'      => 'nullable|string|max:20',
            'gender'     => 'nullable|in:laki-laki,perempuan',
            'birth_date' => 'nullable|date',
            'role'       => $authUser->role === 'admin' ? 'in:admin,customer' : '', // hanya admin bisa ubah role
            'password'   => 'nullable|string|min:6',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    /**
     * 🔹 Hapus user (Admin only)
     */
    public function destroy(User $user, Request $request)
    {
        $authUser = $request->user();

        if ($authUser->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}
