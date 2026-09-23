<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove duplicates using the query builder so this migration works
        // on both MySQL and SQLite test databases.
        $duplicates = DB::table('attendances')
            ->select('course_offering_id', 'student_user_id', 'date', DB::raw('MIN(id) as keep_id'))
            ->groupBy('course_offering_id', 'student_user_id', 'date')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('attendances')
                ->where('course_offering_id', $duplicate->course_offering_id)
                ->where('student_user_id', $duplicate->student_user_id)
                ->where('date', $duplicate->date)
                ->where('id', '!=', $duplicate->keep_id)
                ->delete();
        }

        Schema::table('attendances', function (Blueprint $table) {
            $table->unique(['course_offering_id', 'student_user_id', 'date'], 'unique_attendance_per_day');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique('unique_attendance_per_day');
        });
    }
};
