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
        // Add soft deletes to users table
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to courses table
        if (Schema::hasTable('courses') && !Schema::hasColumn('courses', 'deleted_at')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to course_offerings table
        if (Schema::hasTable('course_offerings') && !Schema::hasColumn('course_offerings', 'deleted_at')) {
            Schema::table('course_offerings', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to departments table
        if (Schema::hasTable('departments') && !Schema::hasColumn('departments', 'deleted_at')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to programs table
        if (Schema::hasTable('programs') && !Schema::hasColumn('programs', 'deleted_at')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to faculties table
        if (Schema::hasTable('faculties') && !Schema::hasColumn('faculties', 'deleted_at')) {
            Schema::table('faculties', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop soft deletes from tables
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('courses') && Schema::hasColumn('courses', 'deleted_at')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('course_offerings') && Schema::hasColumn('course_offerings', 'deleted_at')) {
            Schema::table('course_offerings', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('departments') && Schema::hasColumn('departments', 'deleted_at')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('programs') && Schema::hasColumn('programs', 'deleted_at')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('faculties') && Schema::hasColumn('faculties', 'deleted_at')) {
            Schema::table('faculties', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
