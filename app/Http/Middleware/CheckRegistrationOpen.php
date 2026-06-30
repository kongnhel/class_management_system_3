<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRegistrationOpen
{
    public function handle(Request $request, Closure $next): Response
    {
        $registrationOpen = SystemSetting::get('registration_open', '1');
        $registrationStart = SystemSetting::get('registration_start');
        $registrationEnd = SystemSetting::get('registration_end');

        if ($registrationOpen !== '1') {
            return redirect()->route('login')
                ->with('error', 'ការចុះឈ្មោះបច្ចុប្បន្នត្រូវបានបិទ។ សូមព្យាយាមម្តងទៀតនៅពេលក្រោយ។');
        }

        if ($registrationStart && now()->format('Y-m-d') < $registrationStart) {
            return redirect()->route('login')
                ->with('error', 'ការចុះឈ្មោះនឹងចាប់ផ្តើមនៅថ្ងៃទី ' . $registrationStart . '។');
        }

        if ($registrationEnd && now()->format('Y-m-d') > $registrationEnd) {
            return redirect()->route('login')
                ->with('error', 'ការចុះឈ្មោះបានបញ្ចប់នៅថ្ងៃទី ' . $registrationEnd . '។');
        }

        return $next($request);
    }
}