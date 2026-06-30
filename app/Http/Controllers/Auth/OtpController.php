<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function show()
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->is_verified) {
            return redirect()->route('dashboard');
        }

        $maskedContact = $this->getMaskedContact($user);

        return view('auth.verify-otp', compact('user', 'maskedContact'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:5',
        ]);

        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $result = $this->otpService->verifyOtp($user, $request->otp);

        if ($result['success']) {
            return redirect()->route('dashboard')
                ->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function resend()
    {
        $user = Auth::user();

        if (! $user) {
            if (request()->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'សូមចូលប្រព័ន្ធម្តងទៀត។'], 401);
            }
            return redirect()->route('login');
        }

        $method = $user->verification_method ?? 'email';

        if ($method === 'telegram' && ! $user->telegram_chat_id) {
            $method = 'email';
        }

        $sent = $this->otpService->sendOtp($user, $method);

        if (! $sent) {
            if (request()->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'សូមរង់ចាំមួយភ្លែតមុននឹងស្នើរកូដថ្មី។']);
            }
            return back()->with('warning', 'សូមរង់ចាំមួយភ្លែតមុននឹងស្នើរកូដថ្មី។');
        }

        $maskedContact = $this->getMaskedContact($user);

        if (request()->ajax()) {
            return response()->json(['status' => 'success', 'message' => "កូដផ្ទៀងផ្ទាត់ថ្មីត្រូវបានផ្ញើទៅកាន់ {$maskedContact}។"]);
        }

        return back()->with('success', "កូដផ្ទៀងផ្ទាត់ថ្មីត្រូវបានផ្ញើទៅកាន់ {$maskedContact}។");
    }

    public function showLookup()
    {
        return view('auth.check-verification');
    }

    public function lookup(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
        ]);

        $identifier = trim($request->identifier);

        $user = User::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->orWhere('student_id_code', $identifier)
            ->first();

        if (! $user) {
            return back()->with('error', 'រកមិនឃើញគណនីនេះក្នុងប្រព័ន្ធទេ។');
        }

        if ($user->is_verified) {
            return back()->with('info', 'គណនីនេះត្រូវបានផ្ទៀងផ្ទាត់រួចហើយ។ អ្នកអាចចូលប្រើប្រាស់បាន។');
        }

        Auth::login($user);

        return redirect()->route('otp.show');
    }

    protected function getMaskedContact(User $user): string
    {
        if ($user->verification_method === 'telegram' && $user->phone) {
            return $this->maskPhone($user->phone);
        }

        return $this->maskEmail($user->email);
    }

    protected function maskPhone(string $phone): string
    {
        $len = strlen($phone);
        if ($len <= 4) return $phone;

        $visible = substr($phone, 0, 3) . str_repeat('*', $len - 5) . substr($phone, -2);
        return $visible;
    }

    protected function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) return $email;

        $name = $parts[0];
        $domain = $parts[1];

        if (strlen($name) <= 2) {
            $masked = $name[0] . '**';
        } else {
            $masked = $name[0] . str_repeat('*', strlen($name) - 2) . $name[strlen($name) - 1];
        }

        return $masked . '@' . $domain;
    }
}