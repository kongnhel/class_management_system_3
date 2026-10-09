<?php

namespace Tests\Feature\Student;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Department;
use App\Models\ExamResult;
use App\Models\Faculty;
use App\Models\ReExamResult;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Query ceiling for the student ranking endpoints.
 *
 * The ranking loops used to issue one query per (peer student, offering) pair:
 * User::find() + ExamResult + ReExamResult inside every loop iteration. At
 * 20 students x 3 offerings this test measured 348 queries on /student/my-grades
 * under the pre-fix code. The batching in StudentGradeController, StudentController
 * and GradingService::preloadReExams() must keep both endpoints under the
 * ceilings asserted below.
 */
class GradePerformanceTest extends TestCase
{
    use RefreshDatabase;

    private const SMALL_PEERS = 5;

    private const LARGE_PEERS = 20;

    private const OFFERINGS = 3;

    /** Ceiling for a single request at LARGE_PEERS. */
    private const ABSOLUTE_CEILING = ['my-grades' => 80, 'dashboard' => 60];

    /**
     * Allowed growth when going from SMALL_PEERS to LARGE_PEERS.
     *
     * The second request runs with GradingService's static caches already warm
     * (statics survive between requests inside one test process), so this is a
     * loose upper bound rather than a pure scaling measurement. It exists to
     * catch per-peer query patterns, which add (20 - 5) * OFFERINGS * N queries
     * and blow straight through both this and the absolute ceiling.
     */
    private const PEER_GROWTH_CEILING = 15;

    /** @var array<int, CourseOffering> */
    private array $offerings = [];

    /** @var array<int, Assignment> */
    private array $assignments = [];

    /** @var Collection<int, User> */
    private $students;

    protected function setUp(): void
    {
        parent::setUp();

        fake()->seed(42);

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

        foreach (range(1, self::OFFERINGS) as $n) {
            $offering = CourseOffering::create([
                'course_id' => $course->id,
                'lecturer_user_id' => $lecturer->id,
                'academic_year' => '2025-2026',
                'semester' => 'Fall',
                'capacity' => 50,
            ]);

            $this->offerings[] = $offering;
            $this->assignments[] = Assignment::create([
                'course_offering_id' => $offering->id,
                'title_km' => 'A'.$n,
                'title_en' => 'A'.$n,
                'due_date' => now()->addDay(),
                'max_score' => 20,
            ]);
        }

        $this->students = collect();
    }

    private function enrollStudents(int $count): void
    {
        $new = User::factory()->count($count)->create();

        foreach ($new as $i => $student) {
            foreach ($this->offerings as $k => $offering) {
                StudentCourseEnrollment::create([
                    'student_user_id' => $student->id,
                    'course_offering_id' => $offering->id,
                    'enrollment_date' => now()->toDateString(),
                ]);

                ExamResult::create([
                    'assessment_id' => $this->assignments[$k]->id,
                    'assessment_type' => 'assignment',
                    'student_user_id' => $student->id,
                    'score_obtained' => 10 + ($i % 5),
                    'recorded_at' => now(),
                ]);

                ReExamResult::create([
                    'student_user_id' => $student->id,
                    'course_offering_id' => $offering->id,
                    'assessment_type' => 'assignment',
                    'assessment_id' => $this->assignments[$k]->id,
                    'new_score' => 12,
                    're_exam_date' => now()->toDateString(),
                ]);
            }
        }

        $this->students = $this->students->concat($new);
    }

    private function measure(string $uri): int
    {
        $count = 0;
        DB::listen(function () use (&$count) {
            $count++;
        });

        $this->actingAs($this->students->first())->get($uri)->assertStatus(200);

        return $count;
    }

    public function test_ranking_endpoints_do_not_scale_with_peer_count(): void
    {
        $this->enrollStudents(self::SMALL_PEERS);

        $small = [
            'my-grades' => $this->measure('/student/my-grades'),
            'dashboard' => $this->measure('/student/dashboard'),
        ];

        $this->enrollStudents(self::LARGE_PEERS - self::SMALL_PEERS);

        $large = [
            'my-grades' => $this->measure('/student/my-grades'),
            'dashboard' => $this->measure('/student/dashboard'),
        ];

        fwrite(STDERR, sprintf(
            "\nQUERY my-grades %d -> %d   dashboard %d -> %d\n",
            $small['my-grades'], $large['my-grades'],
            $small['dashboard'], $large['dashboard'],
        ));

        foreach (['my-grades', 'dashboard'] as $route) {
            $this->assertLessThanOrEqual(
                self::ABSOLUTE_CEILING[$route],
                $large[$route],
                sprintf('/student/%s issued %d queries at %d peers.', $route, $large[$route], self::LARGE_PEERS),
            );

            $growth = $large[$route] - $small[$route];
            $this->assertLessThanOrEqual(
                self::PEER_GROWTH_CEILING,
                $growth,
                sprintf(
                    '/student/%s grew by %d queries when peers went from %d to %d.',
                    $route, $growth, self::SMALL_PEERS, self::LARGE_PEERS,
                ),
            );
        }
    }
}
