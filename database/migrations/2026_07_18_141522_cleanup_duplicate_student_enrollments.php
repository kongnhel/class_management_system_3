<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Use the query builder so cleanup works on MySQL and SQLite.
        $enrollmentDuplicates = DB::table('student_course_enrollments')
            ->select('student_user_id', 'course_offering_id', DB::raw('MIN(id) as keep_id'))
            ->groupBy('student_user_id', 'course_offering_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($enrollmentDuplicates as $duplicate) {
            DB::table('student_course_enrollments')
                ->where('student_user_id', $duplicate->student_user_id)
                ->where('course_offering_id', $duplicate->course_offering_id)
                ->where('id', '!=', $duplicate->keep_id)
                ->delete();
        }

        $attendanceDuplicates = DB::table('attendances')
            ->select('student_user_id', 'course_offering_id', 'date', DB::raw('MIN(id) as keep_id'))
            ->groupBy('student_user_id', 'course_offering_id', 'date')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($attendanceDuplicates as $duplicate) {
            DB::table('attendances')
                ->where('student_user_id', $duplicate->student_user_id)
                ->where('course_offering_id', $duplicate->course_offering_id)
                ->where('date', $duplicate->date)
                ->where('id', '!=', $duplicate->keep_id)
                ->delete();
        }
    }

    public function down(): void
    {
        // Cannot reverse cleanup
    }
};
