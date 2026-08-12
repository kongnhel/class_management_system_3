<?php

namespace App\Http\Controllers;

use App\Models\AttendanceQrToken;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\StudentCourseEnrollment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function processScan(Request $request)
    {
        $request->validate(['token' => 'required|string']);
        $user = Auth::user();

        $result = DB::transaction(function () use ($request, $user) {
            $qrToken = AttendanceQrToken::where('token_code', $request->token)->first();

            if (! $qrToken || ! $qrToken->attendance_session_id) {
                return ['success' => false, 'message' => 'Invalid QR code.'];
            }
            if (Carbon::now('Asia/Phnom_Penh')->greaterThan($qrToken->expires_at)) {
                return ['success' => false, 'message' => 'This QR code has expired.'];
            }

            $session = AttendanceSession::lockForUpdate()->find($qrToken->attendance_session_id);
            if (! $session || $session->closed_at) {
                return ['success' => false, 'message' => 'This attendance session is closed.'];
            }

            $isEnrolled = StudentCourseEnrollment::where('student_user_id', $user->id)
                ->where('course_offering_id', $session->course_offering_id)
                ->exists();
            if (! $isEnrolled) {
                return ['success' => false, 'message' => 'You are not enrolled in this class.'];
            }

            $alreadyChecked = AttendanceRecord::where('student_user_id', $user->id)
                ->where('course_offering_id', $session->course_offering_id)
                ->whereDate('date', $session->attendance_date)
                ->exists();
            if ($alreadyChecked) {
                return ['success' => false, 'message' => 'You have already checked in today.'];
            }

            AttendanceRecord::create([
                'student_user_id' => $user->id,
                'user_id' => $user->id,
                'course_offering_id' => $session->course_offering_id,
                'date' => $session->attendance_date,
                'status' => 'present',
                'remarks' => 'QR Scan',
            ]);

            return ['success' => true, 'message' => 'Attendance recorded successfully.'];
        });

        return response()->json($result);
    }
}
