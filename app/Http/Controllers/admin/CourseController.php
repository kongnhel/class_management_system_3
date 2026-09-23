<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Room;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $facultyId = $request->input('faculty_id', '');
        $departmentId = $request->input('department_id', '');
        $room = Room::all();
        $faculties = Faculty::all();
        $allDepartments = Department::orderByDesc('name_km')->get();

        $query = Course::with(['department']);

        if ($facultyId) {
            $query->whereHas('department', function ($dq) use ($facultyId) {
                $dq->where('faculty_id', $facultyId);
            });
        }

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

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

        $coursesGrouped = $coursesData->groupBy([
            fn ($course) => $course->department->name_km ?? __('no_departments_yet'),
        ]);

        return view('admin.courses.index', compact('coursesGrouped', 'room', 'search', 'faculties', 'facultyId', 'departmentId', 'allDepartments'));
    }

    public function create()
    {
        $departments = Department::all();

        return view('admin.courses.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title_km' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_km' => 'nullable|string',
            'description_en' => 'nullable|string',
            'credits' => 'required|numeric|min:0.5',
            'department_id' => 'required|exists:departments,id',
        ]);

        Course::create($request->only([
            'title_km', 'title_en', 'description_km', 'description_en', 'credits', 'department_id',
        ]));

        return redirect()->route('admin.manage-courses')
            ->with('success', __('course_created_successfully'));
    }

    public function show(Course $course)
    {
        return view('courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $departments = Department::all();

        return view('admin.courses.edit', compact('course', 'departments'));
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
        ]);

        $course->update($request->only([
            'title_km', 'title_en', 'description_km', 'description_en', 'credits', 'department_id',
        ]));

        return redirect()->route('admin.manage-courses')
            ->with('success', __('course_updated_successfully'));
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('admin.manage-courses')->with('success', __('course_deleted_successfully'));
    }
}
