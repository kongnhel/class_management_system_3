<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceProfessor;
use App\Models\AttendanceRecord;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\Generation;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = CourseOffering::with(['course', 'lecturer', 'department'])
            ->selectRaw('course_offerings.*, (SELECT COUNT(DISTINCT student_user_id) FROM student_course_enrollments WHERE student_course_enrollments.course_offering_id = course_offerings.id) as student_course_enrollments_count')
            ->whereHas('course')
            ->whereHas('lecturer');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('course', function ($q2) use ($search) {
                    $q2->where('title_km', 'LIKE', "%{$search}%")
                        ->orWhere('title_en', 'LIKE', "%{$search}%")
                        ->orWhere('code', 'LIKE', "%{$search}%");
                })->orWhereHas('lecturer', function ($q3) use ($search) {
                    $q3->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->filled('professor_id')) {
            $query->where('lecturer_user_id', $request->input('professor_id'));
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->input('semester'));
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->input('academic_year'));
        }

        if ($request->filled('generation')) {
            $query->where('generation', $request->input('generation'));
        }

        $courseOfferings = $query->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->paginate(20)
            ->appends($request->query());

        $departments = Department::orderBy('name_km')->get();
        $generations = Generation::where('is_active', true)->orderByDesc('name')->get();
        $academicYears = AcademicYear::orderByDesc('name')->pluck('name');
        $professors = User::where('role', 'professor')->orderBy('name')->get(['id', 'name']);

        return view('admin.attendance.index', compact('courseOfferings', 'departments', 'generations', 'academicYears', 'professors'));
    }

    public function show(Request $request, CourseOffering $courseOffering)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $search = trim((string) $request->input('search', ''));

        $courseOffering->load([
            'course',
            'lecturer',
            'department',
            'studentCourseEnrollments.student.studentProfile',
        ]);

        // Get attendance records for this course offering
        $attendanceRecords = AttendanceRecord::where('course_offering_id', $courseOffering->id)
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('date', '>=', $request->input('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('date', '<=', $request->input('date_to')))
            ->with('student')
            ->get();

        // Deduplicate enrollments by student_user_id (remove duplicate entries)
        $enrollments = $courseOffering->studentCourseEnrollments
            ->unique('student_user_id')
            ->values();

        // Group by student
        $studentAttendance = $enrollments->map(function ($enrollment) use ($attendanceRecords, $courseOffering) {
            $studentRecords = $attendanceRecords->where('student_user_id', $enrollment->student_user_id);
            $totalDays = $studentRecords->count();
            $presentDays = $studentRecords->whereIn('status', ['present', 'late'])->count();
            $absentDays = $studentRecords->where('status', 'absent')->count();
            $permissionDays = $studentRecords->where('status', 'permission')->count();

            $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;

            // Compute attendance score out of 15 (same as student view)
            $attendanceScore = (float) ($enrollment->student->getAttendanceScoreByCourse($courseOffering->id) ?? 0);

            return [
                'student' => $enrollment->student,
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'permission_days' => $permissionDays,
                'attendance_rate' => $attendanceRate,
                'attendance_score' => $attendanceScore,
            ];
        });

        if ($search !== '') {
            $studentAttendance = $studentAttendance->filter(function (array $data) use ($search) {
                $student = $data['student'];
                $haystack = implode(' ', array_filter([
                    $student->name,
                    $student->email,
                    $student->student_id_code,
                    $student->studentProfile?->full_name_km,
                    $student->studentProfile?->full_name_en,
                ]));

                return str_contains(mb_strtolower($haystack), mb_strtolower($search));
            })->values();
        }

        $attendanceStatus = $request->input('attendance_status');
        if ($attendanceStatus) {
            $studentAttendance = $studentAttendance->filter(function (array $data) use ($attendanceStatus) {
                return match ($attendanceStatus) {
                    'no_records' => $data['total_days'] === 0,
                    'low_attendance' => $data['attendance_rate'] < 60,
                    'below_passing' => $data['attendance_score'] < 10,
                    'good_attendance' => $data['attendance_rate'] >= 80,
                    default => true,
                };
            })->values();
        }

        // Calculate overall stats
        $stats = [
            'total_students' => $enrollments->count(),
            'total_records' => $attendanceRecords->count(),
            'present_total' => $attendanceRecords->whereIn('status', ['present', 'late'])->count(),
            'absent_total' => $attendanceRecords->where('status', 'absent')->count(),
            'overall_rate' => $attendanceRecords->count() > 0
                ? round(($attendanceRecords->whereIn('status', ['present', 'late'])->count() / $attendanceRecords->count()) * 100, 1)
                : 0,
        ];

        return view('admin.attendance.show', compact('courseOffering', 'studentAttendance', 'stats'));
    }

    public function exportAttendance(Request $request, CourseOffering $courseOffering)
    {
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $courseOffering->load([
            'course',
            'lecturer',
            'department',
            'studentCourseEnrollments.student.studentProfile',
        ]);

        $attendanceRecords = AttendanceRecord::where('course_offering_id', $courseOffering->id)
            ->with('student')
            ->get();

        if (! empty($validated['date_from'])) {
            $attendanceRecords = $attendanceRecords->filter(fn ($record) => $record->date?->toDateString() >= $validated['date_from']);
        }

        if (! empty($validated['date_to'])) {
            $attendanceRecords = $attendanceRecords->filter(fn ($record) => $record->date?->toDateString() <= $validated['date_to']);
        }

        $enrollments = $courseOffering->studentCourseEnrollments
            ->unique('student_user_id')
            ->values();

        $studentAttendance = $enrollments->map(function ($enrollment) use ($attendanceRecords, $courseOffering) {
            $studentRecords = $attendanceRecords->where('student_user_id', $enrollment->student_user_id);
            $totalDays = $studentRecords->count();
            $presentDays = $studentRecords->whereIn('status', ['present', 'late'])->count();
            $absentDays = $studentRecords->where('status', 'absent')->count();
            $permissionDays = $studentRecords->where('status', 'permission')->count();

            $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;
            $attendanceScore = (float) ($enrollment->student->getAttendanceScoreByCourse($courseOffering->id) ?? 0);

            return [
                'student' => $enrollment->student,
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'permission_days' => $permissionDays,
                'attendance_rate' => $attendanceRate,
                'attendance_score' => $attendanceScore,
            ];
        });

        $stats = [
            'total_students' => $enrollments->count(),
            'total_records' => $attendanceRecords->count(),
            'present_total' => $attendanceRecords->whereIn('status', ['present', 'late'])->count(),
            'absent_total' => $attendanceRecords->where('status', 'absent')->count(),
            'overall_rate' => $attendanceRecords->count() > 0
                ? round(($attendanceRecords->whereIn('status', ['present', 'late'])->count() / $attendanceRecords->count()) * 100, 1)
                : 0,
        ];

        $fileName = 'Attendance_'.str_replace([' ', '/', '\\'], '_', $courseOffering->course->title_km ?? 'report').'.xlsx';

        return Excel::download(
            new \App\Exports\AdminAttendanceExcelExport($courseOffering, $studentAttendance, $stats),
            $fileName
        );
    }

    public function professorCheckins(Request $request)
    {
        $query = AttendanceProfessor::with(['professor', 'courseOffering.course', 'courseOffering.department']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('professor', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('professor_id')) {
            $query->where('professor_id', $request->input('professor_id'));
        }

        if ($request->filled('semester')) {
            $query->whereHas('courseOffering', function ($q) use ($request) {
                $q->where('semester', $request->input('semester'));
            });
        }

        if ($request->filled('academic_year')) {
            $query->whereHas('courseOffering', function ($q) use ($request) {
                $q->where('academic_year', $request->input('academic_year'));
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('verified_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('verified_at', '<=', $request->input('date_to'));
        }

        $checkins = $query->orderBy('verified_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        // Summary stats
        $now = \Carbon\Carbon::now('Asia/Phnom_Penh');
        $monthStart = $now->copy()->startOfMonth();
        $weekStart = $now->copy()->startOfWeek();

        $stats = [
            'total' => AttendanceProfessor::count(),
            'this_month' => AttendanceProfessor::where('verified_at', '>=', $monthStart)->count(),
            'this_week' => AttendanceProfessor::where('verified_at', '>=', $weekStart)->count(),
            'unique_professors' => AttendanceProfessor::where('verified_at', '>=', $monthStart)->distinct('professor_id')->count('professor_id'),
        ];

        $professors = \App\Models\User::where('role', 'professor')
            ->orderBy('name')
            ->get();

        return view('admin.attendance.professor-checkins', compact('checkins', 'stats', 'professors'));
    }

    public function exportProfessorCheckins(Request $request)
    {
        $request->validate([
            'semester' => 'required|string',
            'academic_year' => 'required|string',
            'day_type' => 'required|in:weekend,weekday',
        ], [
            'semester.required' => 'សូមជ្រើសរើសឆមាស',
            'academic_year.required' => 'សូមជ្រើសរើសឆ្នាំសិក្សា',
            'day_type.required' => 'សូមជ្រើសរើសប្រភេទថ្ងៃ',
        ]);

        $query = AttendanceProfessor::with(['professor', 'courseOffering.course', 'courseOffering.department']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('professor', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('professor_id')) {
            $query->where('professor_id', $request->input('professor_id'));
        }

        if ($request->filled('semester')) {
            $query->whereHas('courseOffering', function ($q) use ($request) {
                $q->where('semester', $request->input('semester'));
            });
        }

        if ($request->filled('academic_year')) {
            $query->whereHas('courseOffering', function ($q) use ($request) {
                $q->where('academic_year', $request->input('academic_year'));
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('verified_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('verified_at', '<=', $request->input('date_to'));
        }

        $semester = $request->input('semester');
        $academicYear = $request->input('academic_year');
        $dayType = $request->input('day_type');

        $checkins = $query->orderBy('verified_at', 'desc')->get();

        // Filter by day_type (weekend/weekday) using Carbon
        if ($dayType === 'weekend') {
            $checkins = $checkins->filter(fn ($r) => $r->verified_at && \Carbon\Carbon::parse($r->verified_at)->isWeekend());
        } elseif ($dayType === 'weekday') {
            $checkins = $checkins->filter(fn ($r) => $r->verified_at && ! \Carbon\Carbon::parse($r->verified_at)->isWeekend());
        }

        $now = \Carbon\Carbon::now('Asia/Phnom_Penh');

        $stats = [
            'total' => $checkins->count(),
        ];

        $fileName = 'Professor_Checkins_'.$semester.'_'.$academicYear.'_'.$dayType.'_'.$now->format('Y-m-d_H-i').'.xlsx';
        $fileName = str_replace([' ', '/', '\\'], '_', $fileName);

        return Excel::download(
            new \App\Exports\ProfessorCheckinsExcelExport($checkins, $stats),
            $fileName
        );
    }

    /**
     * Export a single professor's attendance history to Excel
     */
    public function exportProfessorAttendance(Request $request, int $professorId)
    {
        $request->validate([
            'semester' => 'required|string',
            'academic_year' => 'required|string',
            'day_type' => 'required|in:weekend,weekday',
        ], [
            'semester.required' => 'សូមជ្រើសរើសឆមាស',
            'academic_year.required' => 'សូមជ្រើសរើសឆ្នាំសិក្សា',
            'day_type.required' => 'សូមជ្រើសរើសប្រភេទថ្ងៃ',
        ]);

        $professor = \App\Models\User::findOrFail($professorId);

        $semester = $request->input('semester');
        $academicYear = $request->input('academic_year');
        $dayType = $request->input('day_type');

        $query = AttendanceProfessor::with(['courseOffering.course', 'courseOffering.department'])
            ->where('professor_id', $professorId);

        $query->whereHas('courseOffering', function ($q) use ($semester) {
            $q->where('semester', $semester);
        });

        $query->whereHas('courseOffering', function ($q) use ($academicYear) {
            $q->where('academic_year', $academicYear);
        });

        $attendances = $query->orderBy('verified_at', 'desc')->get();

        $stats = [
            'total' => $attendances->count(),
        ];

        $professorName = $professor->name;
        $fileName = 'Professor_Attendance_'.$professorName.'_'.$semester.'_'.$academicYear.'_'.$dayType.'.xlsx';
        $fileName = str_replace([' ', '/', '\\'], '_', $fileName);

        return Excel::download(
            new \App\Exports\ProfessorAttendanceExcelExport($attendances, $stats, $professorName, $semester, $academicYear, $dayType),
            $fileName
        );
    }
}
