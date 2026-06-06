<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'option' => 'nullable|in:info,process',
        ]);

        $userMessage = $validated['message'];
        $chatOption = $validated['option'] ?? 'info';

        // Rate Limiting
        $rateLimitKey = $user
            ? 'ai-chat:user-'.$user->id
            : 'ai-chat:ip-'.$request->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 12)) {
            return response()->json([
                'message' => 'បងផ្ញើសារញឹកញាប់ពេកហើយ! សូមរង់ចាំមួយភ្លែត... 😊',
            ], 429);
        }
        RateLimiter::hit($rateLimitKey, 60);

        try {
            // ១. រក្សាទុកសាររបស់ User ចូល Database ក្នុងស្រុក
            ChatMessage::create([
                'user_id' => $user?->id,
                'message' => $userMessage,
                'sender' => 'user',
            ]);

            $userRole = $user?->role ?? 'student';
            $userName = $user?->name ?? 'ភ្ញៀវ';

            // ២. ទាញទិន្នន័យពី Database មកធ្វើជា Context (ចំណុចសំខាន់)
            $dbContext = $this->getDatabaseContext($user);

            // ៣. ផ្ញើទៅ Dify AI
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.env('DIFY_API_KEY'),
                'Content-Type' => 'application/json',
            ])->timeout(45)->post('https://api.dify.ai/v1/chat-messages', [
                'inputs' => [
                    'user_role' => $userRole,
                    'user_name' => $userName,
                    'chat_option' => $chatOption,
                    'db_context' => $dbContext, // បោះទិន្នន័យដែលទាញបានទៅឱ្យ Dify
                ],
                'query' => $userMessage,
                'response_mode' => 'blocking',
                'user' => 'nmu-user-'.($user?->id ?? 'guest'),
            ]);

            if ($response->failed()) {
                Log::error('Dify API Error: '.$response->body());

                return response()->json([
                    'message' => 'សុំទោស! ប្រព័ន្ធ AI មានបញ្ហាបច្ចេកទេស។ (Check Dify Variables)',
                ], 500);
            }

            $data = $response->json();
            $aiResponse = $data['answer'] ?? 'សុំទោស ខ្ញុំមិនអាចឆ្លើយបានទេ។';

            // ៤. រក្សាទុកសាររបស់ AI ចូល Database
            ChatMessage::create([
                'user_id' => $user?->id,
                'message' => $aiResponse,
                'sender' => 'ai',
            ]);

            return response()->json(['message' => $aiResponse]);

        } catch (\Exception $e) {
            Log::error('AI Chat Exception: '.$e->getMessage());

            return response()->json([
                'message' => 'មានបញ្ហាក្នុងការទាក់ទងជាមួយ AI។ សូមព្យាយាមម្តងទៀត។',
            ], 500);
        }
    }

    /**
     * ទាញទិន្នន័យជាក់ស្ដែងពី Database តាម Role របស់អ្នកប្រើ
     */
    private function getDatabaseContext($user)
    {
        if (! $user) {
            return 'ភ្ញៀវមិនមានទិន្នន័យក្នុងប្រព័ន្ធឡើយ។';
        }

        $role = $user->role;
        $context = 'ព័ត៌មានបច្ចុប្បន្ន៖ ';

        try {
            if ($role === 'admin') {
                $userCount = DB::table('users')->count();
                $facultyCount = DB::table('faculties')->count();
                $context .= "ប្រព័ន្ធមានអ្នកប្រើប្រាស់សរុប {$userCount} នាក់ និងមាន {$facultyCount} មហាវិទ្យាល័យ។";
            } elseif ($role === 'professor') {
                $courseCount = DB::table('course_offerings')->where('lecturer_user_id', $user->id)->count();
                $context .= "លោកគ្រូ/អ្នកគ្រូ មានបង្រៀនចំនួន {$courseCount} មុខវិជ្ជាក្នុងឆមាសនេះ។";
            } else {
                // សិស្ស៖ មើលវត្តមានបន្តិចបន្តួច
                $attendanceCount = DB::table('attendance_records')->where('student_user_id', $user->id)->count();
                $context .= "ប្អូនមានកំណត់ត្រាវត្តមានសរុប {$attendanceCount} ដងក្នុងប្រព័ន្ធ។";
            }

            // បន្ថែមការប្រកាសចុងក្រោយ (Announcements) សម្រាប់គ្រប់គ្នា
            $latestAnnounce = DB::table('announcements')->latest()->first();
            if ($latestAnnounce) {
                $context .= ' ការប្រកាសចុងក្រោយ៖ '.$latestAnnounce->title;
            }
        } catch (\Exception $e) {
            $context .= 'មិនអាចទាញទិន្នន័យបាន (DB Error)។';
        }

        return $context;
    }

    public function getHistory()
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['messages' => []]);
        }

        $messages = ChatMessage::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->take(50)
            ->get(['sender', 'message', 'created_at']);

        return response()->json(['messages' => $messages]);
    }

    public function clearHistory()
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        ChatMessage::where('user_id', $user->id)->delete();

        return response()->json(['message' => 'Chat history cleared successfully']);
    }
}
