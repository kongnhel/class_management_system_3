<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        $token = (string) Str::uuid();

        Cache::put('login_token_'.$token, true, now()->addMinutes(2));

        $qrCode = QrCode::size(200)
            ->color(16, 185, 129)
            ->margin(1)
            ->generate($token);

        return view('auth.login', [
            'qrCode' => $qrCode,
            'token' => $token,
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
