<?php

namespace App\Http\Controllers\professor;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProfessorQuestionController extends Controller
{
    /**
     * Store a newly created question in storage.
     *
     * @param  int  $offering_id  - The ID of the Course Offering
     * @param  \App\Models\Quiz  $quiz  - The Quiz model instance
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $offering_id, Quiz $quiz)
    {
        if (Gate::denies('manage-quiz', $quiz)) {
            if ($quiz->courseOffering->professor_id !== Auth::id()) {
                return back()->with('error', 'អ្នកមិនមានសិទ្ធិគ្រប់គ្រង Quiz នេះទេ។');
            }
        }

        // 2. Validation
        $validatedData = $request->validate([
            'type' => 'required|string|in:multiple_choice',
            'text_km' => 'required|string',
            'score' => 'required|numeric|min:0.1',
        ], [
            'type.required' => 'ប្រភេទសំណួរត្រូវបានទាមទារ។',
            'text_km.required' => 'សំណួរ (ខ្មែរ) ត្រូវបានទាមទារ។',
            'score.required' => 'ពិន្ទុត្រូវបានទាមទារ។',
            'score.numeric' => 'ពិន្ទុត្រូវតែជាលេខ។',
            'score.min' => 'ពិន្ទុត្រូវតែមានយ៉ាងតិច ០.១។',
        ]);

        try {
            $question = $quiz->questions()->create([
                'type' => $validatedData['type'],
                'text_km' => $validatedData['text_km'],
                'text_en' => $request->input('text_en', ''),
                'score' => $validatedData['score'],
            ]);

            return redirect()->route('professor.quizzes.manage-questions', ['offering_id' => $offering_id, 'quiz' => $quiz->id])
                ->with('success', 'សំណួរថ្មីត្រូវបានបង្កើតដោយជោគជ័យ!');

        } catch (\Exception $e) {
            \Log::error('Error creating question for quiz '.$quiz->id.': '.$e->getMessage());

            return back()->withInput()->with('error', 'មានបញ្ហាក្នុងការបង្កើតសំណួរ។ សូមព្យាយាមម្តងទៀត។');
        }
    }

    /**
     * Update the specified question in storage.
     *
     * @param  int  $offering_id  - The ID of the Course Offering
     * @param  \App\Models\Quiz  $quiz  - The Quiz model instance
     * @param  \App\Models\Question  $question  - The Question model instance (injected via Route Model Binding)
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $offering_id, Quiz $quiz, Question $question)
    {
        // 1. Authorization: Ensure the professor owns the course offering AND the question belongs to the quiz.
        if ($question->quiz_id !== $quiz->id || $quiz->courseOffering->professor_id !== Auth::id()) {
            return back()->with('error', 'អ្នកមិនមានសិទ្ធិគ្រប់គ្រងសំណួរនេះទេ។');
        }

        // 2. Validation
        $validatedData = $request->validate([
            'type' => 'required|string|in:multiple_choice',
            'text_km' => 'required|string',
            'score' => 'required|numeric|min:0.1',
        ], [
            'type.required' => 'ប្រភេទសំណួរត្រូវបានទាមទារ។',
            'text_km.required' => 'សំណួរ (ខ្មែរ) ត្រូវបានទាមទារ។',
            'score.required' => 'ពិន្ទុត្រូវបានទាមទារ។',
            'score.numeric' => 'ពិន្ទុត្រូវតែជាលេខ។',
            'score.min' => 'ពិន្ទុត្រូវតែមានយ៉ាងតិច ០.១។',
        ]);

        try {
            $question->update([
                'type' => $validatedData['type'],
                'text_km' => $validatedData['text_km'],
                'text_en' => $request->input('text_en', ''),
                'score' => $validatedData['score'],
            ]);

            // 4. Redirect with success
            return redirect()->route('professor.quizzes.manage-questions', ['offering_id' => $offering_id, 'quiz' => $quiz->id])
                ->with('success', 'សំណួរត្រូវបានកែប្រែដោយជោគជ័យ!');

        } catch (\Exception $e) {
            \Log::error('Error updating question '.$question->id.': '.$e->getMessage());

            return back()->withInput()->with('error', 'មានបញ្ហាក្នុងការកែប្រែសំណួរ។ សូមព្យាយាមម្តងទៀត។');
        }
    }

    /**
     * Remove the specified question from storage.
     *
     * @param  int  $offering_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($offering_id, Quiz $quiz, Question $question)
    {
        if ($question->quiz_id !== $quiz->id || $quiz->courseOffering->professor_id !== Auth::id()) {
            return back()->with('error', 'អ្នកមិនមានសិទ្ធិលុបសំណួរនេះទេ។');
        }

        try {
            $question->delete();

            return redirect()->route('professor.quizzes.manage-questions', ['offering_id' => $offering_id, 'quiz' => $quiz->id])
                ->with('success', 'សំណួរត្រូវបានលុបដោយជោគជ័យ!');
        } catch (\Exception $e) {
            \Log::error('Error deleting question '.$question->id.': '.$e->getMessage());

            return back()->with('error', 'មានបញ្ហាក្នុងការលុបសំណួរ។ សូមព្យាយាមម្តងទៀត។');
        }
    }
}
