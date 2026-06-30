<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login_identifier' => ['required', 'string'],
            'password' => ['nullable', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $identifier = $this->login_identifier;

        // Phone login is handled by PhoneLoginController, not here
        if (preg_match('/^\+?[0-9]{6,15}$/', $identifier)) {
            throw ValidationException::withMessages([
                'login_identifier' => 'សូមប្រើវិធីភ្ជាប់ Telegram OTP សម្រាប់លេខទូរស័ព្ទ។',
            ]);
        }

        // Email/Student ID login requires password
        if (empty($this->password)) {
            throw ValidationException::withMessages([
                'password' => 'សូមបញ្ចូលពាក្យសម្ងាត់។',
            ]);
        }

        $user = User::findForLogin($identifier);

        if (! $user || ! Auth::attempt(['email' => $user->email, 'password' => $this->password], $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey(), 900);

            throw ValidationException::withMessages([
                'login_identifier' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login_identifier' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('login_identifier')).'|'.$this->ip());
    }
}