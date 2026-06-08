<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\CourseOffering;
use App\Models\Program;
use Illuminate\Http\Request;

class AdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = CourseOffering::with(['course', 'lecturer', 'targetPrograms'])
            ->withCount('studentCourseEnrollments');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('course', function ($q2) use ($search) {
                    $q2->where('title_km', 'LIKE', "%{$search}%")
                        ->orWhere('title_en', 'LIKE', "%{$search}%");
                })->orWhereHas('lecturer', function ($q3) use ($search) {
                    $q3->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

        if ($request->filled('program_id')) {
            $query->whereHas('targetPrograms', function ($q) use ($request) {
                $q->where('program_id', $request->input('program_id'));
            });
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->input('semester'));
        }

        $courseOfferings = $query->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->paginate(20)
            ->appends($request->query());

        $programs = Program::orderBy('name_km')->get();

        return view('admin.attendance.index', compact('courseOfferings', 'programs'));
    }

    public function show(CourseOffering $courseOffering)
    {
        $courseOffering->load([
            'course',
            'lecturer',
            'targetPrograms',
            'studentCourseEnrollments.student.profile',
        ]);

        // Get attendance records for this course offering
        $attendanceRecords = AttendanceRecord::where('course_offering_id', $courseOffering->id)
            ->with('student')
            ->get();

        // Group by student
        $studentAttendance = $courseOffering->studentCourseEnrollments->map(function ($enrollment) use ($attendanceRecords) {
            $studentRecords = $attendanceRecords->where('student_user_id', $enrollment->student_user_id);
            $totalDays = $studentRecords->count();
            $presentDays = $studentRecords->where('status', 'present')->count();
            $absentDays = $studentRecords->where('status', 'absent')->count();
            $lateDays = $studentRecords->where('status', 'late')->count();
            $permissionDays = $studentRecords->where('status', 'permission')->count();

            $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;

            return [
                'student' => $enrollment->student,
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'late_days' => $lateDays,
                'permission_days' => $permissionDays,
                'attendance_rate' => $attendanceRate,
            ];
        });

        // Calculate overall stats
        $stats = [
            'total_students' => $courseOffering->studentCourseEnrollments->count(),
            'total_records' => $attendanceRecords->count(),
            'present_total' => $attendanceRecords->where('status', 'present')->count(),
            'absent_total' => $attendanceRecords->where('status', 'absent')->count(),
            'late_total' => $attendanceRecords->where('status', 'late')->count(),
            'overall_rate' => $attendanceRecords->count() > 0
                ? round(($attendanceRecords->where('status', 'present')->count() / $attendanceRecords->count()) * 100, 1)
                : 0,
        ];

        return view('admin.attendance.show', compact('courseOffering', 'studentAttendance', 'stats'));
    }
}
