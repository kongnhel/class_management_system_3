<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('re_exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_offering_id')->constrained()->onDelete('cascade');
            $table->string('assessment_type'); // assignment, midterm, final
            $table->unsignedBigInteger('assessment_id'); // FK to assignments or exams table
            $table->decimal('new_score', 5, 2); // re-exam score that replaces original
            $table->date('re_exam_date');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            // One re-exam attempt per student per course per assessment type
            $table->unique(['student_user_id', 'course_offering_id', 'assessment_type'], 're_exam_unique');
            $table->index(['student_user_id', 'course_offering_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('re_exam_results');
    }
};
