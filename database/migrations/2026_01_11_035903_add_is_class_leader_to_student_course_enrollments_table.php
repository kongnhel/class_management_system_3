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
        Schema::table('student_course_enrollments', function (Blueprint $table) {
            // បន្ថែម column ប្រភេទ boolean (0 = សិស្សធម្មតា, 1 = ប្រធានថ្នាក់)
            $table->boolean('is_class_leader')->default(false)->after('student_user_id');
        });
    }

    public function down()
    {
        Schema::table('student_course_enrollments', function (Blueprint $table) {
            $table->dropColumn('is_class_leader');
        });
    }
};
