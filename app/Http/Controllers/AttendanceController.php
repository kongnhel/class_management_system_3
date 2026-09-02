<?php

namespace App\Http\Controllers;

use App\Models\AttendanceCard;
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
                'source' => 'qr',
                'remarks' => 'QR Scan',
            ]);

            return ['success' => true, 'message' => 'Attendance recorded successfully.'];
        });

        return response()->json($result);
    }

    public function processCardScan(Request $request)
    {
        $request->validate([
            'card_token' => 'required|string',
            'session_id' => 'required|integer|exists:attendance_sessions,id',
        ]);

        $result = DB::transaction(function () use ($request) {
            $token = trim($request->string('card_token')->toString());
            $token = str_starts_with($token, 'NMU-ATTENDANCE-CARD:')
                ? substr($token, strlen('NMU-ATTENDANCE-CARD:'))
                : $token;

            $card = AttendanceCard::where('token_hash', hash('sha256', $token))
                ->whereNull('revoked_at')
                ->with('user')
                ->first();

            if (! $card || ! $card->user || $card->user->role !== 'student') {
                return ['success' => false, 'message' => 'Invalid or revoked attendance card.'];
            }

            $session = AttendanceSession::where('id', $request->integer('session_id'))
                ->where('professor_id', $request->user()->id)
                ->whereNull('closed_at')
                ->with('courseOffering')
                ->lockForUpdate()
                ->first();

            if (! $session || $session->courseOffering?->lecturer_user_id !== $request->user()->id) {
                return ['success' => false, 'message' => 'This attendance session is not available to you.'];
            }

            $now = Carbon::now('Asia/Phnom_Penh');
            if ($now->toDateString() !== $session->attendance_date->toDateString()
                || $now->greaterThan($session->started_at->copy()->addHours(2))) {
                return ['success' => false, 'message' => 'This attendance session is not active.'];
            }

            $student = $card->user;
            $enrolled = StudentCourseEnrollment::where('student_user_id', $student->id)
                ->where('course_offering_id', $session->course_offering_id)
                ->exists();

            if (! $enrolled) {
                return ['success' => false, 'message' => 'This student is not enrolled in this class.'];
            }

            $alreadyChecked = AttendanceRecord::where('student_user_id', $student->id)
                ->where('course_offering_id', $session->course_offering_id)
                ->whereDate('date', $session->attendance_date)
                ->exists();

            if ($alreadyChecked) {
                return ['success' => false, 'message' => 'This student has already checked in today.'];
            }

            AttendanceRecord::create([
                'student_user_id' => $student->id,
                'user_id' => $student->id,
                'course_offering_id' => $session->course_offering_id,
                'date' => $session->attendance_date,
                'status' => 'present',
                'source' => 'qr_card',
                'remarks' => 'QR Card Scan',
            ]);

            $card->update(['last_used_at' => now()]);

            return [
                'success' => true,
                'message' => 'Attendance recorded successfully.',
                'student' => [
                    'id' => $student->id,
                    'name' => $student->studentProfile?->full_name_km ?? $student->name,
                    'student_code' => $student->student_id_code,
                ],
            ];
        });

        return response()->json($result);
    }
}
