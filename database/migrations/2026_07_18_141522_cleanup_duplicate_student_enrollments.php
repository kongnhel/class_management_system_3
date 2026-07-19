<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove duplicate enrollments, keeping the one with the lowest id
        DB::statement('
            DELETE s1 FROM student_course_enrollments s1
            INNER JOIN student_course_enrollments s2
            WHERE s1.id > s2.id
            AND s1.student_user_id = s2.student_user_id
            AND s1.course_offering_id = s2.course_offering_id
        ');

        // Also clean up duplicate attendance records
        DB::statement('
            DELETE a1 FROM attendances a1
            INNER JOIN attendances a2
            WHERE a1.id > a2.id
            AND a1.student_user_id = a2.student_user_id
            AND a1.course_offering_id = a2.course_offering_id
            AND a1.date = a2.date
        ');
    }

    public function down(): void
    {
        // Cannot reverse cleanup
    }
};
