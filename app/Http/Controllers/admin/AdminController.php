<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Fetch some statistics for the dashboard
        $totalUsers = User::count();
        $totalStudents = User::where('role', 'student')->count();
        $totalProfessors = User::where('role', 'professor')->count();
        $totalFaculties = Faculty::count();
        $totalDepartments = Department::count();
        $totalPrograms = Program::count();
        $totalCourses = Course::count();
        $totalCourseOfferings = CourseOffering::count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalStudents',
            'totalProfessors',
            'totalFaculties',
            'totalDepartments',
            'totalPrograms',
            'totalCourses',
            'totalCourseOfferings'
        ));
    }
}
