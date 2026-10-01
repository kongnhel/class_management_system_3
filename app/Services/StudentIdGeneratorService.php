<?php

namespace App\Services;

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StudentIdGeneratorService
{
    /**
     * Map degree_level (Khmer) to ID prefix.
     */
    protected static array $prefixMap = [
        'បរិញ្ញាបត្រ' => 'B',
        'បរិញ្ញាបត្ររង' => 'A',
        'អនុបណ្ឌិត' => 'M',
        'បណ្ឌិត' => 'D',
        'វិញ្ញាបនបត្រ' => 'L',
        'ផ្សេងៗ' => 'X',
    ];

    /**
     * Generate a student ID code.
     *
     * Format: [Prefix]-[GenerationRoman]-[Serial6Digit]
     */
    public function generate(int $departmentId, int|string $generation, ?string $degreeLevel = null): string
    {
        if (! $degreeLevel) {
            $department = Department::findOrFail($departmentId);
            $degreeLevel = $department->degree_level;
        }
        $prefix = $this->getPrefix($degreeLevel);
        $romanGen = $this->toRoman((int) $generation);
        $serial = $this->getNextSerial();

        return sprintf('%s-%s-%s', $prefix, $romanGen, str_pad($serial, 6, '0', STR_PAD_LEFT));
    }

    /**
     * Get prefix from degree_level.
     */
    public function getPrefix(?string $degreeLevel): string
    {
        return static::$prefixMap[$degreeLevel] ?? 'X';
    }

    /**
     * Convert an integer to Roman numerals (supports 1–3999).
     */
    public function toRoman(int $num): string
    {
        if ($num < 1 || $num > 3999) {
            return (string) $num;
        }

        $values = [1000, 900, 500, 400, 100, 90, 50, 40, 10, 9, 5, 4, 1];
        $symbols = ['M', 'CM', 'D', 'CD', 'C', 'XC', 'L', 'XL', 'X', 'IX', 'V', 'IV', 'I'];

        $result = '';
        for ($i = 0; $i < count($values); $i++) {
            while ($num >= $values[$i]) {
                $result .= $symbols[$i];
                $num -= $values[$i];
            }
        }

        return $result;
    }

    /**
     * Get the next serial number across all students.
     *
     * The serial is one global running sequence (the total number of students
     * issued so far), not a sequence that restarts for each generation.
     */
    public function getNextSerial(): int
    {
        $max = (int) User::withTrashed()
            ->toBase()
            ->whereNotNull('student_id_code')
            ->selectRaw('MAX(CAST(SUBSTRING_INDEX(student_id_code, "-", -1) AS UNSIGNED)) as serial')
            ->value('serial');

        return max($max + 1, (int) config('services.nmu.student_id_start', 1));
    }

    /**
     * Resolve the [prefix, roman generation] bucket a student belongs to.
     *
     * Reuses the bucket already present in the code when it parses, otherwise
     * derives it from the student's department and generation. Returns null
     * when neither is possible.
     *
     * @return array{0: string, 1: string}|null
     */
    protected function bucketFor(User $student): ?array
    {
        if (preg_match('/^([A-Z])-([A-Z]+)-\d+$/', (string) $student->student_id_code, $matches)) {
            return [$matches[1], $matches[2]];
        }

        if (! $student->department_id || ! $student->generation) {
            return null;
        }

        $generation = (int) $student->generation;
        if ($generation < 1) {
            return null;
        }

        $department = Department::find($student->department_id);
        if (! $department) {
            return null;
        }

        return [$this->getPrefix($department->degree_level), $this->toRoman($generation)];
    }

    /**
     * One-time migration: rewrite every serial into one dense global sequence.
     *
     * Students are numbered by id (creation order) from STUDENT_ID_START, so the
     * serial becomes the total number of students issued rather than a counter
     * that restarts per generation.
     *
     * @return array{renumbered: int, skipped: int}
     */
    public function renumberAllStudents(): array
    {
        $start = (int) config('services.nmu.student_id_start', 1);

        return DB::transaction(function () use ($start) {
            $students = User::withTrashed()
                ->whereNotNull('student_id_code')
                ->orderBy('id')
                ->get(['id', 'student_id_code', 'department_id', 'generation']);

            $todo = [];
            $skippedIds = [];

            foreach ($students as $student) {
                $bucket = $this->bucketFor($student);

                if ($bucket === null) {
                    $skippedIds[] = $student->id;

                    continue;
                }

                $todo[] = ['student' => $student, 'bucket' => $bucket];
            }

            // Clear first so a new serial can never collide with a row that has
            // not been rewritten yet. Only rows we are rewriting are cleared.
            $query = User::withTrashed()->whereNotNull('student_id_code');
            if ($skippedIds) {
                $query->whereNotIn('id', $skippedIds);
            }
            $query->update(['student_id_code' => null]);

            $serial = $start;
            foreach ($todo as $entry) {
                User::withTrashed()
                    ->where('id', $entry['student']->id)
                    ->update([
                        'student_id_code' => sprintf(
                            '%s-%s-%s',
                            $entry['bucket'][0],
                            $entry['bucket'][1],
                            str_pad((string) $serial, 6, '0', STR_PAD_LEFT),
                        ),
                    ]);

                $serial++;
            }

            return ['renumbered' => count($todo), 'skipped' => count($skippedIds)];
        });
    }

    /**
     * One-time migration: assign new-format IDs to all existing students.
     */
    public function migrateExistingStudents(): int
    {
        $students = User::where('role', 'student')
            ->whereNotNull('department_id')
            ->whereNotNull('generation')
            ->orderBy('id')
            ->get();

        $updated = 0;

        DB::transaction(function () use ($students, &$updated) {
            foreach ($students as $student) {
                if ($student->student_id_code && preg_match('/^[A-Z]-[A-Z]+-\d{6}$/', $student->student_id_code)) {
                    continue;
                }

                $newId = $this->generate($student->department_id, $student->generation);

                while (User::where('student_id_code', $newId)->where('id', '!=', $student->id)->exists()) {
                    $parts = explode('-', $newId);
                    $nextSerial = (int) end($parts) + 1;
                    $parts[2] = str_pad($nextSerial, 6, '0', STR_PAD_LEFT);
                    $newId = implode('-', $parts);
                }

                $student->student_id_code = $newId;
                $student->save();
                $updated++;
            }
        });

        return $updated;
    }
}
