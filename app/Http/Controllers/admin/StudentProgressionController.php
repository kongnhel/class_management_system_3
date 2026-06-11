<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Services\StudentProgressionService;
use Illuminate\Http\Request;

class StudentProgressionController extends Controller
{
    protected $progressionService;

    public function __construct(StudentProgressionService $progressionService)
    {
        $this->progressionService = $progressionService;
    }

    /**
     * Display progression dashboard for a program.
     */
    public function index(Request $request)
    {
        $programId = $request->input('program_id');
        $program = $programId ? Program::findOrFail($programId) : Program::first();

        if (! $program) {
            return redirect()->route('admin.manage-users')
                ->with('error', 'សូមបង្កើតកម្មវិធីសិក្សាមុន។');
        }

        $summary = $this->progressionService->getProgressionSummary($program);
        $programs = Program::all();

        return view('admin.progression.index', compact('program', 'summary', 'programs'));
    }

    /**
     * Show advance form for a specific program and year.
     */
    public function advance(Request $request)
    {
        $programId = $request->input('program_id');
        $program = Program::findOrFail($programId);

        $eligibleStudents = $this->progressionService->getEligibleStudents($program);
        $heldBackStudents = $this->progressionService->getHeldBackStudents($program);

        $firstStudent = $eligibleStudents->first() ?? $heldBackStudents->first();
        $currentYear = $firstStudent
            ? $this->progressionService->getYearLevel($firstStudent, $program)
            : 1;
        $nextYear = $currentYear + 1;
        $maxYear = $this->progressionService->getMaxYearLevel($program);
        $willGraduate = $nextYear > $maxYear;

        return view('admin.progression.advance', compact(
            'program', 'eligibleStudents', 'heldBackStudents',
            'currentYear', 'nextYear', 'maxYear', 'willGraduate'
        ));
    }

    /**
     * Execute the advancement.
     */
    public function executeAdvance(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:users,id',
        ]);

        $program = Program::findOrFail($request->program_id);
        $studentIds = collect($request->student_ids);

        $advanced = $this->progressionService->advanceStudents($studentIds, $program);

        return redirect()->route('admin.progression.index', ['program_id' => $program->id])
            ->with('success', "បានជំរុញនិស្សិត {$advanced} នាក់ទៅជំនាន់ថ្មីដោយជោគជ័យ។");
    }

    /**
     * Auto-graduate eligible students.
     */
    public function autoGraduate(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
        ]);

        $program = Program::findOrFail($request->program_id);
        $graduated = $this->progressionService->autoGraduateStudents($program);

        return redirect()->route('admin.progression.index', ['program_id' => $program->id])
            ->with('success', "បានបញ្ចប់ការសិក្សាដោយជោគជ័យចំពោះនិស្សិត {$graduated} នាក់។");
    }
}
