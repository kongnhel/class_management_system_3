<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\Program;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SystemSettingSeeder::class,
            PermissionSeeder::class,
        ]);

        // Create base data (faculties, departments, programs, academic year)
        $this->seedBaseData();
    }

    private function seedBaseData(): void
    {
        $now = Carbon::now()->toDateTimeString();

        // Faculties
        $faculties = [
            ['id' => 1, 'name_km' => 'មហាវិទ្យាល័យវិទ្យាសាស្ត្រ និងបច្ចេកវិទ្យា', 'name_en' => 'Faculty of Science and Technology', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name_km' => 'មហាវិទ្យាល័យសេដ្ឋកិច្ច និងគ្រប់គ្រង', 'name_en' => 'Faculty of Economics and Management', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name_km' => 'មហាវិទ្យាល័យអក្សរសាស្ត្រ និងមនុស្សសាស្ត្រ', 'name_en' => 'Faculty of Arts and Humanities', 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('faculties')->insert($faculties);

        // Departments
        $departments = [
            ['id' => 1, 'faculty_id' => 1, 'name_km' => 'ដេប៉ាតឺម៉ង់វិទ្យាសាស្ត្រកុំព្យូទ័រ', 'name_en' => 'Computer Science Department', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'faculty_id' => 1, 'name_km' => 'ដេប៉ាតឺម៉ង់គ្រឿងអេឡិចត្រូនិច', 'name_en' => 'Electronics Department', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'faculty_id' => 2, 'name_km' => 'ដេប៉ាតឺម៉ង់គ្រប់គ្រងពាណិជ្ជកម្ម', 'name_en' => 'Business Administration Department', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'faculty_id' => 3, 'name_km' => 'ដេប៉ាតឺម៉ង់ភាសាអង់គ្លេស', 'name_en' => 'English Department', 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('departments')->insert($departments);

        // Programs
        $programs = [
            ['id' => 1, 'department_id' => 1, 'name_km' => 'បរិញ្ញាបត្រវិទ្យាសាស្ត្រកុំព្យូទ័រ', 'name_en' => 'Bachelor of Computer Science', 'degree_level' => 'បរិញ្ញាបត្រ', 'duration_years' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'department_id' => 3, 'name_km' => 'បរិញ្ញាបត្រគ្រប់គ្រងពាណិជ្ជកម្ម', 'name_en' => 'Bachelor of Business Administration', 'degree_level' => 'បរិញ្ញាបត្រ', 'duration_years' => 4, 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('programs')->insert($programs);

        // Academic Year
        DB::table('academic_years')->insert([
            'name' => '2025-2026',
            'start_date' => '2025-10-01',
            'end_date' => '2026-06-30',
            'is_current' => true,
            'description' => 'Academic Year 2025-2026',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->command->info('Base data seeded: 3 faculties, 4 departments, 2 programs, 1 academic year');
    }
}
