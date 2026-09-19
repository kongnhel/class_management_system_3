<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Services\StudentProgressionService;
use Illuminate\Http\Request;

class TransitionController extends Controller
{
    public function __construct(
        private StudentProgressionService $progressionService
    ) {}

    public function create(User $student)
    {
        $transitionDepartments = $this->progressionService->getTransitionDepartments($student);

        return view('admin.students.transition', compact('student', 'transitionDepartments'));
    }

    public function store(Request $request, User $student)
    {
        $validated = $request->validate([
            'bachelor_department_id' => 'required|exists:departments,id',
        ]);

        $bachelorDepartment = Department::findOrFail($validated['bachelor_department_id']);

        if (! $bachelorDepartment->pathway_department_id) {
            return redirect()->back()->with('error', 'មុខវិជ្ជានេះមិនមែនជាផ្លូវបន្តទេ។');
        }

        if (! $this->progressionService->isEligibleForTransition($student)) {
            return redirect()->back()->with('error', 'សិស្សមិនមានសិទ្ធិក្នុងការផ្ទេរនៅពេលនេះទេ។');
        }

        $this->progressionService->transitionToBachelor($student, $bachelorDepartment);

        return redirect()
            ->route('admin.show-user', $student->id)
            ->with('success', 'សិស្សត្រូវបានផ្ទេរទៅមុខវិជ្ជាបរិញ្ញាបត្រដោយជោគជ័យ! កម្រិតឆ្នាំចាប់ផ្តើមពីឆ្នាំទី ៣។');
    }
}
