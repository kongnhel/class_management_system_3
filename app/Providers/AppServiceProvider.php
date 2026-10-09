<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            \URL::forceScheme('https');
        }

        RateLimiter::for('forgot-password', function (Request $request) {
            return Limit::perHour(5)->by(strtolower((string) $request->input('email')) ?: $request->ip());
        });

        // Registration is authorised by a student_id_code, which is a dense
        // sequential range — without a limit this endpoint doubles as an
        // unbounded oracle for which pre-created accounts are still claimable.
        RateLimiter::for('registration', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
