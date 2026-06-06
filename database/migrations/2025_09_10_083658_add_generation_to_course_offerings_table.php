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
            // បន្ថែមជួរឈរ 'generation' ថ្មីសម្រាប់តម្រូវការចុះឈ្មោះដោយស្វ័យប្រវត្តិ
            // ប្រើ after('course_id') ដើម្បីឱ្យវាលេចឡើងបន្ទាប់ពីជួរឈរ 'course_id'
            $table->string('generation')->nullable()->after('course_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_offerings', function (Blueprint $table) {
            $table->dropColumn('generation');
        });
    }
};
