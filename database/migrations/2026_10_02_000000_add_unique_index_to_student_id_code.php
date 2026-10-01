<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Enforce student_id_code uniqueness at the database level.
     *
     * The column was created by 2025_08_24_090845 without a unique index, and
     * the two migrations that could have added one both skipped it: one guards
     * on Schema::hasColumn(), the other put ->unique() in down(). Without the
     * index a colliding serial produces a silent duplicate instead of an
     * exception, so nothing defends the sequence.
     *
     * Must run after 2026_10_01_000000_renumber_student_ids_globally, which
     * collapses overlapping serials first. MySQL permits many NULLs in a unique
     * index, so rows without a code are unaffected.
     */
    public function up(): void
    {
        if (Schema::hasIndex('users', 'users_student_id_code_unique')) {
            return;
        }

        $duplicates = DB::table('users')
            ->whereNotNull('student_id_code')
            ->select('student_id_code', DB::raw('COUNT(*) as total'))
            ->groupBy('student_id_code')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicates->isNotEmpty()) {
            $list = $duplicates
                ->map(fn ($row) => $row->student_id_code.' ('.$row->total.')')
                ->implode(', ');

            throw new RuntimeException(
                'Cannot add a unique index to users.student_id_code: duplicate values remain '.
                "after the renumber: {$list}. Fix these rows, then run the migration again."
            );
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unique('student_id_code', 'users_student_id_code_unique');
        });
    }

    /**
     * Reversible: dropping the index restores the previous (unguarded) state.
     */
    public function down(): void
    {
        if (Schema::hasIndex('users', 'users_student_id_code_unique')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_student_id_code_unique');
            });
        }
    }
};
