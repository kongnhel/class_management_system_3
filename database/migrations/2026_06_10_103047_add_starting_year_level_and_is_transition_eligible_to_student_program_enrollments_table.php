<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_program_enrollments', function (Blueprint $table) {
            $table->integer('starting_year_level')->default(1)->after('program_id');
            $table->boolean('is_transition_eligible')->default(false)->after('starting_year_level');
        });
    }

    public function down(): void
    {
        Schema::table('student_program_enrollments', function (Blueprint $table) {
            $table->dropColumn(['starting_year_level', 'is_transition_eligible']);
        });
    }
};
