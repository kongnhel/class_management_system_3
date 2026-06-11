<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use App\Models\Program;
use App\Models\Room;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $room = Room::all();

        $query = Course::with(['department', 'programs']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title_km', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%")
                  ->orWhereHas('department', function ($dq) use ($search) {
                      $dq->where('name_km', 'like', "%{$search}%");
                  });
            });
        }

        $coursesData = $query->orderBy('department_id')->get();

        $flattenedCourses = $coursesData->flatMap(function ($course) {
            if ($course->programs->isEmpty()) {
                return [$course];
            }

            return $course->programs->map(function ($program) use ($course) {
                $clone = clone $course;
                $clone->assigned_program_name = $program->name_km;

                return $clone;
            });
        });

        $coursesGrouped = $flattenedCourses->groupBy([
            function ($item) {
                return $item->assigned_program_name ?? 'មិនទាន់មានកម្មវិធីសិក្សា';
            },
            function ($item) {
                return $item->generation ? 'ជំនាន់ទី '.$item->generation : 'មិនទាន់កំណត់ជំនាន់';
            },
        ]);

        return view('admin.courses.index', compact('coursesGrouped', 'room', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::all();
        $programs = Program::all();
        $generations = User::select('generation')->distinct()->pluck('generation')->filter()->all();

        return view('admin.courses.create', compact('departments', 'programs', 'generations'));
    }

    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'title_km' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_km' => 'nullable|string',
            'description_en' => 'nullable|string',
            'credits' => 'required|numeric|min:0.5',
            'department_id' => 'required|exists:departments,id',
            'program_id' => 'required|array|min:1',
            'program_id.*' => 'required|exists:programs,id',
            'generation' => 'nullable|string|max:255',
        ]);

        $course = Course::create($request->except('program_id'));

        $course->programs()->sync($request->program_id);

        return redirect()->route('admin.manage-courses')
            ->with('success', 'មុខវិជ្ជាត្រូវបានបង្កើតដោយជោគជ័យ!');
    }

    public function show(Course $course)
    {
        return view('courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $departments = Department::all();
        $programs = Program::all();

        $generations = StudentProfile::select('generation')
            ->distinct()
            ->pluck('generation')
            ->filter()
            ->all();

        $selectedPrograms = $course->programs->pluck('id')->toArray();

        return view('admin.courses.edit', compact('course', 'departments', 'programs', 'generations', 'selectedPrograms'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'title_km' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_km' => 'nullable|string',
            'description_en' => 'nullable|string',
            'credits' => 'required|numeric|min:0.5',
            'department_id' => 'required|exists:departments,id',
            'program_ids' => 'required|array',
            'program_ids.*' => 'exists:programs,id',
            'generation' => 'nullable|string|max:255',
        ]);

        $course->update($request->only([
            'title_km',
            'title_en',
            'description_km',
            'description_en',
            'credits',
            'department_id',
            'generation',
        ]));

        $course->programs()->sync($request->program_ids);

        return redirect()->route('admin.manage-courses')
            ->with('success', 'មុខវិជ្ជាត្រូវបានធ្វើបច្ចុប្បន្នភាពដោយជោគជ័យ');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('admin.manage-courses')->with('success', 'មុខវិជ្ជាត្រូវបានលុបដោយជោគជ័យ');
    }
}
