<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TelegramClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PhoneLoginController extends Controller
{
    protected TelegramClientService $telegramClient;

    public function __construct(TelegramClientService $telegramClient)
    {
        $this->telegramClient = $telegramClient;
    }

    public function checkPhone(Request $request)
    {
        $phone = trim($request->input('phone', ''));

        if (! preg_match('/^\+?[0-9]{6,15}$/', $phone)) {
            return response()->json(['exists' => false, 'verified' => false]);
        }

        $user = User::where('phone', $phone)->first();

        if (! $user) {
            return response()->json(['exists' => false, 'verified' => false]);
        }

        return response()->json([
            'exists' => true,
            'verified' => (bool) $user->phone_verified_at,
        ]);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'login_identifier' => ['required', 'string'],
        ]);

        $identifier = trim($request->login_identifier);

        if (! preg_match('/^\+?[0-9]{6,15}$/', $identifier)) {
            return back()->withErrors([
                'login_identifier' => 'សូមបញ្ចូលលេខទូរស័ព្ទត្រឹមត្រូវ។',
            ])->onlyInput('login_identifier');
        }

        $user = User::where('phone', $identifier)->first();

        if (! $user) {
            return back()->withErrors([
                'login_identifier' => 'រកមិនឃើញគណនីនេះក្នុងប្រព័ន្ធ។',
            ])->onlyInput('login_identifier');
        }

        // Phone already verified → require password, no OTP
        if ($user->phone_verified_at) {
            return back()->withErrors([
                'login_identifier' => 'លេខទូរស័ព្ទនេះបានផ្ទៀងផ្ទាត់រួចហើយ។ សូមបញ្ចូលពាក្យសម្ងាត់ដើម្បីចូលប្រព័ន្ធ។',
            ])->onlyInput('login_identifier');
        }

        if (! $user->telegram_chat_id) {
            return back()->withErrors([
                'login_identifier' => 'លេខទូរស័ព្ទនេះមិនទាន់បានភ្ជាប់ជាមួយ Telegram ឡើយ។ សូមភ្ជាប់ជាមុនសិន។',
            ])->onlyInput('login_identifier');
        }

        // Send code via Telegram Client API (appears as official Telegram login code)
        $result = $this->telegramClient->sendCode($identifier);

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

    public function show()
    {
        $userId = session('otp_user_id');
        $purpose = session('otp_purpose');
        $phone = session('otp_phone');

        if (! $userId || $purpose !== 'phone_login' || ! $phone) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (! $user) {
            session()->forget(['otp_user_id', 'otp_purpose', 'otp_phone']);
            return redirect()->route('login');
        }

        if (! $user->telegram_chat_id) {
            session()->forget(['otp_user_id', 'otp_purpose', 'otp_phone']);
            return redirect()->route('login')->with('error', 'Telegram មិនត្រូវបានភ្ជាប់។');
        }

        $maskedPhone = $this->maskPhone($user->phone);

        return view('auth.phone-otp-verify', compact('user', 'maskedPhone'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:5'],
        ]);

        $userId = session('otp_user_id');
        $purpose = session('otp_purpose');
        $phone = session('otp_phone');

        if (! $userId || $purpose !== 'phone_login' || ! $phone) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (! $user) {
            session()->forget(['otp_user_id', 'otp_purpose', 'otp_phone']);
            return redirect()->route('login');
        }

        // Verify code via Telegram Client API (auth.signIn)
        set_time_limit(180);
        $result = $this->telegramClient->verifyCode($phone, $request->otp);

        if ($result['success']) {
            session()->forget(['otp_user_id', 'otp_purpose', 'otp_phone']);

            // Mark phone verified — next login uses password normally
            $user->update([
                'phone_verified_at' => now(),
                'is_verified' => true,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);

            Auth::login($user);

            return redirect()->intended(route('dashboard', absolute: false))
                ->with('success', 'ចូលប្រព័ន្ធដោយជោគជ័យ!');
        }

        // 2FA password required — redirect to 2FA form
        if (($result['message'] ?? '') === '2FA_REQUIRED') {
            $hint = $this->telegramClient->get2faHint();
            session(['otp_2fa_hint' => $hint]);
            return redirect()->route('phone-otp.2fa');
        }

        return back()->with('error', $result['message']);
    }

    public function resend()
    {
        $userId = session('otp_user_id');
        $purpose = session('otp_purpose');
        $phone = session('otp_phone');

        if (! $userId || $purpose !== 'phone_login' || ! $phone) {
            if (request()->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'សូមចូលប្រព័ន្ធម្តងទៀត។'], 401);
            }
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (! $user || ! $user->telegram_chat_id) {
            if (request()->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Telegram មិនត្រូវបានភ្ជាប់។']);
            }
            return redirect()->route('login')->with('error', 'Telegram មិនត្រូវបានភ្ជាប់។');
        }

        // Resend code via Telegram Client API
        set_time_limit(180);
        $result = $this->telegramClient->sendCode($phone);

        if (! $result['success']) {
            if (request()->ajax()) {
                return response()->json(['status' => 'error', 'message' => $result['message'] ?? 'មានបញ្ហា។ សូមព្យាយាមម្តងទៀត។']);
            }
            return back()->with('warning', $result['message'] ?? 'មានបញ្ហា។ សូមព្យាយាមម្តងទៀត។');
        }

        if (request()->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'កូដថ្មីត្រូវបានផ្ញើទៅ Telegram របស់អ្នក។']);
        }

        return back()->with('success', 'កូដថ្មីត្រូវបានផ្ញើទៅ Telegram របស់អ្នក។');
    }

    public function show2fa()
    {
        $userId = session('otp_user_id');
        $purpose = session('otp_purpose');
        $phone = session('otp_phone');
        $hint = session('otp_2fa_hint', '');

        if (! $userId || $purpose !== 'phone_login' || ! $phone) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        if (! $user) {
            session()->forget(['otp_user_id', 'otp_purpose', 'otp_phone', 'otp_2fa_hint']);
            return redirect()->route('login');
        }

        $maskedPhone = $this->maskPhone($user->phone);

        return view('auth.phone-2fa', compact('user', 'maskedPhone', 'hint'));
    }

    public function verify2fa(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $userId = session('otp_user_id');
        $purpose = session('otp_purpose');
        $phone = session('otp_phone');

        if (! $userId || $purpose !== 'phone_login' || ! $phone) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        if (! $user) {
            session()->forget(['otp_user_id', 'otp_purpose', 'otp_phone', 'otp_2fa_hint']);
            return redirect()->route('login');
        }

        set_time_limit(180);
        $result = $this->telegramClient->verify2fa($phone, $request->password);

        if ($result['success']) {
            session()->forget(['otp_user_id', 'otp_purpose', 'otp_phone', 'otp_2fa_hint']);

            // Mark phone verified — next login uses password normally
            $user->update([
                'phone_verified_at' => now(),
                'is_verified' => true,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);

            Auth::login($user);

            return redirect()->intended(route('dashboard', absolute: false))
                ->with('success', 'ចូលប្រព័ន្ធដោយជោគជ័យ!');
        }

        return back()->with('error', $result['message']);
    }

    protected function maskPhone(string $phone): string
    {
        $len = strlen($phone);
        if ($len <= 4) {
            return $phone;
        }

        return substr($phone, 0, 3) . str_repeat('*', $len - 5) . substr($phone, -2);
    }
}