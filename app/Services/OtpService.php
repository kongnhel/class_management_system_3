<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    const OTP_LENGTH = 5;
    const OTP_EXPIRY_MINUTES = 10;
    const OTP_MAX_ATTEMPTS = 5;
    const OTP_RESEND_COOLDOWN_SECONDS = 60;

    public function generateOtp(): string
    {
        return str_pad((string) random_int(0, pow(10, self::OTP_LENGTH) - 1), self::OTP_LENGTH, '0', STR_PAD_LEFT);
    }

    public function sendOtp(User $user, string $method = 'email'): bool
    {
        $cooldownKey = 'otp_cooldown_' . $user->id;
        if (Cache::has($cooldownKey)) {
            return false;
        }

        $otp = $this->generateOtp();
        $expiresAt = now()->addMinutes(self::OTP_EXPIRY_MINUTES);

        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => $expiresAt,
            'otp_attempts' => 0,
            'otp_last_sent_at' => now(),
            'verification_method' => $method,
        ]);

        Cache::put($cooldownKey, true, self::OTP_RESEND_COOLDOWN_SECONDS);

        if ($method === 'email') {
            $this->sendEmailOtp($user, $otp);
        } elseif ($method === 'telegram') {
            $this->sendTelegramOtp($user, $otp);
        }

        return true;
    }

    public function verifyOtp(User $user, string $otp): array
    {
        if ($user->otp_attempts >= self::OTP_MAX_ATTEMPTS) {
            return ['success' => false, 'message' => 'អ្នកបានព្យាយាមច្រើនដងពេក។ សូមព្យាយាមម្តងទៀតនៅពេលក្រោយ។'];
        }

        if (! $user->otp_expires_at || $user->otp_expires_at->isPast()) {
            return ['success' => false, 'message' => 'កូដ OTP បានផុតកំណត់។ សូមស្នើរកូដថ្មី។'];
        }

        if (! hash_equals($user->otp_code, $otp)) {
            $user->increment('otp_attempts');
            $remaining = self::OTP_MAX_ATTEMPTS - ($user->otp_attempts);
            return ['success' => false, 'message' => "កូដ OTP មិនត្រឹមត្រូវ។ នៅសល់ {$remaining} ឱកាស។"];
        }

        $user->update([
            'otp_code' => null,
            'otp_expires_at' => null,
            'otp_attempts' => 0,
            'is_verified' => true,
        ]);

        return ['success' => true, 'message' => 'ផ្ទៀងផ្ទាត់ជោគជ័យ!'];
    }

    public function getRemainingCooldown(User $user): int
    {
        if (! $user->otp_last_sent_at) {
            return 0;
        }

        $elapsed = now()->diffInSeconds($user->otp_last_sent_at, false);
        $remaining = self::OTP_RESEND_COOLDOWN_SECONDS + $elapsed;

        return max(0, $remaining);
    }

    public function getRemainingAttempts(User $user): int
    {
        return max(0, self::OTP_MAX_ATTEMPTS - $user->otp_attempts);
    }

    protected function sendEmailOtp(User $user, string $otp): void
    {
        try {
            Mail::raw("កូដផ្ទៀងផ្ទាត់របស់អ្នកគឺ: {$otp}\nកូដនេះនឹងផុតកំណត់នៅក្នុង " . self::OTP_EXPIRY_MINUTES . " នាទី។", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('កូដផ្ទៀងផ្ទាត់ NMU');
            });
        } catch (\Exception $e) {
            \Log::error('OTP Email Error: ' . $e->getMessage());
        }
    }

    protected function sendTelegramOtp(User $user, string $otp): void
    {
        if (! $user->telegram_chat_id) {
            return;
        }

        try {
            $botToken = config('services.telegram.bot_token');
            $chatId = $user->telegram_chat_id;

            $message = "🔐 *កូដផ្ទៀងផ្ទាត់ NMU*\n\n";
            $message .= "កូដរបស់អ្នកគឺ: `{$otp}`\n";
            $message .= "ផុតកំណត់ក្នុង " . self::OTP_EXPIRY_MINUTES . " នាទី។";

            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
            \Http::post($url, [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Exception $e) {
            \Log::error('OTP Telegram Error: ' . $e->getMessage());
        }
    }
}