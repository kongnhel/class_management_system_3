<?php

namespace App\Rules;

use App\Services\TurnstileService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;

class Turnstile implements ValidationRule
{
    /**
     * Makes Laravel run this rule even when the attribute is absent or an
     * empty string. Without it the check is skipped for exactly the case
     * it exists for: submitting without completing the widget.
     */
    public bool $implicit = true;

    public function __construct(private readonly string $action) {}

    /**
     * Guards a single request-surface. Handles the missing/empty token itself
     * so callers never need a paired `required` rule - if the widget never
     * rendered (script blocked, keys unset) nothing is ever blocked.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $service = app(TurnstileService::class);

        if (! $service->isEnabled()) {
            if (app()->isProduction()) {
                Log::warning('Turnstile: TURNSTILE_SECRET is empty, protection disabled.');
            }

            return;
        }

        if (! is_string($value) || trim($value) === '') {
            $fail(__('turnstile_failed'));

            return;
        }

        if (! $service->verify($value, request()->ip(), $this->action)) {
            $fail(__('turnstile_failed'));
        }
    }
}
