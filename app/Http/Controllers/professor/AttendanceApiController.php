<?php

namespace App\Http\Controllers\professor;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\AttendanceQrToken;
use App\Models\AttendanceProfessor;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Schedule;
use App\Models\StudentCourseEnrollment;
use App\Services\AttendanceSessionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceApiController extends Controller
{
    public function __construct(private readonly AttendanceSessionService $attendanceSessions) {}

    public function startSession(Request $request)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'schedule_id' => 'required|integer|exists:schedules,id',
        ]);

        $offering = $this->attendanceSessions->ownedOffering($request->integer('course_offering_id'), $request->user()->id);
        $schedule = $this->attendanceSessions->scheduledToday($offering, $request->integer('schedule_id'));
        $this->attendanceSessions->assertWithinAttendanceWindow($schedule);

        $now = Carbon::now('Asia/Phnom_Penh');
        $session = DB::transaction(function () use ($offering, $schedule, $request, $now) {
            $session = AttendanceSession::where('course_offering_id', $offering->id)
                ->whereDate('attendance_date', $now->toDateString())
                ->lockForUpdate()
                ->first();

            if ($session && $session->closed_at) {
                abort(422, 'Attendance for this course has already been closed today.');
            }

            return $session ?? AttendanceSession::create([
                'course_offering_id' => $offering->id,
                'schedule_id' => $schedule->id,
                'professor_id' => $request->user()->id,
                'attendance_date' => $now->toDateString(),
                'started_at' => $now,
            ]);
        });

        $token = $this->replaceToken($session);

        $qrSvg = (string) QrCode::size(300)
            ->margin(2)
            ->generate($token->token_code);

        $offering->loadMissing('course');
        $courseName = $offering->course?->title_en ?? 'N/A';

        return response()->json([
            'success' => true,
            'qr_svg' => $qrSvg,
            'course_name' => $courseName,
            'session_id' => $session->id,
            'expires_at' => $token->expires_at->toISOString(),
            'expires_in' => 15,
        ]);
    }

    public function onlineCheckin(Request $request)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'schedule_id' => 'required|integer|exists:schedules,id',
        ]);

        $offering = $this->attendanceSessions->ownedOffering($request->integer('course_offering_id'), $request->user()->id);
        $schedule = $this->attendanceSessions->scheduledToday($offering, $request->integer('schedule_id'));
        $this->attendanceSessions->assertWithinAttendanceWindow($schedule);

        $now = Carbon::now('Asia/Phnom_Penh');
        $rawToken = Str::random(64);
        $expiresAt = $now->copy()->addMinutes(30);

        $result = DB::transaction(function () use ($offering, $schedule, $request, $now, $rawToken, $expiresAt) {
            $session = AttendanceSession::where('course_offering_id', $offering->id)
                ->whereDate('attendance_date', $now->toDateString())
                ->lockForUpdate()
                ->first();

            if ($session && $session->closed_at) {
                abort(422, 'Attendance for this course has already been closed today.');
            }

            $session ??= AttendanceSession::create([
                'course_offering_id' => $offering->id,
                'schedule_id' => $schedule->id,
                'professor_id' => $request->user()->id,
                'attendance_date' => $now->toDateString(),
                'started_at' => $now,
            ]);

            if ($session->professor_id !== $request->user()->id || $session->schedule_id !== $schedule->id) {
                abort(403, 'This attendance session is not available to you.');
            }

            $alreadyCheckedIn = AttendanceProfessor::where('professor_id', $request->user()->id)
                ->where('course_offering_id', $offering->id)
                ->where('session_id', $session->id)
                ->whereDate('verified_at', $now->toDateString())
                ->exists();

            if (! $alreadyCheckedIn) {
                $attendance = AttendanceProfessor::create([
                    'professor_id' => $request->user()->id,
                    'course_offering_id' => $offering->id,
                    'session_id' => $session->id,
                    'attendance_mode' => 'online',
                    'verified_date' => $now->toDateString(),
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
                        'description' => 'Professor recorded online attendance.',
                    ]);
                }
            }

            $session->update([
                'online_token_hash' => hash('sha256', $rawToken),
                'online_token_expires_at' => $expiresAt,
            ]);

            return [$session, $alreadyCheckedIn];
        });

        return response()->json([
            'success' => true,
            'already_checked_in' => $result[1],
            'session_id' => $result[0]->id,
            'token' => $rawToken,
            'expires_at' => $expiresAt->toISOString(),
            'expires_in' => 30 * 60,
            'mode' => 'online',
        ]);
    }

    public function refreshQr(Request $request)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'schedule_id' => 'required|integer|exists:schedules,id',
        ]);

        $offering = $this->attendanceSessions->ownedOffering($request->integer('course_offering_id'), $request->user()->id);
        $schedule = $this->attendanceSessions->scheduledToday($offering, $request->integer('schedule_id'));
        $this->attendanceSessions->assertWithinAttendanceWindow($schedule);
        $session = AttendanceSession::where('course_offering_id', $offering->id)
            ->whereDate('attendance_date', Carbon::now('Asia/Phnom_Penh')->toDateString())
            ->whereNull('closed_at')
            ->firstOrFail();

        if ($session->schedule_id !== $schedule->id || $session->professor_id !== $request->user()->id) {
            abort(403, 'This attendance session is not available to you.');
        }

        $token = $this->replaceToken($session);

        $qrSvg = (string) QrCode::size(300)
            ->margin(2)
            ->generate($token->token_code);

        return response()->json([
            'success' => true,
            'qr_svg' => $qrSvg,
            'expires_at' => $token->expires_at->toISOString(),
            'expires_in' => 15,
        ]);
    }

    public function getStudents(Request $request, $courseOfferingId)
    {
        $this->attendanceSessions->ownedOffering((int) $courseOfferingId, $request->user()->id);
        $today = Carbon::now('Asia/Phnom_Penh')->toDateString();

        $attendances = AttendanceRecord::where('course_offering_id', $courseOfferingId)
            ->whereDate('date', $today)
            ->with('student')
            ->with('student.studentProfile')
            ->with('student.profile')
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function ($record) {
                return $record->student !== null;
            })
            ->values()
            ->map(function ($record) {
                $studentProfilePic = $record->student->studentProfile?->profile_picture_url ?? null;
                $userProfilePic = $record->student->profile?->profile_picture_url ?? null;
                $avatarPic = $record->student->avatar ?? null;
                $profilePic = null;
                foreach ([$studentProfilePic, $userProfilePic, $avatarPic] as $pic) {
                    if (! empty($pic) && $pic !== 'null') {
                        $profilePic = $pic;
                        break;
                    }
                }

                $remarks = trim((string) $record->remarks);
                $source = $record->source;
                if (! $source) {
                    $source = strcasecmp($remarks, 'System Auto-Absent') === 0
                        ? 'system'
                        : (in_array(strtolower($remarks), ['qr scan', 'qr card scan'], true) ? 'qr' : 'manual');
                }

                return [
                    'id' => $record->id,
                    'status' => $record->status,
                    'name' => $record->student->studentProfile?->full_name_km ?? $record->student->profile?->full_name_km ?? $record->student->name ?? 'N/A',
                    'student_code' => $record->student->student_id_code ?? '',
                    'profile_pic' => $profilePic,
                    'initial' => mb_substr($record->student->studentProfile?->full_name_km ?? $record->student->profile?->full_name_km ?? $record->student->name ?? 'N', 0, 1),
                    'time' => $record->created_at->format('H:i:s'),
                    'source' => $source,
                    'remarks' => $record->remarks ?? '',
                ];
            });

        $totalEnrolled = StudentCourseEnrollment::where('course_offering_id', $courseOfferingId)->count();

        return response()->json([
            'success' => true,
            'attendances' => $attendances,
            'total_enrolled' => $totalEnrolled,
            'counts' => [
                'present' => $attendances->where('status', 'present')->count(),
                'late' => $attendances->where('status', 'late')->count(),
                'permission' => $attendances->where('status', 'permission')->count(),
                'absent' => $attendances->where('status', 'absent')->count(),
                'manual' => $attendances->where('source', 'manual')->count(),
                'qr' => $attendances->whereIn('source', ['qr', 'qr_card'])->count(),
            ],
        ]);
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
        ]);

        $offering = $this->attendanceSessions->ownedOffering($request->integer('course_offering_id'), $request->user()->id);
        $now = Carbon::now('Asia/Phnom_Penh');
        $todayName = $now->format('l');

        $schedule = Schedule::where('course_offering_id', $offering->id)
            ->where('day_of_week', $todayName)
            ->first();

        if (! $schedule) {
            return response()->json([
                'available' => false,
                'status' => 'no_schedule',
                'message' => 'មិនមានកាលវិភាគសម្រាប់ថ្ងៃនេះ។',
            ]);
        }

        $startTime = Carbon::parse($schedule->start_time, 'Asia/Phnom_Penh');
        $endTime = Carbon::parse($schedule->end_time, 'Asia/Phnom_Penh');
        $windowStart = $startTime->copy()->subMinutes(5);
        $windowEnd = $endTime->copy()->addMinutes(10);

        if ($now->lt($windowStart)) {
            $minutesUntil = $now->diffInMinutes($windowStart);

            return response()->json([
                'available' => false,
                'status' => 'not_started',
                'message' => "ការស្កែននឹងចាប់ផ្តើមនៅពេលវេលាជាក់លាក់។ សូមរង់ចាំ {$minutesUntil} នាទីទៀត។",
                'schedule' => [
                    'start_time' => $startTime->format('h:i A'),
                    'end_time' => $endTime->format('h:i A'),
                    'start_minutes' => $minutesUntil,
                ],
            ]);
        }

        if ($now->gt($windowEnd)) {
            return response()->json([
                'available' => false,
                'status' => 'ended',
                'message' => 'ការស្កែនបានបញ្ចប់ហើយ។ ម៉ោងកាលវិភាគចប់។',
                'schedule' => [
                    'start_time' => $startTime->format('h:i A'),
                    'end_time' => $endTime->format('h:i A'),
                ],
            ]);
        }

        return response()->json([
            'available' => true,
            'status' => 'active',
            'message' => 'ការស្កែនកំពុងដំណើរការ។',
            'schedule' => [
                'start_time' => $startTime->format('h:i A'),
                'end_time' => $endTime->format('h:i A'),
                'minutes_remaining' => $now->diffInMinutes($windowEnd),
            ],
        ]);
    }

    public function closeSession(Request $request)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
        ]);

        $offering = $this->attendanceSessions->ownedOffering($request->integer('course_offering_id'), $request->user()->id);
        $today = Carbon::now('Asia/Phnom_Penh')->toDateString();

        $absentCount = DB::transaction(function () use ($offering, $today, $request) {
            $session = AttendanceSession::where('course_offering_id', $offering->id)
                ->whereDate('attendance_date', $today)
                ->lockForUpdate()
                ->firstOrFail();

            if ($session->professor_id !== $request->user()->id) {
                abort(403, 'This attendance session is not available to you.');
            }
            if ($session->closed_at) {
                abort(422, 'Attendance has already been closed.');
            }

            $enrolledIds = StudentCourseEnrollment::where('course_offering_id', $offering->id)->pluck('student_user_id');
            $recordedIds = AttendanceRecord::where('course_offering_id', $offering->id)
                ->whereDate('date', $today)
                ->pluck('student_user_id');
            $absentIds = $enrolledIds->diff($recordedIds);
            $timestamp = now();
            $rows = $absentIds->map(fn ($studentId) => [
                'student_user_id' => $studentId,
                'user_id' => $studentId,
                'course_offering_id' => $offering->id,
                'date' => $today,
                'status' => 'absent',
                'source' => 'system',
                'remarks' => 'System Auto-Absent',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ])->all();

            if ($rows) {
                AttendanceRecord::insert($rows);
            }

            $session->update(['closed_at' => Carbon::now('Asia/Phnom_Penh')]);
            AttendanceQrToken::where('attendance_session_id', $session->id)->delete();

            \Illuminate\Support\Facades\Cache::forget('nmu.att_scores.'.$offering->id);

            return count($rows);
        });

        return response()->json([
            'success' => true,
            'message' => "ការស្រង់វត្តមានត្រូវបានបញ្ចប់! សិស្ស $absentCount នាក់ត្រូវបានដាក់ថាអវត្តមាន។",
            'absent_count' => $absentCount,
        ]);
    }

    private function replaceToken(AttendanceSession $session): AttendanceQrToken
    {
        AttendanceQrToken::where('attendance_session_id', $session->id)->delete();

        return AttendanceQrToken::create([
            'course_offering_id' => $session->course_offering_id,
            'attendance_session_id' => $session->id,
            'token_code' => Str::random(40),
            'expires_at' => now()->addSeconds(15),
        ]);
    }
}
