<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function dropForeignKeyIfExists(string $table, string $column): void
    {
        $constraints = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$table}' AND COLUMN_NAME = '{$column}' AND REFERENCED_TABLE_NAME IS NOT NULL");
        foreach ($constraints as $constraint) {
            try {
                Schema::table($table, function (Blueprint $tbl) use ($constraint) {
                    $tbl->dropForeign($constraint->CONSTRAINT_NAME);
                });
            } catch (\Exception $e) {
                // FK doesn't exist or already dropped
            }
        }
    }

    public function up(): void
    {
        // 1. Ensure departments has the needed columns
        if (! Schema::hasColumn('departments', 'degree_level')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->string('degree_level')->nullable()->after('name_en');
            });
        }
        if (! Schema::hasColumn('departments', 'duration_years')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->integer('duration_years')->default(4)->after('degree_level');
            });
        }
        if (! Schema::hasColumn('departments', 'pathway_department_id')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->foreignId('pathway_department_id')->nullable()->after('duration_years')->constrained('departments')->nullOnDelete();
            });
        }

        // 2. Copy data from programs into their parent departments
        if (Schema::hasTable('programs')) {
            $programs = DB::table('programs')->get();
            foreach ($programs as $program) {
                DB::table('departments')->where('id', $program->department_id)->update([
                    'degree_level' => $program->degree_level,
                    'duration_years' => $program->duration_years,
                ]);
            }

            // 3. Copy pathway_program_id → pathway_department_id
            DB::table('departments')
                ->whereIn('id', DB::table('programs')->whereNotNull('pathway_program_id')->pluck('id'))
                ->update([
                    'pathway_department_id' => DB::raw('(SELECT department_id FROM programs WHERE programs.id = departments.id)'),
                ]);
        }

        // 4. Rename student_program_enrollments → student_department_enrollments (if not already done)
        if (Schema::hasTable('student_program_enrollments') && ! Schema::hasTable('student_department_enrollments')) {
            Schema::rename('student_program_enrollments', 'student_department_enrollments');
        }

        // 5. Fix student_department_enrollments
        if (Schema::hasTable('student_department_enrollments')) {
            // Add department_id if missing
            if (! Schema::hasColumn('student_department_enrollments', 'department_id')) {
                Schema::table('student_department_enrollments', function (Blueprint $table) {
                    $table->foreignId('department_id')->nullable()->after('student_user_id')->constrained('departments')->nullOnDelete();
                });
            }

            // Copy data from program_id → department_id if both exist
            if (Schema::hasColumn('student_department_enrollments', 'program_id') && Schema::hasTable('programs')) {
                DB::statement('UPDATE student_department_enrollments SET department_id = (SELECT department_id FROM programs WHERE programs.id = student_department_enrollments.program_id) WHERE department_id IS NULL');
            }

            // Drop program_id column and its FK
            if (Schema::hasColumn('student_department_enrollments', 'program_id')) {
                $this->dropForeignKeyIfExists('student_department_enrollments', 'program_id');
                Schema::table('student_department_enrollments', function (Blueprint $table) {
                    $table->dropColumn('program_id');
                });
            }
        }

        // 6. Fix users table
        if (Schema::hasTable('users')) {
            if (Schema::hasColumn('users', 'temp_department_id')) {
                if (Schema::hasColumn('users', 'department_id')) {
                    // Both exist — just drop the temp column
                    Schema::table('users', function (Blueprint $table) {
                        $table->dropColumn('temp_department_id');
                    });
                } else {
                    // Only temp exists — rename it
                    Schema::table('users', function (Blueprint $table) {
                        $table->renameColumn('temp_department_id', 'department_id');
                    });
                }
            }
            // Also drop program_id if it still lingers
            if (Schema::hasColumn('users', 'program_id')) {
                $this->dropForeignKeyIfExists('users', 'program_id');
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('program_id');
                });
            }
            // If program_id still exists but department_id doesn't, do the full migration
            elseif (Schema::hasColumn('users', 'program_id') && ! Schema::hasColumn('users', 'department_id')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->foreignId('temp_department_id')->nullable()->after('program_id')->constrained('departments')->nullOnDelete();
                });

                if (Schema::hasTable('programs')) {
                    DB::statement('UPDATE users SET temp_department_id = (SELECT department_id FROM programs WHERE programs.id = users.program_id)');
                }

                $this->dropForeignKeyIfExists('users', 'program_id');
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('program_id');
                });

                Schema::table('users', function (Blueprint $table) {
                    $table->renameColumn('temp_department_id', 'department_id');
                });
            }
            // If both department_id and program_id exist, just drop program_id
            elseif (Schema::hasColumn('users', 'program_id') && Schema::hasColumn('users', 'department_id')) {
                $this->dropForeignKeyIfExists('users', 'program_id');
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('program_id');
                });
            }
        }

        // 7. Fix course_offerings
        if (Schema::hasTable('course_offerings')) {
            if (! Schema::hasColumn('course_offerings', 'department_id')) {
                Schema::table('course_offerings', function (Blueprint $table) {
                    $table->foreignId('department_id')->nullable()->after('course_id')->constrained('departments')->nullOnDelete();
                });
            }
            if (! Schema::hasColumn('course_offerings', 'generation')) {
                Schema::table('course_offerings', function (Blueprint $table) {
                    $table->string('generation')->nullable()->after('department_id');
                });
            }

            // Copy from course_offering_program pivot
            if (Schema::hasTable('course_offering_program')) {
                DB::statement('UPDATE course_offerings SET department_id = (SELECT program_id FROM course_offering_program WHERE course_offering_program.course_offering_id = course_offerings.id LIMIT 1)');

                if (Schema::hasTable('programs')) {
                    DB::statement('UPDATE course_offerings SET department_id = (SELECT department_id FROM programs WHERE programs.id = course_offerings.department_id) WHERE department_id IS NOT NULL');
                }

                DB::statement('UPDATE course_offerings SET generation = (SELECT generation FROM course_offering_program WHERE course_offering_program.course_offering_id = course_offerings.id LIMIT 1)');
            }

            // Drop program_id
            if (Schema::hasColumn('course_offerings', 'program_id')) {
                $this->dropForeignKeyIfExists('course_offerings', 'program_id');
                Schema::table('course_offerings', function (Blueprint $table) {
                    $table->dropColumn('program_id');
                });
            }
        }

        // 8. Drop pivot tables and programs table
        Schema::dropIfExists('course_offering_program');
        Schema::dropIfExists('course_program');
        Schema::dropIfExists('programs');
    }

    public function down(): void
    {
        // Not easily reversible
    }
};
