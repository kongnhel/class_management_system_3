<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', config('app.locale', 'km'));

        if (!in_array($locale, ['km', 'en'])) {
            $locale = 'km';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
