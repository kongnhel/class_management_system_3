<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\User;
use App\Services\StudentProgressionService;
use Illuminate\Http\Request;

class TransitionController extends Controller
{
    public function __construct(
        private StudentProgressionService $progressionService
    ) {}

    /**
     * Show the transition form for a student.
     */
    public function create(User $student)
    {
        $transitionPrograms = $this->progressionService->getTransitionPrograms($student);

        return view('admin.students.transition', compact('student', 'transitionPrograms'));
    }

    /**
     * Execute the transition from associate's to bachelor's program.
     */
    public function store(Request $request, User $student)
    {
        $validated = $request->validate([
            'bachelor_program_id' => 'required|exists:programs,id',
        ]);

        $bachelorProgram = Program::findOrFail($validated['bachelor_program_id']);

        // Verify the program is a valid pathway
        if (! $bachelorProgram->pathway_program_id) {
            return redirect()->back()->with('error', 'កម្មវិធីសិក្សានេះមិនមែនជាផ្លូវបន្តទេ។');
        }

        // Check eligibility
        if (! $this->progressionService->isEligibleForTransition($student)) {
            return redirect()->back()->with('error', 'សិស្សមិនមានសិទ្ធិក្នុងការផ្ទេរនៅពេលនេះទេ។');
        }

        $this->progressionService->transitionToBachelor($student, $bachelorProgram);

        return redirect()
            ->route('admin.show-user', $student->id)
            ->with('success', 'សិស្សត្រូវបានផ្ទេរទៅកម្មវិធីសិក្សាបរិញ្ញាបត្រដោយជោគជ័យ! កម្រិតឆ្នាំចាប់ផ្តើមពីឆ្នាំទី ៣។');
    }
}
