<?php

namespace App\Http\Controllers\professor;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentQuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::all();

        return view('student.quizzes.index', compact('quizzes'));
    }

    public function show(Quiz $quiz)
    {
        $userId = Auth::id();

        $attempts = $quiz->attempts()
            ->where('user_id', $userId)
            ->orderBy('attempt_number', 'desc')
            ->get();

        $canAttempt = $attempts->count() < $quiz->max_attempts;

        $isAvailable = true;
        if ($quiz->start_time && $quiz->start_time->isFuture()) {
            $isAvailable = false;
        }
        if ($quiz->end_time && $quiz->end_time->isPast()) {
            $isAvailable = false;
        }

        return view('student.quizzes.show', compact('quiz', 'attempts', 'canAttempt', 'isAvailable'));
    }

    public function startAttempt(Quiz $quiz)
    {
        $userId = Auth::id();

        $currentAttempts = $quiz->attempts()->where('user_id', $userId)->count();

        if ($currentAttempts >= $quiz->max_attempts) {
            return back()->with('error', 'អ្នកបានប្រើប្រាស់ចំនួនប៉ុនប៉ងអតិបរមា ('.$quiz->max_attempts.') ដែលបានកំណត់ហើយ។');
        }

        if ($quiz->start_time && $quiz->start_time->isFuture()) {
            return back()->with('error', 'Quiz នេះមិនទាន់ដល់ពេលចាប់ផ្តើមនៅឡើយទេ។');
        }
        if ($quiz->end_time && $quiz->end_time->isPast()) {
            return back()->with('error', 'Quiz នេះបានផុតកំណត់ហើយ។');
        }

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $userId,
            'attempt_number' => $currentAttempts + 1,
            'started_at' => Carbon::now(),
            'status' => 'started',
            'score' => null,
        ]);

        return redirect()->route('student.quizzes.take', $attempt->id);
    }

    public function take(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id() || $attempt->status !== 'started') {
            return redirect()->route('student.quizzes.show', $attempt->quiz_id)
                ->with('error', 'ការប៉ុនប៉ងមិនត្រឹមត្រូវ ឬត្រូវបានបញ្ចប់ហើយ។');
        }

        $quiz = $attempt->quiz()->with('questions.options')->first();
        $duration = $quiz->duration_minutes * 60;
        $elapsed = Carbon::now()->diffInSeconds($attempt->started_at);
        $timeRemaining = max(0, $duration - $elapsed);

        return view('student.quizzes.take', compact('attempt', 'quiz', 'timeRemaining'));
    }

    public function submit(Request $request, QuizAttempt $attempt)
    {
        if ($attempt->status === 'finished') {
            return back()->with('error', 'ការប៉ុនប៉ងនេះត្រូវបានបញ្ចប់ហើយ។');
        }

        $attempt->update([
            'status' => 'finished',
            'finished_at' => Carbon::now(),
        ]);

        return redirect()->route('student.quizzes.show', $attempt->quiz_id)
            ->with('success', 'អ្នកបានដាក់ស្នើ Quiz ដោយជោគជ័យ។');
    }
}
