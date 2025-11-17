<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsPolicy
{
    public function handle(Request $request, Closure $next): Response
    {
        // Tangani request 'OPTIONS' (Pre-flight)
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 204)
                ->header('Access-Control-Allow-Origin', 'http://localhost:5173')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                // Pastikan 'X-XSRF-TOKEN' ada di daftar ini
                ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Requested-With, X-CSRF-TOKEN, X-XSRF-TOKEN, Cookie')
                ->header('Access-Control-Allow-Credentials', 'true');
        }

        // Tangani request utama (GET, POST, dll)
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:5173');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Requested-With, X-CSRF-TOKEN, X-XSRF-TOKEN, Cookie');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        return $response;
    }
}
