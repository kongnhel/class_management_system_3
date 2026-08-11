<?php

use App\Models\Schedule as ClassSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    $botToken = config('services.telegram.schedule_bot_token');
    $todayName = Carbon::now('Asia/Phnom_Penh')->format('l');

    if (! filled($botToken)) {
        Log::error('Professor schedule Telegram notification skipped: schedule bot token is missing.');

        return;
    }

    $professors = User::query()
        ->where('role', 'professor')
        ->whereNotNull('telegram_chat_id')
        ->where('telegram_chat_id', '<>', '')
        ->get();

    foreach ($professors as $professor) {
        $todaySchedules = ClassSchedule::with(['courseOffering.course', 'room'])
            ->whereHas('courseOffering', function ($query) use ($professor) {
                $query->where('lecturer_user_id', $professor->id);
            })
            ->where('day_of_week', $todayName)
            ->orderBy('start_time')
            ->get();

        if ($todaySchedules->isEmpty()) {
            continue;
        }

        $professorName = htmlspecialchars((string) $professor->name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $message = "📅 <b>Today's teaching schedule ({$todayName})</b>\n";
        $message .= "Professor: <b>{$professorName}</b>\n\n";

        foreach ($todaySchedules as $index => $schedule) {
            $start = Carbon::parse($schedule->start_time)->format('h:i A');
            $end = $schedule->end_time
                ? Carbon::parse($schedule->end_time)->format('h:i A')
                : '—';
            $subject = htmlspecialchars(
                (string) ($schedule->courseOffering?->course?->title_en
                    ?? $schedule->courseOffering?->course?->title_km
                    ?? 'N/A'),
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );
            $room = htmlspecialchars((string) ($schedule->room?->room_number ?? 'N/A'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

            $message .= ($index + 1).". <b>{$subject}</b>\n";
            $message .= "   ⏰ Time: {$start} - {$end}\n";
            $message .= "   📍 Room: {$room}\n";
            $message .= "--------------------------\n";
        }

        $message .= "\nPlease prepare for today's classes. 🙏";

        try {
            $response = Http::timeout(15)
                ->retry(2, 500)
                ->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $professor->telegram_chat_id,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ]);

            if (! $response->successful()) {
                Log::warning('Professor schedule Telegram notification failed.', [
                    'user_id' => $professor->id,
                    'chat_id' => $professor->telegram_chat_id,
                    'status' => $response->status(),
                    'response' => $response->json() ?? $response->body(),
                ]);
            }
        } catch (Throwable $exception) {
            Log::error('Professor schedule Telegram notification threw an exception.', [
                'user_id' => $professor->id,
                'chat_id' => $professor->telegram_chat_id,
                'message' => $exception->getMessage(),
            ]);
        }
    }
})
    ->name('send-daily-professor-schedule')
    ->dailyAt('07:00')
    ->timezone('Asia/Phnom_Penh');
