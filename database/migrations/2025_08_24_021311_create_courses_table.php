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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')
                ->constrained('departments')
                ->onDelete('no action'); // CHANGED: from 'cascade' to 'no action' for SQL Server compatibility

            // Added program_id column
            $table->foreignId('program_id')
                ->nullable() // Courses can optionally belong to a program, or be general
                ->constrained('programs') // Assumes 'programs' table exists
                ->onDelete('no action'); // Using 'no action' for SQL Server compatibility

            // $table->string('code')->unique(); // E.g., CS101
            $table->string('title_km');
            $table->string('title_en');
            $table->integer('credits');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
