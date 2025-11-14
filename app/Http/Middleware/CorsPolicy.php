<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsPolicy
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:5173');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set(
        'X-XSRF-TOKEN',   // <--- WAJIB
        'X-CSRF-TOKEN',
        'X-Requested-With',
        'Content-Type',
        'Accept');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        if ($request->getMethod() === 'OPTIONS') {
            $response->setStatusCode(204);
        }

        return $response;
    }
}
