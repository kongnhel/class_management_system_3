<?php

namespace App\Http\Controllers\professor;

use App\Http\Controllers\Controller;
use App\Models\CourseOffering;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfessorQuizController extends Controller
{
    private function ownedOffering(int $offeringId): CourseOffering
    {
        return CourseOffering::where('id', $offeringId)
            ->where('lecturer_user_id', Auth::id())
            ->firstOrFail();
    }

    private function ownedQuiz(int $quizId): Quiz
    {
        return Quiz::where('id', $quizId)
            ->whereHas('courseOffering', fn ($q) => $q->where('lecturer_user_id', Auth::id()))
            ->firstOrFail();
    }

    public function index(int $offeringId)
    {
        $courseOffering = $this->ownedOffering($offeringId)->load('course');
        $quizzes = Quiz::where('course_offering_id', $courseOffering->id)->latest('id')->get();

        return view('professor.Quiz.index', compact('courseOffering', 'quizzes'));
    }

    public function store(Request $request, int $offeringId)
    {
        $courseOffering = $this->ownedOffering($offeringId);

        $validated = $request->validate([
            'title_km' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'max_score' => 'required|numeric|min:1',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'description' => 'nullable|string',
            'description_km' => 'nullable|string',
        ]);

        Quiz::create([
            'course_offering_id' => $courseOffering->id,
            'title_km' => $validated['title_km'],
            'title_en' => $validated['title_en'] ?? null,
            'max_score' => $validated['max_score'],
            'quiz_date' => Carbon::parse($validated['start_time'])->toDateString(),
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'description_km' => $validated['description'] ?? ($validated['description_km'] ?? null),
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('professor.quizzes.index', $courseOffering->id)
            ->with('success', __('quiz_created_successfully'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $quiz = $this->ownedQuiz($quiz->id);

        $validated = $request->validate([
            'title_km' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'max_score' => 'required|numeric|min:1',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'description_km' => 'nullable|string',
        ]);

        $quiz->update([
            'title_km' => $validated['title_km'],
            'title_en' => $validated['title_en'] ?? null,
            'max_score' => $validated['max_score'],
            'quiz_date' => Carbon::parse($validated['start_time'])->toDateString(),
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'description_km' => $validated['description_km'] ?? null,
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('professor.quizzes.index', $quiz->course_offering_id)
            ->with('success', __('quiz_updated_successfully'));
    }

    public function destroy(int $offeringId, Quiz $quiz)
    {
        $quiz = $this->ownedQuiz($quiz->id);
        $quiz->delete();

        return redirect()->route('professor.quizzes.index', $offeringId)
            ->with('success', __('quiz_deleted_successfully'));
    }

    public function manageQuestions(int $offeringId, Quiz $quiz)
    {
        $courseOffering = $this->ownedOffering($offeringId)->load('course');
        $quiz = $this->ownedQuiz($quiz->id);
        $quiz->load('questions');

        return view('professor.Quiz.manage-questions', compact('courseOffering', 'quiz'));
    }

    public function storeQuestion(Request $request, int $offeringId, Quiz $quiz)
    {
        $quiz = $this->ownedQuiz($quiz->id);

        $validated = $request->validate([
            'type' => 'required|in:multiple_choice',
            'text_km' => 'required|string',
            'score' => 'required|numeric|min:0',
        ]);

        $quiz->questions()->create($validated);

        return redirect()->route('professor.quizzes.manage-questions', ['offering_id' => $offeringId, 'quiz' => $quiz->id])
            ->with('success', __('question_saved_successfully'));
    }

    public function updateQuestion(Request $request, int $offeringId, Quiz $quiz, QuizQuestion $question)
    {
        $quiz = $this->ownedQuiz($quiz->id);
        $question = $quiz->questions()->where('id', $question->id)->firstOrFail();

        $validated = $request->validate([
            'type' => 'required|in:multiple_choice',
            'text_km' => 'required|string',
            'score' => 'required|numeric|min:0',
        ]);

        $question->update($validated);

        return redirect()->route('professor.quizzes.manage-questions', ['offering_id' => $offeringId, 'quiz' => $quiz->id])
            ->with('success', __('question_saved_successfully'));
    }

    public function destroyQuestion(int $offeringId, Quiz $quiz, QuizQuestion $question)
    {
        $quiz = $this->ownedQuiz($quiz->id);
        $question = $quiz->questions()->where('id', $question->id)->firstOrFail();
        $question->delete();

        return redirect()->route('professor.quizzes.manage-questions', ['offering_id' => $offeringId, 'quiz' => $quiz->id])
            ->with('success', __('question_deleted_successfully'));
    }
}
