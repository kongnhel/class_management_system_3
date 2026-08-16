<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseOffering;
use App\Models\ExamResult;
use App\Models\Quiz;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use App\Services\GradingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentGradeController extends Controller
{
    public function myGrades(Request $request)
    {
        $user = Auth::user();
        $currentYear = $request->input('academic_year');
        $currentSemester = $request->input('semester');

        $enrolledOfferingIds = StudentCourseEnrollment::where('student_user_id', $user->id)->pluck('course_offering_id');
        $allExamResults = ExamResult::where('student_user_id', $user->id)
            ->whereIn('assessment_id', function ($q) use ($enrolledOfferingIds) {
                $q->select('id')->from('assignments')->whereIn('course_offering_id', $enrolledOfferingIds)
                    ->union(DB::table('quizzes')->select('id')->whereIn('course_offering_id', $enrolledOfferingIds))
                    ->union(DB::table('exams')->select('id')->whereIn('course_offering_id', $enrolledOfferingIds));
            })
            ->with(['assignment', 'exam', 'quiz'])
            ->get();

        $filteredOfferingIds = $enrolledOfferingIds;
        if ($currentYear || $currentSemester) {
            $filteredOfferingIds = CourseOffering::whereIn('id', $enrolledOfferingIds)
                ->when($currentYear, fn ($q) => $q->where('academic_year', $currentYear))
                ->when($currentSemester, fn ($q) => $q->where('semester', $currentSemester))
                ->pluck('id');
        }

        $filteredResults = $allExamResults->whereIn('course_offering_id', $filteredOfferingIds);
        $courseOfferings = CourseOffering::whereIn('id', $filteredOfferingIds)
            ->with(['course', 'assignments', 'exams', 'quizzes'])
            ->get();

        $courseGrades = $courseOfferings->map(function ($offering) use ($filteredResults, $user) {
            $courseId = $offering->course_id;
            $items = $filteredResults->where('course_offering_id', $offering->id)->values();
            // Build the breakdown from every assessment in the offering, not only
            // assessments that already have an ExamResult. This keeps score boxes
            // visible when a professor has not entered a result yet.
            $resultFor = function (string $type, int $assessmentId) use ($items) {
                return $items->first(fn ($result) =>
                    $result->assessment_type === $type && (int) $result->assessment_id === $assessmentId
                );
            };

            $assessmentItems = collect();
            foreach ($offering->assignments as $assessment) {
                $result = $resultFor('assignment', $assessment->id);
                $assessmentItems->push($result ?: (new \App\Models\ExamResult([
                    'assessment_id' => $assessment->id,
                    'assessment_type' => 'assignment',
                    'student_user_id' => $user->id,
                    'score_obtained' => 0,
                ]))->setRelation('assignment', $assessment));
            }
            foreach ($offering->exams as $assessment) {
                $result = $resultFor('exam', $assessment->id);
                $assessmentItems->push($result ?: (new \App\Models\ExamResult([
                    'assessment_id' => $assessment->id,
                    'assessment_type' => 'exam',
                    'student_user_id' => $user->id,
                    'score_obtained' => 0,
                ]))->setRelation('exam', $assessment));
            }
            foreach ($offering->quizzes as $assessment) {
                $result = $resultFor('quiz', $assessment->id);
                $assessmentItems->push($result ?: (new \App\Models\ExamResult([
                    'assessment_id' => $assessment->id,
                    'assessment_type' => 'quiz',
                    'student_user_id' => $user->id,
                    'score_obtained' => 0,
                ]))->setRelation('quiz', $assessment));
            }

            $items = $assessmentItems;
            $offeringId = $offering->id;
            $attendanceScore = $offeringId ? $user->getAttendanceScoreByCourse($offeringId) : 0;
            $absCount = $offeringId ? \App\Models\AttendanceRecord::where('student_user_id', $user->id)->where('course_offering_id', $offeringId)->where('status', 'absent')->count() : 0;
            $perCount = $offeringId ? \App\Models\AttendanceRecord::where('student_user_id', $user->id)->where('course_offering_id', $offeringId)->where('status', 'permission')->count() : 0;

            $nonQuiz = $items->where('assessment_type', '!=', 'quiz')->sum('score_obtained');
            $quizBonus = $items->where('assessment_type', 'quiz')->sum('score_obtained');
            $totalObtained = min($attendanceScore + $nonQuiz + $quizBonus, 100);
            $letterGrade = GradingService::getLetterGrade($totalObtained);
<<<<<<< HEAD
            $isFailedByGrade = ! GradingService::isPassing($letterGrade);

            // Check if student failed any individual non-quiz assessment
            $isFailedByAssessment = false;
            foreach ($items as $item) {
                if ($item->assessment_type === 'quiz') {
                    continue;
                }
                $assessment = match ($item->assessment_type) {
                    'assignment' => $item->assignment,
                    'exam' => $item->exam,
                    default => null,
                };
                $maxScore = (float) ($assessment->max_score ?? 100);
                if ($maxScore > 0 && ($item->score_obtained / $maxScore) < 0.5) {
                    $isFailedByAssessment = true;
                    break;
                }
            }
            $isFailed = $isFailedByGrade || $isFailedByAssessment;
            $effectiveLetterGrade = GradingService::getEffectiveLetterGrade($letterGrade, $isFailedByAssessment);
            $course = $offering?->course;
=======
            $isFailed = ! GradingService::isPassing($letterGrade);
            $course = $offering->course;
>>>>>>> 7ce0eb5c34478ec3e72f0dffa95fa79b0581fffb

            // Get course_offering_id for this course for ranking
            $offeringId = $offering?->id;
            $enrollments = $offeringId
                ? StudentCourseEnrollment::where('course_offering_id', $offeringId)->get()
                : collect();
            $rankings = $enrollments->map(function ($enrol) use ($offeringId) {
                $student = User::find($enrol->student_user_id);
                $att = $student ? $student->getAttendanceScoreByCourse($offeringId) : 0;
                $nonQuiz = ExamResult::where('student_user_id', $enrol->student_user_id)->where('assessment_type', '!=', 'quiz')
                    ->whereIn('assessment_id', function ($q) use ($offeringId) {
                        $q->select('id')->from('assignments')->where('course_offering_id', $offeringId)
                            ->union(DB::table('exams')->select('id')->where('course_offering_id', $offeringId));
                    })->sum('score_obtained');
                $quiz = ExamResult::where('student_user_id', $enrol->student_user_id)->where('assessment_type', 'quiz')
                    ->whereIn('assessment_id', function ($q) use ($offeringId) {
                        $q->select('id')->from('quizzes')->where('course_offering_id', $offeringId);
                    })->sum('score_obtained');

                return ['id' => $enrol->student_user_id, 'total' => min((float) $att + (float) $nonQuiz + (float) $quiz, 100)];
            })->sortByDesc('total')->values();

            $rankIndex = $rankings->search(fn ($r) => $r['id'] == $user->id);

            return (object) [
                'course_id' => $courseId,
                'course_code' => $course->code ?? '',
                'course_name_en' => $course->title_en ?? '',
                'course_name_km' => $course->title_km ?? '',
                'credits' => $course->credits ?? 3,
                'academic_year' => $offering?->academic_year ?? '',
                'semester' => $offering?->semester ?? '',
                'course_rank' => ($rankIndex !== false) ? $rankIndex + 1 : '-',
                'total_students' => $rankings->count(),
                'attendance_score' => $attendanceScore,
                'absent_count' => $absCount,
                'permission_count' => $perCount,
                'total_score' => $totalObtained,
                'grade' => $effectiveLetterGrade,
                'grade_points' => GradingService::getGradePoints($effectiveLetterGrade),
                'is_failed' => $isFailed,
                'assessments' => $items,
            ];
        })->values();

        $totalCredits = $courseGrades->sum('credits');
        $weightedPoints = $courseGrades->sum(fn ($g) => $g->grade_points * $g->credits);
        $gpa = $totalCredits > 0 ? round($weightedPoints / $totalCredits, 2) : 0;
        $averageScore = $courseGrades->count() > 0 ? round($courseGrades->avg('total_score'), 1) : 0;

        $peerIds = StudentCourseEnrollment::whereIn('course_offering_id', $filteredOfferingIds)->pluck('student_user_id')->unique();
        $rankings = $peerIds->map(function ($peerId) use ($filteredOfferingIds) {
            $peer = User::find($peerId);
            if (! $peer) {
                return ['id' => $peerId, 'total' => 0];
            }
            $total = 0;
            foreach ($filteredOfferingIds as $offeringId) {
                $nonQuiz = ExamResult::where('student_user_id', $peerId)->where('assessment_type', '!=', 'quiz')
                    ->whereIn('assessment_id', function ($q) use ($offeringId) {
                        $q->select('id')->from('assignments')->where('course_offering_id', $offeringId)
                            ->union(DB::table('exams')->select('id')->where('course_offering_id', $offeringId));
                    })->sum('score_obtained');
                $quiz = ExamResult::where('student_user_id', $peerId)->where('assessment_type', 'quiz')
                    ->whereIn('assessment_id', function ($q) use ($offeringId) {
                        $q->select('id')->from('quizzes')->where('course_offering_id', $offeringId);
                    })->sum('score_obtained');
                $att = $peer->getAttendanceScoreByCourse($offeringId);
                $total += min((float) $nonQuiz + (float) $quiz + (float) $att, 100);
            }

            return ['id' => $peerId, 'total' => $total];
        })->sortByDesc('total')->values();

        $rankIndex = $rankings->search(fn ($r) => $r['id'] == $user->id);
        $overallRank = ($rankIndex !== false) ? $rankIndex + 1 : '-';
        $totalClassmates = $rankings->count();
        $overallGrade = GradingService::getLetterGrade($averageScore);
        $totalFinalScore = round($averageScore, 1);

        $academicYears = CourseOffering::whereIn('id', $enrolledOfferingIds)->pluck('academic_year')->unique()->filter()->values();
        $semesters = CourseOffering::whereIn('id', $enrolledOfferingIds)->pluck('semester')->unique()->filter()->values();

        $grades = $courseGrades->filter(function ($g) use ($currentYear, $currentSemester) {
            if ($currentYear && $g->academic_year !== $currentYear) {
                return false;
            }
            if ($currentSemester && $g->semester !== $currentSemester) {
                return false;
            }

            return true;
        })->values();

        $page = request()->input('page', 1);
        $perPage = 10;
        $grades = new \Illuminate\Pagination\LengthAwarePaginator(
            $grades->forPage($page, $perPage),
            $grades->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('student.my-grades', compact(
            'user', 'grades', 'averageScore', 'overallRank', 'totalClassmates', 'overallGrade', 'totalFinalScore',
            'gpa', 'totalCredits', 'academicYears', 'semesters', 'currentYear', 'currentSemester', 'courseGrades'
        ));
    }

    public function mySchedule()
    {
        $user = Auth::user();
        $enrolledOfferingIds = StudentCourseEnrollment::where('student_user_id', $user->id)->pluck('course_offering_id');
        $schedules = \App\Models\Schedule::whereIn('course_offering_id', $enrolledOfferingIds)
            ->whereHas('courseOffering.course')
            ->with(['room', 'courseOffering.course', 'courseOffering.lecturer', 'courseOffering.targetPrograms'])
            ->orderByRaw("FIELD(day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
            ->orderBy('start_time')
            ->get();
        $studentProgram = $user->program;

        $semester = $schedules->first()?->courseOffering?->semester ?? '';
        $semesterNum = str_replace('ឆមាសទី', '', $semester);

        $generation = $schedules->first()?->courseOffering?->targetPrograms?->first()?->pivot?->generation ?? '';
        $startDate = $schedules->first()?->courseOffering?->start_date ?? now();

        return view('student.my-schedule', compact('schedules', 'studentProgram', 'semester', 'semesterNum', 'user', 'generation', 'startDate'));
    }

    public function enrolledCourses($studentId)
    {
        abort_unless((int) $studentId === (int) Auth::id(), 403);

        $student = User::findOrFail($studentId);
        $enrollments = StudentCourseEnrollment::where('student_user_id', $student->id)
            ->whereHas('courseOffering.course')
            ->with(['courseOffering.course', 'courseOffering.lecturer'])
            ->get();

        return view('student.my-enrolled-courses', compact('student', 'enrollments'));
    }

    public function myAssessments()
    {
        $user = Auth::user();
        $enrolledOfferingIds = StudentCourseEnrollment::where('student_user_id', $user->id)->pluck('course_offering_id');
        $courseOfferings = CourseOffering::whereIn('id', $enrolledOfferingIds)
            ->with(['course', 'lecturer', 'assignments', 'exams', 'quizzes'])
            ->get();

        $allResultIds = ExamResult::where('student_user_id', $user->id)->get()->keyBy(fn ($r) => $r->assessment_type.'_'.$r->assessment_id);

        $assessmentsByCourse = $courseOfferings->map(function ($offering) use ($user, $allResultIds) {
            $items = collect();

            foreach ($offering->assignments as $a) {
                $key = 'assignment_'.$a->id;
                $result = $allResultIds->get($key);
                $items->push(['title' => $a->title_km ?? $a->title_en, 'type' => 'assignment', 'type_label' => 'កិច្ចការ', 'max_score' => $a->max_score, 'score' => $result?->score_obtained, 'date' => $a->due_date, 'notes' => $result?->notes]);
            }

            foreach ($offering->exams as $e) {
                $key = 'exam_'.$e->id;
                $result = $allResultIds->get($key);
                $titleEn = strtolower($e->title_en ?? '');
                $isMidterm = str_contains($titleEn, 'midterm') || str_contains($titleEn, 'ពាក់កណ្ដាល់') || str_contains(strtolower($e->title_km ?? ''), 'ពាក់កណ្ដាល់');
                $typeLabel = $isMidterm ? 'ប្រឡងពាក់កណ្ដាល់' : 'ប្រឡងប្រចាំឆមាស';
                $type = $isMidterm ? 'midterm' : 'final';
                $items->push(['title' => $e->title_km ?? $e->title_en, 'type' => $type, 'type_label' => $typeLabel, 'max_score' => $e->max_score, 'score' => $result?->score_obtained, 'date' => $e->exam_date, 'notes' => $result?->notes]);
            }

            foreach ($offering->quizzes as $q) {
                $key = 'quiz_'.$q->id;
                $result = $allResultIds->get($key);
                $items->push(['title' => $q->title_km ?? $q->title_en, 'type' => 'quiz', 'type_label' => 'Quiz (Bonus)', 'max_score' => $q->max_score, 'score' => $result?->score_obtained, 'date' => $q->quiz_date, 'notes' => $result?->notes]);
            }

            $att = (float) ($user->getAttendanceScoreByCourse($offering->id) ?? 0);

            $scored = $items->filter(fn ($a) => $a['score'] !== null);
            $nonQuiz = $scored->where('type', '!=', 'quiz')->sum('score');
            $quizBonus = $scored->where('type', 'quiz')->sum('score');
            $totalScore = min($att + $nonQuiz + $quizBonus, 100);
            $letterGrade = GradingService::getLetterGrade($totalScore);

            // Check if student failed any individual non-quiz assessment
            $isFailedByGrade = ! GradingService::isPassing($letterGrade);
            $isFailedByAssessment = false;
            foreach ($scored as $item) {
                if ($item['type'] === 'quiz') {
                    continue;
                }
                $maxScore = (float) ($item['max_score'] ?? 100);
                if ($maxScore > 0 && ($item['score'] / $maxScore) < 0.5) {
                    $isFailedByAssessment = true;
                    break;
                }
            }
            $isFailed = $isFailedByGrade || $isFailedByAssessment;
            $effectiveLetterGrade = GradingService::getEffectiveLetterGrade($letterGrade, $isFailedByAssessment);

            return [
                'offering' => $offering,
                'course_name' => $offering->course->title_km ?? $offering->course->title_en,
                'assessments' => $items->values(),
                'attendance_score' => $att,
                'quiz_bonus' => $quizBonus,
                'total_score' => $totalScore,
                'letter_grade' => $effectiveLetterGrade,
                'is_failed' => $isFailed,
            ];
        })->values();

        return view('student.my-assessments', compact('assessmentsByCourse'));
    }

    public function availablePrograms()
    {
        $programs = \App\Models\Program::with('department')->get();

        return view('student.available-programs', compact('programs'));
    }

    public function availableCourses()
    {
        $user = Auth::user();
        $enrolledIds = StudentCourseEnrollment::where('student_user_id', $user->id)->pluck('course_offering_id');
        $courses = CourseOffering::with(['course', 'lecturer'])->withCount('studentCourseEnrollments')
            ->whereHas('targetPrograms', fn ($q) => $q->where('program_id', $user->program_id)->where('generation', $user->generation))
            ->whereHas('course')
            ->where('end_date', '>=', now())->whereNotIn('id', $enrolledIds)->get();

        return view('student.available-courses', compact('courses'));
    }

    public function enrollSelf(Request $request)
    {
        $request->validate(['course_offering_id' => 'required|exists:course_offerings,id']);
        $user = Auth::user();

        $eligible = CourseOffering::whereKey($request->course_offering_id)
            ->where(function ($query) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->whereHas('targetPrograms', fn ($query) => $query
                ->where('program_id', $user->program_id)
                ->where('generation', $user->generation))
            ->exists();

        abort_unless($eligible, 403);

        $exists = StudentCourseEnrollment::where('student_user_id', $user->id)->where('course_offering_id', $request->course_offering_id)->exists();
        if ($exists) {
            return back()->with('error', 'អ្នកបានចុះឈ្មោះរួចហើយ។');
        }
        StudentCourseEnrollment::create(['student_user_id' => $user->id, 'course_offering_id' => $request->course_offering_id, 'enrollment_date' => now(), 'status' => 'enrolled']);

        return back()->with('success', 'ចុះឈ្មោះជោគជ័យ!');
    }

    public function enrollProgram(Request $request)
    {
        $request->validate(['program_id' => 'required|exists:programs,id']);
        $user = Auth::user();

        abort_unless((int) $request->program_id === (int) $user->program_id, 403);

        $gen = $user->generation;
        $offerings = CourseOffering::where(function ($query) {
            $query->whereNull('end_date')->orWhere('end_date', '>=', now());
        })->whereHas('targetPrograms', fn ($q) => $q->where('program_id', $request->program_id)->where('generation', $gen))->get();
        $enrolled = 0;
        foreach ($offerings as $offering) {
            $exists = StudentCourseEnrollment::where('student_user_id', $user->id)->where('course_offering_id', $offering->id)->exists();
            if (! $exists) {
                StudentCourseEnrollment::create(['student_user_id' => $user->id, 'course_offering_id' => $offering->id, 'enrollment_date' => now(), 'status' => 'enrolled']);
                $enrolled++;
            }
        }

        return back()->with('success', "ចុះឈ្មោះបាន {$enrolled} មុខវិជ្ជា!");
    }

    public function myEnrolledCourses()
    {
        $user = Auth::user();
        $enrollments = StudentCourseEnrollment::where('student_user_id', $user->id)
            ->whereHas('courseOffering.course')
            ->with(['courseOffering.course', 'courseOffering.lecturer'])->paginate(10);
        $studentProgram = $user->program;

        return view('student.my-enrolled-courses', compact('enrollments', 'studentProgram'));
    }
}
