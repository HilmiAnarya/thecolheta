<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Middleware\TrustProxies;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        api: __DIR__ . '/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        /**
         * 🌍 GLOBAL MIDDLEWARE
         * Jalan untuk semua request (web & api)
         */
        $middleware->use([
            \App\Http\Middleware\CorsPolicy::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        ]);

        /**
         * 🧩 MIDDLEWARE GROUP: API
         * ❌ Tanpa throttle limiter
         */
        $middleware->group('api', [
            EnsureFrontendRequestsAreStateful::class, // penting untuk Sanctum SPA/API
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        /**
         * 🧩 MIDDLEWARE GROUP: WEB
         */
        $middleware->group('web', [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        /**
         * 🧠 ROUTE MIDDLEWARE (alias)
         */
        $middleware->alias([
            'auth'         => \App\Http\Middleware\Authenticate::class,
            //'guest'        => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'role'         => \App\Http\Middleware\RoleMiddleware::class,
            'auth:sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
