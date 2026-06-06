<?php

namespace App\Http\Controllers\professor;

use App\Http\Controllers\Controller;
use App\Models\CourseOffering;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    /**
     * @param  int  $offering_id
     */
    public function index($offering_id)
    {
        $user = Auth::user();

        $courseOffering = CourseOffering::findOrFail($offering_id);

        $quizzes = Quiz::where('course_offering_id', $offering_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('professor.quiz.index', compact('courseOffering', 'quizzes'));
    }

    /**
     * @param  int  $offering_id
     */
    public function store(Request $request, $offering_id)
    {
        $offering = CourseOffering::findOrFail($offering_id);

        $request->validate([
            'title_km' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'description_km' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            // ត្រូវបន្ថែម rules ទាំងពីរនេះ ព្រោះ Form ពីមុនមិនទាន់មាន input
            'max_attempts' => ['required', 'integer', 'min:1'],
            'duration_minutes' => ['required', 'integer', 'min:1'],

            'max_score' => ['required', 'numeric', 'min:0'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
            'is_published' => ['boolean'],
        ]);

        Quiz::create([
            'course_offering_id' => $offering_id,
            'title_km' => $request->title_km,
            'title_en' => $request->title_en,
            'description_km' => $request->description_km,
            'description_en' => $request->description_en,
            'max_attempts' => $request->max_attempts,
            'max_score' => $request->max_score,
            'duration_minutes' => $request->duration_minutes,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_published' => $request->boolean('is_published', false),
        ]);

        return redirect()->route('professor.quiz.index', $offering_id)
            ->with('success', 'Quiz ថ្មីត្រូវបានបង្កើតដោយជោគជ័យ។');
    }

    /**
     * @param  int  $offering_id
     */
    public function show($offering_id, Quiz $quiz)
    {

        return view('professor.quiz.show', compact('quiz'));
    }

    /**
     * @param  int  $offering_id
     */
    public function update(Request $request, $offering_id, Quiz $quiz)
    {

        $request->validate([
            'title_km' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'max_attempts' => ['required', 'integer', 'min:1'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'max_score' => ['required', 'numeric', 'min:0'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
            'is_published' => ['boolean'],
        ]);

        $quiz->update([
            'title_km' => $request->title_km,
            'title_en' => $request->title_en,
            'description' => $request->description,
            'max_attempts' => $request->max_attempts,
            'max_score' => $request->max_score,
            'duration_minutes' => $request->duration_minutes,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_published' => $request->boolean('is_published', false),
        ]);

        return redirect()->route('professor.quiz.index', $offering_id)
            ->with('success', 'Quiz ត្រូវបានកែប្រែដោយជោគជ័យ។');
    }

    /**
     * @param  int  $offering_id
     */
    public function destroy($offering_id, Quiz $quiz)
    {
        $quiz->delete();

        return redirect()->route('professor.quiz.index', $offering_id)
            ->with('success', 'Quiz ត្រូវបានលុបដោយជោគជ័យ។');
    }

    /**
     * @param  int  $offering_id
     */
    public function manageQuestions($offering_id, Quiz $quiz)
    {
        return view('professor.quiz.manage-questions', compact('quiz', 'offering_id'));
    }
}
