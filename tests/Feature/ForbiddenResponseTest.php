<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * bootstrap/app.php turns every 403 and 419 into a redirect to /login.
 *
 * That is fine for a browser tab, but 14 fetch/axios call sites send
 * `X-Requested-With` / `Accept: application/json` and would be handed HTML in
 * place of the status code, so `.json()` throws and the failure is invisible.
 *
 * 403 is triggered here through the student route that refuses to show another
 * student's enrolled courses.
 */
class ForbiddenResponseTest extends TestCase
{
    use RefreshDatabase;

    private User $student;

    private User $otherStudent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create();
        $this->otherStudent = User::factory()->create();
    }

    public function test_a_json_caller_keeps_the_403(): void
    {
        $response = $this->actingAs($this->student)
            ->withHeaders(['Accept' => 'application/json'])
            ->get('/student/'.$this->otherStudent->id.'/enrolled-courses');

        $response->assertStatus(403);
        $response->assertJsonStructure(['message']);
    }

    public function test_an_xhr_caller_keeps_the_403(): void
    {
        $response = $this->actingAs($this->student)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get('/student/'.$this->otherStudent->id.'/enrolled-courses');

        $response->assertStatus(403);
    }

    public function test_a_browser_caller_still_gets_the_login_redirect(): void
    {
        $response = $this->actingAs($this->student)
            ->get('/student/'.$this->otherStudent->id.'/enrolled-courses');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    }
}
