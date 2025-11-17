<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
// use Illuminate\Http\Middleware\HandleCors; // <-- KITA TIDAK PAKAI INI

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',     // <-- Berisi API stateful kita (Login, Cart, User)
        commands: __DIR__ . '/../routes/console.php',
        api: __DIR__ . '/../routes/api.php',       // <-- Berisi API stateless (Products.index)
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        /**
         * 🌍 GLOBAL MIDDLEWARE
         * Middleware yang berjalan untuk SEMUA request
         */
        $middleware->use([
            // ! INI PERBAIKANNYA:
            // ! Kita daftarkan middleware CORS KUSTOM Anda
            \App\Http\Middleware\CorsPolicy::class,

            // ! Kita HAPUS HandleCors bawaan untuk menghindari konflik
            // \Illuminate\Http\Middleware\HandleCors::class,

            \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        ]);

        /**
         * 🧩 MIDDLEWARE GROUP: API
         * Kita biarkan ini bersih (stateless) karena rute
         * 'api' kita sekarang hanya untuk data publik.
         */
        $middleware->api(append: [
             \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        /**
         * 🧩 MIDDLEWARE GROUP: WEB
         * Kita tidak sentuh ini. Ini sudah benar dan 'stateful'
         * (punya StartSession, EncryptCookies, VerifyCsrfToken).
         * Semua rute API stateful kita (dari routes/web.php) akan
         * otomatis menggunakan grup ini.
         */
        $middleware->group('web', [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class, // <-- Pastikan $except di file ini benar
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        /**
         * 🧠 ROUTE MIDDLEWARE (alias)
         * (Biarkan default, sudah benar)
         */
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
