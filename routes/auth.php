<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PhoneLoginController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\StudentRegistrationController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Phone + Telegram OTP Login
    Route::get('phone-login/check', [PhoneLoginController::class, 'checkPhone'])
        ->name('phone-otp.check');
    Route::post('phone-login', [PhoneLoginController::class, 'sendOtp'])
        ->name('phone-otp.send');
    Route::get('phone-login/verify', [PhoneLoginController::class, 'show'])
        ->name('phone-otp.show');
    Route::post('phone-login/verify', [PhoneLoginController::class, 'verify'])
        ->name('phone-otp.verify');
    Route::post('phone-login/resend', [PhoneLoginController::class, 'resend'])
        ->name('phone-otp.resend');
    Route::get('phone-login/2fa', [PhoneLoginController::class, 'show2fa'])
        ->name('phone-otp.2fa');
    Route::post('phone-login/2fa', [PhoneLoginController::class, 'verify2fa'])
        ->name('phone-otp.2fa.verify');

    Route::get('check-verification', [OtpController::class, 'showLookup'])->name('check-verification');
    Route::post('check-verification', [OtpController::class, 'lookup'])->name('check-verification.lookup');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');

    Route::middleware('registration.open')->group(function () {
        Route::get('register', [StudentRegistrationController::class, 'create'])
            ->name('register');

        Route::post('register', [StudentRegistrationController::class, 'store']);
    });
});

Route::middleware('auth')->group(function () {
    Route::get('verify-otp', [OtpController::class, 'show'])->name('otp.show');
    Route::post('verify-otp', [OtpController::class, 'verify'])->name('otp.verify');
    Route::post('verify-otp/resend', [OtpController::class, 'resend'])->name('otp.resend');

    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
