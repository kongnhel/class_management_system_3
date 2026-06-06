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
        Schema::create('course_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            // Professor ដែលបង្រៀនមុខវិជ្ជានេះ
            $table->foreignId('lecturer_user_id')->constrained('users')->onDelete('no action'); // កែពី 'restrict' ទៅ 'no action'
            $table->string('academic_year'); // E.g., '2025-2026'
            $table->string('semester'); // E.g., 'Fall', 'Spring', 'Summer'
            $table->string('section')->nullable(); // E.g., 'A', 'B'
            $table->integer('capacity');
            $table->string('room_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * បញ្ច្រាស Migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_offerings');
    }
};
