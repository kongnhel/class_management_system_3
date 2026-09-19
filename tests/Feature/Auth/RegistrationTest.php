<?php

use App\Models\Department;
use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $department = Department::factory()->create();
    $student = User::factory()->state([
        'name' => null,
        'email' => null,
        'password' => null,
        'role' => 'student',
        'department_id' => $department->id,
        'generation' => '1',
    ])->create();

    $response = $this->post('/register', [
        'student_id_code' => $student->student_id_code,
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'department_id' => $department->id,
        'generation' => '1',
        'degree_level' => 'បរិញ្ញាបត្រ',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});
