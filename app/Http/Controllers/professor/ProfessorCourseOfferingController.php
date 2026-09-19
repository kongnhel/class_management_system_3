<?php

namespace App\Http\Controllers\professor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;

class ProfessorCourseOfferingController extends Controller
{
    public function myCourseOfferings()
    {
        $user = Auth::user();
        $courseOfferings = CourseOffering::where('lecturer_user_id', $user->id)
            ->with('course.department', 'lecturer')
            ->whereHas('course')
            ->paginate(10);

        return view('professor.my-course-offerings', compact('courseOfferings'));
    }

    public function viewDepartments()
    {
        $departments = Department::with('faculty', 'head')->paginate(10);

        return view('professor.departments.index', compact('departments'));
    }

    public function viewCourses()
    {
        $user = Auth::user();
        $courses = Course::whereHas('courseOfferings', fn ($q) => $q->where('lecturer_user_id', $user->id))
            ->with('department')
            ->paginate(10);

        return view('professor.courses.index', compact('courses'));
    }
}
