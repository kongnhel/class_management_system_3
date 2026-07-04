<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\CourseOffering;
use App\Models\Exam;
use App\Models\Program;
use App\Models\Quiz;
use App\Models\Schedule;
use App\Models\StudentCourseEnrollment;
use App\Models\StudentProgramEnrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class StudentGradeController extends Controller
{
    public function myGrades(Request $request)
    {
        $user = Auth::user();

        // Get student's enrolled course offering IDs
        $enrolledOfferingIds = StudentCourseEnrollment::where('student_user_id', $user->id)
            ->where('status', 'enrolled')
            ->pluck('course_offering_id');

        // Available academic years and semesters for filter
        $availableFilters = CourseOffering::whereIn('id', $enrolledOfferingIds)
            ->select('academic_year', 'semester')
            ->distinct()
            ->orderByDesc('academic_year')
            ->get();

        $academicYears = $availableFilters->pluck('academic_year')->unique()->values();
        $semesters = $availableFilters->pluck('semester')->unique()->values();

        // Filter by academic year and semester
        $filterYear = $request->input('academic_year');
        $filterSemester = $request->input('semester');

        $filteredOfferingIds = CourseOffering::whereIn('id', $enrolledOfferingIds)
            ->when($filterYear, fn ($q) => $q->where('academic_year', $filterYear))
            ->when($filterSemester, fn ($q) => $q->where('semester', $filterSemester))
            ->pluck('id');

        // Get current academic year display
        $currentYear = $filterYear ?? $academicYears->first() ?? date('Y').'-'.(date('Y') + 1);
        $currentSemester = $filterSemester ?? $semesters->first() ?? '';

        // Get all exam results for enrolled courses
        // exam_results has no course_offering_id — link through assessment tables
        $allExamResults = \App\Models\ExamResult::where('student_user_id', $user->id)
            ->get()
            ->map(function ($result) {
                $assessment = match ($result->assessment_type) {
                    'assignment' => \App\Models\Assignment::with('courseOffering.course')->find($result->assessment_id),
                    'quiz' => \App\Models\Quiz::with('courseOffering.course')->find($result->assessment_id),
                    default => \App\Models\Exam::with('courseOffering.course')->find($result->assessment_id),
                };
                if (! $assessment) {
                    return null;
                }

                $result->course_id = $assessment->course_offering_id;
                $result->course_name_en = $assessment->courseOffering?->course?->title_en ?? 'Unknown';
                $result->course_name_km = $assessment->courseOffering?->course?->title_km ?? 'មិនមាន';
                $result->course_code = $assessment->courseOffering?->course?->code ?? '';
                $result->credits = (int) ($assessment->courseOffering?->course?->credits ?? 3);
                $result->academic_year = $assessment->courseOffering?->academic_year ?? '';
                $result->semester = $assessment->courseOffering?->semester ?? '';
                $result->max_score = (float) $assessment->max_score;
                $result->grade = $this->calculateGrade($result->score_obtained, $result->max_score);

                // Better display labels
                $result->display_type = match (true) {
                    $result->assessment_type === 'assignment' => 'Assignment',
                    $result->assessment_type === 'quiz' => 'Quiz',
                    $result->assessment_type === 'exam' && $result->max_score == 15 => 'Midterm',
                    $result->assessment_type === 'exam' && $result->max_score != 15 => 'Final',
                    default => ucfirst($result->assessment_type),
                };

                return $result;
            })
            ->filter()
            ->filter(fn ($result) => $filteredOfferingIds->contains($result->course_id));

        // Build per-course grade objects
        $courseGrades = $allExamResults->groupBy('course_id')->map(function ($items, $courseId) use ($user) {
            $attendanceScore = $user->getAttendanceScoreByCourse($courseId);

            $absCount = \App\Models\AttendanceRecord::where('student_user_id', $user->id)
                ->where('course_offering_id', $courseId)
                ->where('status', 'absent')->count();
            $perCount = \App\Models\AttendanceRecord::where('student_user_id', $user->id)
                ->where('course_offering_id', $courseId)
                ->where('status', 'permission')->count();

            $totalObtained = $items->where('assessment_type', '!=', 'quiz')->sum('score_obtained') + $attendanceScore;
            $quizBonus = $items->where('assessment_type', 'quiz')->sum('score_obtained');
            $totalObtained = min($totalObtained + $quizBonus, 100);

            $finalExamScore = $items->where('display_type', 'Final')->sum('score_obtained');
            $midtermScore = $items->where('display_type', 'Midterm')->sum('score_obtained');
            $assignmentScore = $items->where('display_type', 'Assignment')->sum('score_obtained');

            $isFailed = ($finalExamScore < 24 || $midtermScore < 9 || $assignmentScore < 9 || $attendanceScore < 9);
            $letterGrade = $isFailed ? 'F' : $this->calculateGrade($totalObtained, 100);

            // Per-course rank
            $enrollments = StudentCourseEnrollment::where('course_offering_id', $courseId)->get();
            $rankings = $enrollments->map(function ($enrol) use ($courseId) {
                $student = User::find($enrol->student_user_id);
                $att = $student ? $student->getAttendanceScoreByCourse($courseId) : 0;
                $nonQuizPoints = \App\Models\ExamResult::where('student_user_id', $enrol->student_user_id)
                    ->where('assessment_type', '!=', 'quiz')
                    ->whereIn('assessment_id', function ($q) use ($courseId) {
                        $q->select('id')->from('assignments')->where('course_offering_id', $courseId)
                            ->union(DB::table('exams')->select('id')->where('course_offering_id', $courseId));
                    })->sum('score_obtained');
                $quizPoints = \App\Models\ExamResult::where('student_user_id', $enrol->student_user_id)
                    ->where('assessment_type', 'quiz')
                    ->whereIn('assessment_id', function ($q) use ($courseId) {
                        $q->select('id')->from('quizzes')->where('course_offering_id', $courseId);
                    })->sum('score_obtained');

                return ['id' => $enrol->student_user_id, 'total' => min((float) $att + (float) $nonQuizPoints + (float) $quizPoints, 100)];
            })->sortByDesc('total')->values();

            $rankIndex = $rankings->search(fn ($r) => $r['id'] == $user->id);

            return (object) [
                'course_id' => $courseId,
                'course_code' => $items->first()->course_code,
                'course_name_en' => $items->first()->course_name_en,
                'course_name_km' => $items->first()->course_name_km,
                'credits' => $items->first()->credits,
                'academic_year' => $items->first()->academic_year,
                'semester' => $items->first()->semester,
                'course_rank' => ($rankIndex !== false) ? $rankIndex + 1 : '-',
                'total_students' => $rankings->count(),
                'attendance_score' => $attendanceScore,
                'absent_count' => $absCount,
                'permission_count' => $perCount,
                'total_score' => $totalObtained,
                'grade' => $letterGrade,
                'grade_points' => $this->gradeToPoints($letterGrade),
                'is_failed' => $isFailed,
                'assessments' => $items,
            ];
        })->values();

        // --- Overall rank: compare with peers using SAME filtered data ---
        $overallRank = '-';
        $totalClassmates = 0;
        if ($courseGrades->isNotEmpty()) {
            // Find peers: students enrolled in at least one of the filtered course offerings
            $peerIds = StudentCourseEnrollment::whereIn('course_offering_id', $filteredOfferingIds)
                ->pluck('student_user_id')
                ->unique();

            // For each peer, calculate their total score using ONLY filtered offerings
            $peerTotals = $peerIds->map(function ($peerId) use ($filteredOfferingIds) {
                $peerStudent = User::find($peerId);
                if (! $peerStudent) {
                    return ['id' => $peerId, 'total' => 0];
                }

                $total = 0;
                foreach ($filteredOfferingIds as $offeringId) {
                    $nonQuizPoints = \App\Models\ExamResult::where('student_user_id', $peerId)
                        ->where('assessment_type', '!=', 'quiz')
                        ->whereIn('assessment_id', function ($q) use ($offeringId) {
                            $q->select('id')->from('assignments')->where('course_offering_id', $offeringId)
                                ->union(DB::table('exams')->select('id')->where('course_offering_id', $offeringId));
                        })->sum('score_obtained');
                    $quizPoints = \App\Models\ExamResult::where('student_user_id', $peerId)
                        ->where('assessment_type', 'quiz')
                        ->whereIn('assessment_id', function ($q) use ($offeringId) {
                            $q->select('id')->from('quizzes')->where('course_offering_id', $offeringId);
                        })->sum('score_obtained');

                    $att = $peerStudent->getAttendanceScoreByCourse($offeringId);
                    $total += min((float) $nonQuizPoints + (float) $quizPoints + (float) $att, 100);
                }

                return ['id' => $peerId, 'total' => $total];
            })->sortByDesc('total')->values();

            $totalClassmates = $peerTotals->count();
            $rankIndex = $peerTotals->search(fn ($r) => $r['id'] == $user->id);
            $overallRank = ($rankIndex !== false) ? $rankIndex + 1 : '-';
        }

        // --- Summary stats ---
        $averageScore = $courseGrades->avg('total_score') ?? 0;
        $totalFinalScore = $courseGrades->sum('total_score');

        // Credit-weighted GPA
        $totalCredits = $courseGrades->sum('credits');
        $weightedPoints = $courseGrades->sum(fn ($g) => $g->grade_points * $g->credits);
        $gpa = $totalCredits > 0 ? round($weightedPoints / $totalCredits, 2) : 0;
        $overallGrade = $this->pointsToGrade($gpa);

        // Paginate
        $grades = new \Illuminate\Pagination\LengthAwarePaginator(
            $courseGrades->slice((($request->page ?? 1) - 1) * 10, 10)->values(),
            $courseGrades->count(), 10, $request->page ?? 1,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('student.my-grades', compact(
            'user', 'grades', 'averageScore', 'totalFinalScore',
            'overallRank', 'overallGrade', 'gpa', 'totalCredits',
            'totalClassmates', 'academicYears', 'semesters',
            'currentYear', 'currentSemester', 'courseGrades'
        ));
    }

    /**
     * Convert letter grade to grade points (4.0 scale).
     */
    private function gradeToPoints(string $grade): float
    {
        return match ($grade) {
            'A' => 4.0,
            'B' => 3.0,
            'C' => 2.0,
            'D' => 1.0,
            'E' => 0.5,
            default => 0.0,
        };
    }

    /**
     * Convert GPA to letter grade.
     */
    private function pointsToGrade(float $gpa): string
    {
        if ($gpa >= 3.5) {
            return 'A';
        }
        if ($gpa >= 2.5) {
            return 'B';
        }
        if ($gpa >= 1.5) {
            return 'C';
        }
        if ($gpa >= 1.0) {
            return 'D';
        }
        if ($gpa >= 0.5) {
            return 'E';
        }

        return 'F';
    }

    private function calculateGrade($score, $maxScore)
    {
        if ($maxScore <= 0) {
            return 'F';
        }
        $percentage = ($score / $maxScore) * 100;

        if ($percentage >= 90) {
            return 'A';
        }
        if ($percentage >= 80) {
            return 'B';
        }
        if ($percentage >= 70) {
            return 'C';
        }
        if ($percentage >= 60) {
            return 'D';
        }
        if ($percentage >= 50) {
            return 'E';
        }

        return 'F';
    }

    public function mySchedule()
    {
        $user = Auth::user();

        $studentProgramEnrollment = StudentProgramEnrollment::where('student_user_id', $user->id)
            ->where('status', 'active')
            ->with('program')
            ->first();
        $studentProgram = $studentProgramEnrollment ? $studentProgramEnrollment->program : null;
        $schedules = Schedule::whereHas('courseOffering.studentCourseEnrollments', function ($query) use ($user) {
            $query->where('student_user_id', $user->id);
        })
            ->with(['courseOffering.course', 'courseOffering.lecturer.userProfile', 'room'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('student.my-schedule', compact('user', 'schedules', 'studentProgram'));
    }

    /**
     * Display the list of enrolled courses for a specific student.
     *
     * @param  string  $studentId
     * @return \Illuminate\View\View
     */
    public function enrolledCourses($studentId)
    {
        $student = User::with('studentCourseEnrollments.courseOffering.course')
            ->where('id', $studentId)
            ->whereHas('studentCourseEnrollments', function ($query) {
                $query->where('status', 'enrolled');
            })
            ->firstOrFail();

        if (Auth::id() !== $student->id && ! (Auth::user() && Auth::user()->isAdmin())) {
            abort(403, 'Unauthorized action.');
        }

        $enrollments = $student->studentCourseEnrollments;

        return view('student.enrolled_courses', compact('student', 'enrollments'));
    }

    /**
     * Display the student's assignments.
     * Assumes an 'assignments' table and 'assignment_submissions' table.
     */
    public function myAssignments()
    {
        $user = Auth::user();
        $assignments = Assignment::whereHas('courseOffering.studentCourseEnrollments', function ($query) use ($user) {
            $query->where('student_user_id', $user->id);
        })
            ->with(['courseOffering.course', 'submissions' => function ($query) use ($user) {
                $query->where('student_user_id', $user->id);
            }])
            ->orderBy('due_date', 'asc')
            ->paginate(10);

        $assignments->each(function ($assignment) {
            $submission = $assignment->submissions->first();
            $assignment->isSubmitted = (bool) $submission;
            $assignment->grade = $submission ? $submission->grade_received : null;
        });

        return view('student.my-assignments', compact('user', 'assignments'));
    }

    /**
     * Display the student's exams.
     * Assumes an 'exams' table and 'exam_results' table.
     */
    public function myExams()
    {
        $user = Auth::user();
        $exams = Exam::whereHas('courseOffering.studentCourseEnrollments', function ($query) use ($user) {
            $query->where('student_user_id', $user->id);
        })
            ->with(['courseOffering.course', 'examResults' => function ($query) use ($user) {
                $query->where('student_user_id', $user->id);
            }])
            ->orderBy('exam_date', 'asc')
            ->paginate(10);

        $exams->each(function ($exam) {
            $result = $exam->examResults->first();
            $exam->grade = $result ? $result->score_obtained : null;
        });

        return view('student.my-exams', compact('user', 'exams'));
    }

    public function myQuizzes()
    {
        $user = Auth::user();
        $quizzes = Quiz::whereHas('courseOffering.studentCourseEnrollments', function ($query) use ($user) {
            $query->where('student_user_id', $user->id);
        })
            ->with(['courseOffering.course', 'quizQuestions.quizOptions', 'quizQuestions.studentQuizResponses' => function ($query) use ($user) {
                $query->where('student_user_id', $user->id);
            }])
            ->orderBy('end_date', 'asc')
            ->paginate(10);

        $quizzes->each(function ($quiz) use ($user) {
            $correctAnswers = 0;
            $totalQuestions = $quiz->quizQuestions->count();
            $totalPossibleScore = $quiz->total_points ?? ($totalQuestions > 0 ? $totalQuestions * 10 : 0);

            foreach ($quiz->quizQuestions as $question) {
                $studentResponse = $question->studentQuizResponses->first(function ($response) use ($user) {
                    return $response->student_user_id === $user->id;
                });
                if ($studentResponse && $studentResponse->is_correct) {
                    $correctAnswers++;
                }
            }
            $quiz->studentScore = $correctAnswers;
            $quiz->totalQuestions = $totalQuestions;
            $quiz->totalPossibleScore = $totalPossibleScore;
            $quiz->grade = ($totalQuestions > 0 && $totalPossibleScore > 0) ? round(($correctAnswers / $totalQuestions) * $totalPossibleScore, 2) : 0;
        });

        return view('student.my-quizzes', compact('user', 'quizzes'));
    }

    /**
     * Display the available courses for student enrollment.
     */
    public function availablePrograms()
    {
        $user = Auth::user();

        $enrolledProgramIds = StudentProgramEnrollment::where('student_user_id', $user->id)
            ->where('status', 'active')
            ->pluck('program_id');

        $availablePrograms = Program::whereNotIn('id', $enrolledProgramIds)
            ->with('faculty', 'department')
            ->paginate(10);

        return view('student.available-programs', compact('user', 'availablePrograms'));
    }

    public function availableCourses()
    {
        $user = Auth::user();

        $enrolledCourseOfferingIds = StudentCourseEnrollment::where('student_user_id', $user->id)
            ->pluck('course_offering_id');

        $availableCourses = CourseOffering::with(['course', 'lecturer', 'targetPrograms'])
            ->withCount('studentCourseEnrollments')
            ->where('is_open_for_self_enrollment', true)
            ->where('end_date', '>=', now())
            ->whereHas('targetPrograms', function ($query) use ($user) {
                $query->where('course_offering_program.program_id', $user->program_id)
                      ->where('course_offering_program.generation', $user->generation);
            })
            ->whereNotIn('id', $enrolledCourseOfferingIds)
            ->get();

        return view('student.available-courses', compact('user', 'availableCourses'));
    }

    public function enrollSelf(Request $request)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
        ]);

        $user = Auth::user();
        $courseOffering = CourseOffering::with('targetPrograms')->findOrFail($request->input('course_offering_id'));

        $existingEnrollment = StudentCourseEnrollment::where('student_user_id', $user->id)
            ->where('course_offering_id', $courseOffering->id)
            ->first();

        if ($existingEnrollment) {
            Session::flash('info', 'អ្នកបានចុះឈ្មោះក្នុងវគ្គសិក្សានេះរួចហើយ។');
            return redirect()->back();
        }

        if (!$courseOffering->is_open_for_self_enrollment) {
            Session::flash('error', 'វគ្គសិក្សានេះមិនទាន់បើកសម្រាប់ការចុះឈ្មោះដោយខ្លួនឯងទេ។');
            return redirect()->back();
        }

        if ($courseOffering->end_date && $courseOffering->end_date->isPast()) {
            Session::flash('error', 'វគ្គសិក្សានេះបានបញ្ចប់ហើយ។');
            return redirect()->back();
        }

        $enrolledCount = $courseOffering->studentCourseEnrollments()->count();
        if ($courseOffering->capacity && $enrolledCount >= $courseOffering->capacity) {
            Session::flash('error', 'វគ្គសិក្សានេះពេញហើយ។');
            return redirect()->back();
        }

        $matchesProgram = $courseOffering->targetPrograms->contains(function ($prog) use ($user) {
            return $prog->id == $user->program_id;
        });
        if (!$matchesProgram) {
            Session::flash('error', 'អ្នកមិនមានសិទ្ធិចុះឈ្មោះក្នុងវគ្គសិក្សានេះទេ។');
            return redirect()->back();
        }

        try {
            StudentCourseEnrollment::create([
                'student_user_id' => $user->id,
                'student_id' => $user->id,
                'course_offering_id' => $courseOffering->id,
                'enrollment_date' => now(),
                'status' => 'enrolled',
            ]);

            Session::flash('success', 'ការចុះឈ្មោះដោយជោគជ័យ!');
        } catch (\Exception $e) {
            Session::flash('error', 'មានបញ្ហាក្នុងការចុះឈ្មោះ៖ '.$e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Handles the student's program enrollment request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function enrollProgram(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
        ]);

        $user = Auth::user();
        $programId = $request->input('program_id');

        $existingProgramEnrollment = StudentProgramEnrollment::where('student_user_id', $user->id)
            ->where('program_id', $programId)
            ->first();

        if ($existingProgramEnrollment) {
            Session::flash('info', 'អ្នកបានចុះឈ្មោះក្នុងកម្មវិធីសិក្សានេះរួចហើយ។');

            return redirect()->back();
        }

        DB::transaction(function () use ($user, $programId) {
            StudentProgramEnrollment::create([
                'student_user_id' => $user->id,
                'program_id' => $programId,
                'enrollment_date' => now(),
                'status' => 'active',
            ]);

            $programCourseOfferings = CourseOffering::whereHas('targetPrograms', function ($query) use ($programId) {
                $query->where('course_offering_program.program_id', $programId)
                      ->where('course_offering_program.generation', $user->generation);
            })
                ->where('end_date', '>=', now())
                ->get();

            foreach ($programCourseOfferings as $courseOffering) {
                StudentCourseEnrollment::firstOrCreate([
                    'student_user_id' => $user->id,
                    'course_offering_id' => $courseOffering->id,
                ], [
                    'student_id' => $user->id,
                    'enrollment_date' => now(),
                    'status' => 'enrolled',
                ]);
            }
        });

        Session::flash('success', 'ការចុះឈ្មោះកម្មវិធីសិក្សា និងមុខវិជ្ជាបានជោគជ័យ!');

        return redirect()->route('student.available_programs');
    }

    public function myEnrolledCourses()
    {
        $user = Auth::user();

        $studentProgramEnrollment = \App\Models\StudentProgramEnrollment::where('student_user_id', $user->id)
            ->where('status', 'active')
            ->with('program')
            ->first();

        $studentProgram = $studentProgramEnrollment ? $studentProgramEnrollment->program : null;

        $enrollments = \App\Models\StudentCourseEnrollment::where('student_user_id', $user->id)
            ->with([
                'courseOffering.course',
                'courseOffering.lecturer.userProfile',
                'courseOffering.schedules.room',
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.my-enrolled-courses', compact('user', 'enrollments', 'studentProgram'));
    }
}
