<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! auth()->check()) {
            return redirect('/login'); // Redirect to login if not authenticated
        }

        if (! in_array(auth()->user()->role, $roles)) {
            // Redirect or show an error if role is not allowed
            return abort(403, 'Unauthorized action.'); // 403 Forbidden
        }

        return $next($request);
    }
}
