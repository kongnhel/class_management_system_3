<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\TelegramClientService;
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
        $identifier = trim($request->login_identifier);

        // Phone number login
        if (preg_match('/^\+?[0-9]{6,15}$/', $identifier)) {
            $user = User::where('phone', $identifier)->first();

            if (! $user) {
                return back()->withErrors([
                    'login_identifier' => 'រកមិនឃើញគណនីនេះក្នុងប្រព័ន្ធ។',
                ])->onlyInput('login_identifier');
            }

            // Password provided → try normal password login
            if (! empty($request->password)) {
                if (! Auth::attempt(['email' => $user->email, 'password' => $request->password], $request->boolean('remember'))) {
                    return back()->withErrors([
                        'login_identifier' => 'ពាក្យសម្ងាត់មិនត្រឹមត្រូវ។',
                    ])->onlyInput('login_identifier');
                }

                $request->session()->regenerate();

                // Mark phone as verified on first successful password login
                if (! $user->phone_verified_at) {
                    $user->update(['phone_verified_at' => now()]);
                }

                $user->update([
                    'is_verified' => true,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);

                return redirect()->intended(route('dashboard', absolute: false));
            }

            // No password → check if phone already verified
            if ($user->phone_verified_at) {
                // Phone already verified → require password
                return back()->withErrors([
                    'password' => 'សូមបញ្ចូលពាក្យសម្ងាត់ដើម្បីចូលប្រព័ន្ធ។',
                ])->onlyInput('login_identifier');
            }

            // Phone not verified yet → send Telegram OTP
            if (! $user->telegram_chat_id) {
                return back()->withErrors([
                    'login_identifier' => 'លេខទូរស័ព្ទនេះមិនទាន់បានភ្ជាប់ជាមួយ Telegram ឡើយ។ សូមភ្ជាប់ជាមុនសិន។',
                ])->onlyInput('login_identifier');
            }

            $telegramClient = app(TelegramClientService::class);
            set_time_limit(180);
            $result = $telegramClient->sendCode($identifier);

            if (! $result['success']) {
                return back()->withErrors([
                    'login_identifier' => $result['message'] ?? 'មានបញ្ហាក្នុងការផ្ញើកូដ។ សូមព្យាយាមម្តងទៀត។',
                ])->onlyInput('login_identifier');
            }

            session(['otp_user_id' => $user->id]);
            session(['otp_purpose' => 'phone_login']);
            session(['otp_phone' => $identifier]);

            return redirect()->route('phone-otp.show');
        }

        // Email/Student ID + Password flow
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        if ($user) {
            $user->update([
                'is_verified' => true,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
        }

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