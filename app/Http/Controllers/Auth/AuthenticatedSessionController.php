<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $identifier = trim($request->login_identifier);

        // Phone number login
        if (preg_match('/^\+?[0-9]{6,15}$/', $identifier)) {
            $user = User::where('phone', $identifier)->first();

            if (! $user) {
                return back()->withErrors([
                    'login_identifier' => 'រកមិនឃើញគណនីនេះក្នុងប្រព័ន្ធ។',
                ])->onlyInput('login_identifier');
            }

            if (! Auth::attempt(['email' => $user->email, 'password' => $request->password], $request->boolean('remember'))) {
                return back()->withErrors([
                    'login_identifier' => 'ពាក្យសម្ងាត់មិនត្រឹមត្រូវ។',
                ])->onlyInput('login_identifier');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Email/Student ID + Password flow
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
