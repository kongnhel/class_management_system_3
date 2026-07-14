<?php

namespace App\Http\Controllers\admin;

use App\Exports\CourseStudentsExport;
use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Program;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class CourseOfferingController extends Controller
{
    const LECTURER_FK_COLUMN = 'lecturer_user_id';

    public function index(Request $request)
    {
        $query = CourseOffering::query()
            ->with(['course', 'targetPrograms', 'lecturer', 'schedules.room'])
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

        if ($request->filled('lecturer_id')) {
            $query->where('lecturer_user_id', $request->input('lecturer_id'));
        }

        if ($request->filled('program_id')) {
            $query->whereHas('targetPrograms', function ($q) use ($request) {
                $q->where('program_id', $request->input('program_id'));
            });
        }

        if ($request->filled('generation')) {
            $query->whereHas('targetPrograms', function ($q) use ($request) {
                $q->where('course_offering_program.generation', '=', $request->input('generation'));
            });
        }

        if ($request->filled('semester')) {
            $query->where('semester', '=', $request->input('semester'));
        }
        if ($request->filled('academic_year')) {
            $query->where('academic_year', '=', $request->input('academic_year'));
        }
        if ($request->filled('shift')) {
            $shift = $request->shift;
            $query->whereHas('schedules', function ($q) use ($shift) {
                if ($shift === 'weekend') {
                    $q->whereIn('day_of_week', ['Saturday', 'Sunday']);
                } elseif ($shift === 'weekday') {
                    $q->whereIn('day_of_week', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']);
                }
            });
        }
        $courseOfferings = $query->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->paginate(50)
            ->appends($request->query());

        $courseOfferings->getCollection()->transform(function ($offering) {
            $offering->is_active = now()->between($offering->start_date, $offering->end_date);

            return $offering;
        });

        $programs = Program::orderBy('name_km')->get();

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        $assignedLecturerIds = CourseOffering::distinct()->pluck('lecturer_user_id')->filter()->unique();
        $lecturers = User::whereIn('id', $assignedLecturerIds)
            ->where('role', 'professor')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.course-offerings.index', compact(
            'courseOfferings',
            'programs',
            'academicYears',
            'lecturers'
        ));
    }

    public function create()
    {
        $courses = Course::with('programs:id,name_km,name_en,degree_level')->get(['id','title_km','title_en','generation']);
        $professors = User::where('role', 'professor')->get();
        $programs = Program::all();
        $rooms = Room::all();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        return view('admin.course-offerings.create', compact('courses', 'professors', 'programs', 'rooms', 'academicYears'));
    }

    public function store(Request $request)
    {
        // 1. Define Validation Rules
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'lecturer_user_id' => 'required|exists:users,id',
            'academic_year' => 'required|string|max:255',
            'semester' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',

            'target_programs' => 'required|array|min:1',
            'target_programs.*.program_id' => 'required|exists:programs,id|distinct', // បន្ថែម distinct
            'target_programs.*.generation' => 'required|string|max:255',

            'schedules' => 'required|array|min:1',
            'schedules.*.day_of_week' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'schedules.*.room_id' => 'required|exists:rooms,id',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
        ], [
            'target_programs.required' => 'សូមជ្រើសរើសជំនាញ និងជំនាន់យ៉ាងហោចណាស់មួយ។',
        ]);

        // 2. Conflict Checks
        $validator->after(function ($validator) use ($request) {
            $schedules = $request->input('schedules', []);
            $lecturerId = $request->input('lecturer_user_id');
            $academicYear = $request->input('academic_year');
            $semester = $request->input('semester');

            if (! is_array($schedules)) {
                return;
            }

            foreach ($schedules as $index => $current) {
                $day = $current['day_of_week'] ?? null;
                $start = $current['start_time'] ?? null;
                $end = $current['end_time'] ?? null;
                $roomId = $current['room_id'] ?? null;

                if (! $day || ! $start || ! $end) {
                    continue;
                }

                foreach ($schedules as $innerIndex => $compare) {
                    if ($index === $innerIndex) {
                        continue;
                    }
                    if ($day === ($compare['day_of_week'] ?? '') &&
                        $start < ($compare['end_time'] ?? '') &&
                        $end > ($compare['start_time'] ?? '')) {
                        $validator->errors()->add("schedules.$index", 'ម៉ោងដែលអ្នកបញ្ចូលមកមានការជាន់គ្នាឯងក្នុងបញ្ជីខាងលើ។');
                    }
                }

                $overlapQuery = function ($q) use ($start, $end) {
                    $q->where(function ($query) use ($start, $end) {
                        $query->where('start_time', '<', $end)
                            ->where('end_time', '>', $start);
                    });
                };

                $roomConflict = \App\Models\Schedule::where('day_of_week', $day)
                    ->where('room_id', $roomId)
                    ->whereHas('courseOffering', function ($q) use ($academicYear, $semester) {
                        $q->where('academic_year', $academicYear)
                            ->where('semester', $semester);
                    })
                    ->where($overlapQuery)
                    ->exists();

                if ($roomConflict) {
                    $validator->errors()->add("schedules.$index.room_id", "បន្ទប់នេះជាប់រវល់ហើយ នៅថ្ងៃ $day ចន្លោះម៉ោង $start - $end");
                }

                // --- CHECK B: Lecturer Conflict ---
                $lecturerConflict = \App\Models\Schedule::where('day_of_week', $day)
                    ->whereHas('courseOffering', function ($q) use ($lecturerId, $academicYear, $semester) {
                        $q->where('lecturer_user_id', $lecturerId)
                            ->where('academic_year', $academicYear)
                            ->where('semester', $semester);
                    })
                    ->where($overlapQuery)
                    ->exists();

                if ($lecturerConflict) {
                    $validator->errors()->add('lecturer_user_id', "សាស្ត្រាចារ្យនេះជាប់បង្រៀនថ្នាក់ផ្សេងហើយ នៅថ្ងៃ {$day} ម៉ោង {$start} - {$end}។");
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // 3. Create Course Offering
            $courseOffering = CourseOffering::create([
                'course_id' => $validated['course_id'],
                'lecturer_user_id' => $validated['lecturer_user_id'],
                'academic_year' => $validated['academic_year'],
                'semester' => $validated['semester'],
                'capacity' => $validated['capacity'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]);

            // 4. Save Programs & AUTO ENROLL STUDENTS
            foreach ($validated['target_programs'] as $prog) {
                $courseOffering->targetPrograms()->attach($prog['program_id'], [
                    'generation' => $prog['generation'],
                ]);

                $students = User::where('role', 'student')
                    ->where('program_id', $prog['program_id'])
                    ->where('generation', $prog['generation'])
                    ->get();

                foreach ($students as $student) {
                    \App\Models\StudentCourseEnrollment::firstOrCreate([
                        'student_user_id' => $student->id,
                        'course_offering_id' => $courseOffering->id,
                    ], [
                        'student_id' => $student->id,
                        'enrollment_date' => now(),
                        'status' => 'enrolled',
                    ]);
                }
            }

            // 5. Create Schedules
            foreach ($validated['schedules'] as $scheduleData) {
                $courseOffering->schedules()->create($scheduleData);
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('admin.manage-course-offerings')
                ->with('success', 'ការផ្តល់ជូនមុខវិជ្ជាត្រូវបានបង្កើតដោយជោគជ័យ និងបានបញ្ចូលឈ្មោះសិស្សរួចរាល់!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error creating course offering: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'មានបញ្ហាក្នុងការបង្កើត៖ '.$e->getMessage())
                ->withInput();
        }
    }

    public function edit(CourseOffering $courseOffering)
    {
        // Load relationship
        $courseOffering->load('targetPrograms', 'schedules');
        $courses = Course::all();

        $programs = Program::all();
        $lecturers = User::where('role', 'professor')->get();
        $rooms = Room::all();
        $selectedCourse = Course::find($courseOffering->course_id);
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        return view('admin.course-offerings.edit', compact(
            'courseOffering',
            'programs',
            'lecturers',
            'rooms',
            'selectedCourse',
            'courses',
            'academicYears',
        ));
    }

    public function update(Request $request, CourseOffering $courseOffering)
    {
        // 1. Define Validation Rules
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'lecturer_user_id' => 'required|exists:users,id',
            'academic_year' => 'required|string|max:255',
            'semester' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'target_programs' => 'required|array|min:1',
            'target_programs.*.program_id' => 'required|exists:programs,id|distinct',
            'target_programs.*.generation' => 'required|string|max:255',
            'schedules' => 'required|array|min:1',
            'schedules.*.day_of_week' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'schedules.*.room_id' => 'required|exists:rooms,id',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
        ]);

        $validator->after(function ($validator) use ($request, $courseOffering) {
            $schedules = $request->input('schedules', []);
            $lecturerId = $request->input('lecturer_user_id');
            $academicYear = $request->input('academic_year');
            $semester = $request->input('semester');

            if (! is_array($schedules)) {
                return;
            }

            foreach ($schedules as $index => $current) {
                $day = $current['day_of_week'] ?? null;
                $start = $current['start_time'] ?? null;
                $end = $current['end_time'] ?? null;
                $roomId = $current['room_id'] ?? null;

                if (! $day || ! $start || ! $end) {
                    continue;
                }

                // --- CHECK 0: Internal Conflict (Check ជាន់គ្នាឯងក្នុង Request) ---
                foreach ($schedules as $innerIndex => $compare) {
                    if ($index === $innerIndex) {
                        continue;
                    }
                    if ($day === ($compare['day_of_week'] ?? '') &&
                        $start < ($compare['end_time'] ?? '') &&
                        $end > ($compare['start_time'] ?? '')) {
                        $validator->errors()->add("schedules.$index", 'ម៉ោងដែលអ្នកបញ្ចូលមកមានការជាន់គ្នាឯងក្នុងបញ្ជីខាងលើ។');
                    }
                }

                // Standard Overlap Query Logic
                $overlapQuery = function ($q) use ($start, $end) {
                    $q->where(function ($query) use ($start, $end) {
                        $query->where('start_time', '<', $end)
                            ->where('end_time', '>', $start);
                    });
                };

                // --- CHECK A: Room Conflict ---
                $roomConflict = \App\Models\Schedule::where('day_of_week', $day)
                    ->where('room_id', $roomId)
                    ->where('course_offering_id', '!=', $courseOffering->id)
                    ->whereHas('courseOffering', function ($q) use ($academicYear, $semester) {
                        $q->where('academic_year', $academicYear)
                            ->where('semester', $semester);
                    })
                    ->where($overlapQuery)
                    ->exists();

                if ($roomConflict) {
                    $validator->errors()->add("schedules.$index.room_id", "បន្ទប់នេះជាប់រវល់ហើយ នៅថ្ងៃ $day ចន្លោះម៉ោង $start - $end");
                }

                // --- CHECK B: Lecturer Conflict ---
                $lecturerConflict = \App\Models\Schedule::where('day_of_week', $day)
                    ->whereHas('courseOffering', function ($q) use ($lecturerId, $academicYear, $semester, $courseOffering) {
                        $q->where('lecturer_user_id', $lecturerId)
                            ->where('academic_year', $academicYear)
                            ->where('semester', $semester)
                            ->where('id', '!=', $courseOffering->id);
                    })
                    ->where($overlapQuery)
                    ->exists();

                if ($lecturerConflict) {
                    $validator->errors()->add('lecturer_user_id', "សាស្ត្រាចារ្យនេះជាប់បង្រៀនថ្នាក់ផ្សេងហើយ នៅថ្ងៃ {$day} ម៉ោង {$start} - {$end}។");
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // 3. Update Main Table
            $courseOffering->update([
                'course_id' => $validated['course_id'],
                'lecturer_user_id' => $validated['lecturer_user_id'],
                'academic_year' => $validated['academic_year'],
                'semester' => $validated['semester'],
                'capacity' => $validated['capacity'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]);

            // 4. Sync Programs
            $syncData = [];
            foreach ($validated['target_programs'] as $prog) {
                $syncData[$prog['program_id']] = ['generation' => $prog['generation']];
            }
            $courseOffering->targetPrograms()->sync($syncData);

            // 4b. Auto-enroll students for newly added programs
            foreach ($validated['target_programs'] as $prog) {
                $students = User::where('role', 'student')
                    ->where('program_id', $prog['program_id'])
                    ->where('generation', $prog['generation'])
                    ->get();

                foreach ($students as $student) {
                    \App\Models\StudentCourseEnrollment::firstOrCreate([
                        'student_user_id' => $student->id,
                        'course_offering_id' => $courseOffering->id,
                    ], [
                        'student_id' => $student->id,
                        'enrollment_date' => now(),
                        'status' => 'enrolled',
                    ]);
                }
            }

            // 5. Update Schedules (Delete & Re-create)
            $courseOffering->schedules()->delete();
            foreach ($validated['schedules'] as $scheduleData) {
                $courseOffering->schedules()->create($scheduleData);
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('admin.manage-course-offerings')
                ->with('success', 'ការផ្តល់ជូនមុខវិជ្ជាត្រូវបានកែប្រែដោយជោគជ័យ!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error updating course offering: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'មានបញ្ហា៖ '.$e->getMessage())
                ->withInput();
        }
    }

    public function destroy(CourseOffering $courseOffering)
    {
        try {
            DB::beginTransaction();

            $courseOffering->targetPrograms()->detach();
            $courseOffering->schedules()->delete();
            $courseOffering->studentCourseEnrollments()->delete();
            $courseOffering->assignments()->delete();
            $courseOffering->exams()->delete();
            $courseOffering->quizzes()->delete();

            // លុបពិតៗ (Force Delete)
            $courseOffering->forceDelete();

            DB::commit();

            Session::flash('success', 'ការផ្តល់ជូនមុខវិជ្ជាត្រូវបានលុបចោលពិតៗហើយ។');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Session::flash('error', 'មានបញ្ហា៖ '.$e->getMessage());
        }

        return redirect()->route('admin.manage-course-offerings');
    }

    public function show(CourseOffering $courseOffering)
    {
        $courseOffering->load([
            'course',
            'targetPrograms',
            'lecturer.profile',
            'schedules.room',
            'studentCourseEnrollments.student.profile',
        ]);

        return view('admin.course-offerings.show', compact('courseOffering'));
    }

    public function enrollStudentForm()
    {
        $students = User::where('role', 'student')->orderBy('name')->get();

        $courseOfferings = CourseOffering::with('course', 'lecturer')
            ->where('end_date', '>=', now())
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        return view('admin.enroll_student', compact('students', 'courseOfferings'));
    }

    /**
     * Handle the admin's request to enroll a student in a course offering.
     *
     * @param  \Illuminate->Http->Request  $request
     * @return \Illuminate->Http->RedirectResponse
     */
    public function performEnrollment(Request $request)
    {
        $request->validate([
            'student_user_id' => 'required|exists:users,id',
            'course_offering_id' => 'required|exists:course_offerings,id',
        ]);

        $studentUserId = $request->input('student_user_id');
        $courseOfferingId = $request->input('course_offering_id');

        $existingEnrollment = StudentCourseEnrollment::where('student_user_id', $studentUserId)
            ->where('course_offering_id', $courseOfferingId)
            ->first();

        if ($existingEnrollment) {
            Session::flash('info', 'សិស្សរូបនេះបានចុះឈ្មោះក្នុងវគ្គសិក្សានេះរួចហើយ។');

            return redirect()->back();
        }

        try {
            StudentCourseEnrollment::create([
                'student_user_id' => $studentUserId,
                'course_offering_id' => $courseOfferingId,
                'enrollment_date' => now(),
                'status' => 'enrolled',
            ]);
            Session::flash('success', 'ការចុះឈ្មោះសិស្សដោយជោគជ័យ!');
        } catch (\Exception $e) {
            Session::flash('error', 'មានបញ្ហាក្នុងការចុះឈ្មោះសិស្ស៖ '.$e->getMessage());
        }

        return redirect()->back();
    }

    public function getCoursesByProgram(Program $program)
    {

        $courses = $program->courses()->select('id', 'code', 'title_km')->get();

        return response()->json($courses);
    }

    public function exportStudents($offering_id)
    {
        return Excel::download(new CourseStudentsExport($offering_id), 'students_list_course_'.$offering_id.'.xlsx');
    }
}
