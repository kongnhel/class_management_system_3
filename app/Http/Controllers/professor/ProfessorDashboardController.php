<?php

namespace App\Http\Controllers\professor;

use App\Http\Controllers\Controller;
use App\Models\ProfessorTrustedDevice;
use App\Services\ProfessorDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ProfessorDashboardController extends Controller
{
    public function __invoke(Request $request, ProfessorDashboardService $dashboardService)
    {
        $trustedDevice = null;
        $deviceToken = $request->cookie('professor_trusted_device');

        if ($deviceToken && Schema::hasTable('professor_trusted_devices')) {
            $trustedDevice = ProfessorTrustedDevice::where('user_id', Auth::id())
                ->whereNull('revoked_at')
                ->where('token_hash', hash('sha256', $deviceToken))
                ->exists();
        }

        return view('professor.dashboard', [
            ...$dashboardService->dataFor(Auth::user()),
            'currentDeviceTrusted' => $trustedDevice,
        ]);
    }
}
