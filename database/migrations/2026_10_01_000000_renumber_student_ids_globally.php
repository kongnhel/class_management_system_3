<?php

use App\Services\StudentIdGeneratorService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Rewrite every student_id_code into one dense global sequence.
     *
     * The serial becomes the total number of students issued (starting at
     * STUDENT_ID_START) rather than a counter that restarts for each
     * generation, so two generations can no longer hold the same serial.
     *
     * Students are numbered by id (creation order). Rows whose bucket cannot
     * be resolved keep their existing code untouched.
     */
    public function up(): void
    {
        $result = (new StudentIdGeneratorService)->renumberAllStudents();

        printf(
            "  student_id_code: %d renumbered, %d skipped\n",
            $result['renumbered'],
            $result['skipped'],
        );
    }

    /**
     * Not reversible: the original serials are unrecoverable once rewritten.
     */
    public function down(): void
    {
        //
    }
};
