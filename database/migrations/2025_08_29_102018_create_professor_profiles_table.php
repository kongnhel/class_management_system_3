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
        Schema::create('professor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 💡 Foreign Key to users table
            $table->string('staff_id')->unique()->nullable(); // Unique staff ID, e.g., "P-2023-001"
            $table->string('full_name_km')->nullable();
            $table->string('full_name_en')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('phone_number')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null'); // Foreign key to departments
            $table->string('position')->nullable(); // e.g., "Lecturer", "Assistant Professor"
            $table->text('qualifications')->nullable(); // e.g., "PhD in CS", "Master in Math"
            $table->text('specializations')->nullable(); // e.g., "AI, Machine Learning"
            $table->string('profile_picture_url')->nullable(); // Path to profile picture
            // Add other professor-specific fields here
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professor_profiles');
    }
};
