<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_course_enrollments', function (Blueprint $table) {
            $table->unique(['student_user_id', 'course_offering_id'], 'unique_student_course');
        });
    }

    public function down(): void
    {
        Schema::table('student_course_enrollments', function (Blueprint $table) {
            $table->dropUnique('unique_student_course');
        });
    }
};
