<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AdminGradeOfferingsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(private readonly Collection $offerings)
    {
    }

    public function collection(): Collection
    {
        return $this->offerings;
    }

    public function headings(): array
    {
        return ['Course Code', 'Course', 'Professor', 'Department', 'Semester', 'Academic Year', 'Generation', 'Students'];
    }

    public function map($offering): array
    {
        return [
            $offering->course?->code,
            $offering->course?->title_en ?: $offering->course?->title_km,
            $offering->lecturer?->name,
            $offering->department?->name_en ?: $offering->department?->name_km,
            $offering->semester,
            $offering->academic_year,
            $offering->generation,
            $offering->student_course_enrollments_count,
        ];
    }
}
