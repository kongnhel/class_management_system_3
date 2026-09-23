<?php

namespace App\Http\Controllers\professor;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\AttendanceProfessor;
use App\Models\AttendanceRecord;
use App\Services\AttendanceSessionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Excel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ProfessorAttendanceController extends Controller
{
    public function __construct(private readonly AttendanceSessionService $attendanceSessions) {}

    /**
     * Store a newly created attendance record in storage.
     */
    public function storeAttendance(Request $request)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'student_user_ids' => 'required_without:student_user_id|string',
            'student_user_id' => 'required_without:student_user_ids|nullable',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,permission,late',
            'remarks' => 'nullable|string|max:255',
        ], [
            'student_user_ids.required_without' => 'សូមជ្រើសរើសនិស្សិតយ៉ាងហោចណាស់មួយ។',
            'date.required' => 'កាលបរិច្ឆេទតម្រូវឱ្យបញ្ចូល។',
            'status.required' => 'ស្ថានភាពវត្តមានតម្រូវឱ្យបញ្ចូល។',
        ]);

        $this->attendanceSessions->ownedOffering($request->integer('course_offering_id'), $request->user()->id);

        // Get student IDs from either single or multiple selection
        $studentIds = [];
        if (! empty($request->student_user_ids)) {
            $studentIds = array_filter(explode(',', $request->student_user_ids));
        } elseif (! empty($request->student_user_id)) {
            $studentIds = [$request->student_user_id];
        }

        if (empty($studentIds)) {
            return redirect()->back()->with('error', __('select_at_least_one_student'));
        }

        foreach ($studentIds as $studentId) {
            $studentId = trim($studentId);
            if (empty($studentId)) {
                continue;
            }

            AttendanceRecord::updateOrInsert(
                [
                    'course_offering_id' => $request->input('course_offering_id'),
                    'student_user_id' => $studentId,
                    'date' => $request->input('date'),
                ],
                [
                    'user_id' => $studentId,
                    'status' => $request->input('status'),
                    'remarks' => $request->input('remarks') ?: 'Manual entry',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        return redirect()->route('professor.manage-attendance', ['offering_id' => $request->input('course_offering_id')])
            ->with('success', __('attendance_record_added_successfully'));
    }

    /**
     * Update the specified attendance record in storage.
     */
    public function updateAttendance(Request $request, AttendanceRecord $attendance)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'student_user_id' => [
                'required',
                Rule::exists('student_course_enrollments', 'student_user_id')
                    ->where('course_offering_id', $request->course_offering_id),
            ],
            'date' => 'required|date',
            'status' => 'required|in:present,absent,permission,late',
            'remarks' => 'nullable|string|max:255',
        ], [
            'student_user_id.required' => 'អត្តសញ្ញាណសិស្សតម្រូវឱ្យបញ្ចូល។',
            'student_user_id.exists' => 'អត្តសញ្ញាណសិស្សមិនមានឈ្មោះក្នុងបញ្ជីរៀននៃវគ្គសិក្សានេះទេ។',
            'course_offering_id.required' => 'អត្តសញ្ញាណវគ្គសិក្សាតម្រូវឱ្យបញ្ចូល។',
            'date.required' => 'កាលបរិច្ឆេទតម្រូវឱ្យបញ្ចូល។',
            'status.required' => 'ស្ថានភាពវត្តមានតម្រូវឱ្យបញ្ចូល។',
        ]);

        $offering = $this->attendanceSessions->ownedOffering($request->integer('course_offering_id'), $request->user()->id);
        abort_unless($attendance->course_offering_id === $offering->id, 403);

        $attendance->update($request->only(['course_offering_id', 'student_user_id', 'date', 'status', 'remarks']));

        return redirect()->route('professor.manage-attendance', ['offering_id' => $attendance->course_offering_id])
            ->with('success', __('attendance_record_updated_successfully'));
    }

    /**
     * Remove the specified attendance record from storage.
     */
    public function destroyAttendance(AttendanceRecord $attendance)
    {
        $this->attendanceSessions->ownedOffering($attendance->course_offering_id, request()->user()->id);
        $courseOfferingId = $attendance->course_offering_id;
        $attendance->delete();

        return redirect()->route('professor.manage-attendance', ['offering_id' => $courseOfferingId])
            ->with('success', __('attendance_record_deleted_successfully'));
    }

    // /**
    //      * Verify professor's location and check-in.
    //      */
    //     public function verifyLocation(Request $request)
    //     {
    //         $request->validate([
    //             'course_offering_id' => 'required|exists:course_offerings,id',
    //             'session_id' => 'required|integer',
    //             'lat' => 'required|numeric|between:-90,90',
    //             'lng' => 'required|numeric|between:-180,180',
    //         ]);

    //         $schoolLat = config('services.nmu.lat', env('NMU_LAT', 13.57952292));
    //         $schoolLng = config('services.nmu.lng', env('NMU_LNG', 102.92898894));
    //         $allowedRadius = config('services.nmu.radius', env('NMU_RADIUS', 100));

    //         $professorId = auth()->id();
    //         $now = Carbon::now('Asia/Phnom_Penh');
    //         $today = $now->toDateString();

    //         $exists = AttendanceProfessor::where([
    //             'professor_id' => $professorId,
    //             'course_offering_id' => $request->course_offering_id,
    //             'verified_date' => $today,
    //             'session_id' => $request->session_id,
    //         ])->exists();

    //         if ($exists) {
    //             return response()->json([
    //                 'success' => true,
    //                 'already_checked_in' => true,
    //                 'message' => 'លោកគ្រូបានចុះវត្តមានសម្រាប់ម៉ោងនេះរួចរាល់ហើយ!'
    //             ]);
    //         }

    //         $distance = $this->calculateDistance($request->lat, $request->lng, $schoolLat, $schoolLng);

    //         if ($distance > $allowedRadius) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'លោកគ្រូនៅឆ្ងាយពីសាលាពេកហើយ! ចម្ងាយបច្ចុប្បន្ន៖ ' . round($distance) . ' ម៉ែត្រ។ មកឱ្យជិតសិនលោកគ្រូ!'
    //             ], 403);
    //         }

    //         AttendanceProfessor::create([
    //             'professor_id' => $professorId,
    //             'course_offering_id' => $request->course_offering_id,
    //             'session_id' => $request->session_id,
    //             'verified_date' => $today,
    //             'lat' => $request->lat,
    //             'lng' => $request->lng,
    //             'verified_at' => $now,
    //         ]);

    //         return response()->json([
    //             'success' => true,
    //             'already_checked_in' => false,
    //             'distance' => round($distance),
    //             'message' => 'ចុះវត្តមានបានសម្រេច!'
    //         ]);
    //     }

    /**
     * Verify professor's location and check-in (កែប្រែថ្មី)
     */
    public function verifyLocation(Request $request)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'session_id' => 'required|integer',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $offering = $this->attendanceSessions->ownedOffering($request->integer('course_offering_id'), $request->user()->id);
        $schedule = $this->attendanceSessions->scheduledToday($offering, $request->integer('session_id'));
        $this->attendanceSessions->assertWithinAttendanceWindow($schedule);

        $professorId = auth()->id();
        $now = Carbon::now('Asia/Phnom_Penh');
        $today = $now->toDateString();

        // === CHECK មុនគេថាបាន Check-in រួចហើយឬនៅ ===
        $existing = AttendanceProfessor::where([
            'professor_id' => $professorId,
            'course_offering_id' => $request->course_offering_id,
            'session_id' => $request->session_id,
        ])->whereDate('verified_at', $today)->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'already_checked_in' => true,
                'message' => 'លោកគ្រូបានចុះវត្តមានរួចរាល់ហើយ!',
            ]);
        }

        // គណនាចម្ងាយ
        $schoolLat = config('services.nmu.lat', env('NMU_LAT', 13.57952292));
        $schoolLng = config('services.nmu.lng', env('NMU_LNG', 102.92898894));
        $allowedRadius = config('services.nmu.radius', env('NMU_RADIUS', 100));

        $distance = $this->calculateDistance($request->lat, $request->lng, $schoolLat, $schoolLng);

        if ($distance > $allowedRadius) {
            return response()->json([
                'success' => false,
                'message' => 'លោកគ្រូនៅឆ្ងាយពីសាលាពេក! ចម្ងាយបច្ចុប្បន្ន៖ '.round($distance).' ម៉ែត្រ។',
            ], 403);
        }

        // បង្កើត Check-in តែលើកដំបូងប៉ុណ្ណោះ
        $attendance = AttendanceProfessor::create([
            'professor_id' => $professorId,
            'course_offering_id' => $request->course_offering_id,
            'session_id' => $request->session_id,
            'attendance_mode' => 'on_campus',
            'verified_date' => $today,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'verified_at' => $now,
        ]);

        if (Schema::hasTable('audit_logs')) {
            AuditLog::log([
                'action' => 'professor_attendance_recorded',
                'auditable_type' => get_class($attendance),
                'auditable_id' => $attendance->id,
                'new_values' => [
                    'course_offering_id' => $attendance->course_offering_id,
                    'attendance_mode' => $attendance->attendance_mode,
                    'session_id' => $attendance->session_id,
                ],
                'description' => 'Professor recorded on-campus attendance.',
            ]);
        }

        return response()->json([
            'success' => true,
            'already_checked_in' => false,
            'distance' => round($distance),
            'message' => 'ចុះវត្តមានបានសម្រេច!',
        ]);
    }

    /**
     * Precheck ដើម្បីមើលថាបាន Check-in រួចហើយឬនៅ
     */
    public function precheck(Request $request)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'session_id' => 'required|integer',
        ]);

        $offering = $this->attendanceSessions->ownedOffering($request->integer('course_offering_id'), $request->user()->id);
        $this->attendanceSessions->scheduledToday($offering, $request->integer('session_id'));

        $exists = AttendanceProfessor::where([
            'professor_id' => auth()->id(),
            'course_offering_id' => $request->course_offering_id,
            'session_id' => $request->session_id,
        ])->whereDate('verified_at', Carbon::now('Asia/Phnom_Penh')->toDateString())->exists();

        return response()->json(['checked_in' => $exists]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    // public function precheck(Request $request)
    // {
    //     $request->validate([
    //         'course_offering_id' => 'required|exists:course_offerings,id',
    //         'session_id' => 'required|integer',
    //     ]);

    //     $exists = AttendanceProfessor::where([
    //         'professor_id' => auth()->id(),
    //         'course_offering_id' => $request->course_offering_id,
    //         'verified_date' => Carbon::now('Asia/Phnom_Penh')->toDateString(),
    //         'session_id' => $request->session_id,
    //     ])->exists();

    //     return response()->json(['checked_in' => $exists]);
    // }

    /**
     * Display professor's attendance history
     */
    public function history(Request $request)
    {
        $query = AttendanceProfessor::with(['courseOffering.course', 'courseOffering.department'])
            ->where('professor_id', auth()->id());

        $semester = $request->input('semester');
        $academicYear = $request->input('academic_year');
        $dayType = $request->input('day_type');

        if ($semester) {
            $query->whereHas('courseOffering', function ($q) use ($semester) {
                $q->where('semester', $semester);
            });
        }

        if ($academicYear) {
            $query->whereHas('courseOffering', function ($q) use ($academicYear) {
                $q->where('academic_year', $academicYear);
            });
        }

        if ($dayType === 'weekend') {
            $query->whereHas('courseOffering.schedules', function ($q) {
                $q->whereIn('day_of_week', ['Saturday', 'Sunday', 'សៅរ៍', 'អាទិត្យ']);
            });
        } elseif ($dayType === 'weekday') {
            $query->whereHas('courseOffering.schedules', function ($q) {
                $q->whereNotIn('day_of_week', ['Saturday', 'Sunday', 'សៅរ៍', 'អាទិត្យ']);
            });
        }

        $attendances = $query->orderBy('verified_at', 'desc')->paginate(15);

        $attendanceAuditLogs = Schema::hasTable('audit_logs')
            ? AuditLog::where('auditable_type', AttendanceProfessor::class)
                ->whereIn('auditable_id', $attendances->getCollection()->pluck('id'))
                ->latest()
                ->get()
                ->unique('auditable_id')
                ->keyBy('auditable_id')
            : collect();

        return view('professor.attendance.history', compact('attendances', 'attendanceAuditLogs', 'semester', 'academicYear', 'dayType'));
    }

    /**
     * Export professor's attendance history to Excel
     */
    public function exportAttendance(Request $request)
    {
        $request->validate([
            'semester' => 'required|string',
            'academic_year' => 'required|string',
            'day_type' => 'required|in:weekend,weekday',
        ], [
            'semester.required' => 'សូមជ្រើសរើសឆមាស',
            'academic_year.required' => 'សូមជ្រើសរើសឆ្នាំសិក្សា',
            'day_type.required' => 'សូមជ្រើសរើសប្រភេទថ្ងៃ',
        ]);

        $semester = $request->input('semester');
        $academicYear = $request->input('academic_year');
        $dayType = $request->input('day_type');

        $query = AttendanceProfessor::with(['courseOffering.course', 'courseOffering.department'])
            ->where('professor_id', auth()->id());

        $query->whereHas('courseOffering', function ($q) use ($semester) {
            $q->where('semester', $semester);
        });

        $query->whereHas('courseOffering', function ($q) use ($academicYear) {
            $q->where('academic_year', $academicYear);
        });

        if ($dayType === 'weekend') {
            $query->whereHas('courseOffering.schedules', function ($q) {
                $q->whereIn('day_of_week', ['Saturday', 'Sunday', 'សៅរ៍', 'អាទិត្យ']);
            });
        } elseif ($dayType === 'weekday') {
            $query->whereHas('courseOffering.schedules', function ($q) {
                $q->whereNotIn('day_of_week', ['Saturday', 'Sunday', 'សៅរ៍', 'អាទិត្យ']);
            });
        }

        $attendances = $query->orderBy('verified_at', 'desc')->get();

        if (Schema::hasTable('audit_logs') && $attendances->isNotEmpty()) {
            $auditIps = AuditLog::where('auditable_type', AttendanceProfessor::class)
                ->whereIn('auditable_id', $attendances->pluck('id'))
                ->latest()
                ->get()
                ->unique('auditable_id')
                ->keyBy('auditable_id');

            $attendances->each(function ($attendance) use ($auditIps) {
                $attendance->audit_ip = $auditIps[$attendance->id]?->ip_address;
            });
        }

        $professorName = auth()->user()->name;

        $stats = [
            'total' => $attendances->count(),
        ];

        $fileName = 'Professor_Attendance_'.$professorName.'_'.$semester.'_'.$academicYear.'_'.$dayType.'.xlsx';
        $fileName = str_replace([' ', '/', '\\'], '_', $fileName);

        return Excel::download(
            new \App\Exports\ProfessorAttendanceExcelExport($attendances, $stats, $professorName, $semester, $academicYear, $dayType),
            $fileName
        );
    }
}
