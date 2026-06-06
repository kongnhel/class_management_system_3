<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('assignments', function (Blueprint $table) {
            // បន្ថែម column grading_category_id ជាប្រភេទ Foreign Key
            $table->foreignId('grading_category_id')->nullable()->constrained('grading_categories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropForeign(['grading_category_id']);
            $table->dropColumn('grading_category_id');
        });
    }
};
