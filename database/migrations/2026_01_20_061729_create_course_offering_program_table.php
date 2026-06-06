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
        Schema::create('course_offering_program', function (Blueprint $table) {
            $table->id();

            // ១. ភ្ជាប់ទៅ Course Offering (មុខវិជ្ជា)
            $table->foreignId('course_offering_id')
                ->constrained('course_offerings')
                ->onDelete('cascade');

            // ២. ភ្ជាប់ទៅ Program (ជំនាញ)
            $table->foreignId('program_id')
                ->constrained('programs')
                ->onDelete('cascade');

            // ៣. ដាក់ជំនាន់ (Generation) នៅទីនេះវិញ
            // ព្រោះ Software អាចរៀន Gen 16 ឯ Networking អាចរៀន Gen 15
            $table->string('generation');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_offering_program');
    }
};
