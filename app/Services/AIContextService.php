<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AIContextService
{
    public function __construct(
        protected NmuWebsiteService $websiteService
    ) {}

    public function getDatabaseContext(User $user, string $chatOption = 'info'): string
    {
        $cacheKey = "ai_context_{$user->id}_{$chatOption}";

        return Cache::remember($cacheKey, 300, function () use ($user, $chatOption) {
            return $this->buildContext($user, $chatOption);
        });
    }

    public function clearCache(User $user): void
    {
        $keys = [
            "ai_context_{$user->id}_info",
            "ai_context_{$user->id}_process",
            "ai_context_{$user->id}_search",
        ];
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    protected function buildContext(User $user, string $chatOption): string
    {
        $context = "=== NMU Class Management System - Live Data ===\n";
        $context .= "Current Date: ".now()->format('Y-m-d H:i')." (".now()->format('l').")\n";

        $currentYear = DB::table('academic_years')->where('is_current', true)->first();
        if ($currentYear) {
            $context .= "Active Academic Year: {$currentYear->name}\n";
        }

        $context .= "\n";

        try {
            // Add comprehensive DB data based on role
            match ($user->role) {
                'student' => $this->addStudentContext($user, $context),
                'professor' => $this->addProfessorContext($user, $context),
                'admin' => $this->addAdminContext($user, $context),
                default => null,
            };

            // Add announcements
            $announcements = DB::table('announcements')
                ->where(function ($q) use ($user) {
                    $q->where('target_role', 'all')->orWhere('target_role', $user->role);
                })
                ->latest()
                ->limit(5)
                ->get(['title_km', 'content_km', 'created_at']);

            if ($announcements->isNotEmpty()) {
                $context .= "\n=== LATEST ANNOUNCEMENTS ===\n";
                foreach ($announcements as $a) {
                    $content = strip_tags($a->content_km ?? '');
                    $context .= "- [{$a->created_at}] {$a->title_km}";
                    if ($content) {
                        $context .= ": " . Str::limit($content, 200);
                    }
                    $context .= "\n";
                }
            }

            // Add NMU website data
            $context .= "\n" . $this->websiteService->getUniversityInfo();

        } catch (\Exception $e) {
            $context .= "\n(Database context unavailable: {$e->getMessage()})\n";
        }

        return $context;
    }

    // ========================================================================
    // STUDENT CONTEXT (Comprehensive)
    // ========================================================================
    protected function addStudentContext(User $user, string &$context): void
    {
        $context .= "=== STUDENT PROFILE ===\n";
        $context .= "Name: {$user->name}\n";
        $context .= "Role: Student\n";
        $context .= "Student ID: ".($user->student_id_code ?: 'N/A')."\n";
        $context .= "Generation: ".($user->generation ?: 'N/A')."\n";

        $profile = DB::table('student_profiles')->where('user_id', $user->id)->first();
        if ($profile) {
            $context .= "Full Name (KM): ".($profile->full_name_km ?: 'N/A')."\n";
            $context .= "Full Name (EN): ".($profile->full_name_en ?: 'N/A')."\n";
            $context .= "Gender: ".($profile->gender ?: 'N/A')."\n";
            $context .= "Phone: ".($profile->phone_number ?: 'N/A')."\n";
            $context .= "Date of Birth: ".($profile->date_of_birth ?: 'N/A')."\n";
            $context .= "Address: ".($profile->address ?: 'N/A')."\n";
        }

        $program = DB::table('programs')->where('id', $user->program_id)->first();
        if ($program) {
            $context .= "Program: {$program->name_km} ({$program->name_en})\n";
            $context .= "Degree Level: ".($program->degree_level ?: 'N/A')."\n";
            $context .= "Duration: ".($program->duration_years ?: 'N/A')." years\n";

            $dept = DB::table('departments')->where('id', $program->department_id)->first();
            if ($dept) {
                $context .= "Department: {$dept->name_km} ({$dept->name_en})\n";
            }
            $faculty = DB::table('faculties')->where('id', $dept->faculty_id ?? null)->first();
            if ($faculty) {
                $context .= "Faculty: {$faculty->name_km} ({$faculty->name_en})\n";
            }
        }

        // Enrolled courses
        $enrollments = DB::table('student_course_enrollments')
            ->join('course_offerings', 'student_course_enrollments.course_offering_id', '=', 'course_offerings.id')
            ->join('courses', 'course_offerings.course_id', '=', 'courses.id')
            ->leftJoin('users as lecturer', 'course_offerings.lecturer_user_id', '=', 'lecturer.id')
            ->where('student_course_enrollments.student_user_id', $user->id)
            ->select(
                'course_offerings.id as offering_id',
                'courses.title_km as course_name',
                'courses.title_en as course_name_en',
                'courses.credits',
                'course_offerings.section',
                'course_offerings.academic_year',
                'course_offerings.semester',
                'lecturer.name as lecturer_name',
                'student_course_enrollments.final_grade',
                'student_course_enrollments.status as enrollment_status'
            )
            ->get();

        if ($enrollments->isNotEmpty()) {
            $context .= "\n=== ENROLLED COURSES ({$enrollments->count()} courses) ===\n";
            foreach ($enrollments as $e) {
                $grade = $e->final_grade ?? 'Not graded';
                $context .= "- [ID:{$e->offering_id}] {$e->course_name} ({$e->course_name_en}) | Section: {$e->section} | Credits: ".($e->credits ?: '?')." | Lecturer: ".($e->lecturer_name ?: 'TBA')." | Year: ".($e->academic_year ?: 'N/A')." | Semester: ".($e->semester ?: 'N/A')." | Grade: {$grade}\n";
            }
        }

        // Attendance
        $attendanceOverall = DB::table('attendances')
            ->where('student_user_id', $user->id)
            ->selectRaw("COUNT(CASE WHEN status = 'present' THEN 1 END) as present_count")
            ->selectRaw("COUNT(CASE WHEN status = 'absent' THEN 1 END) as absent_count")
            ->selectRaw("COUNT(CASE WHEN status = 'permission' THEN 1 END) as permission_count")
            ->selectRaw("COUNT(*) as total")
            ->first();

        if ($attendanceOverall && $attendanceOverall->total > 0) {
            $pct = round(($attendanceOverall->present_count / $attendanceOverall->total) * 100, 1);
            $context .= "\n=== ATTENDANCE SUMMARY ===\n";
            $context .= "Total sessions: {$attendanceOverall->total}\n";
            $context .= "Present: {$attendanceOverall->present_count} ({$pct}%)\n";
            $context .= "Absent: {$attendanceOverall->absent_count}\n";
            $context .= "Permission: {$attendanceOverall->permission_count}\n";
        }

        // Per-course attendance
        $courseAttendance = DB::table('attendances')
            ->join('course_offerings', 'attendances.course_offering_id', '=', 'course_offerings.id')
            ->join('courses', 'course_offerings.course_id', '=', 'courses.id')
            ->where('attendances.student_user_id', $user->id)
            ->select(
                'courses.title_km',
                DB::raw("COUNT(CASE WHEN attendances.status = 'present' THEN 1 END) as present_count"),
                DB::raw("COUNT(CASE WHEN attendances.status = 'absent' THEN 1 END) as absent_count"),
                DB::raw("COUNT(CASE WHEN attendances.status = 'permission' THEN 1 END) as permission_count"),
                DB::raw("COUNT(*) as total")
            )
            ->groupBy('courses.title_km')
            ->get();

        if ($courseAttendance->isNotEmpty()) {
            $context .= "\nPer-Course Attendance:\n";
            foreach ($courseAttendance as $ca) {
                $pct = $ca->total > 0 ? round(($ca->present_count / $ca->total) * 100, 1) : 0;
                $context .= "- {$ca->title_km}: {$ca->present_count}/{$ca->total} ({$pct}%) | Absent: {$ca->absent_count} | Permission: {$ca->permission_count}\n";
            }
        }

        // Schedule
        $schedules = DB::table('schedules')
            ->join('course_offerings', 'schedules.course_offering_id', '=', 'course_offerings.id')
            ->join('courses', 'course_offerings.course_id', '=', 'courses.id')
            ->leftJoin('rooms', 'schedules.room_id', '=', 'rooms.id')
            ->join('student_course_enrollments', 'course_offerings.id', '=', 'student_course_enrollments.course_offering_id')
            ->where('student_course_enrollments.student_user_id', $user->id)
            ->select('courses.title_km', 'schedules.day_of_week', 'schedules.start_time', 'schedules.end_time', 'rooms.room_number')
            ->orderByRaw("FIELD(schedules.day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
            ->get();

        if ($schedules->isNotEmpty()) {
            $context .= "\n=== WEEKLY SCHEDULE ===\n";
            foreach ($schedules as $s) {
                $room = $s->room_number ?: 'TBA';
                $context .= "- {$s->day_of_week}: {$s->title_km} ({$s->start_time}-{$s->end_time}) @ Room {$room}\n";
            }
        }

        // Assignments
        $assignments = DB::table('assignments')
            ->join('course_offerings', 'assignments.course_offering_id', '=', 'course_offerings.id')
            ->join('courses', 'course_offerings.course_id', '=', 'courses.id')
            ->join('student_course_enrollments', 'course_offerings.id', '=', 'student_course_enrollments.course_offering_id')
            ->where('student_course_enrollments.student_user_id', $user->id)
            ->orderBy('assignments.due_date')
            ->limit(10)
            ->get(['assignments.title', 'assignments.title_km', 'assignments.due_date', 'assignments.max_score', 'courses.title_km as course_name']);

        if ($assignments->isNotEmpty()) {
            $context .= "\n=== ASSIGNMENTS ===\n";
            foreach ($assignments as $a) {
                $context .= "- [{$a->course_name}] ".($a->title_km ?: $a->title)." | Due: {$a->due_date} | Max Score: ".($a->max_score ?: '?')."\n";
            }
        }

        // Exams
        $exams = DB::table('exams')
            ->join('course_offerings', 'exams.course_offering_id', '=', 'course_offerings.id')
            ->join('courses', 'course_offerings.course_id', '=', 'courses.id')
            ->join('student_course_enrollments', 'course_offerings.id', '=', 'student_course_enrollments.course_offering_id')
            ->where('student_course_enrollments.student_user_id', $user->id)
            ->orderBy('exams.exam_date')
            ->get(['exams.title', 'exams.title_km', 'exams.exam_date', 'exams.max_score', 'courses.title_km as course_name']);

        if ($exams->isNotEmpty()) {
            $context .= "\n=== EXAMS ===\n";
            foreach ($exams as $e) {
                $context .= "- [{$e->course_name}] ".($e->title_km ?: $e->title)." | Date: {$e->exam_date} | Max Score: ".($e->max_score ?: '?')."\n";
            }
        }

        // Grades
        $grades = DB::table('student_course_enrollments')
            ->join('course_offerings', 'student_course_enrollments.course_offering_id', '=', 'course_offerings.id')
            ->join('courses', 'course_offerings.course_id', '=', 'courses.id')
            ->where('student_course_enrollments.student_user_id', $user->id)
            ->get(['courses.title_km', 'student_course_enrollments.final_grade', 'student_course_enrollments.status']);

        if ($grades->isNotEmpty()) {
            $context .= "\n=== GRADES ===\n";
            foreach ($grades as $g) {
                $context .= "- {$g->title_km}: ".($g->final_grade ?: 'Not graded')." ({$g->status})\n";
            }
        }

        // Exam results
        $examResults = DB::table('exam_results')
            ->join('exams', 'exam_results.assessment_id', '=', 'exams.id')
            ->join('course_offerings', 'exams.course_offering_id', '=', 'course_offerings.id')
            ->join('courses', 'course_offerings.course_id', '=', 'courses.id')
            ->where('exam_results.student_user_id', $user->id)
            ->latest('exam_results.created_at')
            ->limit(10)
            ->get(['exams.title_km as exam_title', 'courses.title_km', 'exam_results.score_obtained', 'exams.max_score', 'exam_results.assessment_type']);

        if ($examResults->isNotEmpty()) {
            $context .= "\n=== ASSESSMENT RESULTS ===\n";
            foreach ($examResults as $er) {
                $pct = $er->max_score > 0 ? round(($er->score_obtained / $er->max_score) * 100, 1) : '?';
                $context .= "- [{$er->title_km}] {$er->exam_title} ({$er->assessment_type}): {$er->score_obtained}/{$er->max_score} ({$pct}%)\n";
            }
        }

        // Quizzes
        $quizzes = DB::table('quizzes')
            ->join('course_offerings', 'quizzes.course_offering_id', '=', 'course_offerings.id')
            ->join('courses', 'course_offerings.course_id', '=', 'courses.id')
            ->join('student_course_enrollments', 'course_offerings.id', '=', 'student_course_enrollments.course_offering_id')
            ->where('student_course_enrollments.student_user_id', $user->id)
            ->orderBy('quizzes.quiz_date')
            ->get(['quizzes.title', 'quizzes.title_km', 'quizzes.quiz_date', 'quizzes.max_score', 'courses.title_km as course_name']);

        if ($quizzes->isNotEmpty()) {
            $context .= "\n=== QUIZZES ===\n";
            foreach ($quizzes as $q) {
                $context .= "- [{$q->course_name}] ".($q->title_km ?: $q->title)." | Date: {$q->quiz_date} | Max Score: ".($q->max_score ?: '?')."\n";
            }
        }
    }

    // ========================================================================
    // PROFESSOR CONTEXT (Comprehensive)
    // ========================================================================
    protected function addProfessorContext(User $user, string &$context): void
    {
        $context .= "=== PROFESSOR PROFILE ===\n";
        $context .= "Name: {$user->name}\n";
        $context .= "Role: Professor\n";

        $profile = DB::table('professor_profiles')->where('user_id', $user->id)->first();
        if ($profile) {
            $context .= "Full Name (KM): ".($profile->full_name_km ?: 'N/A')."\n";
            $context .= "Full Name (EN): ".($profile->full_name_en ?: 'N/A')."\n";
            $context .= "Position: ".($profile->position ?: 'N/A')."\n";
            $context .= "Qualifications: ".($profile->qualifications ?: 'N/A')."\n";
            $context .= "Phone: ".($profile->phone_number ?: 'N/A')."\n";
        }

        $dept = DB::table('departments')->where('id', $user->department_id)->first();
        if ($dept) {
            $context .= "Department: {$dept->name_km} ({$dept->name_en})\n";
            $faculty = DB::table('faculties')->where('id', $dept->faculty_id)->first();
            if ($faculty) {
                $context .= "Faculty: {$faculty->name_km} ({$faculty->name_en})\n";
            }
        }

        // All course offerings
        $offerings = DB::table('course_offerings')
            ->join('courses', 'course_offerings.course_id', '=', 'courses.id')
            ->where('course_offerings.lecturer_user_id', $user->id)
            ->whereNull('course_offerings.deleted_at')
            ->select(
                'course_offerings.id',
                'courses.title_km',
                'courses.title_en',
                'courses.credits',
                'course_offerings.section',
                'course_offerings.academic_year',
                'course_offerings.semester',
                'course_offerings.capacity',
                'course_offerings.start_date',
                'course_offerings.end_date'
            )
            ->get();

        if ($offerings->isNotEmpty()) {
            $context .= "\n=== COURSE OFFERINGS ({$offerings->count()} total) ===\n";
            $totalStudents = 0;
            foreach ($offerings as $o) {
                $enrolled = DB::table('student_course_enrollments')
                    ->where('course_offering_id', $o->id)
                    ->count();
                $totalStudents += $enrolled;

                $context .= "\n--- [ID:{$o->id}] {$o->title_km} ({$o->title_en}) ---\n";
                $context .= "Section: {$o->section} | Year: ".($o->academic_year ?: 'N/A')." | Semester: ".($o->semester ?: 'N/A')." | Credits: ".($o->credits ?: '?')." | Capacity: {$o->capacity} | Enrolled: {$enrolled}\n";
                $context .= "Period: ".($o->start_date ?: 'N/A')." to ".($o->end_date ?: 'N/A')."\n";

                // Students list
                $students = DB::table('student_course_enrollments')
                    ->join('users', 'student_course_enrollments.student_user_id', '=', 'users.id')
                    ->leftJoin('student_profiles', 'users.id', '=', 'student_profiles.user_id')
                    ->where('student_course_enrollments.course_offering_id', $o->id)
                    ->select('users.name', 'users.student_id_code', 'student_profiles.full_name_km', 'student_course_enrollments.final_grade', 'student_course_enrollments.status')
                    ->orderBy('users.name')
                    ->get();

                if ($students->isNotEmpty()) {
                    $context .= "Students ({$students->count()}):\n";
                    foreach ($students as $s) {
                        $grade = $s->final_grade ?? '-';
                        $sid = $s->student_id_code ?? '';
                        $context .= "  - {$s->name} ({$s->full_name_km}) [{$sid}] | Grade: {$grade} | Status: {$s->status}\n";
                    }
                }

                // Schedules
                $schedules = DB::table('schedules')
                    ->leftJoin('rooms', 'schedules.room_id', '=', 'rooms.id')
                    ->where('schedules.course_offering_id', $o->id)
                    ->get(['schedules.day_of_week', 'schedules.start_time', 'schedules.end_time', 'rooms.room_number']);

                if ($schedules->isNotEmpty()) {
                    $context .= "Schedule:\n";
                    foreach ($schedules as $s) {
                        $context .= "  - {$s->day_of_week} {$s->start_time}-{$s->end_time} @ Room ".($s->room_number ?: 'TBA')."\n";
                    }
                }

                // Assignments for this course
                $assignments = DB::table('assignments')
                    ->where('course_offering_id', $o->id)
                    ->orderBy('created_at', 'desc')
                    ->get(['id', 'title', 'title_km', 'due_date', 'max_score']);

                if ($assignments->isNotEmpty()) {
                    $context .= "Assignments:\n";
                    foreach ($assignments as $a) {
                        $context .= "  - [ID:{$a->id}] ".($a->title_km ?: $a->title)." | Due: {$a->due_date} | Max: {$a->max_score}\n";
                    }
                }

                // Exams
                $exams = DB::table('exams')
                    ->where('course_offering_id', $o->id)
                    ->orderBy('exam_date')
                    ->get(['id', 'title', 'title_km', 'exam_date', 'max_score']);

                if ($exams->isNotEmpty()) {
                    $context .= "Exams:\n";
                    foreach ($exams as $e) {
                        $context .= "  - [ID:{$e->id}] ".($e->title_km ?: $e->title)." | Date: {$e->exam_date} | Max: {$e->max_score}\n";
                    }
                }

                // Quizzes
                $quizzes = DB::table('quizzes')
                    ->where('course_offering_id', $o->id)
                    ->orderBy('quiz_date')
                    ->get(['id', 'title', 'title_km', 'quiz_date', 'max_score']);

                if ($quizzes->isNotEmpty()) {
                    $context .= "Quizzes:\n";
                    foreach ($quizzes as $q) {
                        $context .= "  - [ID:{$q->id}] ".($q->title_km ?: $q->title)." | Date: {$q->quiz_date} | Max: {$q->max_score}\n";
                    }
                }

                // Grade results for this course
                $results = DB::table('exam_results')
                    ->join('users', 'exam_results.student_user_id', '=', 'users.id')
                    ->where('exam_results.assessment_type', 'exam')
                    ->whereIn('exam_results.assessment_id', $exams->pluck('id'))
                    ->get(['users.name', 'exam_results.assessment_id', 'exam_results.score_obtained']);

                if ($results->isNotEmpty()) {
                    $context .= "Exam Results:\n";
                    foreach ($results as $r) {
                        $context .= "  - {$r->name}: {$r->score_obtained}\n";
                    }
                }
            }
            $context .= "\nTotal Students Across All Courses: {$totalStudents}\n";
        }

        // Grading categories
        $categories = DB::table('grading_categories')
            ->whereIn('course_offering_id', $offerings->pluck('id')->toArray())
            ->get(['name_km', 'weight_percentage', 'course_offering_id']);

        if ($categories->isNotEmpty()) {
            $context .= "\n=== GRADING CATEGORIES ===\n";
            foreach ($categories as $c) {
                $context .= "- [Offering:{$c->course_offering_id}] {$c->name_km} (Weight: {$c->weight_percentage}%)\n";
            }
        }

        // All students in system (for search)
        $allStudents = DB::table('users')
            ->where('role', 'student')
            ->leftJoin('student_profiles', 'users.id', '=', 'student_profiles.user_id')
            ->leftJoin('programs', 'users.program_id', '=', 'programs.id')
            ->select('users.name', 'users.student_id_code', 'student_profiles.full_name_km', 'programs.name_km as program')
            ->orderBy('users.name')
            ->limit(50)
            ->get();

        if ($allStudents->isNotEmpty()) {
            $context .= "\n=== ALL STUDENTS IN SYSTEM ({$allStudents->count()}) ===\n";
            foreach ($allStudents as $s) {
                $context .= "- {$s->name} ({$s->full_name_km}) [{$s->student_id_code}] - ".($s->program ?: 'N/A')."\n";
            }
        }
    }

    // ========================================================================
    // ADMIN CONTEXT (Comprehensive)
    // ========================================================================
    protected function addAdminContext(User $user, string &$context): void
    {
        $context .= "=== ADMIN PROFILE ===\n";
        $context .= "Name: {$user->name}\n";
        $context .= "Role: Admin\n";

        // System overview
        $userStats = DB::table('users')
            ->selectRaw("role, COUNT(*) as count")
            ->groupBy('role')
            ->pluck('count', 'role');

        $context .= "\n=== SYSTEM OVERVIEW ===\n";
        $context .= "Total Users: ".$userStats->sum()."\n";
        $context .= "Students: ".$userStats->get('student', 0)."\n";
        $context .= "Professors: ".$userStats->get('professor', 0)."\n";
        $context .= "Admins: ".$userStats->get('admin', 0)."\n";

        // All faculties
        $faculties = DB::table('faculties')->get();
        $context .= "\n=== FACULTIES ({$faculties->count()}) ===\n";
        foreach ($faculties as $f) {
            $deptCount = DB::table('departments')->where('faculty_id', $f->id)->count();
            $programCount = DB::table('programs')->where('faculty_id', $f->id)->count();
            $context .= "- [ID:{$f->id}] {$f->name_km} ({$f->name_en}) | Departments: {$deptCount} | Programs: {$programCount}\n";
        }

        // All departments
        $departments = DB::table('departments')->get();
        $context .= "\n=== DEPARTMENTS ({$departments->count()}) ===\n";
        foreach ($departments as $d) {
            $profCount = DB::table('users')->where('role', 'professor')->where('department_id', $d->id)->count();
            $studentCount = DB::table('users')->where('role', 'student')->where('department_id', $d->id)->count();
            $context .= "- [ID:{$d->id}] {$d->name_km} ({$d->name_en}) | Professors: {$profCount} | Students: {$studentCount}\n";
        }

        // All programs
        $programs = DB::table('programs')->get();
        $context .= "\n=== PROGRAMS ({$programs->count()}) ===\n";
        foreach ($programs as $p) {
            $studentCount = DB::table('users')->where('role', 'student')->where('program_id', $p->id)->count();
            $context .= "- [ID:{$p->id}] {$p->name_km} ({$p->name_en}) | Level: ".($p->degree_level ?: 'N/A')." | Students: {$studentCount} | Duration: ".($p->duration_years ?: '?')." years\n";
        }

        // All courses
        $courses = DB::table('courses')->get();
        $context .= "\n=== ALL COURSES ({$courses->count()}) ===\n";
        foreach ($courses as $c) {
            $dept = DB::table('departments')->where('id', $c->department_id)->first();
            $context .= "- [ID:{$c->id}] {$c->title_km} ({$c->title_en}) | Credits: ".($c->credits ?: '?')." | Dept: ".($dept->name_km ?? 'N/A')."\n";
        }

        // Course offerings
        $offeringCount = DB::table('course_offerings')->whereNull('deleted_at')->count();
        $context .= "\n=== COURSE OFFERINGS ({$offeringCount} active) ===\n";

        $offerings = DB::table('course_offerings')
            ->join('courses', 'course_offerings.course_id', '=', 'courses.id')
            ->leftJoin('users as lecturer', 'course_offerings.lecturer_user_id', '=', 'lecturer.id')
            ->whereNull('course_offerings.deleted_at')
            ->orderBy('course_offerings.created_at', 'desc')
            ->limit(20)
            ->get([
                'course_offerings.id',
                'courses.title_km',
                'courses.title_en',
                'course_offerings.section',
                'course_offerings.academic_year',
                'course_offerings.semester',
                'lecturer.name as teacher_name',
                'course_offerings.capacity'
            ]);

        foreach ($offerings as $o) {
            $enrolled = DB::table('student_course_enrollments')->where('course_offering_id', $o->id)->count();
            $context .= "- [ID:{$o->id}] {$o->title_km} ({$o->title_en}) | Section: {$o->section} | Year: ".($o->academic_year ?: '?')." | Sem: ".($o->semester ?: '?')." | Lecturer: ".($o->teacher_name ?: 'TBA')." | Students: {$enrolled}/{$o->capacity}\n";
        }

        // All rooms
        $rooms = DB::table('rooms')->get();
        $context .= "\n=== ROOMS ({$rooms->count()}) ===\n";
        foreach ($rooms as $r) {
            $context .= "- [ID:{$r->id}] {$r->name} | Capacity: ".($r->capacity ?: 'N/A')." | Floor: ".($r->floor ?: 'N/A')."\n";
        }

        // All professors
        $professors = DB::table('users')
            ->where('role', 'professor')
            ->leftJoin('professor_profiles', 'users.id', '=', 'professor_profiles.user_id')
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->select('users.name', 'users.id', 'professor_profiles.full_name_km', 'departments.name_km as dept_name')
            ->get();

        $context .= "\n=== ALL PROFESSORS ({$professors->count()}) ===\n";
        foreach ($professors as $p) {
            $courseCount = DB::table('course_offerings')->where('lecturer_user_id', $p->id)->whereNull('deleted_at')->count();
            $context .= "- {$p->name} ({$p->full_name_km}) | Dept: ".($p->dept_name ?: 'N/A')." | Active Courses: {$courseCount}\n";
        }

        // Recent users
        $recentUsers = DB::table('users')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get(['name', 'role', 'email', 'created_at']);
        if ($recentUsers->isNotEmpty()) {
            $context .= "\n=== RECENT USER REGISTRATIONS ===\n";
            foreach ($recentUsers as $u) {
                $context .= "- {$u->name} ({$u->role}) | Email: ".($u->email ?: 'N/A')." | Date: {$u->created_at}\n";
            }
        }

        // Audit logs
        $auditLogs = DB::table('audit_logs')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        if ($auditLogs->isNotEmpty()) {
            $context .= "\n=== RECENT SYSTEM ACTIVITY ===\n";
            foreach ($auditLogs as $log) {
                $context .= "- [{$log->created_at}] {$log->action} on {$log->auditable_type}\n";
            }
        }

        // Generations
        $generations = DB::table('generations')->get();
        if ($generations->isNotEmpty()) {
            $context .= "\n=== GENERATIONS ===\n";
            foreach ($generations as $g) {
                $count = DB::table('users')->where('role', 'student')->where('generation', $g->name)->count();
                $context .= "- {$g->name} | Students: {$count}\n";
            }
        }

        // Grading categories
        $gradingCategories = DB::table('grading_categories')->get();
        if ($gradingCategories->isNotEmpty()) {
            $context .= "\n=== GRADING CATEGORIES ===\n";
            foreach ($gradingCategories as $gc) {
                $context .= "- {$gc->name_km} (Weight: {$gc->weight_percentage}%)\n";
            }
        }
    }
}
