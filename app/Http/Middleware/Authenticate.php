<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Handle unauthenticated users.
     */
    protected function redirectTo($request): ?string
    {
        // Jika request ke API, balikan JSON error
        if ($request->expectsJson()) {
            abort(response()->json([
                'message' => 'Unauthenticated.',
            ], 401));
        }

        // Jika bukan API (web), redirect ke login
        return route('login');
    }
}
