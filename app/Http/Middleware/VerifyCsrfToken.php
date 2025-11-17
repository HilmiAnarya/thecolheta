<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Daftar URI yang dikecualikan dari verifikasi CSRF.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/*', // <-- Ini sudah benar untuk /api/login, /api/cart, dll.
        'sanctum/csrf-cookie' // <-- ! TAMBAHKAN INI (Untuk GET request)
    ];
}
