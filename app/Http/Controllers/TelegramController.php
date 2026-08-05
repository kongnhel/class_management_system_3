<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $secret = config('services.telegram.webhook_secret');
        $providedSecret = (string) $request->header('X-Telegram-Bot-Api-Secret-Token');

        if (! $secret || ! hash_equals($secret, $providedSecret)) {
            abort(403);
        }

        $chatId = $request->input('message.chat.id');
        $text = $request->input('message.text');

        if (is_string($text) && preg_match('/^\/start\s+(\d+)$/', trim($text), $matches)) {
            $user = User::find((int) $matches[1]);
            if ($user) {
                $user->telegram_chat_id = $chatId;
                $user->save();

                $this->notifyTelegram($chatId, '✅ ការភ្ជាប់គណនីជោគជ័យ! អ្នកនឹងទទួលបានពិន្ទុតាមរយៈ Bot នេះ។');
            }
        }
    }

    private function sendReply($chatId, $message)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
        ]);
    }

    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            $users = User::whereNotNull('telegram_chat_id')->get();
            $botToken = env('TELEGRAM_BOT_TOKEN2');

            foreach ($users as $user) {
                $todaySchedules = \App\Models\Schedule::where('professor_id', $user->id)
                    ->whereDate('date', now())
                    ->orderBy('start_time', 'asc')
                    ->get();

                if ($todaySchedules->isNotEmpty()) {
                    $message = '📅 <b>ជម្រាបសួរលោកគ្រូ '.($user->profile->full_name_km ?? $user->name)."</b>\n";
                    $message .= "នេះគឺជាកាលវិភាគបង្រៀនរបស់លោកគ្រូសម្រាប់ថ្ងៃនេះ៖\n\n";

                    foreach ($todaySchedules as $index => $item) {
                        $num = $index + 1;
                        $message .= "{$num}. <b>{$item->subject_name}</b>\n";
                        $message .= "   ⏰ ម៉ោង: {$item->start_time} - {$item->end_time}\n";
                        $message .= "   📍 បន្ទប់: {$item->room_name}\n";
                        $message .= "--------------------------\n";
                    }

                    $message .= "\nសូមលោកគ្រូត្រៀមខ្លួនឱ្យបានរួចរាល់។ សូមអរគុណ!";

                    Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                        'chat_id' => $user->telegram_chat_id,
                        'text' => $message,
                        'parse_mode' => 'HTML',
                    ]);
                }
            }
        })->dailyAt('07:00');
    }
}
