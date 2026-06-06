<?php

namespace App\Exports;

use App\Models\StudentCourseEnrollment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CourseStudentsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected $courseOfferingId;

    public function __construct($courseOfferingId)
    {
        $this->courseOfferingId = $courseOfferingId;
    }

    public function collection()
    {
        return StudentCourseEnrollment::with(['student.studentProfile', 'student.studentProgramEnrollments.program'])
            ->where('course_offering_id', $this->courseOfferingId)
            ->get();
    }

    public function map($enrollment): array
    {
        $student = $enrollment->student;
        $profile = $student->studentProfile;
        $program = $student->studentProgramEnrollments->first()?->program;

        return [
            $student->student_id_code ?? 'N/A',
            $profile->full_name_km ?? $student->name,
            ($profile->gender == 'M' || $profile->gender == 'Male') ? 'ប្រុស' : 'ស្រី',
            $profile->date_of_birth ? \Carbon\Carbon::parse($profile->date_of_birth)->format('d/m/Y') : '-',
            $program->name_km ?? '-',
            $profile->phone_number ?? '-',
            $student->email, // អ៊ីមែល
        ];
    }

    public function headings(): array
    {
        return [
            'អត្តលេខ',
            'ឈ្មោះនិស្សិត',
            'ភេទ',
            'ថ្ងៃខែឆ្នាំកំណើត',
            'ជំនាញ/ដេប៉ាតឺម៉ង់',
            'លេខទូរស័ព្ទ',
            'អ៊ីមែល',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
