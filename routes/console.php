<?php

use App\Models\Schedule as ClassSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    $botToken = env('TELEGRAM_BOT_TOKEN2');
    $todayName = Carbon::now()->format('l');

    // ទាញយកតែ User ណាដែលមាន Role ជា Professor និងមាន Telegram ID
    $users = User::where('role', 'professor')
        ->whereNotNull('telegram_chat_id')
        ->get();

    foreach ($users as $user) {
        $todaySchedules = ClassSchedule::with(['courseOffering.course', 'room'])
            ->whereHas('courseOffering', function ($query) use ($user) {
                $query->where('lecturer_user_id', $user->id);
            })
            ->where('day_of_week', $todayName)
            ->orderBy('start_time', 'asc')
            ->get();

        if ($todaySchedules->isNotEmpty()) {
            $message = '📅 <b>កាលវិភាគបង្រៀនថ្ងៃនេះ ('.$todayName.")</b>\n";
            $message .= 'សាស្ត្រាចារ្យ៖ <b>'.$user->name."</b>\n\n";

            foreach ($todaySchedules as $index => $item) {
                $num = $index + 1;
                $start = Carbon::parse($item->start_time)->format('h:i A');
                $end = Carbon::parse($item->end_time)->format('h:i A');

                $subject = $item->courseOffering->course->title_en ?? 'N/A';
                $room = $item->room->room_name ?? 'បន្ទប់ '.$item->room_number;

                $message .= "{$num}. <b>{$subject}</b>\n";
                $message .= "   ⏰ ម៉ោង: {$start} - {$end}\n";
                $message .= "   📍 បន្ទប់: {$room}\n";
                $message .= "--------------------------\n";
            }

            $message .= "\nសូមលោកគ្រូអញ្ជើញបង្រៀនដោយរីករាយ! 🙏";

            Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $user->telegram_chat_id,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);
        }
    }
})
    ->name('send-daily-professor-schedule') // បន្ថែមឈ្មោះដើម្បីកុំឱ្យ Error Overlapping
    ->dailyAt('07:00')
    ->timezone('Asia/Phnom_Penh')
    ->withoutOverlapping();
