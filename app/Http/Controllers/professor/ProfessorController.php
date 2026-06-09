<?php

namespace App\Http\Controllers\professor;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Department;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Program;
use App\Models\Quiz;
use App\Models\Schedule;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ProfessorController extends Controller
{
    public function dashboard()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $todayName = now()->format('l');
        $todayDate = now()->toDateString();

        $todaySchedules = \App\Models\Schedule::whereHas('courseOffering', function ($query) use ($user) {
            $query->where('lecturer_user_id', $user->id);
        })
            ->where('day_of_week', $todayName)
            ->with(['courseOffering.course.programs', 'courseOffering.targetPrograms', 'room'])
            ->orderBy('start_time', 'asc')
            ->get();

        $todayOfferingIds = $todaySchedules->pluck('course_offering_id')->unique()->toArray();
        $completedOfferingIds = AttendanceRecord::whereIn('course_offering_id', $todayOfferingIds)
            ->where('date', $todayDate)
            ->pluck('course_offering_id')
            ->toArray();

        $todaySchedules->each(function ($schedule) use ($completedOfferingIds) {
            $schedule->is_completed_today = in_array($schedule->course_offering_id, $completedOfferingIds);
        });

        // 3. Count Total Unique Students (for all courses taught by this professor)
        $totalStudents = \App\Models\StudentCourseEnrollment::whereHas('courseOffering', function ($q) use ($user) {
            $q->where('lecturer_user_id', $user->id);
        })->distinct('student_user_id')->count('student_user_id');

        // 3b. Count today's attendance records for this professor's courses
        $todayAttendanceCount = AttendanceRecord::whereHas('courseOffering', function ($q) use ($user) {
            $q->where('lecturer_user_id', $user->id);
        })->where('date', $todayDate)->count();

        // 4. Upcoming Assignments
        $upcomingAssignments = \App\Models\Assignment::whereHas('courseOffering', function ($query) use ($user) {
            $query->where('lecturer_user_id', $user->id);
        })
            ->whereDate('due_date', '>=', $todayDate)
            ->orderBy('due_date')
            ->take(5)
            ->get();

        // 5. Upcoming Exams
        $upcomingExams = \App\Models\Exam::whereHas('courseOffering', function ($query) use ($user) {
            $query->where('lecturer_user_id', $user->id);
        })
            ->whereDate('exam_date', '>=', $todayDate)
            ->orderBy('exam_date')
            ->take(5)
            ->get();

        // 6. Upcoming Quizzes
        $upcomingQuizzes = \App\Models\Quiz::whereHas('courseOffering', function ($query) use ($user) {
            $query->where('lecturer_user_id', $user->id);
        })
            ->whereDate('quiz_date', '>=', $todayDate)
            ->orderBy('quiz_date', 'asc')
            ->take(5)
            ->get();

        // 7. Announcements
        $announcements = \App\Models\Announcement::where('target_role', 'all')
            ->orWhere('target_role', 'professor')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 8. Professor's course offerings
        $myCourseOfferings = \App\Models\CourseOffering::where('lecturer_user_id', $user->id)
            ->with('course')
            ->get();

        // 9. At-risk students (attendance < 75% or low grades)
        $atRiskStudents = collect();
        foreach ($myCourseOfferings as $offering) {
            $enrollments = \App\Models\StudentCourseEnrollment::where('course_offering_id', $offering->id)
                ->with('student')
                ->get();

            foreach ($enrollments as $enrollment) {
                $student = $enrollment->student;
                if (! $student) {
                    continue;
                }

                // Check attendance
                $totalClasses = \App\Models\Schedule::where('course_offering_id', $offering->id)->count();
                $attendedClasses = AttendanceRecord::where('course_offering_id', $offering->id)
                    ->where('student_user_id', $student->id)
                    ->where('status', 'present')
                    ->count();

                $attendanceRate = $totalClasses > 0 ? ($attendedClasses / $totalClasses) * 100 : 100;

                if ($attendanceRate < 75) {
                    $atRiskStudents->push([
                        'student' => $student,
                        'course' => $offering->course->title_km ?? $offering->course->title_en,
                        'reason' => 'វត្តមាន '.round($attendanceRate).'%',
                        'type' => 'attendance',
                    ]);
                }
            }
        }
        $atRiskStudents = $atRiskStudents->take(5);

        // 10. Ungraded submissions count
        $ungradedSubmissionsCount = \App\Models\Submission::whereHas('assignment.courseOffering', function ($q) use ($user) {
            $q->where('lecturer_user_id', $user->id);
        })->whereNull('grade_received')->count();

        // 11. Total assessments pending grades
        $pendingAssessments = \App\Models\Assignment::whereHas('courseOffering', function ($q) use ($user) {
            $q->where('lecturer_user_id', $user->id);
        })->whereDate('due_date', '<', $todayDate)
            ->whereDoesntHave('examResults')
            ->count();

        return view('professor.dashboard', compact(
            'user',
            'todaySchedules',
            'totalStudents',
            'todayAttendanceCount',
            'upcomingAssignments',
            'upcomingExams',
            'upcomingQuizzes',
            'announcements',
            'myCourseOfferings',
            'atRiskStudents',
            'ungradedSubmissionsCount',
            'pendingAssessments'
        ));
    }

    /**
     * API to get course offerings with associated students for modals.
     */
    public function getStudentsInCourseOffering($offering_id)
    {
        $user = Auth::user();

        // ១. បន្ថែម Relationship 'studentProgramEnrollments.program' ដើម្បីបង្ហាញព័ត៌មាន Program និង Generation
        $courseOffering = CourseOffering::where('id', $offering_id)
            ->where('lecturer_user_id', $user->id)
            ->with([
                'course',
                'targetPrograms',
                'studentCourseEnrollments.student.studentProfile',
                'studentCourseEnrollments.student.studentProgramEnrollments.program', //
            ])
            ->firstOrFail();

        // ២. រៀបចំបញ្ជីឈ្មោះនិស្សិត និងគណនាស្ថិតិ
        $stats = [
            'total' => $courseOffering->studentCourseEnrollments->count(),
            'male' => 0,
            'female' => 0,
            'leaders' => 0,
        ];

        $students = $courseOffering->studentCourseEnrollments->map(function ($enrollment) use (&$stats) {
            $student = $enrollment->student;

            // ឆែកភេទ (Gender) ពី Profile
            $gender = strtoupper($student->studentProfile->gender ?? '');
            if (in_array($gender, ['M', 'MALE', 'ប្រុស'])) {
                $stats['male']++;
            } elseif (in_array($gender, ['F', 'FEMALE', 'ស្រី'])) {
                $stats['female']++;
            }

            // ឆែកប្រធានថ្នាក់
            if ($enrollment->is_class_leader) {
                $stats['leaders']++;
            }

            return $student;
        });

        // ៣. រៀបចំ Pagination
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage('studentsPage');
        $currentItems = $students->slice(($currentPage - 1) * $perPage, $perPage)->values()->all();

        $paginatedStudents = new LengthAwarePaginator($currentItems, $students->count(), $perPage, $currentPage, [
            'path' => request()->url(),
            'pageName' => 'studentsPage',
        ]);

        return view('professor.students.index', compact('courseOffering', 'paginatedStudents', 'stats'));
    }

    /**
     * Display an 'all-in-one' view for professors,
     * combining various data points from all their courses.
     */
    public function allDataView(Request $request)
    {
        $user = Auth::user();

        $allCourseOfferings = CourseOffering::where('lecturer_user_id', $user->id)
            ->with(['course', 'targetPrograms'])
            ->paginate(10);

        $allAssignments = Assignment::whereHas('courseOffering', function ($q) use ($user) {
            $q->where('lecturer_user_id', $user->id);
        })->with('courseOffering.course')->paginate(10);

        $allExams = Exam::whereHas('courseOffering', function ($q) use ($user) {
            $q->where('lecturer_user_id', $user->id);
        })->with('courseOffering.course')->paginate(10);

        $allQuizzes = Quiz::whereHas('courseOffering', function ($q) use ($user) {
            $q->where('lecturer_user_id', $user->id);
        })->with('courseOffering.course')->paginate(10);

        $allAttendance = AttendanceRecord::whereHas('courseOffering', function ($q) use ($user) {
            $q->where('lecturer_user_id', $user->id);
        })->with('student', 'courseOffering.course')->orderBy('date', 'desc')->paginate(10);

        $allGrades = \App\Models\StudentCourseEnrollment::whereHas('courseOffering', function ($q) use ($user) {
            $q->where('lecturer_user_id', $user->id);
        })->with(['student', 'courseOffering.course'])->paginate(10);

        $allDepartments = Department::all();
        $allPrograms = Program::all();
        $allCourses = Course::all();

        return view('professor.all-data-view', compact(
            'allCourseOfferings',
            'allAssignments',
            'allExams',
            'allQuizzes',
            'allAttendance',
            'allGrades',
            'allDepartments',
            'allPrograms',
            'allCourses'
        ));
    }

    public function showStudentProfile(CourseOffering $courseOffering, User $student)
    {
        if (! $student->isStudent()) {
            abort(404);
        }

        $student->loadMissing('studentProfile', 'program');

        return view('professor.students.show_profile', compact('courseOffering', 'student'));
    }

    public function showStudentsInCourse(CourseOffering $courseOffering)
    {
        if ($courseOffering->lecturer_user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $courseOffering->load(['targetPrograms', 'course.programs']);

        $studentIds = $courseOffering->studentCourseEnrollments()->pluck('student_user_id');
        $students = User::whereIn('id', $studentIds)
            ->with(['studentProfile', 'studentProgramEnrollments.program'])
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('professor.students.index', compact('courseOffering', 'students'));
    }

    public function mySchedule()
    {
        $user = Auth::user();
        if ($user->role !== 'professor') {
            return redirect()->route('dashboard')->with('error', 'អ្នកមិនមានសិទ្ធិចូលប្រើមុខងារនេះទេ។');
        }

        $courseOfferings = CourseOffering::where('lecturer_user_id', $user->id)
            ->with(['course', 'schedules'])
            ->get();

        return view('professor.my-schedule', compact('user', 'courseOfferings'));
    }

    public function createAssessment($offering_id)
    {
        $courseOffering = CourseOffering::findOrFail($offering_id);

        return view('professor.assignments.create', compact('courseOffering'));
    }

    public function toggleClassLeader($offeringId, $studentUserId)
    {
        // ១. ស្វែងរក record ក្នុង table student_course_enrollments
        $enrollment = DB::table('student_course_enrollments')
            ->where('course_offering_id', $offeringId)
            ->where('student_user_id', $studentUserId)
            ->first();

        if (! $enrollment) {
            return back()->with('error', 'រកមិនឃើញទិន្នន័យនិស្សិតក្នុង Database ទេ!');
        }

        // ២. ប្តូរតម្លៃ (Toggle) បើ 0 ទៅ 1, បើ 1 ទៅ 0
        $newStatus = $enrollment->is_class_leader ? 0 : 1;

        // ៣. Update ចូល Database ផ្ទាល់
        DB::table('student_course_enrollments')
            ->where('course_offering_id', $offeringId)
            ->where('student_user_id', $studentUserId)
            ->update(['is_class_leader' => $newStatus]);

        return back()->with('success', 'ស្ថានភាពប្រធានថ្នាក់ត្រូវបានផ្លាស់ប្តូរ!');
    }

    public function markAsRead(Request $request, Announcement $announcement)
    {
        if (Auth::user()->role === 'professor') {
            $userId = Auth::id();
            $exists = \App\Models\AnnouncementRead::where('announcement_id', $announcement->id)
                ->where('user_id', $userId)
                ->exists();

            if (! $exists) {
                \App\Models\AnnouncementRead::create([
                    'announcement_id' => $announcement->id,
                    'user_id' => $userId,
                    'read_at' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'សេចក្តីប្រកាសត្រូវបានសម្គាល់ថាបានអានហើយ។',
            ]);
        }

        return response()->json(['success' => false, 'message' => 'គ្មានការអនុញ្ញាត។'], 403);
    }

    public function assignLeader($courseOfferingId, $studentId)
    {
        // ស្វែងរកមុខវិជ្ជា
        $courseOffering = CourseOffering::findOrFail($courseOfferingId);

        // ដកតំណែងប្រធានថ្នាក់ចាស់ចេញសិន (ប្រសិនបើចង់ឱ្យមានប្រធានថ្នាក់តែម្នាក់)
        // ប្រសិនបើអ្នកចង់ឱ្យមានប្រធានថ្នាក់ច្រើននាក់ អ្នកអាចយកផ្នែកនេះចេញ
        DB::table('student_course_enrollments')
            ->where('course_offering_id', $courseOfferingId)
            ->update(['is_class_leader' => false]);

        // ឆែកមើលស្ថានភាពបច្ចុប្បន្នរបស់និស្សិត
        $enrollment = DB::table('student_course_enrollments')
            ->where('course_offering_id', $courseOfferingId)
            ->where('student_id', $studentId)
            ->first();

        // ប្តូរស្ថានភាព (Toggle)
        $newStatus = ! ($enrollment->is_class_leader ?? false);

        DB::table('student_course_enrollments')
            ->where('course_offering_id', $courseOfferingId)
            ->where('student_id', $studentId)
            ->update(['is_class_leader' => $newStatus]);

        $message = $newStatus ? 'បានតែងតាំងប្រធានថ្នាក់ជោគជ័យ!' : 'បានដកតំណែងប្រធានថ្នាក់ជោគជ័យ!';

        return redirect()->back()->with('success', $message);
    }

    public function attendanceIndex($courseOfferingId)
    {
        $courseOffering = CourseOffering::with('students.studentProfile')->findOrFail($courseOfferingId);

        if ($courseOffering->lecturer_user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $students = $courseOffering->students; // យកបញ្ជីនិស្សិតក្នុងថ្នាក់នោះ
        $today = now()->format('Y-m-d');

        return view('professor.attendance.index', compact('courseOffering', 'students', 'today'));
    }

    public function attendanceStore(Request $request, $courseOfferingId)
    {
        $courseOffering = CourseOffering::findOrFail($courseOfferingId);

        if ($courseOffering->lecturer_user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'attendance_date' => 'required|date',
            'attendance' => 'required|array',
        ]);

        foreach ($request->attendance as $studentId => $status) {
            if (! in_array($status, ['present', 'absent', 'late', 'permission'])) {
                continue;
            }
            DB::table('attendances')->updateOrInsert(
                [
                    'course_offering_id' => $courseOfferingId,
                    'user_id' => $studentId,
                    'date' => $request->attendance_date,
                ],
                [
                    'student_user_id' => $studentId,
                    'status' => $status,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        return redirect()->back()->with('success', 'បានរក្សាទុកវត្តមានដោយជោគជ័យ!');
    }

    // AttendanceRecord
    // export

    public function attendanceReport($courseOfferingId)
    {
        $courseOffering = CourseOffering::findOrFail($courseOfferingId);

        $students = User::whereHas('enrolledCourses', function ($query) use ($courseOfferingId) {
            $query->where('course_offering_id', $courseOfferingId);
        })
            ->withCount([
                'attendanceRecords as present_count' => function ($query) use ($courseOfferingId) {
                    $query->where('course_offering_id', $courseOfferingId)
                        ->where('status', 'present');
                },
                'attendanceRecords as absent_count' => function ($query) use ($courseOfferingId) {
                    $query->where('course_offering_id', $courseOfferingId)
                        ->where('status', 'absent');
                },
                'attendanceRecords as permission_count' => function ($query) use ($courseOfferingId) {
                    $query->where('course_offering_id', $courseOfferingId)
                        ->where('status', 'permission');
                },
                'attendanceRecords as late_count' => function ($query) use ($courseOfferingId) {
                    $query->where('course_offering_id', $courseOfferingId)
                        ->where('status', 'late');
                },
            ])
            ->get();

        return view('professor.attendance.report', compact('courseOffering', 'students'));
    }

    public function allAttendance(Request $request)
    {
        $user = Auth::user();

        $professorCourseOfferings = CourseOffering::where('lecturer_user_id', $user->id)
            ->with('course')
            ->get();

        $attendances = AttendanceRecord::whereHas('courseOffering', function ($q) use ($user) {
            $q->where('lecturer_user_id', $user->id);
        })
            ->with('student.profile', 'courseOffering.course')
            ->orderBy('date', 'desc')
            ->paginate(15);

        $attendances->each(function ($record) {
            $record->status_km = match ($record->status) {
                'present' => 'មានវត្តមាន',
                'absent' => 'អវត្តមាន',
                'late' => 'មកយឺត',
                'permission' => 'មានច្បាប់',
                default => 'មិនស្គាល់',
            };
        });

        $students = \App\Models\User::where('role', 'student')
            ->with('profile')
            ->get();

        return view('professor.all-attendance', compact('professorCourseOfferings', 'attendances', 'students'));
    }

    public function manageAttendance($offering_id)
    {
        $courseOffering = CourseOffering::with(['course', 'studentCourseEnrollments.student.profile'])->findOrFail($offering_id);

        if ($courseOffering->lecturer_user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $attendanceRecords = AttendanceRecord::where('course_offering_id', $offering_id)
            ->with('student.profile')
            ->orderBy('date', 'desc')
            ->paginate(10);

        $attendanceRecords->each(function ($record) {
            $record->status_km = match ($record->status) {
                'present' => 'មានវត្តមាន',
                'absent' => 'អវត្តមាន',
                'late' => 'មកយឺត',
                'permission' => 'មានច្បាប់',
                default => 'មិនស្គាល់',
            };
        });

        return view('professor.manage-attendance', compact('courseOffering', 'attendanceRecords'));
    }

    // សម្រាប់បង្ហាញទំព័រ Edit

    public function showGradebook($offering_id)
    {
        // ១. ទាញយកព័ត៌មានមុខវិជ្ជា (Course Offering)
        $courseOffering = CourseOffering::with('course')->findOrFail($offering_id);

        // ២. ទាញបញ្ជីឈ្មោះសិស្ស ព្រមជាមួយ "វត្តមាន" ក្នុងមុខវិជ្ជានេះ
        $students = User::where('role', 'student')
            ->whereHas('courseOfferings', function ($q) use ($offering_id) {
                $q->where('course_offering_id', $offering_id);
            })
            ->with(['attendanceRecords' => function ($q) use ($offering_id) {
                $q->where('course_offering_id', $offering_id);
            }])
            ->get();

        // ៣. ទាញរាល់ការវាយតម្លៃទាំងអស់ (Assessments)
        $assignments = Assignment::where('course_offering_id', $offering_id)->get();
        $quizzes = Quiz::where('course_offering_id', $offering_id)->get();
        $exams = Exam::where('course_offering_id', $offering_id)->get();

        // បញ្ចូលគ្នាជា Collection តែមួយសម្រាប់បង្ហាញក្នុង Header តារាង
        $assessments = $assignments->concat($quizzes)->concat($exams);

        // ៤. រៀបចំទិន្នន័យពិន្ទុដាក់ក្នុង Array ដើម្បីងាយស្រួលទាញក្នុង Blade
        $gradebook = [];
        foreach ($students as $student) {
            foreach ($assignments as $a) {
                // ឧបមាថាអ្នកមាន Model AssignmentSubmission សម្រាប់រក្សាពិន្ទុ
                $student->attendance_score = $this->getAttendanceScore($student->id, $offering_id);
                $submission = $a->submissions()->where('user_id', $student->id)->first();
                $gradebook[$student->id]['assignment_'.$a->id] = $submission ? $submission->score : 0;
            }
            // ធ្វើដូចគ្នាសម្រាប់ Quiz និង Exam...
        }

        return view('professor.gradebook', compact('courseOffering', 'students', 'assessments', 'gradebook'));
    }

    // totalAttendanceWeight
    public function getAttendanceScore($studentId, $courseOfferingId)
    {
        $student = User::find($studentId);
        if (! $student) {
            return 0;
        }

        return $student->getAttendanceScoreByCourse($courseOfferingId);
    }

    public function exportStudentsDocx($offering_id)
    {
        $user = Auth::user();

        $courseOffering = CourseOffering::where('id', $offering_id)
            ->where('lecturer_user_id', $user->id)
            ->with([
                'course',
                'studentCourseEnrollments.student.studentProfile',
                'studentCourseEnrollments.student.studentProgramEnrollments.program',
            ])->firstOrFail();

        $students = $courseOffering->studentCourseEnrollments;

        // រៀបចំ HTML សម្រាប់ Word
        $html = view('professor.students.export_word', compact('courseOffering', 'students'))->render();

        $fileName = 'Student_List_'.time().'.doc';

        return response($html)
            ->header('Content-Type', 'application/msword')
            ->header('Content-Disposition', "attachment; filename=\"$fileName\"");
    }

    // ឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲ
    public function exportGradebookDocx($offering_id)
    {
        $courseOffering = \App\Models\CourseOffering::with([
            'course',
            'studentCourseEnrollments.student.studentProfile',
        ])->findOrFail($offering_id);

        // ១. ទាញយក Assignments, Exams, Quizzes
        $assignments = \App\Models\Assignment::where('course_offering_id', $offering_id)->get();
        $exams = \App\Models\Exam::where('course_offering_id', $offering_id)->get();
        $quizzes = \App\Models\Quiz::where('course_offering_id', $offering_id)->get();

        $assessments = collect($assignments)->concat($exams)->concat($quizzes)->sortBy('created_at');

        // ទាញយកពិន្ទុទាំងអស់មកទុកក្នុង Memory តែម្តង (ដើម្បីល្បឿនលឿន)
        $studentIds = $courseOffering->studentCourseEnrollments->pluck('student_user_id');
        $allResults = \App\Models\ExamResult::whereIn('student_user_id', $studentIds)
            ->whereIn('assessment_id', $assessments->pluck('id'))
            ->get();

        // ២. រៀបចំ Gradebook និងគណនាពិន្ទុ
        $gradebook = [];
        $students = $courseOffering->studentCourseEnrollments->map(function ($enrollment) use ($assessments, $allResults, &$gradebook, $offering_id) {
            $student = $enrollment->student;

            // ប្រើ Method ដែលអ្នកមានស្រាប់សម្រាប់ពិន្ទុវត្តមាន
            $attendanceScore = $student->getAttendanceScoreByCourse($offering_id);
            $totalScore = $attendanceScore;

            foreach ($assessments as $assessment) {
                // កំណត់ប្រភេទឱ្យត្រូវតាម Database (assignment, quiz, exam)
                $type = ($assessment instanceof \App\Models\Assignment) ? 'assignment' :
                       (($assessment instanceof \App\Models\Quiz) ? 'quiz' : 'exam');

                // ស្វែងរកពិន្ទុពី Collection ដែលយើងទាញទុកមុននេះ
                $score = $allResults->where('assessment_id', $assessment->id)
                    ->where('student_user_id', $student->id)
                    ->where('assessment_type', $type)
                    ->first()?->score_obtained ?? 0;

                // រក្សាទុកក្នុង Array សម្រាប់ផ្ញើទៅ Blade
                $gradebook[$student->id][$type.'_'.$assessment->id] = $score;

                // បូកបញ្ចូលក្នុងពិន្ទុសរុប
                $totalScore += (float) $score;
            }

            $student->temp_attendance = $attendanceScore;
            $student->temp_total = $totalScore;

            return $student;
        });

        // ៣. តម្រៀប Ranking តាមពិន្ទុសរុប
        $students = $students->sortByDesc('temp_total')->values();

        // ៤. ផ្ដល់ Rank និង Grade
        foreach ($students as $index => $student) {
            $student->rank = $index + 1;
            $ts = $student->temp_total;

            if ($ts >= 85) {
                $student->letterGrade = 'A';
            } elseif ($ts >= 80) {
                $student->letterGrade = 'B+';
            } elseif ($ts >= 70) {
                $student->letterGrade = 'B';
            } elseif ($ts >= 65) {
                $student->letterGrade = 'C+';
            } elseif ($ts >= 50) {
                $student->letterGrade = 'C';
            } else {
                $student->letterGrade = 'F';
            }
        }

        // ៥. បង្កើត HTML សម្រាប់ Word
        $html = view('professor.grades.export_word', compact('courseOffering', 'students', 'assessments', 'gradebook'))->render();

        // ប្តូរឈ្មោះ File និងការពារការខូចអក្សរខ្មែរ
        $fileName = 'Gradebook_'.str_replace([' ', '/', '\\'], '_', $courseOffering->course->title_km).'.doc';

        return response($html)
            ->header('Content-Type', 'application/msword; charset=utf-8')
            ->header('Content-Disposition', "attachment; filename*=UTF-8''".rawurlencode($fileName));
    }
    // ឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲឲ

    public function notifyTelegram($chatId, $message)
    {
        $token = env('TELEGRAM_BOT_TOKEN');

        // ឆែកមើលថាតើមាន Token និង Chat ID ឬអត់មុននឹងផ្ញើ
        if (! $token || ! $chatId) {
            return false;
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            \Log::error('Telegram Notification Error: '.$e->getMessage());

            return false;
        }
    }

    public function publishGrades($offering_id)
    {
        $courseOffering = CourseOffering::with('studentCourseEnrollments.student')->findOrFail($offering_id);
        $courseName = $courseOffering->course->title_km;

        foreach ($courseOffering->studentCourseEnrollments as $enrollment) {
            $student = $enrollment->student;

            // ឆែកថាតើសិស្សម្នាក់ហ្នឹងបានភ្ជាប់ Telegram (មាន chat_id) ឬនៅ
            if ($student->telegram_chat_id) {
                $msg = "<b>🔔 ដំណឹងលទ្ធផលសិក្សាថ្មី!</b>\n\n";
                $msg .= "មុខវិជ្ជា៖ <b>{$courseName}</b>\n";
                $msg .= "ស្ថានភាព៖ ពិន្ទុត្រូវបានគ្រូបោះពុម្ពផ្សាយហើយ។\n";
                $msg .= "🔗 ចូលមើលពិន្ទុ៖ <a href='".url('/student/my-grades')."'>ចុចទីនេះ</a>";

                $this->notifyTelegram($student->telegram_chat_id, $msg);
            }
        }

        return back()->with('success', 'បានផ្ញើដំណឹងទៅកាន់ Telegram របស់និស្សិតរួចរាល់!');
    }

    public function sendGradeTelegram($enrollment_id)
    {
        // ទាញយក Enrollment ដោយភ្ជាប់ជាមួយ studentUser (Table users)
        $enrollment = \App\Models\StudentCourseEnrollment::with(['studentUser', 'courseOffering.course'])
            ->findOrFail($enrollment_id);

        $studentUser = $enrollment->student;

        // ត្រួតពិនិត្យ Chat ID លើ studentUser មិនមែនលើ student ទេ
        if (! $studentUser || ! $studentUser->telegram_chat_id) {
            return back()->with('error', 'និស្សិតនេះមិនទាន់បានភ្ជាប់ជាមួយ Telegram Bot នៅឡើយទេ!');
        }

        $token = env('TELEGRAM_BOT_TOKEN');

        $message = "<b>🔔 លទ្ធផលសិក្សា</b>\n\n";
        $message .= "និស្សិត៖ <b>{$studentUser->name}</b>\n";
        $message .= "មុខវិជ្ជា៖ <b>{$enrollment->courseOffering->course->title_km}</b>\n";
        $message .= 'ស្ថានភាព៖ ពិន្ទុត្រូវបានផ្សាយហើយ។';

        // ហៅប្រើ function notifyTelegram ដែលអ្នកមានស្រាប់
        $this->notifyTelegram($studentUser->telegram_chat_id, $message);

        return back()->with('success', 'បានផ្ញើទៅ Telegram រួចរាល់!');
    }
    // professor.grades.store

    public function sendAllTelegram(Request $request, $offering_id)
    {
        $courseOffering = CourseOffering::with('course', 'studentCourseEnrollments.student.profile')->findOrFail($offering_id);

        if ($courseOffering->lecturer_user_id !== auth()->id()) {
            abort(403, 'អ្នកមិនមានសិទ្ធិចូលប្រើប្រាស់មុខវិជ្ជានេះទេ។');
        }

        $assessmentId = $request->input('assessment_id');
        $type = $request->input('assessment_type');

        // ១. ទាញយកព័ត៌មានវិញ្ញាសា
        $assessment = match ($type) {
            'assignment' => \App\Models\Assignment::find($assessmentId),
            'quiz' => \App\Models\Quiz::find($assessmentId),
            'exam' => \App\Models\Exam::find($assessmentId),
            default => null
        };

        if (! $assessment) {
            return back()->with('error', 'រកមិនឃើញទិន្នន័យវិញ្ញាសាឡើយ។');
        }

        // ២. រៀបចំព័ត៌មានសាស្ត្រាចារ្យ (Contact Link)
        $professor = auth()->user();
        // សន្មតថាទំនាក់ទំនងគឺ professorProfile ឬ userProfile
        $profProfile = $professor->professorProfile ?: $professor->userProfile;

        // បង្កើត Link ទៅកាន់ Telegram លោកគ្រូ (ប្រសិនបើគ្មាន វានឹងដាក់ Link ទៅកាន់ Bot)
        $professorContact = ($profProfile && $profProfile->telegram_user)
            ? 'https://t.me/'.str_replace('@', '', $profProfile->telegram_user)
            : 'https://t.me/kong_grade_bot';

        $typeName = match ($type) {
            'assignment' => 'កិច្ចការ (Assignment)',
            'quiz' => 'កម្រងសំណួរ (Quiz)',
            'exam' => 'ការប្រឡង (Exam)',
            default => 'វិញ្ញាសា'
        };

        $title = $assessment->title_km ?? $assessment->title_en;
        $sentCount = 0;

        foreach ($courseOffering->studentCourseEnrollments as $enrollment) {
            $student = $enrollment->student;

            if ($student && $student->telegram_chat_id) {

                // ៣. ទាញយកពិន្ទុពី Table ExamResult
                $result = \App\Models\ExamResult::where('assessment_id', $assessmentId)
                    ->where('assessment_type', $type)
                    ->where('student_user_id', $student->id)
                    ->first();

                $score = $result ? number_format($result->score_obtained, 1) : '---';
                $maxScore = $assessment->max_score ?? 100;

                // ៤. រៀបចំ Template សារ Telegram
                $message = "<b>📢 ដំណឹងលទ្ធផលសិក្សា</b>\n\n";
                $message .= 'សួស្តីនិស្សិត៖ <b>'.($student->profile?->full_name_km ?? $student->name)."</b>\n";
                $message .= "មុខវិជ្ជា៖ <b>{$courseOffering->course->title_en}</b>\n";
                $message .= "ប្រភេទ៖ <b>{$typeName}</b>\n";
                $message .= "វិញ្ញាសា៖ <b>{$title}</b>\n";
                $message .= "--------------------------------\n";
                $message .= "🎯 ពិន្ទុទទួលបាន៖ <code>{$score} / {$maxScore}</code>\n";
                $message .= "--------------------------------\n\n";

                // បន្ថែម Link ទំនាក់ទំនងសាស្ត្រាចារ្យ
                $message .= "💬 បើមានចម្ងល់សូមទាក់ទងសាស្ត្រាចារ្យ៖\n";
                $message .= "👉 <a href='{$professorContact}'>ចុចទីនេះដើម្បីផ្ញើសារ</a>\n\n";

                $message .= '👉 សូមចូលពិនិត្យមើលពិន្ទុលម្អិតក្នុងប្រព័ន្ធ។';

                $this->notifyTelegram($student->telegram_chat_id, $message);
                $sentCount++;
            }
        }

        return back()->with('success', "បានផ្ញើដំណឹងពិន្ទុ {$title} ទៅកាន់និស្សិតចំនួន {$sentCount} នាក់ រួចរាល់។");
    }

    public function updateTelegram(Request $request)
    {
        $request->validate([
            'telegram_chat_id' => 'required|numeric',
        ]);

        $user = auth()->user();
        $user->telegram_chat_id = $request->telegram_chat_id;
        $user->save();

        return back()->with('success', 'អបអរសាទរ! គណនី Telegram របស់អ្នកត្រូវបានភ្ជាប់ហើយ។');
    }

    public function sendTelegramSchedule($chatId, $message)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');

        $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);

        return $response->successful();
    }

    public function notifyProfessorSchedule()
    {
        $user = auth()->user();
        $chatId = $user->profile?->telegram_chat_id;

        if (! $chatId) {
            return;
        }

        // ទាញយកកាលវិភាគថ្ងៃនេះ (ឧទាហរណ៍)
        $schedules = Schedule::where('professor_id', $user->id)
            ->whereDate('class_date', now())
            ->get();

        if ($schedules->isEmpty()) {
            $message = '📅 ជម្រាបសួរលោកគ្រូ! ថ្ងៃនេះលោកគ្រូមិនមានកាលវិភាគបង្រៀនទេ។';
        } else {
            $message = "📅 <b>កាលវិភាគបង្រៀនថ្ងៃនេះ៖</b>\n\n";
            foreach ($schedules as $item) {
                $message .= "🔹 ម៉ោង: {$item->start_time} - {$item->end_time}\n";
                $message .= "🔹 មុខវិជ្ជា: {$item->subject_name}\n";
                $message .= "🔹 បន្ទប់: {$item->room}\n";
                $message .= "----------------------\n";
            }
        }

        $this->sendTelegramSchedule($chatId, $message);
    }

    // professor.assessments
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            // ១. ទាញយកសាស្ត្រាចារ្យទាំងឡាយណាដែលមាន Telegram Chat ID
            $users = User::whereNotNull('telegram_chat_id')->get();
            $botToken = env('TELEGRAM_BOT_TOKEN2'); // កុំភ្លេចដាក់ក្នុង .env

            foreach ($users as $user) {
                // ២. ទាញយកកាលវិភាគថ្ងៃនេះរបស់សាស្ត្រាចារ្យម្នាក់ៗ
                // លោកគ្រូត្រូវកែសម្រួល Logic ទាញកាលវិភាគតាម Database របស់លោកគ្រូ
                $todaySchedules = \App\Models\Schedule::where('professor_id', $user->id)
                    ->whereDate('date', now())
                    ->orderBy('start_time', 'asc')
                    ->get();

                if ($todaySchedules->isNotEmpty()) {
                    $message = '📅 <b>ជម្រាបសួរលោកគ្រូ '.($user->profile->full_name_km ?? $user->name)."</b>\n";
                    $message .= "នេះគឺជាកាលវិភាគបង្រៀនរបស់លោកគ្រូសម្រាប់ថ្ងៃនេះ៖\n\n";

                    foreach ($todaySchedules as $index => $item) {
                        $num = $index + 1;
                        $message .= "{$num}. <b>{$item->subject_name}</b>\n";
                        $message .= "   ⏰ ម៉ោង: {$item->start_time} - {$item->end_time}\n";
                        $message .= "   📍 បន្ទប់: {$item->room_name}\n";
                        $message .= "--------------------------\n";
                    }

                    $message .= "\nសូមលោកគ្រូត្រៀមខ្លួនឱ្យបានរួចរាល់។ សូមអរគុណ!";

                    // ៣. ផ្ញើសារទៅកាន់ Telegram
                    Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                        'chat_id' => $user->telegram_chat_id,
                        'text' => $message,
                        'parse_mode' => 'HTML',
                    ]);
                }
            }
        })->dailyAt('07:00');
    }

    // --------------------------------------------------------------------------
}
// showProfile
