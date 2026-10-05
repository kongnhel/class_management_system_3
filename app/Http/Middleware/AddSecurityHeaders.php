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
        if (! method_exists($response, 'header')) {
            return $response;
        }

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
        // 5. Content Security Policy
        // 'unsafe-inline' + 'unsafe-eval' in script-src are required by Livewire
        // 'unsafe-inline' in style-src is required by Tailwind + Livewire inline styles
        // localhost entries are for development; harmless in production
        // challenges.cloudflare.com is Cloudflare Turnstile (the CAPTCHA
        // replacement on the guest forms). It needs script-src for api.js,
        // frame-src for the widget iframe and connect-src for the widget's
        // own calls. Without all three the widget never renders, no token is
        // submitted and every login/register/reset is rejected.
        $csp = "default-src 'self' http://localhost:* http://127.0.0.1:* [::1]:*; ".
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' ".
               'https://cdn.jsdelivr.net https://www.gstatic.com https://unpkg.com '.
               'https://challenges.cloudflare.com '.
               'http://localhost:* http://127.0.0.1:* [::1]:*; '.
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://fonts.bunny.net ".
               'http://localhost:* http://127.0.0.1:* [::1]:*; '.
               "img-src 'self' data: https: http://localhost:* http://127.0.0.1:* [::1]:*; ".
               "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com https://fonts.bunny.net; ".
               "connect-src 'self' https://www.gstatic.com https://firebase.googleapis.com ".
               'https://challenges.cloudflare.com '.
               'ws://localhost:* ws://127.0.0.1:* http://localhost:* http://127.0.0.1:* [::1]:*; '.
               "frame-src 'self' https://challenges.cloudflare.com ".
               'http://localhost:* http://127.0.0.1:* [::1]:*;';

        $response->header('Content-Security-Policy', $csp);

        // 6. Remove server header info
        $response->header('Server', 'Server');

        // 7. Prevent referrer leaking
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 8. Permissions Policy
        // $response->header(
        //     'Permissions-Policy',
        //     'geolocation=(), microphone=(), camera=()'
        // );

        return $response;
    }
}
