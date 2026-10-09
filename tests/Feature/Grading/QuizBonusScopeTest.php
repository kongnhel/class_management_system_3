<?php

namespace Tests\Feature\Grading;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Department;
use App\Models\ExamResult;
use App\Models\Faculty;
use App\Models\Quiz;
use App\Models\User;
use App\Services\GradingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * checkCriticalComponents() scopes the rows it buckets to the offering being
 * graded, but calculateFinalGrade() summed the quiz bonus from the raw input.
 *
 * Controllers load a student's results across every enrollment they have and
 * then call calculateFinalGrade() once per offering, so a quiz sat in one
 * course was added to the total of every other course — and the overall rank
 * loop counts that number once per offering.
 */
class QuizBonusScopeTest extends TestCase
{
    use RefreshDatabase;

    private User $student;

    private CourseOffering $offeringA;

    private CourseOffering $offeringB;

    protected function setUp(): void
    {
        parent::setUp();

        $faculty = Faculty::create(['name_km' => 'F', 'name_en' => 'Faculty']);
        $department = Department::create([
            'faculty_id' => $faculty->id,
            'name_km' => 'D',
            'name_en' => 'Department',
        ]);
        $course = Course::create([
            'department_id' => $department->id,
            'title_km' => 'C',
            'title_en' => 'Course',
            'credits' => 3,
        ]);
        $lecturer = User::factory()->professor()->create();

        $this->offeringA = $this->makeOffering($course->id, $lecturer->id);
        $this->offeringB = $this->makeOffering($course->id, $lecturer->id);

        $this->student = User::factory()->create();

        // Offering A: an assignment worth 20 and a quiz worth 10.
        $this->recordResult(
            Assignment::create([
                'course_offering_id' => $this->offeringA->id,
                'title_km' => 'A',
                'title_en' => 'Homework',
                'due_date' => now()->addDay(),
                'max_score' => 20,
            ])->id,
            'assignment',
            20,
        );
        $this->recordResult($this->makeQuiz($this->offeringA->id)->id, 'quiz', 10);

        // Offering B: same shape, so either side leaking is visible.
        $this->recordResult(
            Assignment::create([
                'course_offering_id' => $this->offeringB->id,
                'title_km' => 'B',
                'title_en' => 'Homework',
                'due_date' => now()->addDay(),
                'max_score' => 20,
            ])->id,
            'assignment',
            20,
        );
        $this->recordResult($this->makeQuiz($this->offeringB->id)->id, 'quiz', 10);
    }

    private function makeOffering(int $courseId, int $lecturerId): CourseOffering
    {
        return CourseOffering::create([
            'course_id' => $courseId,
            'lecturer_user_id' => $lecturerId,
            'academic_year' => '2025-2026',
            'semester' => 'Fall',
            'capacity' => 50,
        ]);
    }

    private function makeQuiz(int $offeringId): Quiz
    {
        return Quiz::create([
            'course_offering_id' => $offeringId,
            'title_km' => 'Q',
            'title_en' => 'Quiz',
            'max_score' => 100,
            'quiz_date' => now()->addDay(),
        ]);
    }

    private function recordResult(int $assessmentId, string $type, int $score): void
    {
        ExamResult::create([
            'assessment_id' => $assessmentId,
            'assessment_type' => $type,
            'student_user_id' => $this->student->id,
            'score_obtained' => $score,
            'recorded_at' => now(),
        ]);
    }

    /**
     * @return array{0: ExamResult[]|iterable<int, object>, 1: float}
     */
    private function gradeFor(CourseOffering $offering): array
    {
        $allResults = ExamResult::where('student_user_id', $this->student->id)->get();

        $grade = GradingService::calculateFinalGrade(15.0, $allResults, $this->student, $offering->id);

        return [$allResults, (float) $grade['total_score']];
    }

    public function test_a_quiz_from_another_offering_is_not_added_to_this_one(): void
    {
        [, $total] = $this->gradeFor($this->offeringA);

        // attendance 15 + assignment 20 + quiz 10, and nothing from offering B
        $this->assertSame(45.0, $total, 'Offering A picked up a quiz scored against offering B.');
    }

    public function test_scoping_works_in_both_directions(): void
    {
        [, $total] = $this->gradeFor($this->offeringB);

        $this->assertSame(45.0, $total, 'Offering B should count only its own assignment and quiz.');
    }

    public function test_both_students_results_are_still_handed_to_the_service(): void
    {
        [$results] = $this->gradeFor($this->offeringA);

        $this->assertCount(4, $results, 'The service is given the whole cross-offering set; it must scope internally.');
    }
}
