<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Add security headers to all HTTP responses
 * Protects against common web vulnerabilities
 */
class AddSecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Prevent MIME type sniffing
        $response->header('X-Content-Type-Options', 'nosniff');

        // 2. Prevent Clickjacking attacks
        $response->header('X-Frame-Options', 'DENY');

        // 3. Enable XSS Protection in older browsers
        $response->header('X-XSS-Protection', '1; mode=block');

        // 4. Force HTTPS in production
        if (config('app.env') === 'production') {
            $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        /**
         * 5. Content Security Policy (Updated for Livewire & Assets)
         * - ថែម 'unsafe-eval' ក្នុង script-src ដើម្បីឱ្យ Livewire ដើរ
         * - ថែម 'unsafe-inline' ក្នុង style-src ដើម្បីឱ្យ CSS ដើរ
         * - ថែម https://fonts.gstatic.com សម្រាប់ Font
         */

        $csp = "default-src 'self' http://localhost:* http://127.0.0.1:* [::1]:*; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' " .
               "https://cdn.jsdelivr.net https://www.gstatic.com https://unpkg.com " .
               "http://localhost:* http://127.0.0.1:* [::1]:*; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://fonts.bunny.net " .
               "http://localhost:* http://127.0.0.1:* [::1]:*; " .
               "img-src 'self' data: https: http://localhost:* http://127.0.0.1:* [::1]:*; " .
               "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com https://fonts.bunny.net; " .
               "connect-src 'self' https://www.gstatic.com https://firebase.googleapis.com " .
               "ws://localhost:* ws://127.0.0.1:* http://localhost:* http://127.0.0.1:* [::1]:*;";

        // $response->header('Content-Security-Policy', $csp);

        // 6. Remove server header info
        $response->header('Server', 'Server');

        // 7. Prevent referrer leaking
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 8. Permissions Policy
        $response->header(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=()'
        );

        return $response;
    }
}