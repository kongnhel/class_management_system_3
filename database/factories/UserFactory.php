<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'role' => 'student',
            'student_id_code' => fake()->unique()->numerify('STU#####'),
            'generation' => (string) fake()->numberBetween(1, 20),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'student_id_code' => null,
            'generation' => null,
        ]);
    }

    public function professor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'professor',
            'student_id_code' => null,
            'generation' => null,
        ]);
    }
}
;