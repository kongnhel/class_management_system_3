<?php

namespace App\Http\Controllers\professor;

use App\Http\Controllers\Controller;
use App\Models\AttendanceQrToken;
use App\Models\AttendanceRecord;
use App\Models\CourseOffering;
use App\Models\Schedule;
use App\Models\StudentCourseEnrollment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceApiController extends Controller
{
    public function startSession(Request $request)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
        ]);

        $courseOfferingId = $request->course_offering_id;

        AttendanceQrToken::where('course_offering_id', $courseOfferingId)->delete();

        $token = Str::random(40);

        AttendanceQrToken::create([
            'course_offering_id' => $courseOfferingId,
            'token_code' => $token,
            'expires_at' => now()->addSeconds(15),
        ]);

        $qrSvg = (string) QrCode::size(300)
            ->margin(2)
            ->generate($token);

        $courseOffering = CourseOffering::with('course')->find($courseOfferingId);
        $courseName = $courseOffering ? ($courseOffering->course->title_en ?? 'N/A') : 'N/A';

        return response()->json([
            'success' => true,
            'qr_svg' => $qrSvg,
            'course_name' => $courseName,
            'expires_at' => now()->addSeconds(15)->toISOString(),
        ]);
    }

    public function refreshQr(Request $request)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
        ]);

        $courseOfferingId = $request->course_offering_id;

        AttendanceQrToken::where('course_offering_id', $courseOfferingId)->delete();

        $token = Str::random(40);

        AttendanceQrToken::create([
            'course_offering_id' => $courseOfferingId,
            'token_code' => $token,
            'expires_at' => now()->addSeconds(15),
        ]);

        $qrSvg = (string) QrCode::size(300)
            ->margin(2)
            ->generate($token);

        return response()->json([
            'success' => true,
            'qr_svg' => $qrSvg,
            'expires_at' => now()->addSeconds(15)->toISOString(),
        ]);
    }

    public function getStudents(Request $request, $courseOfferingId)
    {
        $attendances = AttendanceRecord::where('course_offering_id', $courseOfferingId)
            ->where('date', now()->toDateString())
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
                    if (!empty($pic) && $pic !== 'null') { $profilePic = $pic; break; }
                }

                return [
                    'id' => $record->id,
                    'status' => $record->status,
                    'name' => $record->student->studentProfile?->full_name_km ?? $record->student->profile?->full_name_km ?? $record->student->name ?? 'N/A',
                    'student_code' => $record->student->student_id_code ?? '',
                    'profile_pic' => $profilePic,
                    'initial' => mb_substr($record->student->studentProfile?->full_name_km ?? $record->student->profile?->full_name_km ?? $record->student->name ?? 'N', 0, 1),
                    'time' => $record->created_at->format('H:i:s'),
                ];
            });

        $totalEnrolled = StudentCourseEnrollment::where('course_offering_id', $courseOfferingId)->count();

            return response()->json([
            'success' => true,
            'attendances' => $attendances,
            'total_enrolled' => $totalEnrolled,
            'counts' => [
                'present' => $attendances->where('status', 'present')->count(),
                'permission' => $attendances->where('status', 'permission')->count(),
            ],
        ]);
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
        ]);

        $courseOfferingId = $request->course_offering_id;
        $now = Carbon::now('Asia/Phnom_Penh');
        $todayName = $now->format('l');

        $schedule = Schedule::where('course_offering_id', $courseOfferingId)
            ->where('day_of_week', $todayName)
            ->first();

        if (! $schedule) {
            return response()->json([
                'available' => false,
                'status' => 'no_schedule',
                'message' => 'មិនមានកាលវិភាគសម្រាប់ថ្ងៃនេះ។',
            ]);
        }

        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);
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

        $courseOfferingId = $request->course_offering_id;
        $today = now()->toDateString();

        $enrolledStudents = StudentCourseEnrollment::where('course_offering_id', $courseOfferingId)
            ->pluck('student_user_id');

        $absentCount = 0;

        foreach ($enrolledStudents as $studentId) {
            $hasRecord = AttendanceRecord::where('student_user_id', $studentId)
                ->where('course_offering_id', $courseOfferingId)
                ->where('date', $today)
                ->exists();

            if (! $hasRecord) {
                AttendanceRecord::create([
                    'student_user_id' => $studentId,
                    'user_id' => $studentId,
                    'course_offering_id' => $courseOfferingId,
                    'date' => $today,
                    'status' => 'absent',
                    'remarks' => 'System Auto-Absent',
                ]);
                $absentCount++;
            }
        }

        AttendanceQrToken::where('course_offering_id', $courseOfferingId)->delete();

        return response()->json([
            'success' => true,
            'message' => "ការស្រង់វត្តមានត្រូវបានបញ្ចប់! សិស្ស $absentCount នាក់ត្រូវបានដាក់ថាអវត្តមាន។",
            'absent_count' => $absentCount,
        ]);
    }
}
