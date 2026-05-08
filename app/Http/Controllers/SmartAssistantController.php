<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SmartAssistantController extends Controller
{
    /**
     * មុខងារចម្បងសម្រាប់ കൈប្រកួតជាមួយ AI និង Database
     */
    public function generateResponse(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'option' => 'required|string', // 'info' ឬ 'process'
        ]);

        $user = Auth::user();
        $userMessage = $request->message;
        $option = $request->option;

        // ១. រៀបចំ Context ចេញពី Database ផ្អែកលើ Role
        $databaseContext = $this->getDatabaseContext($user);

        // ២. រៀបចំ System Instruction ឱ្យ AI យល់ពីតួនាទី និងទិន្នន័យ
        $systemInstruction = "អ្នកគឺជា 'ជំនួយការឆ្លាតវៃរបស់ NMU'។ គោលការណ៍៖\n" .
            "១. ប្រើទិន្នន័យពិតនេះដើម្បីឆ្លើយ៖ {$databaseContext}\n" .
            "២. បើ User សួរពី '{$option}' ចូរពន្យល់ឱ្យចំគោលដៅ។\n" .
            "៣. ហាមនិយាយឈ្មោះ Table ឬកូដបច្ចេកទេស។\n" .
            "៤. ប្រើភាសាខ្មែរផ្អែមល្ហែម (បង, លោកគ្រូ, អ្នកគ្រូ)។";

        try {
            // ៣. ហៅទៅកាន់ Gemini API (បងត្រូវដាក់ API_KEY ក្នុង .env)
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . env('GEMINI_API_KEY'), [
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => $systemInstruction . "\n\nសំណួរអ្នកប្រើ៖ " . $userMessage]]]
                ]
            ]);

            $data = $response->json();
            $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'] ?? "សុំទោសបង! ខ្ញុំមិនទាន់អាចរកចម្លើយជូនបានទេ។";

            return response()->json([
                'message' => $aiResponse
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'មានបញ្ហាបច្ចេកទេស៖ ' . $e->getMessage()], 500);
        }
    }

    /**
     * មុខងារទាញទិន្នន័យពីគ្រប់ Table តាម Role
     */
private function getDatabaseContext($user)
{
    // ត្រូវប្រាកដថា $user គឺជា Object របស់មនុស្សម្នាក់ (Authenticated User)
    // ប្រសិនបើបងប្រើ Auth::user() វានឹងស្គាល់ ->role ភ្លាម
    
    $role = $user->role; 
    $context = "ព័ត៌មានអ្នកប្រើ៖ ឈ្មោះ {$user->name}, តួនាទី {$role}។\n";

    if ($role === 'admin') {
        // Admin: មើលចំនួន User និងមហាវិទ្យាល័យ
        $userCount = DB::table('users')->count();
        $facultyCount = DB::table('faculties')->count();
        $latestAnnounce = DB::table('announcements')->latest()->first();
        
        $context .= "ទិន្នន័យប្រព័ន្ធ៖ មានអ្នកប្រើប្រាស់សរុប {$userCount} នាក់, មហាវិទ្យាល័យចំនួន {$facultyCount}។ ";
        if ($latestAnnounce) {
            $context .= "ការប្រកាសចុងក្រោយគឺ៖ " . $latestAnnounce->title;
        }
    } 
    elseif ($role === 'professor') {
        // Professor: មើលថ្នាក់ដែលខ្លួនបង្រៀន
        $courseCount = DB::table('course_offerings')->where('lecturer_user_id', $user->id)->count();
        $context .= "ទិន្នន័យបង្រៀន៖ លោកគ្រូមានបង្រៀនចំនួន {$courseCount} មុខវិជ្ជា/ថ្នាក់ ក្នុងប្រព័ន្ធ។";
    } 
    elseif ($role === 'student') {
        // Student: មើលវត្តមានខ្លួនឯង
        $present = DB::table('attendance_records')
                    ->where('student_user_id', $user->id)
                    ->where('status', 'present')
                    ->count();
        $absent = DB::table('attendance_records')
                    ->where('student_user_id', $user->id)
                    ->where('status', 'absent')
                    ->count();
                    
        $context .= "ទិន្នន័យសិក្សា៖ ប្អូនមានវត្តមានសរុប {$present} ដង និងអវត្តមាន {$absent} ដង។";
    }

    return $context;
}
}