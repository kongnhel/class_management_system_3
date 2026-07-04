<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('grading_category_id')->nullable()->after('max_score')->constrained('grading_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['grading_category_id']);
            $table->dropColumn('grading_category_id');
        });
    }
};
