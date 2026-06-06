<?php

namespace App\Http\Controllers;

use App\Models\AttendanceQrToken;
use App\Models\AttendanceRecord;
use App\Models\StudentCourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function processScan(Request $request)
    {
        $request->validate(['token' => 'required|string']);
        $user = Auth::user();

        $qrData = AttendanceQrToken::where('token_code', $request->token)->first();

        if (! $qrData) {
            return response()->json(['success' => false, 'message' => 'QR Code មិនត្រឹមត្រូវ!']);
        }
        if (now()->greaterThan($qrData->expires_at)) {
            return response()->json(['success' => false, 'message' => 'QR Code ផុតកំណត់ហើយ!']);
        }
        $isEnrolled = StudentCourseEnrollment::where('student_user_id', $user->id)
            ->where('course_offering_id', $qrData->course_offering_id)
            ->exists();

        if (! $isEnrolled) {
            return response()->json(['success' => false, 'message' => 'បងគ្មានឈ្មោះក្នុងថ្នាក់នេះទេ!']);
        }

        $alreadyChecked = AttendanceRecord::where('student_user_id', $user->id)
            ->where('course_offering_id', $qrData->course_offering_id)
            ->where('date', now()->toDateString())
            ->exists();

        if ($alreadyChecked) {
            return response()->json(['success' => false, 'message' => 'បងបានស្កែនរួចរាល់ហើយ!']);
        }

        AttendanceRecord::create([
            'student_user_id' => $user->id,
            'user_id' => $user->id,
            'course_offering_id' => $qrData->course_offering_id,
            'date' => now()->toDateString(),
            'status' => 'present',
            'remarks' => 'QR Scan',
        ]);

        return response()->json(['success' => true, 'message' => 'វត្តមានត្រូវបានកត់ត្រា!']);
    }

    public function closeAttendance($courseOfferingId)
    {
        $today = now()->toDateString();

        $enrolledStudents = \App\Models\StudentCourseEnrollment::where('course_offering_id', $courseOfferingId)
            ->pluck('student_user_id');

        $presentStudents = \App\Models\AttendanceRecord::where('course_offering_id', $courseOfferingId)
            ->where('date', $today)
            ->pluck('student_user_id');

        $absentStudents = $enrolledStudents->diff($presentStudents);

        foreach ($absentStudents as $studentId) {
            \App\Models\AttendanceRecord::create([
                'student_user_id' => $studentId,
                'user_id' => $studentId,
                'course_offering_id' => $courseOfferingId,
                'date' => $today,
                'status' => 'absent',
                'remarks' => 'Auto-generated (No Scan)',
            ]);
        }

        return back()->with('success', 'បញ្ជីវត្តមានត្រូវបានបិទ! អ្នកមិនបានស្កែនត្រូវបានដាក់ថា អវត្តមាន។');
    }
}
