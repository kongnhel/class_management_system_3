<?php

namespace Tests\Feature\Student;

use App\Models\Assignment;
use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Department;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Faculty;
use App\Models\Quiz;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * /student/my-assessments rebuilds ExamResult-like objects to feed
 * GradingService::calculateFinalGrade(). It used to stamp every row with
 * assessment_id => 0, which the grading allow-list (type:realId) rejected, so
 * every assessment was dropped and the summary total collapsed to
 * attendance + quiz bonus alone.
 *
 * Exam rows also have to carry their real `exam` model: without it the
 * classifier cannot tell midterm from final and the re-exam advice is wrong.
 */
class MyAssessmentsTest extends TestCase
{
    use RefreshDatabase;

    private User $student;

    private CourseOffering $offering;

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

        $this->offering = CourseOffering::create([
            'course_id' => $course->id,
            'lecturer_user_id' => $lecturer->id,
            'academic_year' => '2025-2026',
            'semester' => 'Fall',
            'capacity' => 50,
        ]);

        $this->student = User::factory()->create();
        StudentCourseEnrollment::create([
            'student_user_id' => $this->student->id,
            'course_offering_id' => $this->offering->id,
            'enrollment_date' => now()->toDateString(),
        ]);

        // attendance: 15/15
        AttendanceRecord::create([
            'course_offering_id' => $this->offering->id,
            'student_user_id' => $this->student->id,
            'user_id' => $this->student->id,
            'date' => now()->toDateString(),
            'status' => 'present',
        ]);

        // assignment: 20/20
        $this->recordResult(
            Assignment::create([
                'course_offering_id' => $this->offering->id,
                'title_km' => 'A',
                'title_en' => 'Homework',
                'due_date' => now()->addDay(),
                'max_score' => 20,
            ])->id,
            'assignment',
            20,
        );

        // midterm: 15/15
        $this->recordResult(
            Exam::create([
                'course_offering_id' => $this->offering->id,
                'title_km' => 'Mid',
                'title_en' => 'Midterm Test',
                'exam_date' => now()->addWeek(),
                'duration_minutes' => 60,
                'max_score' => 15,
            ])->id,
            'exam',
            15,
        );

        // final: 40/50
        $this->recordResult(
            Exam::create([
                'course_offering_id' => $this->offering->id,
                'title_km' => 'Final',
                'title_en' => 'Final Test',
                'exam_date' => now()->addMonth(),
                'duration_minutes' => 120,
                'max_score' => 50,
            ])->id,
            'exam',
            40,
        );

        // quiz bonus: 5
        $this->recordResult(
            Quiz::create([
                'course_offering_id' => $this->offering->id,
                'title_km' => 'Q',
                'title_en' => 'Quiz',
                'max_score' => 100,
                'quiz_date' => now()->addDay(),
            ])->id,
            'quiz',
            5,
        );
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

    public function test_summary_total_counts_every_assessment(): void
    {
        // attendance 15 + assignment 20 + midterm 15 + final 40 + quiz bonus 5
        $response = $this->actingAs($this->student)->get('/student/my-assessments');

        $response->assertStatus(200);

        $this->assertMatchesRegularExpression(
            '/>\s*95\.0\s*</',
            $response->getContent(),
            'Summary total should be 95.0 (attendance 15 + 20 + 15 + 40 + quiz 5).',
        );
    }

    public function test_everything_passing_is_not_reported_as_needing_a_retake(): void
    {
        $response = $this->actingAs($this->student)->get('/student/my-assessments');

        $response->assertStatus(200);

        $response->assertDontSee(__('retake_needed'), false);
    }
}
