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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            // ត្រូវប្រាកដថា CourseOffering Model និង Migration មានរួចហើយ
            $table->foreignId('course_offering_id')->constrained()->onDelete('cascade');

            $table->string('title_km');
            $table->string('title_en')->nullable();
            $table->text('description')->nullable();

            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();

            $table->unsignedSmallInteger('duration_minutes')->default(60); // ឧ. 60 នាទី
            $table->unsignedSmallInteger('max_attempts')->default(1);

            // បន្ទាត់កំហុសត្រូវបានកែតម្រូវទៅជា decimal().unsigned()
            $table->decimal('max_score', 8, 2)->unsigned(); // ពិន្ទុសរុបដែលអាចទទួលបាន (ឧ. 100.00)

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
