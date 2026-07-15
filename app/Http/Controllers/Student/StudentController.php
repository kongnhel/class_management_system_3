<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\CourseOffering;
use App\Models\Schedule;
use App\Models\StudentCourseEnrollment;
use App\Services\GradingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $studentId = $user->id;
        $todayName = now()->format('l');
        $todayDate = now()->toDateString();

        // Attendance counts
        $totalPresent = \App\Models\AttendanceRecord::where('student_user_id', $studentId)->where('status', 'present')->count();
        $totalAbsent = \App\Models\AttendanceRecord::where('student_user_id', $studentId)->where('status', 'absent')->count();
        $totalPermission = \App\Models\AttendanceRecord::where('student_user_id', $studentId)->where('status', 'permission')->count();
        $totalLate = \App\Models\AttendanceRecord::where('student_user_id', $studentId)->where('status', 'late')->count();

        // Today's enrolled courses
        $todayOfferingIds = Schedule::where('day_of_week', $todayName)->pluck('course_offering_id');
        $enrolledCourses = CourseOffering::whereIn('id', $todayOfferingIds)
            ->whereHas('students', fn ($q) => $q->where('student_user_id', $studentId))
            ->whereHas('course')
            ->with(['course', 'lecturer', 'studentCourseEnrollments' => fn ($q) => $q->where('student_user_id', $studentId)])
            ->get();

        $todayRecords = \App\Models\AttendanceRecord::where('student_user_id', $studentId)
            ->whereIn('course_offering_id', $enrolledCourses->pluck('id'))
            ->where('date', $todayDate)
            ->pluck('status', 'course_offering_id')
            ->toArray();
        $enrolledCourses->each(fn ($o) => $o->today_status = $todayRecords[$o->id] ?? null);

        // Today's schedule
        $upcomingSchedules = Schedule::whereHas('courseOffering.studentCourseEnrollments', fn ($q) => $q->where('student_user_id', $studentId))
            ->whereHas('courseOffering.course')
            ->with(['room', 'courseOffering.course', 'courseOffering.lecturer'])
            ->where('day_of_week', $todayName)
            ->orderBy('start_time', 'asc')
            ->get();

        // Student program
        $studentProgramEnrollment = \App\Models\StudentProgramEnrollment::where('student_user_id', $studentId)
            ->where('status', 'active')->with('program')->first();
        $studentProgram = $studentProgramEnrollment?->program;

        // Available courses for self-enrollment
        $availableCoursesInProgram = collect([]);
        if ($studentProgram) {
            $enrolledIds = StudentCourseEnrollment::where('student_user_id', $studentId)->pluck('course_offering_id');
            $availableCoursesInProgram = CourseOffering::with(['course', 'lecturer'])->withCount('studentCourseEnrollments')
                ->whereHas('targetPrograms', fn ($q) => $q->where('program_id', $user->program_id)->where('generation', $user->generation))
                ->whereHas('course')
                ->where('end_date', '>=', now())->whereNotIn('id', $enrolledIds)->get();
        }

        // Course progress
        $completedCoursesCount = StudentCourseEnrollment::where('student_user_id', $studentId)->where('status', 'completed')->count();
        $totalCoursesInProgram = $studentProgram ? CourseOffering::whereHas('targetPrograms', fn ($q) => $q->where('program_id', $studentProgram->id))->distinct('course_id')->count() : 0;

        // Notifications
        $allAnnouncements = Announcement::where('target_role', 'all')->orWhere('target_role', 'student')
            ->with(['poster', 'reads' => fn ($q) => $q->where('user_id', $studentId)])
            ->orderBy('created_at', 'desc')->get()
            ->map(fn ($a) => tap($a, function ($a) {
                $a->type = 'announcement';
                $a->title = $a->title_km ?? $a->title_en;
                $a->content = $a->content_km ?? $a->content_en;
                $a->is_read = $a->reads->isNotEmpty();
                $a->sender_name = $a->poster->name ?? __('រដ្ឋបាលសាលា');
            }));
        $allNotifications = $user->notifications->map(function ($n) {
            $n->type = 'notification';
            $n->title = $n->data['title'] ?? 'ការជូនដំណឹងថ្មី';
            $n->content = $n->data['message'] ?? 'អ្នកមានការជូនដំណឹងថ្មី។';
            $n->sender_name = $n->data['from_user_name'] ?? 'ប្រព័ន្ធ';
            $n->is_read = $n->read_at !== null;
            return $n;
        });
        $combinedFeed = $allAnnouncements->merge($allNotifications)->sortByDesc('created_at');

        // Attendance score (max 15)
        $attendanceScore = max(0, 15 - floor($totalAbsent / 2) - floor($totalPermission / 4));

        // ── Academic Performance ──
        $gpa = 0;
        $averageScore = 0;
        $overallRank = '-';
        $totalClassmates = 0;
        $overallGrade = 'N/A';

        $enrolledOfferingIds = StudentCourseEnrollment::where('student_user_id', $studentId)->pluck('course_offering_id');

        // Get exam_results with assessment relationships to resolve course_offering_id
        $allExamResults = \App\Models\ExamResult::where('student_user_id', $studentId)
            ->whereIn('assessment_id', function ($q) use ($enrolledOfferingIds) {
                $q->select('id')->from('assignments')->whereIn('course_offering_id', $enrolledOfferingIds)
                    ->union(DB::table('quizzes')->select('id')->whereIn('course_offering_id', $enrolledOfferingIds))
                    ->union(DB::table('exams')->select('id')->whereIn('course_offering_id', $enrolledOfferingIds));
            })
            ->with(['assignment.courseOffering.course', 'exam.courseOffering.course', 'quiz.courseOffering.course'])
            ->get();

        // Map each result to its course_offering_id and course_id
        $resultsWithCourse = $allExamResults->map(function ($result) {
            $assessment = match($result->assessment_type) {
                'assignment' => $result->assignment,
                'exam' => $result->exam,
                'quiz' => $result->quiz,
                default => null,
            };
            return [
                'score_obtained' => $result->score_obtained,
                'assessment_type' => $result->assessment_type,
                'course_id' => $assessment?->courseOffering?->course_id,
                'course_offering_id' => $assessment?->courseOffering?->id,
                'credits' => $assessment?->courseOffering?->course?->credits ?? 3,
            ];
        })->filter(fn ($r) => $r['course_id'] !== null);

        if ($resultsWithCourse->isNotEmpty()) {
            $courseGrades = $resultsWithCourse->groupBy('course_id')->map(function ($items, $courseId) use ($user) {
                $att = $user->getAttendanceScoreByCourse($courseId);
                $nonQuiz = $items->where('assessment_type', '!=', 'quiz')->sum('score_obtained');
                $quiz = $items->where('assessment_type', 'quiz')->sum('score_obtained');
                $total = min($att + $nonQuiz + $quiz, 100);
                $grade = $total >= 50 ? 'P' : 'F';
                return ['total' => $total, 'grade' => $grade, 'credits' => $items->first()['credits'] ?? 3];
            });

            $averageScore = round($courseGrades->avg('total'), 1);
            $totalCredits = $courseGrades->sum('credits');
            $weightedPoints = $courseGrades->sum(fn ($g) => ($g['grade'] === 'P' ? 2.0 : 0.0) * $g['credits']);
            $gpa = $totalCredits > 0 ? round($weightedPoints / $totalCredits, 2) : 0;
            $overallGrade = $averageScore >= 90 ? 'A' : ($averageScore >= 80 ? 'B' : ($averageScore >= 70 ? 'C' : ($averageScore >= 60 ? 'D' : ($averageScore >= 50 ? 'E' : 'F'))));

            // Rank
            $peerIds = StudentCourseEnrollment::whereIn('course_offering_id', $enrolledOfferingIds)->pluck('student_user_id')->unique();
            $rankings = $peerIds->map(function ($peerId) use ($enrolledOfferingIds) {
                $peer = \App\Models\User::find($peerId);
                if (!$peer) return ['id' => $peerId, 'total' => 0];
                $assessQuery = function ($q) use ($enrolledOfferingIds) {
                    $q->select('id')->from('assignments')->whereIn('course_offering_id', $enrolledOfferingIds)
                        ->union(DB::table('quizzes')->select('id')->whereIn('course_offering_id', $enrolledOfferingIds))
                        ->union(DB::table('exams')->select('id')->whereIn('course_offering_id', $enrolledOfferingIds));
                };
                $nonQuiz = \App\Models\ExamResult::where('student_user_id', $peerId)->where('assessment_type', '!=', 'quiz')
                    ->whereIn('assessment_id', $assessQuery)->sum('score_obtained');
                $quiz = \App\Models\ExamResult::where('student_user_id', $peerId)->where('assessment_type', 'quiz')
                    ->whereIn('assessment_id', $assessQuery)->sum('score_obtained');
                $att = $peer->getAttendanceScoreByCourse($enrolledOfferingIds->first());
                return ['id' => $peerId, 'total' => min((float) $nonQuiz + (float) $quiz + (float) $att, 100)];
            })->sortByDesc('total')->values();
            $totalClassmates = $rankings->count();
            $rankIndex = $rankings->search(fn ($r) => $r['id'] == $studentId);
            $overallRank = ($rankIndex !== false) ? $rankIndex + 1 : '-';
        }

        return view('student.dashboard', compact(
            'user', 'totalPresent', 'totalAbsent', 'totalPermission', 'totalLate',
            'enrolledCourses', 'upcomingSchedules', 'studentProgram', 'availableCoursesInProgram',
            'completedCoursesCount', 'totalCoursesInProgram', 'combinedFeed', 'todayName',
            'attendanceScore', 'gpa', 'averageScore', 'overallRank', 'totalClassmates', 'overallGrade'
        ));
    }

    public function updateTelegram(Request $request)
    {
        $request->validate(['telegram_chat_id' => 'required|numeric']);
        auth()->user()->update(['telegram_chat_id' => $request->telegram_chat_id]);
        return back()->with('success', 'អបអរសាទរ! គណនី Telegram របស់អ្នកត្រូវបានភ្ជាប់ហើយ។');
    }

    public function myTimetable()
    {
        return redirect()->route('student.my-schedule');
    }
}
