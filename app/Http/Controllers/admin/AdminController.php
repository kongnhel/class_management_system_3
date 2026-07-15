<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AttendanceRecord;
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
        $todayDate = now()->toDateString();
        $todayName = now()->format('l');

        $totalUsers = User::count();
        $totalStudents = User::where('role', 'student')->count();
        $totalProfessors = User::where('role', 'professor')->count();
        $totalFaculties = Faculty::count();
        $totalDepartments = Department::count();
        $totalPrograms = Program::count();
        $totalCourses = Course::count();
        $totalCourseOfferings = CourseOffering::whereHas('course')->count();

        $todayAttendanceCount = AttendanceRecord::where('date', $todayDate)->count();
        $todayPresentCount = AttendanceRecord::where('date', $todayDate)->where('status', 'present')->count();
        $todayAbsentCount = AttendanceRecord::where('date', $todayDate)->where('status', 'absent')->count();

        $activeCourseOfferings = CourseOffering::whereNull('deleted_at')
            ->where('end_date', '>=', now())
            ->whereHas('course')
            ->count();

        $recentUsers = User::latest()->limit(5)->get(['id', 'name', 'role', 'created_at']);

        $announcements = Announcement::latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalStudents',
            'totalProfessors',
            'totalFaculties',
            'totalDepartments',
            'totalPrograms',
            'totalCourses',
            'totalCourseOfferings',
            'todayAttendanceCount',
            'todayPresentCount',
            'todayAbsentCount',
            'activeCourseOfferings',
            'recentUsers',
            'announcements'
        ));
    }
}
