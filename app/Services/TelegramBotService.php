<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    public function send(string|int $chatId, string $message, ?string $token = null): bool
    {
        $token ??= config('services.telegram.bot_token');

        if (! filled($token) || ! filled($chatId)) {
            return false;
        }

        try {
            $response = Http::timeout(15)
                ->retry(2, 500)
                ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ]);

            if (! $response->successful()) {
                Log::warning('Telegram message failed to send.', [
                    'status' => $response->status(),
                    'telegram_description' => $response->json('description'),
                ]);
            }

            return $response->successful();
        } catch (ConnectionException $exception) {
            Log::error('Telegram request could not connect.', ['message' => $exception->getMessage()]);

            return false;
        }
    }
}
