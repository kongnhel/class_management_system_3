<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TurnstileService
{
    private const SITEVERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    private const TIMEOUT_SECONDS = 10;

    private const MAX_TOKEN_LENGTH = 2048;

    public function isEnabled(): bool
    {
        return (bool) config('services.turnstile.secret');
    }

    /**
     * Validate a cf-turnstile-response token against Cloudflare siteverify.
     *
     * Returns false only for a definite verdict against the request: a bad,
     * expired or replayed token, a mismatched action, or a hostname outside
     * the allowlist. When siteverify itself cannot be reached the check fails
     * open, so a Cloudflare outage cannot lock every user out of the portal.
     */
    public function verify(string $token, ?string $remoteIp = null, ?string $action = null): bool
    {
        if (! $this->isEnabled()) {
            return true;
        }

        if (trim($token) === '' || strlen($token) > self::MAX_TOKEN_LENGTH) {
            return false;
        }

        $expectedHostnames = $this->expectedHostnames();

        if ($expectedHostnames === []) {
            Log::warning('Turnstile: TURNSTILE_HOSTNAMES is empty, refusing token.');

            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(self::TIMEOUT_SECONDS)
                ->post(self::SITEVERIFY_URL, array_filter([
                    'secret' => config('services.turnstile.secret'),
                    'response' => $token,
                    'remoteip' => $remoteIp,
                ], fn ($value) => $value !== null && $value !== ''));
        } catch (Throwable $e) {
            Log::warning('Turnstile: siteverify unreachable, failing open. '.$e->getMessage());

            return true;
        }

        if (! $response->successful()) {
            Log::warning('Turnstile: siteverify returned HTTP '.$response->status().', failing open.');

            return true;
        }

        $result = $response->json();

        if (! is_array($result) || ($result['success'] ?? false) !== true) {
            Log::info('Turnstile: token rejected. error-codes='.json_encode($result['error-codes'] ?? null));

            return false;
        }

        if ($action !== null && ($result['action'] ?? null) !== $action) {
            Log::warning('Turnstile: action mismatch. expected='.$action.' got='.($result['action'] ?? 'null'));

            return false;
        }

        $hostname = $result['hostname'] ?? null;

        if (! is_string($hostname) || ! in_array($hostname, $expectedHostnames, true)) {
            Log::warning('Turnstile: hostname outside allowlist. got='.var_export($hostname, true));

            return false;
        }

        return true;
    }

    /**
     * Approved frontend hostnames from TURNSTILE_HOSTNAMES (comma separated).
     *
     * @return list<string>
     */
    public function expectedHostnames(): array
    {
        return array_values(array_filter(array_map(
            'trim',
            explode(',', (string) config('services.turnstile.hostnames', ''))
        )));
    }
}
