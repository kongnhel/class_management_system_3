<?php

namespace App\Http\Controllers\professor;

use App\Http\Controllers\Controller;
use App\Models\AttendanceQrToken;
use App\Models\AttendanceRecord;
use App\Models\CourseOffering;
use App\Models\StudentCourseEnrollment;
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
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function ($record) {
                return $record->student !== null;
            })
            ->values()
            ->map(function ($record) {
                $profile = $record->student->profile ?? null;
                $pic = $profile?->profile_picture_url ?? null;
                $av = $record->student->avatar ?? null;
                $profilePic = (!empty($pic) && $pic !== 'null') ? $pic : ((!empty($av) && $av !== 'null') ? $av : null);

                return [
                    'id' => $record->id,
                    'status' => $record->status,
                    'name' => $profile->full_name_km ?? $record->student->name ?? 'N/A',
                    'student_code' => $record->student->student_id_code ?? '',
                    'profile_pic' => $profilePic,
                    'initial' => mb_substr($profile->full_name_km ?? $record->student->name ?? 'N', 0, 1),
                    'time' => $record->created_at->format('h:i:s A'),
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
