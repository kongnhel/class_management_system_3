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
        Schema::table('attendance_professors', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance_professors', 'verified_date')) {
                $table->date('verified_date')->nullable()->after('verified_at');
            } else {
                $table->date('verified_date')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance_professors', function (Blueprint $table) {
            if (Schema::hasColumn('attendance_professors', 'verified_date')) {
                $table->dropColumn('verified_date');
            }
        });
    }
};
