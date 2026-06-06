<?php

namespace App\Http\Requests\Auth;

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
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    //     public function authenticate(): void
    //     {
    //         $this->ensureIsNotRateLimited();

    //         if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
    //             RateLimiter::hit($this->throttleKey());

    //             throw ValidationException::withMessages([
    //                 'email' => trans('auth.failed'),
    //             ]);
    //         }

    //         RateLimiter::clear($this->throttleKey());
    //     }

    //     /**
    //      * Ensure the login request is not rate limited.
    //      *
    //      * @throws \Illuminate\Validation\ValidationException
    //      */
    //     // public function ensureIsNotRateLimited(): void
    //     // {
    //     //     if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
    //     //         return;
    //     //     }

    //     //     event(new Lockout($this));

    //     //     $seconds = RateLimiter::availableIn($this->throttleKey());

    //     //     throw ValidationException::withMessages([
    //     //         'email' => trans('auth.throttle', [
    //     //             'seconds' => $seconds,
    //     //             'minutes' => ceil($seconds / 60),
    //     //         ]),
    //     //     ]);
    //     // }
    // public function ensureIsNotRateLimited(): void
    // {
    //     if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
    //         return;
    //     }

    //     event(new Lockout($this));

    //     // កំណត់ឱ្យវា Lock ១៥ នាទី (៩០០ វិនាទី) នៅត្រង់នេះ
    //     $seconds = RateLimiter::availableIn($this->throttleKey());

    //     // ប្រសិនបើចង់ឱ្យវា Lock ១៥ នាទីភ្លាមៗពេលគ្រប់ ៥ ដង
    //     // អ្នកអាចប្រើ RateLimiter::hit($this->throttleKey(), 900); នៅត្រង់នេះក៏បាន

    //     throw ValidationException::withMessages([
    //         'email' => trans('auth.throttle', [
    //             'seconds' => $seconds,
    //             'minutes' => ceil($seconds / 60),
    //         ]),
    //     ]);
    // }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    // Lock 15 នាទីពេល hit 5 ដង
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            // ← 900 វិនាទី = 15 នាទី
            RateLimiter::hit($this->throttleKey(), 900);

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
} //
