<?php

namespace App\Http\Controllers\professor;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\GradingCategory;
use App\Models\Quiz;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use App\Services\GradingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfessorGradeController extends Controller
{
    /**
     * Verify that the assessment belongs to the authenticated professor's course offering.
     * Aborts 403 if unauthorized.
     */
    private function authorizeAssessment($assessment): void
    {
        $userId = Auth::id();
        $courseOffering = $assessment->courseOffering;

        if (! $courseOffering || $courseOffering->lecturer_user_id !== $userId) {
            abort(403, 'អ្នកមិនមានសិទ្ធិចូលប្រើប្រាស់ទិន្នន័យនេះទេ។');
        }
    }

    /**
     * Verify that the course offering belongs to the authenticated professor.
     * Aborts 403 if unauthorized.
     */
    private function authorizeCourseOffering(CourseOffering $courseOffering): void
    {
        if ($courseOffering->lecturer_user_id !== Auth::id()) {
            abort(403, 'អ្នកមិនមានសិទ្ធិចូលប្រើប្រាស់មុខវិជ្ជានេះទេ។');
        }
    }

    public function manageGrades($offering_id)
    {
        $courseOffering = CourseOffering::with([
            'course',
            'studentCourseEnrollments.student.studentProfile',
        ])->findOrFail($offering_id);

        $this->authorizeCourseOffering($courseOffering);

        $assignments = \App\Models\Assignment::where('course_offering_id', $offering_id)->get();
        $exams = \App\Models\Exam::where('course_offering_id', $offering_id)->get();
        $quizzes = \App\Models\Quiz::where('course_offering_id', $offering_id)->get();

        $assessments = collect($assignments)->concat($exams)->concat($quizzes)->sortBy('created_at');

        $allResults = \App\Models\ExamResult::whereIn('student_user_id', $courseOffering->studentCourseEnrollments->pluck('student_user_id'))
            ->get();

        $gradebook = [];
        $students = $courseOffering->studentCourseEnrollments->map(function ($enrollment) use ($assessments, $allResults, &$gradebook, $offering_id) {
            $student = $enrollment->student;

            $attendanceScore = (float) ($student->getAttendanceScoreByCourse($offering_id) ?? 0);
            $baseScore = $attendanceScore;
            $quizBonus = 0;

            foreach ($assessments as $assessment) {
                $type = ($assessment instanceof \App\Models\Assignment) ? 'assignment' :
                       (($assessment instanceof \App\Models\Quiz) ? 'quiz' : 'exam');

                $scoreRecord = $allResults->where('assessment_id', $assessment->id)
                    ->where('student_user_id', $student->id)
                    ->where('assessment_type', $type)
                    ->first();

                $score = $scoreRecord ? (float) $scoreRecord->score_obtained : 0;
                $gradebook[$student->id][$type.'_'.$assessment->id] = $score;

                if ($type === 'quiz') {
                    $quizBonus += $score;
                } else {
                    $baseScore += $score;
                }
            }

            $totalScore = min($baseScore + $quizBonus, 100);
            $student->temp_total = (float) $totalScore;

            return $student;
        });

        $students = $students->sortByDesc('temp_total')->values();

        foreach ($students as $index => $student) {
            $student->rank = $index + 1;
            $student->letterGrade = GradingService::getLetterGrade($student->temp_total);
        }

        return view('professor.grades.index', compact('courseOffering', 'students', 'assessments', 'gradebook'));
    }

    public function createAssessmentForm($offering_id)
    {
        $courseOffering = CourseOffering::with('course')->findOrFail($offering_id);
        $gradingCategories = GradingCategory::where('course_id', $courseOffering->course->id)->get();

        return view('professor.assessments.create', compact('courseOffering', 'gradingCategories'));
    }

    public function storeAssessment(Request $request, $offering_id)
    {
        $request->validate([
            'assessment_type' => 'required|in:assignment,exam,quiz',
            'title_en' => 'required|string|max:255',
            'title_km' => 'required|string|max:255',
            'max_score' => 'required|numeric|min:1',
            'assessment_date' => 'required|date',
            'duration_minutes' => 'nullable|integer|min:1',
        ]);

        $courseOffering = CourseOffering::findOrFail($offering_id);
        $type = $request->input('assessment_type');
        $titleEn = $request->input('title_en');
        $titleKm = $request->input('title_km');

        // Auto-assign grading category by matching title_en to category name_en
        $gradingCategory = GradingCategory::where('course_id', $courseOffering->course_id)
            ->where('name_en', $titleEn)
            ->first();
        $gradingCategoryId = $gradingCategory?->id;

        $existingAssessment = null;
        if ($type === 'exam') {
            $existingAssessment = Exam::where('course_offering_id', $offering_id)
                ->where('title_en', $titleEn)->first();
        } elseif ($type === 'quiz') {
            $existingAssessment = \App\Models\Quiz::where('course_offering_id', $offering_id)
                ->where('title_en', $titleEn)->first();
        } elseif ($type === 'assignment') {
            $existingAssessment = Assignment::where('course_offering_id', $offering_id)
                ->where('title_en', $titleEn)->first();
        }

        if ($existingAssessment) {
            return back()->withInput()->with('error', 'វិញ្ញាសានេះមានរួចហើយ! អ្នកមិនអាចបង្កើតជាន់គ្នាបានទេ។');
        }

        if ($type === 'quiz') {
            \App\Models\Quiz::create([
                'course_offering_id' => $courseOffering->id,
                'title_km' => $titleKm,
                'title_en' => $titleEn,
                'max_score' => $request->input('max_score'),
                'quiz_date' => $request->input('assessment_date'),
                'grading_category_id' => $gradingCategoryId,
            ]);
        } elseif ($type === 'assignment') {
            Assignment::create([
                'course_offering_id' => $courseOffering->id,
                'title_km' => $titleKm,
                'title_en' => $titleEn,
                'max_score' => $request->max_score,
                'due_date' => $request->assessment_date,
                'grading_category_id' => $gradingCategoryId,
            ]);
        } else {
            Exam::create([
                'course_offering_id' => $courseOffering->id,
                'title_km' => $titleKm,
                'title_en' => $titleEn,
                'max_score' => $request->max_score,
                'exam_date' => $request->assessment_date,
                'duration_minutes' => $request->input('duration_minutes'),
            ]);
        }

        return redirect()->route('professor.manage-grades', ['offering_id' => $offering_id])
            ->with('success', 'ការវាយតម្លៃត្រូវបានបង្កើតដោយជោគជ័យ!');
    }

    public function destroyAssessment(Request $request, $id)
    {
        $type = $request->input('assessment_type');
        $assessment = null;

        if ($type === 'quiz') {
            $assessment = \App\Models\Quiz::find($id);
        } elseif ($type === 'assignment') {
            $assessment = \App\Models\Assignment::find($id);
        } elseif ($type === 'exam') {
            $assessment = \App\Models\Exam::find($id);
        }

        if (! $assessment) {
            return back()->with('error', 'រកមិនឃើញទិន្នន័យដែលត្រូវលុប!');
        }

        $this->authorizeAssessment($assessment);

        if ($type === 'quiz') {
            \App\Models\ExamResult::where('assessment_id', $id)->delete();
        } elseif ($type === 'assignment') {
            \App\Models\Submission::where('assignment_id', $id)->delete();
        } elseif ($type === 'exam') {
            \App\Models\ExamResult::where('assessment_id', $id)->delete();
        }

        $assessment->delete();

        return back()->with('success', 'លុបការវាយតម្លៃ និងពិន្ទុដែលពាក់ព័ន្ធបានជោគជ័យ!');
    }

    public function showGradeEntryForm(Request $request, $assessment_id)
    {
        $type = $request->query('type');
        $search = $request->query('search');
        $assessment = null;

        if ($type === 'assignment') {
            $assessment = \App\Models\Assignment::with(['courseOffering.studentCourseEnrollments.student.studentProfile', 'examResults'])
                ->findOrFail($assessment_id);
        } elseif ($type === 'exam') {
            $assessment = \App\Models\Exam::with(['courseOffering.studentCourseEnrollments.student.studentProfile', 'examResults'])
                ->findOrFail($assessment_id);
        } elseif ($type === 'quiz') {
            $assessment = \App\Models\Quiz::with(['courseOffering.studentCourseEnrollments.student.studentProfile', 'examResults'])
                ->findOrFail($assessment_id);
        } else {
            abort(404, 'ប្រភេទការវាយតម្លៃមិនត្រឹមត្រូវ');
        }

        $this->authorizeAssessment($assessment);

        $students = $assessment->courseOffering->studentCourseEnrollments->map(function ($enrollment) {
            return $enrollment->student;
        })->filter();

        if (! empty($search)) {
            $students = $students->filter(function ($student) use ($search) {
                $searchLower = mb_strtolower($search, 'UTF-8');
                $nameKm = mb_strtolower($student->studentProfile?->full_name_km ?? '', 'UTF-8');
                $nameEn = mb_strtolower($student->studentProfile?->full_name_en ?? '', 'UTF-8');
                $userName = mb_strtolower($student->name ?? '', 'UTF-8');
                $studentId = mb_strtolower($student->student_id_code ?? '', 'UTF-8');

                return str_contains($nameKm, $searchLower) ||
                       str_contains($nameEn, $searchLower) ||
                       str_contains($userName, $searchLower) ||
                       str_contains($studentId, $searchLower);
            });
        }

        $students = $students->sortBy('name');
        $scores = [];

        foreach ($assessment->examResults as $result) {
            if ($result->assessment_type === $type) {
                $scores[$result->student_user_id] = [
                    'score' => $result->score_obtained,
                    'notes' => $result->notes,
                ];
            }
        }

        return view('professor.grades.edit', compact('assessment', 'students', 'scores', 'type', 'search'));
    }

    public function storeGradesForAssessment(Request $request, $assessment_id)
    {
        $type = $request->input('assessment_type');

        $modelClass = match ($type) {
            'assignment' => Assignment::class,
            'exam' => Exam::class,
            'quiz' => Quiz::class,
        };

        $assessment = $modelClass::findOrFail($assessment_id);
        $this->authorizeAssessment($assessment);

        $request->validate([
            'grades' => 'required|array',
            'assessment_type' => 'required|in:assignment,exam,quiz',
            'grades.*.score' => 'nullable|numeric|min:0|max:'.$assessment->max_score,
            'grades.*.notes' => 'nullable|string|max:500',
        ]);

        $offering_id = $assessment->course_offering_id;

        DB::beginTransaction();
        try {
            foreach ($request->input('grades') as $student_id => $gradeData) {
                if (! isset($gradeData['score']) || $gradeData['score'] === '') {
                    continue;
                }

                ExamResult::updateOrCreate(
                    [
                        'assessment_id' => $assessment_id,
                        'student_user_id' => $student_id,
                        'assessment_type' => $type,
                    ],
                    [
                        'score_obtained' => $gradeData['score'],
                        'notes' => $gradeData['notes'] ?? null,
                        'recorded_at' => now(),
                    ]
                );
            }

            DB::commit();

            return redirect()->route('professor.manage-grades', ['offering_id' => $offering_id])
                ->with('success', 'រក្សាទុកពិន្ទុបានជោគជ័យ!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'មានបញ្ហាបច្ចេកទេស។ សូមព្យាយាមម្តងទៀត។');
        }
    }

    public function storeGrades(Request $request, $assessment_id)
    {
        $request->validate([
            'assessment_type' => 'required|in:assignment,exam,quiz',
            'grades' => 'required|array',
        ]);

        $type = $request->input('assessment_type');
        $grades = $request->input('grades');

        $modelClass = match ($type) {
            'assignment' => Assignment::class,
            'exam' => Exam::class,
            'quiz' => Quiz::class,
        };

        $assessment = $modelClass::findOrFail($assessment_id);
        $this->authorizeAssessment($assessment);

        \DB::beginTransaction();
        try {
            foreach ($grades as $studentId => $data) {
                if ($data['score'] === null || $data['score'] === '') {
                    continue;
                }

                if ($type === 'assignment') {
                    \App\Models\Submission::updateOrCreate(
                        ['assignment_id' => $assessment_id, 'student_user_id' => $studentId],
                        [
                            'grade_received' => $data['score'],
                            'feedback' => $data['notes'],
                        ]
                    );
                } else {
                    \App\Models\ExamResult::updateOrCreate(
                        [
                            'assessment_id' => $assessment_id,
                            'student_user_id' => $studentId,
                            'assessment_type' => $type,
                        ],
                        [
                            'score_obtained' => $data['score'],
                            'notes' => $data['notes'],
                            'recorded_at' => now(),
                        ]
                    );
                }
            }
            \DB::commit();

            return back()->with('success', 'រក្សាទុកពិន្ទុបានជោគជ័យ');

        } catch (\Exception $e) {
            \DB::rollBack();

            return back()->with('error', 'មានបញ្ហាបច្ចេកទេស។ សូមព្យាយាមម្តងទៀត។');
        }
    }

    public function exportCSV(Request $request, $id)
    {
        $rawType = $request->query('type');
        $type = ucfirst(strtolower($rawType));

        if ($type === 'Assignment') {
            $assessment = \App\Models\Assignment::with('courseOffering.targetPrograms')->findOrFail($id);
        } elseif ($type === 'Quiz') {
            $assessment = \App\Models\Quiz::with('courseOffering.targetPrograms')->findOrFail($id);
        } else {
            $assessment = \App\Models\Exam::with('courseOffering.targetPrograms')->findOrFail($id);
            $type = 'Exam';
        }

        $courseOffering = $assessment->courseOffering;

        $students = \App\Models\User::whereHas('studentCourseEnrollments', function ($q) use ($courseOffering) {
            $q->where('course_offering_id', $courseOffering->id)
                ->where('status', 'enrolled');
        })
            ->with('userProfile')
            ->get();

        $results = \App\Models\ExamResult::where('assessment_id', $id)
            ->where('assessment_type', strtolower($type))
            ->get()
            ->keyBy('student_user_id');

        $courseName = str_replace([' ', '/', '\\'], '_', $courseOffering->course->title_en ?? 'Subject');

        $fileName = "Grades_{$courseName}_{$type}_ID{$id}.csv";

        $headers = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=$fileName",
        ];

        $callback = function () use ($students, $results) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['ID', 'Student Code', 'Name', 'Score', 'Notes']);

            foreach ($students as $student) {
                $scoreRecord = $results->get($student->id);
                $score = $scoreRecord ? $scoreRecord->score_obtained : '';
                $notes = $scoreRecord ? $scoreRecord->notes : '';

                fputcsv($file, [
                    $student->id,
                    $student->student_id_code,
                    $student->userProfile?->full_name_km ?? $student->name,
                    $score,
                    $notes,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importCSV(Request $request, $id)
    {
        $request->validate([
            'excel_file' => 'required|mimes:csv,txt',
            'type' => 'required',
            'offering_id' => 'required',
        ]);

        $type = $request->input('type');
        $offering_id = $request->input('offering_id');

        if (($handle = fopen($request->file('excel_file')->getRealPath(), 'r')) !== false) {
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            fgetcsv($handle);

            DB::beginTransaction();
            try {
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    if (empty($data[0])) {
                        continue;
                    }

                    $studentUserId = trim($data[0]);
                    $score = trim($data[3]);
                    $notes = trim($data[4] ?? '');

                    if ($score !== '') {
                        \App\Models\ExamResult::updateOrCreate(
                            [
                                'assessment_id' => $id,
                                'student_user_id' => $studentUserId,
                                'assessment_type' => $type,
                            ],
                            [
                                'score_obtained' => $score,
                                'notes' => $notes,
                                'recorded_at' => now(),
                            ]
                        );
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                return back()->with('error', 'មានបញ្ហាក្នុងការបញ្ចូលទិន្នន័យ។ សូមពិនិត្យមើលឯកសាររបស់អ្នក។');
            }
            fclose($handle);
        }

        return redirect()->route('professor.manage-grades', ['offering_id' => $offering_id])
            ->with('success', 'បញ្ចូលពិន្ទុ '.ucfirst($type).' ជោគជ័យ!');
    }

    public function editAttendance($student_id, $course_id)
    {
        $student = User::findOrFail($student_id);
        $courseOffering = CourseOffering::findOrFail($course_id);

        $this->authorizeCourseOffering($courseOffering);

        $autoScore = $student->getAttendanceScoreByCourse($course_id);

        $enrollment = StudentCourseEnrollment::where('student_user_id', $student_id)
            ->where('course_offering_id', $course_id)
            ->firstOrFail();

        return view('professor.grades.edit_attendance', compact('student', 'courseOffering', 'autoScore', 'enrollment'));
    }

    public function updateAttendanceScore(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'course_id' => 'required',
            'score' => 'nullable|numeric|min:0|max:15',
        ]);

        $courseOffering = CourseOffering::findOrFail($request->course_id);
        $this->authorizeCourseOffering($courseOffering);

        $enrollment = \App\Models\StudentCourseEnrollment::where('student_user_id', $request->student_id)
            ->where('course_offering_id', $request->course_id)
            ->firstOrFail();

        $enrollment->attendance_score_manual = $request->score;
        $enrollment->save();

        if ($request->score >= 15) {
            \App\Models\AttendanceRecord::where('student_user_id', $request->student_id)
                ->where('course_offering_id', $request->course_id)
                ->update(['status' => 'present']);
        }

        return redirect()->back()->with('success', 'បានធ្វើបច្ចុប្បន្នភាពពិន្ទុវត្តមានរួចរាល់');
    }

    public function assessmentEdit($id, $type)
    {
        if ($type === 'assignment') {
            $assessment = \App\Models\Assignment::findOrFail($id);
        } elseif ($type === 'quiz') {
            $assessment = \App\Models\Quiz::findOrFail($id);
        } elseif ($type === 'exam') {
            $assessment = \App\Models\Exam::findOrFail($id);
        } else {
            abort(404);
        }

        $this->authorizeAssessment($assessment);

        $courseOffering = \App\Models\CourseOffering::findOrFail(
            $assessment->course_offering_id
        );

        $gradingCategories = \App\Models\GradingCategory::where(
            'course_id', $courseOffering->course_id
        )->get();

        return view(
            'professor.assessments.edit',
            compact('assessment', 'type', 'courseOffering', 'gradingCategories')
        );
    }

    public function update(Request $request, $id, $type)
    {
        $request->validate([
            'title_km' => 'required|string|max:255',
            'max_score' => 'required|numeric|min:1',
            'assessment_date' => 'required|date',
            'grading_category_id' => 'required',
        ]);

        $modelClass = match ($type) {
            'assignment' => \App\Models\Assignment::class,
            'quiz' => \App\Models\Quiz::class,
            'exam' => \App\Models\Exam::class,
            default => null,
        };

        if (! $modelClass) {
            abort(404);
        }

        $model = $modelClass::findOrFail($id);
        $this->authorizeAssessment($model);

        $updateData = [
            'title_km' => $request->title_km,
            'max_score' => $request->max_score,
            'grading_category_id' => $request->grading_category_id,
        ];

        $dateField = match ($type) {
            'assignment' => 'due_date',
            'quiz' => 'quiz_date',
            'exam' => 'exam_date',
        };
        $updateData[$dateField] = $request->assessment_date;

        $model->update($updateData);

        return redirect()
            ->route('professor.manage-grades', [
                'offering_id' => $model->course_offering_id,
            ])
            ->with('success', 'កែសម្រួលបានជោគជ័យ!');
    }

    public function checkDuplicate(Request $request)
    {
        $request->validate([
            'offering_id' => 'required|exists:course_offerings,id',
            'type' => 'required|in:assignment,exam,quiz',
            'title_km' => 'required|string',
        ]);

        $offeringId = $request->offering_id;
        $titleKm = $request->title_km;
        $type = $request->type;

        $query = match ($type) {
            'exam' => Exam::where('course_offering_id', $offeringId),
            'quiz' => \App\Models\Quiz::where('course_offering_id', $offeringId),
            'assignment' => Assignment::where('course_offering_id', $offeringId),
        };

        $existing = $query->where('title_km', 'LIKE', '%'.$titleKm.'%')->first();

        if ($existing) {
            return response()->json([
                'duplicate' => true,
                'message' => 'វិញ្ញាសានេះអាចមានរួចហើយ! មានឈ្មោះស្រដៀងគ្នា: "'.$existing->title_km.'"',
            ]);
        }

        return response()->json(['duplicate' => false]);
    }

    public function allGrades()
    {
        $user = Auth::user();

        $query = DB::table('exam_results as er')
            ->join('users as u', 'er.student_user_id', '=', 'u.id')
            ->leftJoin('exams as e', function ($join) {
                $join->on('er.assessment_id', '=', 'e.id')
                     ->where('er.assessment_type', '=', 'exam');
            })
            ->leftJoin('assignments as a', function ($join) {
                $join->on('er.assessment_id', '=', 'a.id')
                     ->where('er.assessment_type', '=', 'assignment');
            })
            ->leftJoin('course_offerings as co_exam', 'e.course_offering_id', '=', 'co_exam.id')
            ->leftJoin('courses as c_exam', 'co_exam.course_id', '=', 'c_exam.id')
            ->leftJoin('course_offerings as co_assign', 'a.course_offering_id', '=', 'co_assign.id')
            ->leftJoin('courses as c_assign', 'co_assign.course_id', '=', 'c_assign.id')
            ->where(function ($q) use ($user) {
                $q->where('co_exam.lecturer_user_id', $user->id)
                  ->orWhere('co_assign.lecturer_user_id', $user->id);
            })
            ->select(
                'u.name as student_name',
                DB::raw("CASE WHEN er.assessment_type = 'exam' THEN c_exam.title_km ELSE c_assign.title_km END as course_title_km"),
                'er.assessment_type',
                'er.score_obtained as score',
                DB::raw("CASE WHEN er.assessment_type = 'exam' THEN e.max_score ELSE a.max_score END as max_score"),
                'er.recorded_at as date'
            )
            ->orderBy('er.recorded_at', 'desc');

        $paginator = $query->paginate(10);

        $grades = $paginator->getCollection()->map(function ($row) {
            return (object) [
                'student_name' => $row->student_name ?? 'N/A',
                'course_title_km' => $row->course_title_km ?? 'N/A',
                'assessment_type' => $row->assessment_type,
                'score' => $row->score,
                'max_score' => $row->max_score ?? 100,
                'date' => $row->date,
            ];
        });

        $grades = new \Illuminate\Pagination\LengthAwarePaginator(
            $grades,
            $paginator->total(),
            $paginator->perPage(),
            $paginator->currentPage(),
            ['path' => url('/professor/all-grades')]
        );

        return view('professor.all-grades', compact('grades'));
    }
}
