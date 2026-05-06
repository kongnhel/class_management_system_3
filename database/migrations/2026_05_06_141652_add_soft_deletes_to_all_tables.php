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
        // បញ្ជី Table ដែលត្រូវបន្ថែម Soft Deletes
        $tables = [
            'users',
            'courses',
            'course_offerings',
            'departments',
            'programs',
            'faculties'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $blueprint) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'deleted_at')) {
                        $blueprint->softDeletes();
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'courses',
            'course_offerings',
            'departments',
            'programs',
            'faculties'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $blueprint) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'deleted_at')) {
                        $blueprint->dropSoftDeletes();
                    }
                });
            }
        }
    }
};