<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Services\AIContextService;
use App\Services\NmuWebsiteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class SmartAssistantController extends Controller
{
    public function __construct(
        protected AIContextService $contextService,
        protected NmuWebsiteService $websiteService
    ) {}

    public function generateResponse(Request $request)
    {
        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'option' => 'required|string|in:info,process,search',
        ]);

        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'សូមចូលប្រព័ន្ធសិនមេ!'], 401);
        }

        $rateKey = 'ai-chat:'.$user->id;
        $maxAttempts = config('dify.rate_limit.max_attempts', 10);

        if (RateLimiter::tooManyAttempts($rateKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($rateKey);

            return response()->json([
                'message' => "មេផ្ញើសារលឿនពេកហើយ! សូមរង់ចាំ {$seconds} វិនាទីទៀត... ☕",
                'retry_after' => $seconds,
            ], 429);
        }
        RateLimiter::hit($rateKey, 60);

        try {
            ChatMessage::create([
                'user_id' => $user->id,
                'message' => $request->message,
                'sender' => 'user',
            ]);

            // Build context based on chat option
            $dbContext = $this->contextService->getDatabaseContext($user, $request->option);

            // Add website search data for search mode
            if ($request->option === 'search') {
                $websiteData = $this->websiteService->searchNmuData($request->message);
                $dbContext .= "\n\n=== NMU WEBSITE SEARCH RESULTS ===\n" . $websiteData;
            }

            $conversationId = $this->getConversationId($user->id);

            // Build the system prompt based on option
            $systemPrompt = $this->buildSystemPrompt($request->option, $user);

            // Get user gender for Dify inputs
            $user->load('profile', 'studentProfile');
            $profile = $user->role === 'student' ? $user->studentProfile : $user->profile;
            $userGender = $profile->gender ?? 'unknown';

            $payload = [
                'inputs' => [
                    'user_name' => $user->name,
                    'user_role' => $user->role,
                    'user_gender' => $userGender,
                    'chat_option' => $request->option,
                    'db_context' => $dbContext,
                    'system_prompt' => $systemPrompt,
                ],
                'query' => $request->message,
                'response_mode' => 'blocking',
                'user' => 'nmu-user-'.$user->id,
            ];

            if ($conversationId) {
                $payload['conversation_id'] = $conversationId;
            }

            Log::info('Dify Request', [
                'user_id' => $user->id,
                'query' => $request->message,
                'option' => $request->option,
                'context_length' => strlen($dbContext),
            ]);

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer '.config('dify.api_key'),
                'Content-Type' => 'application/json',
            ])->timeout(config('dify.timeout', 60))->post(config('dify.api_url'), $payload);

            if ($response->failed()) {
                Log::error('Dify Error: '.$response->body());
                $errorData = $response->json();
                $errorMsg = $errorData['message'] ?? 'Unknown error';

                return response()->json(['message' => "AI API Error: {$errorMsg}"], 500);
            }

            $data = $response->json();

            if (! isset($data['answer'])) {
                Log::error('Dify unexpected response: '.json_encode($data));

                return response()->json(['message' => 'AI returned unexpected response format. Check logs.'], 500);
            }

            $aiAnswer = $data['answer'];
            $newConversationId = $data['conversation_id'] ?? null;

            if ($newConversationId && ! $conversationId) {
                $this->saveConversationId($user->id, $newConversationId);
            }

            $chatMessage = ChatMessage::create([
                'user_id' => $user->id,
                'message' => $aiAnswer,
                'sender' => 'ai',
            ]);

            return response()->json([
                'message' => $aiAnswer,
                'message_id' => $chatMessage->id,
            ]);

        } catch (\Exception $e) {
            Log::error('SmartAssistant Exception: '.$e->getMessage());

            return response()->json(['message' => 'មានបញ្ហាខាងក្នុងម៉ាស៊ីនហើយមេ! Error: '.$e->getMessage()], 500);
        }
    }

    public function feedback(Request $request)
    {
        $request->validate([
            'message_id' => 'required|exists:chat_messages,id',
            'feedback' => 'required|in:up,down',
        ]);

        ChatMessage::where('id', $request->message_id)
            ->where('sender', 'ai')
            ->update(['feedback' => $request->feedback]);

        return response()->json(['success' => true]);
    }

    public function getHistory()
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['messages' => []]);
        }

        $messages = ChatMessage::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->take(50)
            ->get(['id', 'sender', 'message', 'created_at']);

        return response()->json(['messages' => $messages]);
    }

    public function clearHistory()
    {
        $user = Auth::user();
        if ($user) {
            ChatMessage::where('user_id', $user->id)->delete();
            $this->contextService->clearCache($user);

            return response()->json(['message' => 'ជោគជ័យ!']);
        }

        return response()->json(['message' => 'Error'], 401);
    }

    protected function buildSystemPrompt(string $option, $user): string
    {
        $role = $user->role;
        $roleName = match ($role) {
            'admin' => 'Admin',
            'professor' => 'Professor',
            'student' => 'Student',
            default => 'User',
        };

        // Get user gender from profile
        $user->load('profile', 'studentProfile');
        $profile = $role === 'student' ? $user->studentProfile : $user->profile;
        $userGender = $profile->gender ?? '';

        $basePrompt = "You are NMU Smart Assistant, an AI helper for National Meanchey University (សាកលវិទ្យាល័យជាតិមានជ័យ). You speak Khmer and English. You have access to the university's database and website data.";

        $pronounRule = <<<EOD

--- ការប្រើសព្វនាម (ផ្អែកលើ តួនាទី និង ភេទ) ---
សូមប្រើសព្វនាមឱ្យបានត្រឹមត្រូវទៅតាមតួនាទី និងភេទរបស់អ្នកប្រើប្រាស់៖
• សិស្ស (Student) → ប្រើពាក្យ "ប្អូន" ឬ "ប្អូនប្រុស" (បើភេទប្រុស) និង "ប្អូនស្រី" (បើភេទស្រី)។
• សាស្ត្រាចារ្យ/គ្រូ (Professor/Teacher) → ប្រើពាក្យ "លោកគ្រូ" (បើភេទប្រុស) និង "អ្នកគ្រូ" (បើភេទស្រី)។ បើមិនច្បាស់ ប្រើរួមថា "លោកគ្រូ/អ្នកគ្រូ"។
• អ្នកគ្រប់គ្រង (Admin) → ប្រើពាក្យ "លោក" (បើភេទប្រុស) និង "លោកស្រី" (បើភេទស្រី)។
EOD;

        $optionPrompt = match ($option) {
            'info' => "You provide information about students, professors, courses, grades, attendance, schedules, and university data. Answer questions using the database context provided. Be helpful and accurate. Use data from the context to answer questions.",
            'process' => "You explain how to use the Class Management System. Guide users through features like: creating assessments, grading students, taking attendance, enrolling courses, importing data, exporting reports. Be step-by-step and clear.",
            'search' => "You are a search assistant. When users ask about specific data (students, professors, courses, grades, attendance, schedules, university info), search through the provided database context and website data to find matching records. Present results in a clear, organized format. If asked about university information, use the NMU website data. Search comprehensively across all available data.",
            default => "You are a helpful assistant for NMU Class Management System.",
        };

        return "{$basePrompt}\n\nRole: {$roleName}\nUser Gender: {$userGender}\n{$pronounRule}\n\n{$optionPrompt}";
    }

    private function getConversationId($userId)
    {
        $lastMessage = ChatMessage::where('user_id', $userId)
            ->where('sender', 'ai')
            ->whereNotNull('conversation_id')
            ->latest()
            ->first();

        return $lastMessage?->conversation_id;
    }

    private function saveConversationId($userId, $conversationId)
    {
        ChatMessage::where('user_id', $userId)
            ->whereNull('conversation_id')
            ->update(['conversation_id' => $conversationId]);
    }
}
