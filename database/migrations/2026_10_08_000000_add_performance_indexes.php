<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const INDEXES = [
        'attendances' => [
            'attendances_student_status_date' => ['student_user_id', 'status', 'date'],
            'attendances_date_status' => ['date', 'status'],
        ],
        'users' => [
            'users_role' => ['role'],
        ],
    ];

    public function up(): void
    {
        foreach (self::INDEXES as $table => $indexes) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($indexes as $name => $columns) {
                if (! Schema::hasIndex($table, $columns)) {
                    Schema::table($table, function (Blueprint $blueprint) use ($columns, $name) {
                        $blueprint->index($columns, $name);
                    });
                }
            }
        }
    }

    public function down(): void
    {
        //
    }
};
