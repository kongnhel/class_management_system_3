<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AIChatController extends Controller
{
    /**
     * បង្ហាញ Interface សម្រាប់ Chat
     */
    public function index()
    {
        return view('ai-chat.index');
    }

    /**
     * ផ្ញើសារទៅកាន់ Dify AI ជាមួយជម្រើស (Information ឬ Process Flow)
     */
    public function sendMessage(Request $request)
    {
        $user = auth()->user();
        $userMessage = $request->input('message');
        
        // ទទួលយក Option ពី Frontend (ឧទាហរណ៍៖ 'info' ឬ 'process')
        // បើ User មិនបានរើស យើងទម្លាក់ទៅ 'info' ជា Default
        $chatOption = $request->input('option', 'info'); 

        // 1. ត្រួតពិនិត្យសារទទេ
        if (!$userMessage) {
            return response()->json(['message' => 'សូមមេត្តាវាយសំណួររបស់បងសិន! 😊']);
        }

        // 2. Rate Limiting (10 Messages per Minute)
        $rateLimitKey = "ai-chat:" . ($user->id ?? 'guest');
        if (RateLimiter::tooManyAttempts($rateLimitKey, 10)) {
            return response()->json([
                'message' => 'បងផ្ញើសារលឿនពេកហើយ! សូមរង់ចាំមួយភ្លែតសិន។'
            ], 429);
        }
        RateLimiter::hit($rateLimitKey, 60);

        try {
            // 3. រក្សាសាររបស់ User ចូល Database
            ChatMessage::create([
                'user_id' => $user->id ?? null,
                'message' => $userMessage,
                'sender'  => 'user',
            ]);

            // 4. រៀបចំទិន្នន័យសម្រាប់ផ្ញើទៅ Dify
            $userRole = $user->role ?? 'student'; 
            $userName = $user->name ?? 'ភ្ញៀវ';

            // 5. ហៅទៅកាន់ Dify API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('DIFY_API_KEY'),
                'Content-Type' => 'application/json',
            ])->timeout(30)->post("https://api.dify.ai/v1/chat-messages", [
                'inputs' => [
                    'user_role'   => $userRole,   // តួនាទី
                    'user_name'   => $userName,   // ឈ្មោះ
                    'chat_option' => $chatOption, // ជម្រើស: info ឬ process
                ],
                'query' => $userMessage,
                'response_mode' => 'blocking',
                'user' => 'nmu-user-' . ($user->id ?? 'guest'),
            ]);

            $data = $response->json();

            // 6. ត្រួតពិនិត្យ Error
            if ($response->failed()) {
                Log::error("Dify API Error: " . json_encode($data));
                return response()->json(['message' => 'សុំទោសបង ប្រព័ន្ធ AI កំពុងមានបញ្ហាបច្ចេកទេស។'], 500);
            }

            $aiResponse = $data['answer'] ?? 'សុំទោសបង ខ្ញុំមិនអាចរកឃើញព័ត៌មាននេះទេ។';

            // 7. រក្សាសាររបស់ AI ចូល Database
            ChatMessage::create([
                'user_id' => $user->id ?? null,
                'message' => $aiResponse,
                'sender'  => 'ai',
            ]);

            return response()->json(['message' => $aiResponse]);

        } catch (\Exception $e) {
            Log::error("AI Chat Exception: " . $e->getMessage());
            return response()->json(['message' => 'មានបញ្ហាតភ្ជាប់ទៅកាន់ AI។'], 500);
        }
    }
}