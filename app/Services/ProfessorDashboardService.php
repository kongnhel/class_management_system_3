<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\AttendanceRecord;
use App\Models\CourseOffering;
use App\Models\Exam;
use App\Models\Quiz;
use App\Models\Schedule;
use App\Models\StudentCourseEnrollment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Collection;

class ProfessorDashboardService
{
    public function dataFor(User $user): array
    {
        $todayName = now()->format('l');
        $todayDate = now()->toDateString();

        $todaySchedules = Schedule::query()
            ->whereHas('courseOffering', fn ($query) => $query->where('lecturer_user_id', $user->id))
            ->where('day_of_week', $todayName)
            ->with(['courseOffering.course.programs', 'courseOffering.targetPrograms', 'room'])
            ->orderBy('start_time')
            ->get();

        $todayOfferingIds = $todaySchedules->pluck('course_offering_id')->unique();
        $enrolledCounts = StudentCourseEnrollment::query()
            ->whereIn('course_offering_id', $todayOfferingIds)
            ->selectRaw('course_offering_id, COUNT(DISTINCT student_user_id) as total')
            ->groupBy('course_offering_id')
            ->pluck('total', 'course_offering_id');
        $attendanceCounts = AttendanceRecord::query()
            ->whereIn('course_offering_id', $todayOfferingIds)
            ->whereDate('date', $todayDate)
            ->selectRaw('course_offering_id, COUNT(DISTINCT student_user_id) as total')
            ->groupBy('course_offering_id')
            ->pluck('total', 'course_offering_id');

        $completedOfferingIds = $todayOfferingIds->filter(fn ($id) => ($enrolledCounts[$id] ?? 0) > 0
            && ($attendanceCounts[$id] ?? 0) >= $enrolledCounts[$id]);
        $todaySchedules->each(fn ($schedule) => $schedule->is_completed_today = $completedOfferingIds->contains($schedule->course_offering_id));

        $myCourseOfferings = CourseOffering::query()
            ->where('lecturer_user_id', $user->id)
            ->with('course')
            ->whereHas('course')
            ->get();
        $offeringIds = $myCourseOfferings->pluck('id');

        $scheduleCounts = Schedule::query()
            ->whereIn('course_offering_id', $offeringIds)
            ->selectRaw('course_offering_id, COUNT(*) as total')
            ->groupBy('course_offering_id')
            ->pluck('total', 'course_offering_id');
        $presentCounts = AttendanceRecord::query()
            ->whereIn('course_offering_id', $offeringIds)
            ->where('status', 'present')
            ->selectRaw('course_offering_id, student_user_id, COUNT(*) as total')
            ->groupBy('course_offering_id', 'student_user_id')
            ->get()
            ->keyBy(fn ($row) => $row->course_offering_id.'-'.$row->student_user_id);
        $offeringsById = $myCourseOfferings->keyBy('id');

        $atRiskStudents = StudentCourseEnrollment::query()
            ->whereIn('course_offering_id', $offeringIds)
            ->with('student')
            ->get()
            ->map(function ($enrollment) use ($scheduleCounts, $presentCounts, $offeringsById) {
                $student = $enrollment->student;
                $totalClasses = $scheduleCounts[$enrollment->course_offering_id] ?? 0;
                $attended = $presentCounts->get($enrollment->course_offering_id.'-'.$enrollment->student_user_id)?->total ?? 0;
                $rate = $totalClasses > 0 ? ($attended / $totalClasses) * 100 : 100;

                if (! $student || $rate >= 75) {
                    return null;
                }

                $course = $offeringsById[$enrollment->course_offering_id]->course;

                return [
                    'student' => $student,
                    'course' => $course->title_km ?? $course->title_en,
                    'reason' => 'Attendance '.round($rate).'%',
                    'type' => 'attendance',
                ];
            })
            ->filter()
            ->take(5)
            ->values();

        return [
            'user' => $user,
            'todaySchedules' => $todaySchedules,
            'totalStudents' => StudentCourseEnrollment::whereHas('courseOffering', fn ($query) => $query->where('lecturer_user_id', $user->id))->distinct('student_user_id')->count('student_user_id'),
            'todayAttendanceCount' => AttendanceRecord::whereHas('courseOffering', fn ($query) => $query->where('lecturer_user_id', $user->id))->whereDate('date', $todayDate)->count(),
            'upcomingAssignments' => Assignment::whereHas('courseOffering', fn ($query) => $query->where('lecturer_user_id', $user->id))->whereDate('due_date', '>=', $todayDate)->orderBy('due_date')->take(5)->get(),
            'upcomingExams' => Exam::whereHas('courseOffering', fn ($query) => $query->where('lecturer_user_id', $user->id))->whereDate('exam_date', '>=', $todayDate)->orderBy('exam_date')->take(5)->get(),
            'upcomingQuizzes' => Quiz::whereHas('courseOffering', fn ($query) => $query->where('lecturer_user_id', $user->id))->whereDate('quiz_date', '>=', $todayDate)->orderBy('quiz_date')->take(5)->get(),
            'announcements' => Announcement::whereIn('target_role', ['all', 'professor'])->latest()->take(5)->get(),
            'myCourseOfferings' => $myCourseOfferings,
            'atRiskStudents' => $atRiskStudents,
            'ungradedSubmissionsCount' => Submission::whereHas('assignment.courseOffering', fn ($query) => $query->where('lecturer_user_id', $user->id))->whereNull('grade_received')->count(),
            'pendingAssessments' => Assignment::whereHas('courseOffering', fn ($query) => $query->where('lecturer_user_id', $user->id))->whereDate('due_date', '<', $todayDate)->whereDoesntHave('examResults')->count(),
        ];
    }
}
