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
            // Check if student_id_code column exists before adding
            if (! Schema::hasColumn('users', 'student_id_code')) {
                $table->string('student_id_code')->nullable()->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Check if student_id_code column exists before attempting to drop it
            if (Schema::hasColumn('users', 'student_id_code')) {
                $table->dropColumn('student_id_code');
            }
        });
    }
};
