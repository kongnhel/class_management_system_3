<?php

namespace App\Http\Controllers\admin;

use App\Exports\CourseStudentsExport;
use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Generation;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            ->with(['course', 'department', 'lecturer', 'schedules.room'])
            ->withCount('studentCourseEnrollments')
            ->whereHas('course')
            ->whereHas('lecturer');

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

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->filled('faculty_id')) {
            $query->whereHas('department', function ($q) use ($request) {
                $q->where('faculty_id', $request->input('faculty_id'));
            });
        }

        if ($request->filled('generation')) {
            $query->where('generation', $request->input('generation'));
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

        $departments = Department::orderBy('name_km')->get();

        $faculties = Faculty::with('departments')->orderBy('name_km')->get();

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        $assignedLecturerIds = CourseOffering::distinct()->pluck('lecturer_user_id')->filter()->unique();
        $lecturers = User::whereIn('id', $assignedLecturerIds)
            ->where('role', 'professor')
            ->orderBy('name')
            ->get(['id', 'name']);

        $generations = Generation::where('is_active', true)->orderBy('name', 'desc')->pluck('name')->filter()->values();

        return view('admin.course-offerings.index', compact(
            'courseOfferings',
            'departments',
            'faculties',
            'academicYears',
            'lecturers',
            'generations'
        ));
    }

    public function create()
    {
        $courses = Course::get(['id', 'title_km', 'title_en', 'department_id']);
        $professors = User::where('role', 'professor')->get();
        $departments = Department::all();
        $faculties = Faculty::all();
        $rooms = Room::all();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $generations = Generation::where('is_active', true)->orderByDesc('name')->get();

        return view('admin.course-offerings.create', compact('courses', 'professors', 'departments', 'faculties', 'rooms', 'academicYears', 'generations'));
    }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'lecturer_user_id' => 'required|exists:users,id',
            'academic_year' => 'required|string|max:255',
            'semester' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'department_id' => 'required|exists:departments,id',
            'generation' => 'nullable|string|max:255',

            'schedules' => 'required|array|min:1',
            'schedules.*.day_of_week' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'schedules.*.room_id' => 'required|exists:rooms,id',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
        ]);

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

                if ($this->findRoomConflicts($day, $roomId, $start, $end, $academicYear, $semester)->isNotEmpty()) {
                    $validator->errors()->add("schedules.$index.room_id", "បន្ទប់នេះជាប់រវល់ហើយ នៅថ្ងៃ $day ចន្លោះម៉ោង $start - $end");
                }

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

            $courseOffering = CourseOffering::create([
                'course_id' => $validated['course_id'],
                'lecturer_user_id' => $validated['lecturer_user_id'],
                'academic_year' => $validated['academic_year'],
                'semester' => $validated['semester'],
                'capacity' => $validated['capacity'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'department_id' => $validated['department_id'],
                'generation' => $validated['generation'] ?? null,
            ]);

            // Auto-enroll matching students
            $students = User::where('role', 'student')
                ->where('department_id', $validated['department_id'])
                ->when($validated['generation'] ?? null, fn ($q, $gen) => $q->where('generation', $gen))
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

            $this->generateSchedulesFromPattern($courseOffering, $validated);

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('admin.manage-course-offerings')
                ->with('success', __('ការផ្តល់ជូនមុខវិជ្ជាត្រូវបានបង្កើតដោយជោគជ័យ និងបានបញ្ចូលឈ្មោះសិស្សរួចរាល់។'));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error creating course offering: '.$e->getMessage());

            return redirect()->back()
                ->with('error', __('មានបញ្ហាក្នុងការបង្កើត៖ ').$e->getMessage())
                ->withInput();
        }
    }

    public function edit(CourseOffering $courseOffering)
    {
        $courseOffering->load('schedules', 'department');
        $courses = Course::all();

        $departments = Department::all();
        $faculties = Faculty::all();
        $lecturers = User::where('role', 'professor')->get();
        $rooms = Room::all();
        $selectedCourse = Course::find($courseOffering->course_id);
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $generations = Generation::where('is_active', true)->orderByDesc('name')->get();

        return view('admin.course-offerings.edit', compact(
            'courseOffering',
            'departments',
            'faculties',
            'lecturers',
            'rooms',
            'selectedCourse',
            'courses',
            'academicYears',
            'generations',
        ));
    }

    public function update(Request $request, CourseOffering $courseOffering)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'lecturer_user_id' => 'required|exists:users,id',
            'academic_year' => 'required|string|max:255',
            'semester' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'department_id' => 'required|exists:departments,id',
            'generation' => 'nullable|string|max:255',

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

                if ($this->findRoomConflicts($day, $roomId, $start, $end, $academicYear, $semester, $courseOffering->id)->isNotEmpty()) {
                    $validator->errors()->add("schedules.$index.room_id", "បន្ទប់នេះជាប់រវល់ហើយ នៅថ្ងៃ $day ចន្លោះម៉ោង $start - $end");
                }

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

            $courseOffering->update([
                'course_id' => $validated['course_id'],
                'lecturer_user_id' => $validated['lecturer_user_id'],
                'academic_year' => $validated['academic_year'],
                'semester' => $validated['semester'],
                'capacity' => $validated['capacity'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'department_id' => $validated['department_id'],
                'generation' => $validated['generation'] ?? null,
            ]);

            // Auto-enroll matching students
            $students = User::where('role', 'student')
                ->where('department_id', $validated['department_id'])
                ->when($validated['generation'] ?? null, fn ($q, $gen) => $q->where('generation', $gen))
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

            $courseOffering->schedules()->delete();
            $this->generateSchedulesFromPattern($courseOffering, $validated);

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('admin.manage-course-offerings')
                ->with('success', __('ការផ្តល់ជូនមុខវិជ្ជាត្រូវបានកែប្រែដោយជោគជ័យ។'));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error updating course offering: '.$e->getMessage());

            return redirect()->back()
                ->with('error', __('មានបញ្ហា៖ ').$e->getMessage())
                ->withInput();
        }
    }

    public function destroy(CourseOffering $courseOffering)
    {
        try {
            DB::beginTransaction();

            $courseOffering->schedules()->delete();
            $courseOffering->studentCourseEnrollments()->delete();
            $courseOffering->assignments()->delete();
            $courseOffering->exams()->delete();
            $courseOffering->quizzes()->delete();

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
            'department',
            'lecturer.profile',
            'schedules.room',
            'studentCourseEnrollments.student.profile',
        ]);

        $attendanceRecords = \App\Models\AttendanceRecord::where('course_offering_id', $courseOffering->id)
            ->with('student.profile', 'student.studentProfile')
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy('date');

        return view('admin.course-offerings.show', compact('courseOffering', 'attendanceRecords'));
    }

    public function enrollStudentForm()
    {
        $students = User::where('role', 'student')->orderBy('name')->get();

        $courseOfferings = CourseOffering::with('course', 'lecturer')
            ->where('end_date', '>=', now())
            ->whereHas('course')
            ->whereHas('lecturer')
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        return view('admin.enroll_student', compact('students', 'courseOfferings'));
    }

    public function performEnrollment(Request $request)
    {
        $request->validate([
            'student_user_id' => 'required|exists:users,id',
            'course_offering_id' => 'required|exists:course_offerings,id',
        ]);

        $studentUserId = $request->input('student_user_id');
        $courseOfferingId = $request->input('course_offering_id');

        $existingEnrollment = \App\Models\StudentCourseEnrollment::where('student_user_id', $studentUserId)
            ->where('course_offering_id', $courseOfferingId)
            ->first();

        if ($existingEnrollment) {
            Session::flash('info', 'សិស្សរូបនេះបានចុះឈ្មោះក្នុងវគ្គសិក្សានេះរួចហើយ។');

            return redirect()->back();
        }

        try {
            \App\Models\StudentCourseEnrollment::create([
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

    public function getCoursesByDepartment(Department $department)
    {
        $courses = $department->courses()->select('id', 'code', 'title_km')->get();

        return response()->json($courses);
    }

    public function exportStudents($offering_id)
    {
        abort_unless(Auth::user()?->isProfessor(), 403);

        abort_unless(
            CourseOffering::whereKey($offering_id)
                ->where('lecturer_user_id', Auth::id())
                ->exists(),
            403
        );

        return Excel::download(new CourseStudentsExport($offering_id), 'students_list_course_'.$offering_id.'.xlsx');
    }

    private function findRoomConflicts(
        string $day,
        $roomId,
        string $start,
        string $end,
        string $academicYear,
        string $semester,
        ?int $ignoreOfferingId = null
    ) {
        return Schedule::where('day_of_week', $day)
            ->where('room_id', $roomId)
            ->when($ignoreOfferingId, function ($q) use ($ignoreOfferingId) {
                $q->where('course_offering_id', '!=', $ignoreOfferingId);
            })
            ->whereHas('courseOffering', function ($q) use ($academicYear, $semester) {
                $q->where('academic_year', $academicYear)
                    ->where('semester', $semester);
            })
            ->where(function ($query) use ($start, $end) {
                $query->where('start_time', '<', $end)
                    ->where('end_time', '>', $start);
            })
            ->with(['courseOffering.course:id,title_km,title_en', 'courseOffering.lecturer:id,name'])
            ->get();
    }

    public function checkRoomAvailability(Request $request)
    {
        $data = $request->validate([
            'day_of_week' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'room_id' => 'required|integer|exists:rooms,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'academic_year' => 'required|string',
            'semester' => 'required|string',
            'ignore_offering_id' => 'nullable|integer|exists:course_offerings,id',
        ]);

        $startTime = substr($data['start_time'], 0, 5);
        $endTime = substr($data['end_time'], 0, 5);

        $conflicts = $this->findRoomConflicts(
            $data['day_of_week'],
            $data['room_id'],
            $data['start_time'],
            $data['end_time'],
            $data['academic_year'],
            $data['semester'],
            $data['ignore_offering_id'] ?? null
        );

        $bookings = Schedule::where('day_of_week', $data['day_of_week'])
            ->where('room_id', $data['room_id'])
            ->when($data['ignore_offering_id'] ?? null, function ($q) use ($data) {
                $q->where('course_offering_id', '!=', $data['ignore_offering_id']);
            })
            ->whereHas('courseOffering', function ($q) use ($data) {
                $q->where('academic_year', $data['academic_year'])
                    ->where('semester', $data['semester']);
            })
            ->with(['courseOffering.course:id,title_km,title_en', 'courseOffering.lecturer:id,name'])
            ->get();

        $requestedStart = $this->timeToMinutes($startTime);
        $requestedEnd = $this->timeToMinutes($endTime);

        $slots = collect($this->sessionSlots())->map(function ($slot) use ($bookings) {
            $slotStart = $this->timeToMinutes($slot['start']);
            $slotEnd = $this->timeToMinutes($slot['end']);

            $booking = $bookings->first(function ($b) use ($slotStart, $slotEnd) {
                return $this->timeToMinutes(substr($b->start_time, 0, 5)) < $slotEnd
                    && $this->timeToMinutes(substr($b->end_time, 0, 5)) > $slotStart;
            });

            return [
                'start' => $slot['start'],
                'end' => $slot['end'],
                'status' => $booking ? 'busy' : 'free',
                'course' => $booking?->courseOffering?->course?->title_km ?? $booking?->courseOffering?->course?->title_en,
                'lecturer' => $booking?->courseOffering?->lecturer?->name,
            ];
        })->values();

        $conflictList = $conflicts->map(function ($c) {
            return [
                'course' => $c->courseOffering?->course?->title_km ?? $c->courseOffering?->course?->title_en ?? '',
                'lecturer' => $c->courseOffering?->lecturer?->name ?? '',
                'start' => substr($c->start_time, 0, 5),
                'end' => substr($c->end_time, 0, 5),
            ];
        })->values();

        $matchesSessionSlot = collect($this->sessionSlots())->contains(function ($s) use ($startTime, $endTime) {
            return $s['start'] === $startTime && $s['end'] === $endTime;
        });

        return response()->json([
            'conflict' => $conflictList->isNotEmpty(),
            'message' => $conflictList->isNotEmpty()
                ? 'បន្ទប់នេះជាប់រវល់ហើយ នៅថ្ងៃ '.$data['day_of_week'].' ចន្លោះម៉ោង '.$startTime.' - '.$endTime
                : '',
            'conflicts' => $conflictList,
            'slots' => $slots,
            'no_time_available' => $slots->isNotEmpty() && $slots->every(fn ($s) => $s['status'] === 'busy'),
            'times_match_session' => $matchesSessionSlot,
        ]);
    }

    private function sessionSlots(): array
    {
        $sessionMinutes = (int) config('school.schedule.session_minutes', 90);
        $slots = [];

        foreach (config('school.schedule.windows', []) as $window) {
            $cursor = $this->timeToMinutes($window['start']);
            $windowEnd = $this->timeToMinutes($window['end']);

            while ($cursor + $sessionMinutes <= $windowEnd) {
                $slots[] = [
                    'start' => $this->minutesToTime($cursor),
                    'end' => $this->minutesToTime($cursor + $sessionMinutes),
                ];
                $cursor += $sessionMinutes;
            }
        }

        return $slots;
    }

    private function timeToMinutes(string $time): int
    {
        [$h, $m] = array_map('intval', explode(':', substr($time, 0, 5)));

        return $h * 60 + $m;
    }

    private function minutesToTime(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    private function generateSchedulesFromPattern(CourseOffering $offering, array $data)
    {
        foreach ($data['schedules'] as $scheduleData) {
            $offering->schedules()->create($scheduleData);
        }
    }
}
