<?php

namespace App\Http\Controllers\professor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfessorAssigmentController extends Controller
{
    /**
     * Manage exams for a specific course offering.
     */
    public function manageExams($offering_id)
    {
        $courseOffering = CourseOffering::with('course')->findOrFail($offering_id);
        $exams = Exam::where('course_offering_id', $offering_id)
            ->orderBy('exam_date', 'asc')
            ->paginate(10);

        return view('professor.manage-exams', compact('courseOffering', 'exams'));
    }

    /**
     * Store a newly created exam for a specific course offering.
     */
    public function storeExam(Request $request, $offering_id)
    {
        $validatedData = $request->validate([
            'title_km' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_km' => 'nullable|string',
            'description_en' => 'nullable|string',
            'exam_date' => 'required|date',
            'duration_minutes' => 'required|integer|min:10',
            'max_score' => 'required|numeric|min:0',
        ]);
        $courseOffering = CourseOffering::where('id', $offering_id)
            ->where('lecturer_user_id', Auth::id())
            ->firstOrFail();

        $exam = new Exam($validatedData);
        $exam->course_offering_id = $courseOffering->id;
        $exam->save();

        return redirect()->route('professor.manage-exams', ['offering_id' => $offering_id])
            ->with('success', 'ការប្រលងត្រូវបានបន្ថែមដោយជោគជ័យ!');
    }
}
