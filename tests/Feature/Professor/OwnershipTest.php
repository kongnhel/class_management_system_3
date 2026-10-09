<?php

namespace Tests\Feature\Professor;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Department;
use App\Models\ExamResult;
use App\Models\Faculty;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * Professor endpoints are gated by role:professor only, so any professor can
 * reach any URL. These tests pin the second gate: the offering must belong to
 * the authenticated professor.
 *
 * Note on status codes: bootstrap/app.php renders every 403 as a redirect to
 * /login, so the assertions target the effect (was the write attempted?) rather
 * than the HTTP status.
 */
class OwnershipTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $intruder;

    private User $student;

    private CourseOffering $offering;

    private Assignment $assignment;

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

        $this->owner = User::factory()->professor()->create();
        $this->intruder = User::factory()->professor()->create();
        $this->student = User::factory()->create();

        $this->offering = CourseOffering::create([
            'course_id' => $course->id,
            'lecturer_user_id' => $this->owner->id,
            'academic_year' => '2025-2026',
            'semester' => 'Fall',
            'capacity' => 50,
        ]);

        $this->assignment = Assignment::create([
            'course_offering_id' => $this->offering->id,
            'title_km' => 'A',
            'title_en' => 'A',
            'due_date' => now()->addDay(),
            'max_score' => 20,
        ]);

        StudentCourseEnrollment::create([
            'student_user_id' => $this->student->id,
            'course_offering_id' => $this->offering->id,
            'enrollment_date' => now()->toDateString(),
        ]);
    }

    private function assessmentPayload(): array
    {
        return [
            'assessment_type' => 'assignment',
            'title_en' => 'Homework 1',
            'title_km' => 'ផ្ទះ',
            'max_score' => 20,
            'assessment_date' => now()->toDateString(),
        ];
    }

    private function importPayload(): array
    {
        return [
            'excel_file' => UploadedFile::fake()->createWithContent(
                'grades.csv',
                "student,name,note,score\n".$this->student->id.",x,x,15\n"
            ),
            'type' => 'assignment',
            'offering_id' => $this->offering->id,
        ];
    }

    public function test_a_professor_cannot_create_an_assessment_on_another_professors_offering(): void
    {
        $this->actingAs($this->intruder)
            ->post("/professor/course-offerings/{$this->offering->id}/assessments", $this->assessmentPayload());

        $this->assertSame(1, Assignment::count(), 'Intruder managed to create an assessment.');
    }

    public function test_a_professor_can_create_an_assessment_on_their_own_offering(): void
    {
        $this->actingAs($this->owner)
            ->post("/professor/course-offerings/{$this->offering->id}/assessments", $this->assessmentPayload());

        $this->assertSame(2, Assignment::count(), 'Owner could not create an assessment.');
    }

    public function test_a_professor_cannot_import_grades_into_another_professors_assessment(): void
    {
        $this->actingAs($this->intruder)
            ->post("/assessment/{$this->assignment->id}/import-csv", $this->importPayload());

        $this->assertSame(0, ExamResult::count(), 'Intruder managed to import grades.');
    }

    public function test_a_professor_can_import_grades_into_their_own_assessment(): void
    {
        $this->actingAs($this->owner)
            ->post("/assessment/{$this->assignment->id}/import-csv", $this->importPayload());

        $this->assertSame(1, ExamResult::count(), 'Owner could not import grades.');
    }

    public function test_a_professor_cannot_toggle_class_leader_on_another_professors_offering(): void
    {
        $this->actingAs($this->intruder)
            ->patch("/professor/course-offering/{$this->offering->id}/student/{$this->student->id}/toggle-leader");

        $this->assertSame(
            0,
            (int) StudentCourseEnrollment::where('student_user_id', $this->student->id)->value('is_class_leader'),
            'Intruder managed to promote a class leader.'
        );
    }

    public function test_a_professor_can_toggle_class_leader_on_their_own_offering(): void
    {
        $this->actingAs($this->owner)
            ->patch("/professor/course-offering/{$this->offering->id}/student/{$this->student->id}/toggle-leader");

        $this->assertSame(
            1,
            (int) StudentCourseEnrollment::where('student_user_id', $this->student->id)->value('is_class_leader'),
            'Owner could not promote a class leader.'
        );
    }
}
