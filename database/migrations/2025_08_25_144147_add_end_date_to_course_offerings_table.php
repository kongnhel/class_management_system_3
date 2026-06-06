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
        Schema::table('course_offerings', function (Blueprint $table) {
            // Adds the 'end_date' column as a nullable datetime.
            // You can change 'nullable()' to 'default(now())' or 'useCurrent()' if a default is desired.
            // $table->dateTime('end_date')->nullable()->after('start_date');
            $table->datetime('end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_offerings', function (Blueprint $table) {
            // Drops the 'end_date' column if the migration is rolled back.
            $table->dropColumn('end_date');
        });
    }
};
