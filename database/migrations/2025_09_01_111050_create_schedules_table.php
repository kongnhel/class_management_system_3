<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * រត់ Migration ។
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            // បង្កើត Foreign Key ទៅកាន់តារាង course_offerings
            $table->foreignId('course_offering_id')->constrained()->onDelete('cascade');
            // បន្ថែម Field សម្រាប់កាលវិភាគ
            $table->string('day_of_week');
            $table->foreignId('room_id')->constrained()->onDelete('cascade'); // ជំនួស room_number ទៅជា room_id
            $table->time('start_time');
            $table->time('end_time')->nullable();

            $table->timestamps();
        });
    }

    /**
     * ត្រឡប់ Migration ។
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
