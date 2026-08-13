<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Grading Settings
        SystemSetting::set('absence_threshold', '2', 'number', 'grading');
        SystemSetting::set('permission_threshold', '4', 'number', 'grading');
        SystemSetting::set('attendance_weight', '15', 'number', 'grading');
        SystemSetting::set('midterm_weight', '15', 'number', 'grading');
        SystemSetting::set('group_assignment_weight', '20', 'number', 'grading');
        SystemSetting::set('final_exam_weight', '50', 'number', 'grading');

        // Enrollment Settings
        SystemSetting::set('self_enrollment_enabled', '1', 'boolean', 'enrollment');
        SystemSetting::set('max_enrollment_per_course', '50', 'number', 'enrollment');

        // Registration Settings
        SystemSetting::set('registration_open', '1', 'boolean', 'registration');
        SystemSetting::set('registration_start', now()->format('Y-m-d'), 'text', 'registration');
        SystemSetting::set('registration_end', now()->addMonth()->format('Y-m-d'), 'text', 'registration');

        // General Settings
        SystemSetting::set('school_name', 'សាកលវិទ្យាល័យជាតិមានជ័យ', 'text', 'general');
        SystemSetting::set('school_name_en', 'National University of Meanchey', 'text', 'general');
        SystemSetting::set('current_academic_year', now()->year.'-'.(now()->year + 1), 'text', 'general');
    }
}
