<?php

namespace Tests\Feature\Auth;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * store()'s catch block used to do back()->with('error', 'Error: '.$e->getMessage()).
 *
 * QueryException::formatMessage() builds
 *   "{driver error} (Connection: {name}{host, port, database}, SQL: {sql with bindings inlined})"
 * so a failure on this route handed an unauthenticated caller the database
 * connection details, the statement, and every bound value — including the
 * bcrypt hash of the password just submitted.
 *
 * Any failure inside the transaction qualifies: an email unique race, a
 * duplicate enrollment race, or the database being unreachable.
 */
class RegistrationErrorLeakTest extends TestCase
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

        User::factory()->create([
            'student_id_code' => 'STU99999',
            'email' => null,
            'password' => null,
        ]);
    }

    private function payload(): array
    {
        return [
            'student_id_code' => 'STU99999',
            'email' => 'new-student@example.com',
            'name' => 'Test Student',
            'department_id' => $this->department->id,
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'generation' => '1',
            'degree_level' => 'bachelor',
        ];
    }

    public function test_a_failure_inside_registration_does_not_leak_the_exception(): void
    {
        // Blow up inside the transaction with text shaped like a QueryException.
        Event::listen(Registered::class, function (): void {
            throw new \RuntimeException(
                'SQLSTATE[HY000] [2002] Connection refused '
                .'(Connection: mysql (host=10.0.0.5, db=prod_nmu), SQL: select * from `users` where `email` = ?)'
            );
        });

        $response = $this->post('/register', $this->payload());

        $response->assertStatus(302);
        $response->assertSessionHas('error', __('auth_error_generic'));

        $error = (string) session('error');
        foreach (['SQLSTATE', 'Connection refused', '10.0.0.5', 'prod_nmu', 'select * from'] as $needle) {
            $this->assertStringNotContainsString($needle, $error, 'Exception text reached the response.');
        }
    }

    public function test_the_transaction_still_rolls_back_when_it_fails(): void
    {
        Event::listen(Registered::class, function (): void {
            throw new \RuntimeException('boom');
        });

        $this->post('/register', $this->payload());

        $this->assertNull(
            User::where('student_id_code', 'STU99999')->first()->password,
            'The account must not be half-claimed after a failed registration.',
        );
    }
}
