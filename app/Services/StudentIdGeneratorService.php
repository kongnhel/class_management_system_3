<?php

namespace App\Services;

use App\Models\Program;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StudentIdGeneratorService
{
    /**
     * Map degree_level (Khmer) to ID prefix.
     */
    protected static array $prefixMap = [
        'បរិញ្ញាបត្រ' => 'B',  // Bachelor's Degree
        'បរិញ្ញាបត្ររង' => 'A',  // Associate Degree
        'អនុបណ្ឌិត' => 'M',  // Master's Degree
        'បណ្ឌិត' => 'D',  // Doctorate (PhD)
        'វិញ្ញាបនបត្រ' => 'L',  // Diploma/Certificate
        'ផ្សេងៗ' => 'X',  // Other
    ];

    /**
     * Generate a student ID code.
     *
     * Format: [Prefix]-[GenerationRoman]-[Serial6Digit]
     * Example: B-XVI-004686
     */
    public function generate(int $programId, int|string $generation): string
    {
        $program = Program::findOrFail($programId);
        $prefix = $this->getPrefix($program->degree_level);
        $romanGen = $this->toRoman((int) $generation);
        $serial = $this->getNextSerial($prefix, $romanGen);

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
     * Get the next serial number for a given prefix and Roman generation.
     * Queries existing student_id_code values matching the pattern.
     */
    public function getNextSerial(string $prefix, string $romanGen): int
    {
        $pattern = "{$prefix}-{$romanGen}-%";

        $lastCode = User::where('student_id_code', 'LIKE', $pattern)
            ->orderByDesc('student_id_code')
            ->value('student_id_code');

        if (! $lastCode) {
            return 1;
        }

        // Extract the serial part (after the last hyphen)
        $parts = explode('-', $lastCode);
        $lastSerial = (int) end($parts);

        return $lastSerial + 1;
    }

    /**
     * One-time migration: assign new-format IDs to all existing students.
     * Returns the number of students updated.
     */
    public function migrateExistingStudents(): int
    {
        $students = User::where('role', 'student')
            ->whereNotNull('program_id')
            ->whereNotNull('generation')
            ->orderBy('id')
            ->get();

        $updated = 0;

        DB::transaction(function () use ($students, &$updated) {
            foreach ($students as $student) {
                // Skip students that already have a valid new-format ID
                if ($student->student_id_code && preg_match('/^[A-Z]-[A-Z]+-\d{6}$/', $student->student_id_code)) {
                    continue;
                }

                $newId = $this->generate($student->program_id, $student->generation);

                // Ensure uniqueness (handle edge case of duplicates)
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
