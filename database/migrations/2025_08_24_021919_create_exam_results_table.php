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
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();

            // ១. ប្តូរពី exam_id មកជា assessment_id និងដក constrained() ចេញ
            // ដើម្បីឱ្យវាអាចទទួលយក ID ពីតារាងផ្សេងៗគ្នា (Exams, Assignments, Quizzes)
            $table->unsignedBigInteger('assessment_id');

            // ២. បន្ថែម assessment_type ដើម្បីចំណាំប្រភេទ (exam, assignment, quiz)
            $table->string('assessment_type');

            // ៣. រក្សាទុក student_user_id ភ្ជាប់ទៅតារាង users ដដែល
            $table->foreignId('student_user_id')->constrained('users')->onDelete('cascade');

            $table->integer('score_obtained');
            $table->dateTime('recorded_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            // ៤. បន្ថែម Index ដើម្បីឱ្យការ Query ទិន្នន័យបានលឿន
            $table->index(['assessment_id', 'assessment_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
