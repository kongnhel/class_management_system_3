<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\CourseOffering;
use App\Models\Program;
use Illuminate\Http\Request;

class AdminGradeController extends Controller
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

        return view('admin.grades.index', compact('courseOfferings', 'programs'));
    }

    public function show(CourseOffering $courseOffering)
    {
        $courseOffering->load([
            'course',
            'lecturer',
            'targetPrograms',
            'studentCourseEnrollments.student.profile',
        ]);

        $enrollments = $courseOffering->studentCourseEnrollments
            ->sortBy('student.name')
            ->values();

        $stats = [
            'total' => $enrollments->count(),
            'graded' => $enrollments->where('final_grade', '!=', null)->count(),
            'avg_grade' => $enrollments->where('final_grade', '!=', null)->avg('final_grade'),
            'max_grade' => $enrollments->where('final_grade', '!=', null)->max('final_grade'),
            'min_grade' => $enrollments->where('final_grade', '!=', null)->min('final_grade'),
        ];

        return view('admin.grades.show', compact('courseOffering', 'enrollments', 'stats'));
    }

    public function exportGrades(CourseOffering $courseOffering)
    {
        $courseOffering->load([
            'course',
            'lecturer',
            'targetPrograms',
            'studentCourseEnrollments.student.profile',
        ]);

        $fileName = 'grades_'.$courseOffering->course->title_en.'_'.$courseOffering->academic_year.'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($courseOffering) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, ['Student ID', 'Name', 'Email', 'Grade', 'Status']);

            // Data
            foreach ($courseOffering->studentCourseEnrollments as $enrollment) {
                fputcsv($file, [
                    $enrollment->student->student_id_code ?? '',
                    $enrollment->student->name ?? '',
                    $enrollment->student->email ?? '',
                    $enrollment->final_grade ?? 'N/A',
                    $enrollment->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
