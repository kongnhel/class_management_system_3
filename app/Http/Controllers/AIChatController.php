<?php

namespace App\Http\Controllers;
use App\Models\ChatMessage;
use App\Http\Requests\SendAIChatMessageRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AIChatController extends Controller
{
    /**
     * Display AI Chat interface (for all authenticated users)
     */
    public function index()
    {
        return view('ai-chat.index');
    }

    /**
     * Send message to AI and store conversation
     * 
     * @param SendAIChatMessageRequest $request - Validated request with 'message' field
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(SendAIChatMessageRequest $request)
    {
        // ===== STEP 1: VALIDATION ALREADY DONE BY FORM REQUEST =====
        $validated = $request->validated();
        $userMessage = $validated['message'];

        // ===== STEP 2: RATE LIMITING (5 messages per minute per user) =====
        $userId = auth()->id();
        $rateLimitKey = "ai-chat:{$userId}";
        
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            Log::warning("AI Chat rate limit exceeded for user: $userId");
            return response()->json([
                'error' => 'សូមរង់ចាំមួយភាពបង្ហាប់ពេលឈប់ មុនពេលផ្ញើសារលើកក្រោយ'
            ], 429);
        }

        RateLimiter::hit($rateLimitKey, 60);

        try {
            // ===== STEP 3: SAVE USER MESSAGE =====
            ChatMessage::create([
                'user_id' => $userId,
                'message' => $userMessage,
                'sender' => 'user',
            ]);

            // ===== STEP 4: PREPARE SYSTEM INSTRUCTION =====
            $systemInstruction = "អ្នកគឺជាជំនួយការឆ្លាតវៃរបស់សាកលវិទ្យាល័យជាតិមានជ័យ (National Meanchey University)។ " .
                "ភារកិច្ចរបស់អ្នកគឺឆ្លើយតែសំណួរដែលទាក់ទងនឹងប្រព័ន្ធគ្រប់គ្រងថ្នាក់រៀន ការលើកលែងតាម កម្មវិធីសិក្សា គ្រូបង្រៀន និងសិស្ស។";

            // ===== STEP 5: CALL GEMINI API =====
            $apiKey = config('services.gemini.api_key') ?? env('GEMINI_API_KEY');
            
            if (!$apiKey) {
                Log::error("Gemini API key not configured");
                return response()->json([
                    'error' => 'សេវាកម្ម AI មិនអាចប្រើបាននៅពេលនេះ'
                ], 503);
            }

            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($url, [
                'contents' => [
                    'parts' => [
                        [
                            'text' => "Context: {$systemInstruction}\n\nUser Question: {$userMessage}"
                        ]
                    ]
                ]
            ], [
                'key' => $apiKey
            ]);

            $data = $response->json();

            // ===== STEP 6: ERROR HANDLING (NEVER expose API key) =====
            if ($response->failed()) {
                if (isset($data['error'])) {
                    $errorMsg = $data['error']['message'] ?? 'Unknown API error';
                    
                    // Log full error server-side only
                    Log::error("Gemini API Error: {$errorMsg}", [
                        'user_id' => $userId,
                        'status_code' => $response->status()
                    ]);
                    
                    // Return generic error to user (never expose API details)
                    if ($response->status() === 429) {
                        return response()->json([
                            'error' => 'សេវាកម្ម AI មានបន្ទុកលើស។ សូមព្យាយាមម្តងទៀតក្រោយ'
                        ], 503);
                    }
                    
                    return response()->json([
                        'error' => 'មិនអាចទទួលបានឆ្លើយលើគ្រប់គ្រងន័យបានទេ'
                    ], 500);
                }
            }

            // ===== STEP 7: EXTRACT RESPONSE =====
            $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$aiResponse) {
                Log::warning("Gemini returned empty response for user: $userId");
                return response()->json([
                    'error' => 'គ្មានឆ្លើយលើគ្រប់គ្រងន័យ'
                ], 500);
            }

            // ===== STEP 8: SAVE AI RESPONSE =====
            ChatMessage::create([
                'user_id' => $userId,
                'message' => $aiResponse,
                'sender' => 'ai',
            ]);

            return response()->json(['message' => $aiResponse]);

        } catch (\Exception $e) {
            // ===== STEP 9: CATCH ALL ERRORS =====
            Log::error("AI Chat Exception for user {$userId}: " . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'error' => 'មានបញ្ហាក្នុងលំនិត្តលើបរិក្ខាររុងចម្រើន'
            ], 500);
        }
    }
}