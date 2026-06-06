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
        // Add 'generation' column to the 'users' table
        Schema::table('users', function (Blueprint $table) {
            $table->string('generation')->after('password')->nullable();
        });

        // Add 'generation' column to the 'student_profiles' table
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('generation')->after('student_code_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop 'generation' column from the 'users' table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('generation');
        });

        // Drop 'generation' column from the 'student_profiles' table
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn('generation');
        });
    }
};
