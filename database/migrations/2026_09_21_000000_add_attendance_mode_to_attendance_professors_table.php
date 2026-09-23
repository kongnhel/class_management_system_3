<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('attendance_professors', 'attendance_mode')) {
            Schema::table('attendance_professors', function (Blueprint $table) {
                $table->string('attendance_mode', 20)->default('on_campus')->after('session_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('attendance_professors', 'attendance_mode')) {
            Schema::table('attendance_professors', function (Blueprint $table) {
                $table->dropColumn('attendance_mode');
            });
        }
    }
};
