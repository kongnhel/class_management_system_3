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
            // Removed ->unique() constraint here to allow multiple NULLs in SQL Server.
            // Uniqueness for actual student IDs will be enforced in application logic/validation.
            if (! Schema::hasColumn('users', 'student_id_code')) {
                $table->string('student_id_code')->nullable()->after('role');
            }

            // Add department_id for professors.
            if (! Schema::hasColumn('users', 'department_id')) {
                $table->foreignId('department_id')
                    ->nullable()
                    ->constrained('departments')
                    ->onDelete('no action')
                    ->after('student_id_code');
            }

            // Add program_id for students.
            if (! Schema::hasColumn('users', 'program_id')) {
                $table->foreignId('program_id')
                    ->nullable()
                    ->constrained('programs')
                    ->onDelete('no action')
                    ->after('department_id');
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
