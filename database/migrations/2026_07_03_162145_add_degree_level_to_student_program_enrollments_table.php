<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_program_enrollments', function (Blueprint $table) {
            $table->string('degree_level')->nullable()->after('program_id');
        });
    }

    public function down(): void
    {
        Schema::table('student_program_enrollments', function (Blueprint $table) {
            $table->dropColumn('degree_level');
        });
    }
};
