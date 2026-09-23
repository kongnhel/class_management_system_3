<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\CourseOffering;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Generation;
use App\Services\StudentProgressionService;
use Illuminate\Http\Request;

class StudentProgressionController extends Controller
{
    protected $progressionService;

    public function __construct(StudentProgressionService $progressionService)
    {
        $this->progressionService = $progressionService;
    }

    public function index(Request $request)
    {
        $facultyId = $request->input('faculty_id');
        $departmentId = $request->input('department_id');
        $courseId = $request->input('course_id');
        $generation = $request->input('generation');
        $semester = $request->input('semester');
        $scheduleGroup = $request->input('schedule_group');
        $search = $request->input('search');

        $faculties = Faculty::orderBy('name_km')->get();
        $generations = Generation::where('is_active', true)->orderByDesc('name')->get();

        $allDepartments = Department::with('faculty')->orderBy('name_km')->get();
        $availableDepartments = $facultyId
            ? $allDepartments->where('faculty_id', $facultyId)
            : $allDepartments;

        $department = $departmentId
            ? Department::findOrFail($departmentId)
            : $availableDepartments->first();

        if (! $department) {
            return redirect()->route('admin.manage-users')
                ->with('error', 'សូមបង្កើតមុខវិជ្ជាមុន។');
        }

        $courseOfferings = CourseOffering::where('department_id', $department->id)
            ->with(['course', 'schedules'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filters = compact('facultyId', 'courseId', 'generation', 'semester', 'scheduleGroup', 'search');

        $summary = $this->progressionService->getProgressionSummary($department, $filters);

        return view('admin.progression.index', compact('department', 'summary', 'faculties', 'courseOfferings', 'generations', 'filters', 'allDepartments'));
    }

    public function advance(Request $request)
    {
        $departmentId = $request->input('department_id');
        $department = Department::findOrFail($departmentId);

        $eligibleStudents = $this->progressionService->getAllEligibleStudents($department);
        $heldBackStudents = $this->progressionService->getAllHeldBackStudents($department);
        $maxYear = $this->progressionService->getMaxYearLevel($department);

        return view('admin.progression.advance', compact(
            'department', 'eligibleStudents', 'heldBackStudents', 'maxYear'
        ));
    }

    public function executeAdvance(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:users,id',
        ]);

        $department = Department::findOrFail($request->department_id);
        $studentIds = collect($request->student_ids);

        $advanced = $this->progressionService->advanceStudents($studentIds, $department);

        return redirect()->route('admin.progression.index', ['department_id' => $department->id])
            ->with('success', "បានជំរុញនិស្សិត {$advanced} នាក់ទៅជំនាន់ថ្មីដោយជោគជ័យ។");
    }

    public function autoGraduate(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
        ]);

        $department = Department::findOrFail($request->department_id);
        $graduated = $this->progressionService->autoGraduateStudents($department);

        return redirect()->route('admin.progression.index', ['department_id' => $department->id])
            ->with('success', "បានបញ្ចប់ការសិក្សាដោយជោគជ័យចំពោះនិស្សិត {$graduated} នាក់។");
    }
}
