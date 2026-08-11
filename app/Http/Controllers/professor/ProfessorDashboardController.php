<?php

namespace App\Http\Controllers\professor;

use App\Http\Controllers\Controller;
use App\Services\ProfessorDashboardService;
use Illuminate\Support\Facades\Auth;

class ProfessorDashboardController extends Controller
{
    public function __invoke(ProfessorDashboardService $dashboardService)
    {
        return view('professor.dashboard', $dashboardService->dataFor(Auth::user()));
    }
}
