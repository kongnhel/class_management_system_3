<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class UtilityController extends Controller
{
    /**
     * Serve the local Livewire JavaScript bundle with long-cache headers.
     */
    public function livewireJs()
    {
        $path = base_path('vendor/livewire/livewire/dist/livewire.js');

        if (! File::exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'application/javascript; charset=utf-8',
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }

    /**
     * Switch the application locale (km / en).
     */
    public function switchLocale(string $locale)
    {
        if (! in_array($locale, ['km', 'en'])) {
            abort(400);
        }

        session(['locale' => $locale]);
        app()->setLocale($locale);

        return redirect()->back();
    }

    /**
     * Landing route: redirect based on authentication and role.
     */
    public function root()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            if ($user->isProfessor()) {
                return redirect()->route('professor.dashboard');
            }
            if ($user->isStudent()) {
                return redirect()->route('student.dashboard');
            }

            return redirect()->route('auth.login');
        }

        return redirect()->route('login');
    }

    /**
     * Generic dashboard redirect based on role.
     */
    public function dashboard()
    {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif (Auth::user()->isProfessor()) {
            return redirect()->route('professor.dashboard');
        }

        return redirect()->route('student.dashboard');
    }

    /**
     * Return the authenticated user (Sanctum API).
     */
    public function apiUser(Request $request)
    {
        return $request->user();
    }
}
