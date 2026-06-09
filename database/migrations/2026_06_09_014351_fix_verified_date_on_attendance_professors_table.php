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
            $table->date('verified_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('attendance_professors', function (Blueprint $table) {
            $table->date('verified_date')->nullable(false)->change();
        });
    }
};
