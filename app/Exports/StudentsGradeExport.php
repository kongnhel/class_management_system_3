<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsGradeExport implements FromCollection, WithHeadings, WithMapping
{
    protected $students;

    public function __construct($students)
    {
        $this->students = $students;
    }

    public function collection()
    {
        return $this->students;
    }

    public function map($student): array
    {
        return [
            $student->id,
            $student->student_id_code,
            $student->studentProfile?->full_name_km ?? $student->name,
        ];
    }

    public function headings(): array
    {
        return ['ID', 'Student Code', 'Name', 'Score', 'Notes'];
    }
}
