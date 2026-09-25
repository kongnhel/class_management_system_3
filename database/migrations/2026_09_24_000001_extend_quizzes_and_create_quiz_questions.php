<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->text('description_km')->nullable();
            $table->text('description_en')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->boolean('is_published')->default(false);
        });

        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->string('type')->default('multiple_choice');
            $table->text('text_km');
            $table->text('text_en')->nullable();
            $table->decimal('score', 8, 2)->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn(['description_km', 'description_en', 'start_time', 'end_time', 'is_published']);
        });
    }
};
