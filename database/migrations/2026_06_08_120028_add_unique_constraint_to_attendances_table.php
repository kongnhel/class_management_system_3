<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            DELETE a1 FROM attendances a1
            INNER JOIN attendances a2
            WHERE a1.id > a2.id
            AND a1.course_offering_id = a2.course_offering_id
            AND a1.student_user_id = a2.student_user_id
            AND a1.date = a2.date
        ');

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
