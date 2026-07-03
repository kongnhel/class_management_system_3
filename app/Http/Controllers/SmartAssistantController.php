<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class SmartAssistantController extends Controller
{
    public function generateResponse(Request $request)
    {
        $request->validate([
            'message' => [
                'required',
                'string',
                'max:2000',
            ],
            'option' => 'required|string|in:info,process',
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

            $dbContext = $this->getDatabaseContext($user);

            $conversationId = $this->getConversationId($user->id);

            $payload = [
                'inputs' => [
                    'user_name' => $user->name,
                    'user_role' => $user->role,
                    'chat_option' => $request->option,
                    'db_context' => $dbContext,
                ],
                'query' => $request->message,
                'response_mode' => 'blocking',
                'user' => 'nmu-user-'.$user->id,
            ];

            if ($conversationId) {
                $payload['conversation_id'] = $conversationId;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.config('dify.api_key'),
                'Content-Type' => 'application/json',
            ])->timeout(config('dify.timeout', 45))->post(config('dify.api_url'), $payload);

            if ($response->failed()) {
                Log::error('Dify Error: '.$response->body());

                return response()->json(['message' => 'សុំទោសបង! AI ដើរខុសបច្ចេកទេសបន្តិចហើយ។'], 500);
            }

            $data = $response->json();
            $aiAnswer = $data['answer'] ?? 'ខ្ញុំមិនដឹងឆ្លើយថាម៉េចទេមេ...';
            $newConversationId = $data['conversation_id'] ?? null;

            if ($newConversationId && ! $conversationId) {
                $this->saveConversationId($user->id, $newConversationId);
            }

            ChatMessage::create([
                'user_id' => $user->id,
                'message' => $aiAnswer,
                'sender' => 'ai',
            ]);

            return response()->json(['message' => $aiAnswer]);

        } catch (\Exception $e) {
            Log::error('SmartAssistant Exception: '.$e->getMessage());

            return response()->json(['message' => 'មានបញ្ហាខាងក្នុងម៉ាស៊ីនហើយមេ!'], 500);
        }
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

    private function getDatabaseContext($user)
    {
        $role = $user->role;
        $context = "ទិន្នន័យបច្ចុប្បន្នពីប្រព័ន្ធ NMU៖\n";

        try {
            if ($role === 'student') {
                $courses = DB::table('student_course_enrollments')
                    ->join('course_offerings', 'student_course_enrollments.course_offering_id', '=', 'course_offerings.id')
                    ->join('courses', 'course_offerings.course_id', '=', 'courses.id')
                    ->where('student_course_enrollments.student_user_id', $user->id)
                    ->select('courses.title_km', 'course_offerings.section')
                    ->get();

                $courseNames = $courses->map(fn ($c) => "{$c->title_km} (Section: {$c->section})")->implode(', ');
                $context .= '- មុខវិជ្ជាកំពុងរៀន៖ '.($courseNames ?: 'មិនទាន់មាន')."\n";

                $attendance = DB::table('attendances')
                    ->where('student_user_id', $user->id)
                    ->selectRaw("COUNT(CASE WHEN status = 'present' THEN 1 END) as present_count")
                    ->selectRaw("COUNT(CASE WHEN status != 'present' THEN 1 END) as absent_count")
                    ->first();
                $context .= "- វត្តមាន៖ មក ({$attendance->present_count}), អវត្តមាន ({$attendance->absent_count})\n";

                $schedules = DB::table('schedules')
                    ->join('course_offerings', 'schedules.course_offering_id', '=', 'course_offerings.id')
                    ->join('courses', 'course_offerings.course_id', '=', 'courses.id')
                    ->join('student_course_enrollments', 'course_offerings.id', '=', 'student_course_enrollments.course_offering_id')
                    ->where('student_course_enrollments.student_user_id', $user->id)
                    ->select('courses.title_km', 'schedules.day_of_week', 'schedules.start_time')
                    ->get();
                $context .= '- កាលវិភាគ៖ '.$schedules->map(fn ($s) => "{$s->title_km} ថ្ងៃ{$s->day_of_week} ({$s->start_time})")->implode(' | ')."\n";

                $assignments = DB::table('assignments')
                    ->join('course_offerings', 'assignments.course_offering_id', '=', 'course_offerings.id')
                    ->join('student_course_enrollments', 'course_offerings.id', '=', 'student_course_enrollments.course_offering_id')
                    ->where('student_course_enrollments.student_user_id', $user->id)
                    ->where('assignments.due_date', '>=', now())
                    ->select('assignments.title', 'assignments.due_date')
                    ->orderBy('assignments.due_date')
                    ->limit(5)
                    ->get();
                $assignmentList = $assignments->map(fn ($a) => "{$a->title} (ផុតកំណត់: {$a->due_date})")->implode(', ');
                $context .= '- កិច្ចការនិស្សិត៖ '.($assignmentList ?: 'គ្មាន')."\n";

                $grades = DB::table('student_course_enrollments')
                    ->join('course_offerings', 'student_course_enrollments.course_offering_id', '=', 'course_offerings.id')
                    ->join('courses', 'course_offerings.course_id', '=', 'courses.id')
                    ->where('student_course_enrollments.student_user_id', $user->id)
                    ->whereNotNull('student_course_enrollments.final_grade')
                    ->select('courses.title_km', 'student_course_enrollments.final_grade')
                    ->get();
                $gradeList = $grades->map(fn ($g) => "{$g->title_km}: {$g->final_grade}")->implode(', ');
                $context .= '- ពិន្ទុចុងក្រោយ៖ '.($gradeList ?: 'មិនទាន់មាន')."\n";

            } elseif ($role === 'professor') {
                $teachings = DB::table('course_offerings')
                    ->join('courses', 'course_offerings.course_id', '=', 'courses.id')
                    ->where('lecturer_user_id', $user->id)
                    ->select('courses.title_km', 'course_offerings.section', 'course_offerings.capacity')
                    ->get();
                $context .= '- លោកគ្រូមានបង្រៀនមុខវិជ្ជា៖ '.$teachings->map(fn ($t) => "{$t->title_km} (Section: {$t->section}, និស្សិត: {$t->capacity}នាក់)")->implode(', ')."\n";

                $studentCounts = DB::table('course_offerings')
                    ->join('student_course_enrollments', 'course_offerings.id', '=', 'student_course_enrollments.course_offering_id')
                    ->where('course_offerings.lecturer_user_id', $user->id)
                    ->select('course_offerings.id', DB::raw('COUNT(student_course_enrollments.id) as enrolled'))
                    ->groupBy('course_offerings.id')
                    ->get();
                $context .= '- និស្សិតសរុប៖ '.$studentCounts->sum('enrolled')."នាក់។\n";

            } elseif ($role === 'admin') {
                $stats = [
                    'users' => DB::table('users')->count(),
                    'faculties' => DB::table('faculties')->count(),
                    'departments' => DB::table('departments')->count(),
                    'programs' => DB::table('programs')->count(),
                    'courses' => DB::table('courses')->count(),
                    'rooms' => DB::table('rooms')->count(),
                    'offerings' => DB::table('course_offerings')->whereNull('deleted_at')->count(),
                ];

                $depts = DB::table('departments')->pluck('name_km')->implode(', ');
                $courses = DB::table('courses')->latest()->limit(10)->pluck('title_km')->implode(', ');

                $activeOfferings = DB::table('course_offerings')
                    ->join('courses', 'course_offerings.course_id', '=', 'courses.id')
                    ->join('users', 'course_offerings.lecturer_user_id', '=', 'users.id')
                    ->whereNull('course_offerings.deleted_at')
                    ->select('courses.title_km', 'users.name as teacher', 'course_offerings.section')
                    ->limit(5)
                    ->get();

                $offeringList = $activeOfferings->map(function ($o) {
                    return "ថ្នាក់ {$o->title_km} (គ្រូ: {$o->teacher}, Section: {$o->section})";
                })->implode('; ');

                $announcements = DB::table('announcements')->latest()->limit(2)->pluck('title_km')->implode(' និង ');

                $context .= "--- របាយការណ៍គ្រប់គ្រង NMU ---\n";
                $context .= "- ស្ថិតិ៖ និស្សិត/បុគ្គលិក {$stats['users']} នាក់, {$stats['faculties']} មហាវិទ្យាល័យ, {$stats['departments']} ដេប៉ាតឺម៉ង់, {$stats['programs']} កម្មវិធីសិក្សា។\n";
                $context .= "- ធនធាន៖ មុខវិជ្ជាសរុប {$stats['courses']} មុខ (មានដូចជា៖ {$courses}...), បន្ទប់សិក្សា {$stats['rooms']} បន្ទប់។\n";
                $context .= "- សកម្មភាពបង្រៀន៖ មានការបើកថ្នាក់ {$stats['offerings']} ថ្នាក់ (ឧទាហរណ៍៖ {$offeringList})។\n";
                $context .= "- ដេប៉ាតឺម៉ង់ដែលមាន៖ {$depts}។\n";
                $context .= '- ប្រកាសចុងក្រោយ៖ '.($announcements ?: 'គ្មាន')."។\n";
            }

            $latestAnnounce = DB::table('announcements')->latest()->first();
            if ($latestAnnounce) {
                $context .= '- សេចក្តីប្រកាសចុងក្រោយ៖ '.$latestAnnounce->title_km."\n";
            }

        } catch (\Exception $e) {
            $context .= "- (មិនអាចទាញទិន្នន័យលម្អិតបានដោយសារបញ្ហា DB)\n";
        }

        return $context;
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
            ->get(['sender', 'message', 'created_at']);

        return response()->json(['messages' => $messages]);
    }

    public function clearHistory()
    {
        $user = Auth::user();
        if ($user) {
            ChatMessage::where('user_id', $user->id)->delete();

            return response()->json(['message' => 'ជោគជ័យ!']);
        }

        return response()->json(['message' => 'Error'], 401);
    }
}
