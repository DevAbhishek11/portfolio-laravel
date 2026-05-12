<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://unpkg.com; " .
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://cdn.quilljs.com; " .
                "font-src 'self' https://fonts.gstatic.com; " .
                "img-src 'self' data: blob:; " .
                "connect-src 'self';"
        );

        return $response;
    }
}
