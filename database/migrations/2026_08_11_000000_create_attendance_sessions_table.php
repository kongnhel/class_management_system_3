<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

        Schema::table('attendance_qr_tokens', function (Blueprint $table) {
            $table->foreignId('attendance_session_id')->nullable()->after('course_offering_id')
                ->constrained('attendance_sessions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendance_qr_tokens', function (Blueprint $table) {
            $table->dropConstrainedForeignId('attendance_session_id');
        });

        Schema::dropIfExists('attendance_sessions');
    }
};
