<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\ProfessorTrustedDevice;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProfessorLoginSecurityService
{
    public function recordLogin(Request $request, User $user): void
    {
        if (! $user->isProfessor() || ! Schema::hasTable('professor_trusted_devices')) {
            return;
        }

        $deviceToken = $request->cookie('professor_trusted_device');
        $trustedDevice = $deviceToken
            ? ProfessorTrustedDevice::where('user_id', $user->id)
                ->whereNull('revoked_at')
                ->where('token_hash', hash('sha256', $deviceToken))
                ->first()
            : null;

        if ($trustedDevice) {
            $trustedDevice->update([
                'last_used_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } else {
            $user->notify(new GeneralNotification([
                'title' => 'New professor login detected',
                'message' => 'A login was detected from an untrusted device. IP: '.($request->ip() ?? 'unknown').'. If this was not you, change your password and contact an administrator.',
                'from_user_name' => 'Security system',
            ]));
        }

        if (Schema::hasTable('audit_logs')) {
            AuditLog::log([
                'action' => $trustedDevice ? 'professor_login_trusted_device' : 'professor_login_untrusted_device',
                'auditable_type' => get_class($user),
                'auditable_id' => $user->id,
                'new_values' => ['trusted_device' => (bool) $trustedDevice, 'ip_address' => $request->ip()],
                'description' => $trustedDevice ? 'Professor logged in from a trusted device.' : 'Professor logged in from an untrusted device.',
            ]);
        }
    }
}
