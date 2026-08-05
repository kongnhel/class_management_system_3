<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    protected $model = Program::class;

    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'name_km' => fake()->unique()->company(),
            'name_en' => fake()->unique()->company(),
            'degree_level' => 'បរិញ្ញាបត្រ',
            'duration_years' => 4,
        ];
    }
}

