<?php

use App\Rules\Turnstile;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

function enableTurnstile(string $secret = 'test-secret', string $hostnames = 'localhost'): void
{
    config([
        'services.turnstile.secret' => $secret,
        'services.turnstile.site_key' => 'test-site-key',
        'services.turnstile.hostnames' => $hostnames,
    ]);
}

function turnstileBody(string $action = 'login', string $hostname = 'localhost'): array
{
    return ['success' => true, 'action' => $action, 'hostname' => $hostname];
}

function loginTokenErrors(array $data): array
{
    return Validator::make($data, [
        'cf-turnstile-response' => [new Turnstile('login')],
    ])->errors()->toArray();
}

test('the rule is skipped entirely when no secret is configured', function () {
    enableTurnstile(secret: '');

    expect(loginTokenErrors([]))->toBe([]);
});

test('an absent token is rejected while turnstile is enabled', function () {
    enableTurnstile();

    expect(loginTokenErrors([]))
        ->toHaveKey('cf-turnstile-response')
        ->and(loginTokenErrors([])['cf-turnstile-response'][0])->toBe(__('turnstile_failed'));
});

test('a blank token is rejected while turnstile is enabled', function () {
    enableTurnstile();

    expect(loginTokenErrors(['cf-turnstile-response' => '   ']))
        ->toHaveKey('cf-turnstile-response');
});

test('a token cloudflare verified for this action is accepted', function () {
    enableTurnstile();
    Http::fake(['challenges.cloudflare.com/*' => Http::response(turnstileBody())]);

    expect(loginTokenErrors(['cf-turnstile-response' => 'tok-good']))->toBe([]);
});

test('a token cloudflare rejected is refused', function () {
    enableTurnstile();
    Http::fake(['challenges.cloudflare.com/*' => Http::response([
        'success' => false,
        'error-codes' => ['invalid-input-response'],
    ])]);

    expect(loginTokenErrors(['cf-turnstile-response' => 'tok-bad']))
        ->toHaveKey('cf-turnstile-response');
});

test('a token minted for another form is refused', function () {
    enableTurnstile();
    Http::fake(['challenges.cloudflare.com/*' => Http::response(turnstileBody(action: 'register'))]);

    expect(loginTokenErrors(['cf-turnstile-response' => 'tok-other-action']))
        ->toHaveKey('cf-turnstile-response');
});

test('a token from an unapproved hostname is refused', function () {
    enableTurnstile();
    Http::fake(['challenges.cloudflare.com/*' => Http::response(turnstileBody(hostname: 'evil.example.com'))]);

    expect(loginTokenErrors(['cf-turnstile-response' => 'tok-wrong-host']))
        ->toHaveKey('cf-turnstile-response');
});

test('an empty hostname allowlist refuses every token', function () {
    enableTurnstile(hostnames: '');
    Http::fake(['challenges.cloudflare.com/*' => Http::response(turnstileBody())]);

    expect(loginTokenErrors(['cf-turnstile-response' => 'tok']))
        ->toHaveKey('cf-turnstile-response');
});

test('the check fails open when siteverify is unreachable', function () {
    enableTurnstile();
    Http::fake(fn () => throw new ConnectionException('Could not resolve host'));

    expect(loginTokenErrors(['cf-turnstile-response' => 'tok']))->toBe([]);
});

test('the check fails open when siteverify returns a server error', function () {
    enableTurnstile();
    Http::fake(['challenges.cloudflare.com/*' => Http::response('', 500)]);

    expect(loginTokenErrors(['cf-turnstile-response' => 'tok']))->toBe([]);
});

test('the login route rejects a submit without the turnstile token', function () {
    enableTurnstile();

    $this->post('/login', [
        'login_identifier' => 'someone@example.com',
        'password' => 'secret-password',
    ])->assertSessionHasErrors('cf-turnstile-response');
});

test('the login route accepts a token cloudflare verified', function () {
    enableTurnstile();
    Http::fake(['challenges.cloudflare.com/*' => Http::response(turnstileBody())]);

    $this->post('/login', [
        'login_identifier' => 'someone@example.com',
        'password' => 'secret-password',
        'cf-turnstile-response' => 'tok-good',
    ])->assertSessionDoesntHaveErrors('cf-turnstile-response');
});

test('the login route works normally while turnstile is unconfigured', function () {
    enableTurnstile(secret: '');

    $this->post('/login', [
        'login_identifier' => 'someone@example.com',
        'password' => 'secret-password',
    ])->assertSessionDoesntHaveErrors('cf-turnstile-response');
});

test('the security header csp lets the turnstile widget load', function () {
    $csp = $this->get('/login')->headers->get('Content-Security-Policy');

    expect($csp)->not->toBeNull();

    foreach (['script-src', 'frame-src', 'connect-src'] as $directive) {
        preg_match('/'.preg_quote($directive, '/').'\s+([^;]*)/', (string) $csp, $matches);

        expect($matches[1] ?? '')->toContain('https://challenges.cloudflare.com');
    }
});
