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
    /**
     * មុខងារចម្បងសម្រាប់ទាក់ទងជាមួយ Dify AI ដោយប្រើ Context ពី Database
     */
    public function generateResponse(Request $request)
    {
        // ១. Validate ទិន្នន័យ
        $request->validate([
            'message' => 'required|string|max:2000',
            'option' => 'required|string', // 'info' ឬ 'process'
        ]);

        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'សូមចូលប្រព័ន្ធសិនមេ!'], 401);
        }

        // ២. Rate Limiting (ការពារការ Spam)
        $rateKey = 'ai-chat:'.$user->id;
        if (RateLimiter::tooManyAttempts($rateKey, 10)) {
            return response()->json([
                'message' => 'មេផ្ញើសារលឿនពេកហើយ! សម្រាក ១ នាទីសិនទៅ ចាំសួរទៀត... ☕',
            ], 429);
        }
        RateLimiter::hit($rateKey, 60);

        try {
            // ៣. រក្សាទុកសារ User ចូល DB ក្នុងស្រុក
            ChatMessage::create([
                'user_id' => $user->id,
                'message' => $request->message,
                'sender' => 'user',
            ]);

            // ៤. ទាញ Context ពេញលេញពី Database (Full Connection)
            $dbContext = $this->getDatabaseContext($user);

            // ៥. ហៅទៅកាន់ Dify API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.env('DIFY_API_KEY'),
                'Content-Type' => 'application/json',
            ])->timeout(40)->post('https://api.dify.ai/v1/chat-messages', [
                'inputs' => [
                    'user_name' => $user->name,
                    'user_role' => $user->role,
                    'chat_option' => $request->option,
                    'db_context' => $dbContext, // បោះទិន្នន័យពិតទៅឱ្យ AI
                ],
                'query' => $request->message,
                'response_mode' => 'blocking',
                'user' => 'nmu-user-'.$user->id,
            ]);

            if ($response->failed()) {
                Log::error('Dify Error: '.$response->body());

                return response()->json(['message' => 'សុំទោសបង! AI ដើរខុសបច្ចេកទេសបន្តិចហើយ។'], 500);
            }

            $aiAnswer = $response->json()['answer'] ?? 'ខ្ញុំមិនដឹងឆ្លើយថាម៉េចទេមេ...';

            // ៦. រក្សាទុកសារ AI ចូល DB
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

    /**
     * ទាញទិន្នន័យពិតៗពី Database តាមតួនាទី (The Core Connection)
     */
    private function getDatabaseContext($user)
    {
        $role = $user->role;
        $context = "ទិន្នន័យបច្ចុប្បន្នពីប្រព័ន្ធ NMU៖\n";

        try {
            if ($role === 'student') {
                // ១. មុខវិជ្ជាដែលកំពុងរៀន
                $courses = DB::table('student_course_enrollments')
                    ->join('course_offerings', 'student_course_enrollments.course_offering_id', '=', 'course_offerings.id')
                    ->join('courses', 'course_offerings.course_id', '=', 'courses.id')
                    ->where('student_course_enrollments.student_user_id', $user->id)
                    ->select('courses.title_km', 'course_offerings.section')
                    ->get();

                $courseNames = $courses->map(fn ($c) => "{$c->title_km} (Section: {$c->section})")->implode(', ');
                $context .= '- មុខវិជ្ជាកំពុងរៀន៖ '.($courseNames ?: 'មិនទាន់មាន')."\n";

                // ២. សង្ខេបវត្តមាន
                $attendance = DB::table('attendance_records')
                    ->where('student_user_id', $user->id)
                    ->selectRaw("COUNT(CASE WHEN status = 'present' THEN 1 END) as present_count")
                    ->selectRaw("COUNT(CASE WHEN status != 'present' THEN 1 END) as absent_count")
                    ->first();
                $context .= "- វត្តមាន៖ មក ({$attendance->present_count}), អវត្តមាន ({$attendance->absent_count})\n";

                // ៣. កាលវិភាគ
                $schedules = DB::table('schedules')
                    ->join('course_offerings', 'schedules.course_offering_id', '=', 'course_offerings.id')
                    ->join('courses', 'course_offerings.course_id', '=', 'courses.id')
                    ->join('student_course_enrollments', 'course_offerings.id', '=', 'student_course_enrollments.course_offering_id')
                    ->where('student_course_enrollments.student_user_id', $user->id)
                    ->select('courses.title_km', 'schedules.day_of_week', 'schedules.start_time')
                    ->get();
                $context .= '- កាលវិភាគ៖ '.$schedules->map(fn ($s) => "{$s->title_km} ថ្ងៃ{$s->day_of_week} ({$s->start_time})")->implode(' | ')."\n";
            } elseif ($role === 'professor') {
                // ១. ថ្នាក់ដែលត្រូវបង្រៀន
                $teachings = DB::table('course_offerings')
                    ->join('courses', 'course_offerings.course_id', '=', 'courses.id')
                    ->where('lecturer_user_id', $user->id)
                    ->select('courses.title_km', 'course_offerings.section', 'course_offerings.capacity')
                    ->get();
                $context .= '- លោកគ្រូមានបង្រៀនមុខវិជ្ជា៖ '.$teachings->map(fn ($t) => "{$t->title_km} (Section: {$t->section}, និស្សិត: {$t->capacity}នាក់)")->implode(', ')."\n";
            } elseif ($role === 'admin') {
                // ១. ទាញយកស្ថិតិទូទៅ
                $stats = [
                    'users' => DB::table('users')->count(),
                    'faculties' => DB::table('faculties')->count(),
                    'departments' => DB::table('departments')->count(),
                    'programs' => DB::table('programs')->count(),
                    'courses' => DB::table('courses')->count(),
                    'rooms' => DB::table('rooms')->count(),
                    'offerings' => DB::table('course_offerings')->whereNull('deleted_at')->count(),
                ];

                // ២. ទាញយកបញ្ជីដេប៉ាតឺម៉ង់ (យកទាំងអស់ដើម្បីឱ្យ AI ស្គាល់)
                $depts = DB::table('departments')->pluck('name_km')->implode(', ');

                // ៣. ទាញយកមុខវិជ្ជាសំខាន់ៗ (យក ១០ មុខវិជ្ជាចុងក្រោយដែលទើបបង្កើត)
                $courses = DB::table('courses')->latest()->limit(10)->pluck('title_km')->implode(', ');

                // ៤. ទាញយកសកម្មភាពបើកថ្នាក់ (Course Offerings) ដែលកំពុងដំណើរការ
                $activeOfferings = DB::table('course_offerings')
                    ->join('courses', 'course_offerings.course_id', '=', 'courses.id')
                    ->join('users', 'course_offerings.lecturer_user_id', '=', 'users.id')
                    ->join('rooms', 'course_offerings.room_number', '=', 'rooms.id', 'left') // បើមាន Table rooms
                    ->whereNull('course_offerings.deleted_at')
                    ->select('courses.title_km', 'users.name as teacher', 'course_offerings.section')
                    ->limit(5)
                    ->get();

                $offeringList = $activeOfferings->map(function ($o) {
                    return "ថ្នាក់ {$o->title_km} (គ្រូ: {$o->teacher}, Section: {$o->section})";
                })->implode('; ');

                // ៥. ទាញយកសេចក្តីប្រកាសចុងក្រោយ
                $announcements = DB::table('announcements')->latest()->limit(2)->pluck('title_km')->implode(' និង ');

                // រៀបចំ Context ឱ្យ AI យល់ពី Database ទាំងមូល
                $context .= "--- របាយការណ៍គ្រប់គ្រង NMU ---\n";
                $context .= "- ស្ថិតិ៖ និស្សិត/បុគ្គលិក {$stats['users']} នាក់, {$stats['faculties']} មហាវិទ្យាល័យ, {$stats['departments']} ដេប៉ាតឺម៉ង់, {$stats['programs']} កម្មវិធីសិក្សា។\n";
                $context .= "- ធនធាន៖ មុខវិជ្ជាសរុប {$stats['courses']} មុខ (មានដូចជា៖ {$courses}...), បន្ទប់សិក្សា {$stats['rooms']} បន្ទប់។\n";
                $context .= "- សកម្មភាពបង្រៀន៖ មានការបើកថ្នាក់ {$stats['offerings']} ថ្នាក់ (ឧទាហរណ៍៖ {$offeringList})។\n";
                $context .= "- ដេប៉ាតឺម៉ង់ដែលមាន៖ {$depts}។\n";
                $context .= '- ប្រកាសចុងក្រោយ៖ '.($announcements ?: 'គ្មាន')."។\n";
            }

            // សេចក្តីប្រកាសចុងក្រោយ (សម្រាប់គ្រប់គ្នា)
            $latestAnnounce = DB::table('announcements')->latest()->first();
            if ($latestAnnounce) {
                $context .= '- សេចក្តីប្រកាសចុងក្រោយ៖ '.$latestAnnounce->title_km."\n";
            }

        } catch (\Exception $e) {
            $context .= "- (មិនអាចទាញទិន្នន័យលម្អិតបានដោយសារបញ្ហា DB)\n";
        }

        return $context;
    }

    /**
     * ទាញប្រវត្តិសន្ទនា
     */
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

    /**
     * លុបប្រវត្តិសន្ទនា
     */
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
