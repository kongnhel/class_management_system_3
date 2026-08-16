<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('attendance_sessions')) {
            Schema::create('attendance_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_offering_id')->constrained()->cascadeOnDelete();
                $table->foreignId('schedule_id')->constrained()->cascadeOnDelete();
                $table->foreignId('professor_id')->constrained('users')->cascadeOnDelete();
                $table->date('attendance_date');
                $table->timestamp('started_at');
                $table->timestamp('closed_at')->nullable();
                $table->timestamps();

                $table->unique(['course_offering_id', 'attendance_date'], 'unique_attendance_session_per_day');
            });
        }

        // The QR-token table exists in older deployments and may use a different
        // storage engine/schema. The application already validates this relation
        // explicitly, so do not add a cross-table FK here during deployment.
        if (Schema::hasTable('attendance_qr_tokens') && ! Schema::hasColumn('attendance_qr_tokens', 'attendance_session_id')) {
            Schema::table('attendance_qr_tokens', function (Blueprint $table) {
                $table->unsignedBigInteger('attendance_session_id')->nullable()->after('course_offering_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('attendance_qr_tokens') && Schema::hasColumn('attendance_qr_tokens', 'attendance_session_id')) {
            Schema::table('attendance_qr_tokens', function (Blueprint $table) {
                $table->dropColumn('attendance_session_id');
            });
        }

        Schema::dropIfExists('attendance_sessions');
    }
};
