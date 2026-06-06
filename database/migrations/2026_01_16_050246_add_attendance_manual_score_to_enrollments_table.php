<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('student_course_enrollments', function (Blueprint $table) {
            // បន្ថែម column attendance_score_manual (ពិន្ទុពេញ ១៥ ដូច្នេះប្រើ decimal ៥,២ គឺគ្រប់គ្រាន់)
            $table->decimal('attendance_score_manual', 5, 2)->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_course_enrollments', function (Blueprint $table) {
            //
        });
    }
};
