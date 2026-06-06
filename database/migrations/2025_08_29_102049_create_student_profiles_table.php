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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 💡 Foreign Key to users table
            $table->string('student_code_id')->unique()->nullable(); // Unique student ID, e.g., "S-2023-001"
            $table->string('full_name_km')->nullable();
            $table->string('full_name_en')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('phone_number')->nullable();
            $table->string('address')->nullable(); // General address
            $table->string('profile_picture_url')->nullable(); // Path to profile picture
            // Add other student-specific fields here
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
