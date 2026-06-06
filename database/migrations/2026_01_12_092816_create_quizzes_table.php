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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            // ភ្ជាប់ទៅកាន់តារាង Course Offerings
            $table->foreignId('course_offering_id')->constrained()->onDelete('cascade');

            // ភ្ជាប់ទៅកាន់តារាង Grading Categories (បើមាន)
            $table->foreignId('grading_category_id')->nullable()->constrained()->onDelete('set null');

            $table->string('title_km'); // ចំណងជើងខ្មែរ
            $table->string('title_en'); // ចំណងជើងអង់គ្លេស
            $table->decimal('max_score', 8, 2)->default(100.00); // ពិន្ទុពេញ
            $table->date('quiz_date'); // ថ្ងៃធ្វើតេស្ត

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
