<?php

use App\Services\StudentIdGeneratorService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $generator = new StudentIdGeneratorService();
        $generator->migrateExistingStudents();
    }

    public function down(): void
    {
        // Reset student_id_code to null (cannot reverse the generated IDs)
        Schema::table('users', function ($table) {
            $table->string('student_id_code')->nullable()->unique()->change();
        });
    }
};
