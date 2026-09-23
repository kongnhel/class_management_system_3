<?php

namespace App\Http\Controllers\professor;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ProfessorTrustedDevice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TrustedDeviceController extends Controller
{
    public function index(Request $request): View
    {
        $devices = ProfessorTrustedDevice::where('user_id', $request->user()->id)
            ->latest('trusted_at')
            ->get();

        return view('professor.security.trusted-devices', compact('devices'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isProfessor(), 403);

        $validated = $request->validate([
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        $lockKey = 'trusted-device-registration:' . $request->user()->id . ':' . $request->session()->getId();

        return Cache::lock($lockKey, 10)->block(3, function () use ($request, $validated): RedirectResponse {
            // Reuse the current trusted device when this browser is already trusted.
            // This makes repeated clicks idempotent and prevents duplicate records.
            $existingToken = $request->cookie('professor_trusted_device');
            $existingDevice = $existingToken
                ? ProfessorTrustedDevice::where('user_id', $request->user()->id)
                    ->whereNull('revoked_at')
                    ->where('token_hash', hash('sha256', $existingToken))
                    ->first()
                : null;

            if ($existingDevice) {
                $existingDevice->update([
                    'last_used_at' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return redirect()->back()
                    ->with('success', 'This device is already trusted.');
            }

            $rawToken = Str::random(80);
            $trustedDevice = ProfessorTrustedDevice::create([
                'user_id' => $request->user()->id,
                'token_hash' => hash('sha256', $rawToken),
                'device_name' => $validated['device_name'] ?? Str::limit((string) $request->userAgent(), 115),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'trusted_at' => now(),
            ]);

            if (Schema::hasTable('audit_logs')) {
                AuditLog::log([
                    'action' => 'professor_trusted_device_registered',
                    'auditable_type' => get_class($trustedDevice),
                    'auditable_id' => $trustedDevice->id,
                    'new_values' => [
                        'device_name' => $trustedDevice->device_name,
                        'ip_address' => $trustedDevice->ip_address,
                    ],
                    'description' => 'Professor registered a trusted device.',
                ]);
            }

            return redirect()->back()
                ->withCookie(cookie(
                    'professor_trusted_device',
                    $rawToken,
                    525600,
                    '/',
                    null,
                    $request->isSecure(),
                    true,
                    false,
                    'lax'
                ))
                ->with('success', 'This device is now trusted for professor security checks.');
        });
    }

    public function revoke(Request $request, ProfessorTrustedDevice $trustedDevice): RedirectResponse
    {
        abort_unless($trustedDevice->user_id === $request->user()->id, 403);

        $trustedDevice->update(['revoked_at' => now()]);

        return redirect()->route('professor.security.trusted-devices')
            ->withCookie(cookie()->forget('professor_trusted_device'))
            ->with('success', 'The trusted device has been revoked.');
    }
}
