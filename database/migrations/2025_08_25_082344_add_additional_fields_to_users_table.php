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
        Schema::table('users', function (Blueprint $table) {
            // Add student_id_code if it doesn't exist.
            // It should be nullable as only students have it.
            // Moved after 'role' for logical placement.
            if (! Schema::hasColumn('users', 'student_id_code')) {
                $table->string('student_id_code')->unique()->nullable()->after('role');
            }

            // Add department_id for professors.
            // It should be nullable as only professors have it.
            // Changed onDelete('set null') to onDelete('no action') for SQL Server compatibility.
            if (! Schema::hasColumn('users', 'department_id')) {
                $table->foreignId('department_id')
                    ->nullable()
                    ->constrained('departments') // Assumes 'departments' table exists
                    ->onDelete('no action') // Changed from 'set null'
                    ->after('student_id_code'); // Place after student_id_code
            }

            // Add program_id for students.
            // It should be nullable as only students have it.
            // Changed onDelete('set null') to onDelete('no action') for SQL Server compatibility.
            if (! Schema::hasColumn('users', 'program_id')) {
                $table->foreignId('program_id')
                    ->nullable()
                    ->constrained('programs') // Assumes 'programs' table exists
                    ->onDelete('no action') // Changed from 'set null'
                    ->after('department_id'); // Place after department_id
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign keys first to avoid integrity constraints errors
            if (Schema::hasColumn('users', 'program_id')) {
                $table->dropForeign(['program_id']);
                $table->dropColumn('program_id');
            }
            if (Schema::hasColumn('users', 'department_id')) {
                $table->dropForeign(['department_id']);
                $table->dropColumn('department_id');
            }
            if (Schema::hasColumn('users', 'student_id_code')) {
                $table->dropColumn('student_id_code');
            }
        });
    }
};
