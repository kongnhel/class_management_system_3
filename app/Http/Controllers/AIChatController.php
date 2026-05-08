<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AIChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $user = auth()->user();

        // Validation
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'option'  => 'nullable|in:info,process'
        ]);

        $userMessage = $validated['message'];
        $chatOption  = $validated['option'] ?? 'info';

        // Rate Limiting (ប្រើ User ID ឬ IP សម្រាប់ Guest)
        $rateLimitKey = $user 
            ? "ai-chat:user-" . $user->id 
            : "ai-chat:ip-" . $request->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 12)) {
            return response()->json([
                'message' => 'បងផ្ញើសារញឹកញាប់ពេកហើយ! សូមរង់ចាំមួយភ្លែត... 😊'
            ], 429);
        }
        RateLimiter::hit($rateLimitKey, 60);

        try {
            // រក្សាទុកសាររបស់ User
            ChatMessage::create([
                'user_id' => $user?->id,
                'message' => $userMessage,
                'sender'  => 'user',
            ]);

            $userRole = $user?->role ?? 'student';
            $userName = $user?->name ?? 'ភ្ញៀវ';

            // ផ្ញើទៅ Dify AI
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('DIFY_API_KEY'),
                'Content-Type' => 'application/json',
            ])->timeout(35)->post("https://api.dify.ai/v1/chat-messages", [
                'inputs' => [
                    'user_role'   => $userRole,
                    'user_name'   => $userName,
                    'chat_option' => $chatOption,
                ],
                'query' => $userMessage,
                'response_mode' => 'blocking',
                'user' => 'nmu-user-' . ($user?->id ?? 'guest'),
            ]);

            if ($response->failed()) {
                Log::error("Dify API Error: " . $response->body());
                return response()->json([
                    'message' => 'សុំទោស! ប្រព័ន្ធ AI មានបញ្ហាបច្ចេកទេសមួយចំនួន។ សូមព្យាយាមម្តងទៀត។'
                ], 500);
            }

            $data = $response->json();
            $aiResponse = $data['answer'] ?? 'សុំទោស ខ្ញុំមិនអាចឆ្លើយបានទេ។';

            // រក្សាទុកសាររបស់ AI
            ChatMessage::create([
                'user_id' => $user?->id,
                'message' => $aiResponse,
                'sender'  => 'ai',
            ]);

            return response()->json(['message' => $aiResponse]);

        } catch (\Exception $e) {
            Log::error("AI Chat Exception: " . $e->getMessage());
            return response()->json([
                'message' => 'មានបញ្ហាក្នុងការទាក់ទងជាមួយ AI។ សូមព្យាយាមម្តងទៀត។'
            ], 500);
        }
    }
    public function getHistory()
{
    $user = auth()->user();
    
    if (!$user) {
        return response()->json(['messages' => []]);
    }

    $messages = ChatMessage::where('user_id', $user->id)
        ->orderBy('created_at', 'asc')
        ->take(50) // យក 50 សារចុងក្រោយ
        ->get(['sender', 'message', 'created_at']);

    return response()->json([
        'messages' => $messages
    ]);
}

/**
 * លុបប្រវត្តិសន្ទនាទាំងអស់
 */
public function clearHistory()
{
    $user = auth()->user();
    
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    // លុបសារទាំងអស់របស់ User នេះ
    ChatMessage::where('user_id', $user->id)->delete();

    return response()->json(['message' => 'Chat history cleared successfully']);
}
}