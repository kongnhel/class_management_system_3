<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'faculty_id' => Faculty::factory(),
            'name_km' => fake()->unique()->company(),
            'name_en' => fake()->unique()->company(),
        ];
    }
}

