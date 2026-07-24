<?php

namespace App\Livewire\Teacher;

use App\Models\AttendanceQrToken;
use App\Models\AttendanceRecord;
use App\Models\Schedule;
use App\Models\StudentCourseEnrollment;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceModal extends Component
{
    public $isOpen = false;

    public $courseId;

    public $qrCodeImage;

    public $showConfirmation = false;

    public $scheduleInfo = null;

    public $scanStatus = 'loading';

    protected $listeners = ['openAttendanceModal' => 'open'];

    public function open($courseOfferingId)
    {
        $this->courseId = $courseOfferingId;
        $this->isOpen = true;
        $this->checkScheduleWindow();

        if ($this->scanStatus === 'active') {
            $this->generateToken();
        }
    }

    public function close()
    {
        $this->isOpen = false;
        $this->courseId = null;
        $this->qrCodeImage = null;
        $this->scheduleInfo = null;
        $this->scanStatus = 'loading';
    }

    public function checkScheduleWindow()
    {
        if (! $this->courseId) {
            $this->scanStatus = 'no_schedule';
            return;
        }

        $now = Carbon::now('Asia/Phnom_Penh');
        $todayName = $now->format('l');

        $schedule = Schedule::where('course_offering_id', $this->courseId)
            ->where('day_of_week', $todayName)
            ->first();

        if (! $schedule) {
            $this->scanStatus = 'no_schedule';
            $this->scheduleInfo = null;
            return;
        }

        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);
        $windowStart = $startTime->copy()->subMinutes(5);
        $windowEnd = $endTime->copy()->addMinutes(10);

        $this->scheduleInfo = [
            'start_time' => $startTime->format('h:i A'),
            'end_time' => $endTime->format('h:i A'),
            'start_hour' => $startTime->format('H:i'),
            'end_hour' => $endTime->format('H:i'),
        ];

        if ($now->lt($windowStart)) {
            $this->scanStatus = 'not_started';
            $this->scheduleInfo['minutes_until'] = $now->diffInMinutes($windowStart);
        } elseif ($now->gt($windowEnd)) {
            $this->scanStatus = 'ended';
        } else {
            $this->scanStatus = 'active';
            $this->scheduleInfo['minutes_remaining'] = $now->diffInMinutes($windowEnd);
        }
    }

    public function generateToken()
    {
        if ($this->scanStatus !== 'active' || ! $this->courseId) {
            return;
        }

        AttendanceQrToken::where('course_offering_id', $this->courseId)->delete();

        $token = Str::random(40);

        AttendanceQrToken::create([
            'course_offering_id' => $this->courseId,
            'token_code' => $token,
            'expires_at' => now()->addSeconds(30),
        ]);

        $this->qrCodeImage = (string) QrCode::size(300)
            ->margin(2)
            ->generate($token);
    }

    public function closeAttendance()
    {
        if (! $this->courseId) {
            return;
        }

        $today = now()->toDateString();

        $enrolledStudents = StudentCourseEnrollment::where('course_offering_id', $this->courseId)
            ->pluck('student_user_id');

        $absentCount = 0;

        foreach ($enrolledStudents as $studentId) {
            $hasRecord = AttendanceRecord::where('student_user_id', $studentId)
                ->where('course_offering_id', $this->courseId)
                ->where('date', $today)
                ->exists();

            if (! $hasRecord) {
                AttendanceRecord::create([
                    'student_user_id' => $studentId,
                    'user_id' => $studentId,
                    'course_offering_id' => $this->courseId,
                    'date' => $today,
                    'status' => 'absent',
                    'remarks' => 'System Auto-Absent',
                ]);
                $absentCount++;
            }
        }

        AttendanceQrToken::where('course_offering_id', $this->courseId)->delete();
        $this->showConfirmation = false;

        $this->isOpen = false;

        session()->flash('success', "ការស្រង់វត្តមានត្រូវបានបញ្ចប់! សិស្ស $absentCount នាក់ត្រូវបានដាក់ថាអវត្តមាន។");

        $this->dispatch('attendanceClosed');
    }

    public function render()
    {
        $courseName = '...';

        if ($this->courseId) {
            $courseOffering = \App\Models\CourseOffering::with('course')->find($this->courseId);
            $courseName = $courseOffering ? ($courseOffering->course->title_en ?? 'N/A') : 'N/A';
        }

        if ($this->isOpen && $this->courseId) {
            $this->checkScheduleWindow();

            if ($this->scanStatus === 'active') {
                $latestToken = AttendanceQrToken::where('course_offering_id', $this->courseId)
                    ->latest()
                    ->first();

                if (! $latestToken || now()->greaterThan($latestToken->expires_at)) {
                    $this->generateToken();
                } elseif (! $this->qrCodeImage) {
                    $this->qrCodeImage = (string) QrCode::size(300)
                        ->margin(2)
                        ->generate($latestToken->token_code);
                }
            } else {
                $this->qrCodeImage = null;
                AttendanceQrToken::where('course_offering_id', $this->courseId)->delete();
            }
        }

        $attendances = [];
        if ($this->isOpen && $this->courseId) {
            $attendances = AttendanceRecord::where('course_offering_id', $this->courseId)
                ->where('date', now()->toDateString())
                ->with('student')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('professor.attendance.attendance-modal', [
            'attendances' => $attendances,
            'courseName' => $courseName,
        ]);
    }
}
