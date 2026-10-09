<?php

namespace Tests\Feature\Auth;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * POST /register is authorised by a student_id_code, which the generator
 * issues as one dense sequential range — so an unauthenticated caller can walk
 * the range until they find a pre-created account and claim it. The AJAX
 * lookup at /api/check-student was already limited to 10/min, but this route
 * answered the same question with no limit at all.
 */
class RegistrationThrottleTest extends TestCase
{
    use RefreshDatabase;

    private Department $department;

    protected function setUp(): void
    {
        parent::setUp();

        $faculty = Faculty::create(['name_km' => 'F', 'name_en' => 'Faculty']);
        $this->department = Department::create([
            'faculty_id' => $faculty->id,
            'name_km' => 'D',
            'name_en' => 'Department',
        ]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'student_id_code' => 'STU99999',
            'email' => 'registrar@example.com',
            'name' => 'Test Student',
            'department_id' => $this->department->id,
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'generation' => '1',
            'degree_level' => 'bachelor',
        ], $overrides);
    }

    public function test_attempts_are_rate_limited_per_ip(): void
    {
        foreach (range(1, 5) as $attempt) {
            $this->post('/register', $this->payload(['email' => "probe{$attempt}@example.com"]))
                ->assertStatus(302, "Attempt {$attempt} should have been served.");
        }

        $this->post('/register', $this->payload(['email' => 'probe6@example.com']))
            ->assertStatus(429);
    }

    public function test_a_pre_created_student_can_still_register(): void
    {
        $student = User::factory()->create([
            'student_id_code' => 'STU99999',
            'email' => null,
            'password' => null,
        ]);

        $this->post('/register', $this->payload())
            ->assertRedirect();

        $student->refresh();

        $this->assertNotNull($student->password, 'The account should have been claimed.');
        $this->assertSame('registrar@example.com', $student->email);
        $this->assertAuthenticatedAs($student);
    }
}
