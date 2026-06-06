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
            // បន្ថែម column student_id និង is_class_leader (បើមិនទាន់មាន)
            if (! Schema::hasColumn('student_course_enrollments', 'student_id')) {
                $table->unsignedBigInteger('student_id')->after('id');
            }
            if (! Schema::hasColumn('student_course_enrollments', 'is_class_leader')) {
                $table->boolean('is_class_leader')->default(false);
            }
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
