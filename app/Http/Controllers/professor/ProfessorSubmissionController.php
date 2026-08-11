<?php

namespace App\Http\Controllers\professor;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\CourseOffering;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfessorSubmissionController extends Controller
{
    /**
     * Verify that the assignment belongs to the authenticated professor's course offering.
     */
    private function authorizeAssignment(Assignment $assignment): void
    {
        $userId = Auth::id();
        $courseOffering = $assignment->courseOffering;

        if (! $courseOffering || $courseOffering->lecturer_user_id !== $userId) {
            abort(403, 'អ្នកមិនមានសិទ្ធិចូលប្រើប្រាស់ទិន្នន័យនេះទេ។');
        }
    }

    /**
     * Display a listing of submissions for an assignment.
     */
    public function index(Request $request, $offeringId, $assignmentId)
    {
        $courseOffering = CourseOffering::with('course')->findOrFail($offeringId);

        if ($courseOffering->lecturer_user_id !== Auth::id()) {
            abort(403, 'អ្នកមិនមានសិទ្ធិចូលប្រើប្រាស់មុខវិជ្ជានេះទេ។');
        }

        $assignment = Assignment::findOrFail($assignmentId);

        $this->authorizeAssignment($assignment);

        $submissions = Submission::where('assignment_id', $assignmentId)
            ->with('student.userProfile', 'student.studentProfile')
            ->orderBy('submission_date', 'desc');

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $submissions->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('student_id_code', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            if ($request->status === 'graded') {
                $submissions->whereNotNull('grade_received');
            } elseif ($request->status === 'ungraded') {
                $submissions->whereNull('grade_received');
            }
        }

        $submissions = $submissions->paginate(15)->withQueryString();

        return view('professor.submissions.index', compact('courseOffering', 'assignment', 'submissions'));
    }

    /**
     * Display the specified submission.
     */
    public function show($offeringId, $assignmentId, $submissionId)
    {
        $courseOffering = CourseOffering::with('course')->findOrFail($offeringId);

        if ($courseOffering->lecturer_user_id !== Auth::id()) {
            abort(403, 'អ្នកមិនមានសិទ្ធិចូលប្រើប្រាស់មុខវិជ្ជានេះទេ។');
        }

        $assignment = Assignment::findOrFail($assignmentId);
        $this->authorizeAssignment($assignment);

        $submission = Submission::with('student.userProfile', 'student.studentProfile')
            ->where('assignment_id', $assignmentId)
            ->findOrFail($submissionId);

        return view('professor.submissions.show', compact('courseOffering', 'assignment', 'submission'));
    }

    /**
     * Grade a submission with feedback.
     */
    public function grade(Request $request, $offeringId, $assignmentId, $submissionId)
    {
        $courseOffering = CourseOffering::findOrFail($offeringId);

        if ($courseOffering->lecturer_user_id !== Auth::id()) {
            abort(403, 'អ្នកមិនមានសិទ្ធិចូលប្រើប្រាស់មុខវិជ្ជានេះទេ។');
        }

        $assignment = Assignment::findOrFail($assignmentId);
        $this->authorizeAssignment($assignment);

        $submission = Submission::where('assignment_id', $assignmentId)
            ->findOrFail($submissionId);

        $validatedData = $request->validate([
            'grade_received' => 'required|integer|min:0|max:'.$assignment->max_score,
            'feedback' => 'nullable|string|max:1000',
        ]);

        $submission->update([
            'grade_received' => $validatedData['grade_received'],
            'feedback' => $validatedData['feedback'] ?? null,
        ]);

        return back()->with('success', 'ការដាក់ពិន្ទុបានជោគជ័យ!');
    }

    /**
     * Download a submission file.
     */
    public function download($offeringId, $assignmentId, $submissionId)
    {
        $courseOffering = CourseOffering::findOrFail($offeringId);

        if ($courseOffering->lecturer_user_id !== Auth::id()) {
            abort(403, 'អ្នកមិនមានសិទ្ធិចូលប្រើប្រាស់មុខវិជ្ជានេះទេ។');
        }

        $assignment = Assignment::findOrFail($assignmentId);
        $this->authorizeAssignment($assignment);

        $submission = Submission::where('assignment_id', $assignmentId)
            ->findOrFail($submissionId);

        if (! $submission->file_path || ! Storage::exists($submission->file_path)) {
            return back()->with('error', 'មិនមានឯកសារសម្រាប់ទាញយកទេ។');
        }

        $fileName = basename($submission->file_path);

        return Storage::download($submission->file_path, $fileName);
    }
}
