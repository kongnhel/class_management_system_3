<?php

namespace App\Services;

use App\Models\CourseOffering;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class AttendanceSessionService
{
    public function ownedOffering(int $offeringId, int $professorId): CourseOffering
    {
        $offering = CourseOffering::findOrFail($offeringId);

        if ((int) $offering->lecturer_user_id !== $professorId) {
            throw new AuthorizationException('You are not assigned to this course offering.');
        }

        return $offering;
    }

    public function scheduledToday(CourseOffering $offering, int $scheduleId): Schedule
    {
        $schedule = $offering->schedules()->findOrFail($scheduleId);
        $today = Carbon::now('Asia/Phnom_Penh')->format('l');

        if ($schedule->day_of_week !== $today) {
            throw ValidationException::withMessages([
                'schedule_id' => 'This schedule is not scheduled for today.',
            ]);
        }

        return $schedule;
    }

    public function assertWithinAttendanceWindow(Schedule $schedule): void
    {
        $now = Carbon::now('Asia/Phnom_Penh');
        $start = Carbon::parse($schedule->start_time, 'Asia/Phnom_Penh');
        $end = Carbon::parse($schedule->end_time, 'Asia/Phnom_Penh');
        $windowStart = $start->copy()->subMinutes(5);
        $windowEnd = $end->copy()->addMinutes(10);

        if ($now->lt($windowStart) || $now->gt($windowEnd)) {
            throw ValidationException::withMessages([
                'schedule_id' => 'Attendance is only available from 5 minutes before class until 10 minutes after it ends.',
            ]);
        }
    }
}
