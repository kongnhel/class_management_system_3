<?php

namespace App\Exports;

use App\Models\Assignment;
use App\Models\Exam;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssessmentGradesExport implements FromCollection, WithHeadings, WithStyles
{
    protected $assessment;
    protected string $type;
    protected Collection $students;
    protected Collection $results;

    public function __construct($assessment, string $type, Collection $students, Collection $results)
    {
        $this->assessment = $assessment;
        $this->type = $type;
        $this->students = $students;
        $this->results = $results;
    }

    public function headings(): array
    {
        return ['ID', 'អត្តលេខ', 'ឈ្មោះ', 'ពិន្ទុ', 'កំណត់ចំណាំ'];
    }

    public function collection(): Collection
    {
        return $this->students->map(function ($student) {
            $scoreRecord = $this->results->get($student->id);
            return [
                'id' => $student->id,
                'student_id_code' => $student->student_id_code,
                'name' => $student->userProfile?->full_name_km ?? $student->name,
                'score' => $scoreRecord ? $scoreRecord->score_obtained : '',
                'notes' => $scoreRecord ? $scoreRecord->notes : '',
            ];
        });
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(25);

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'E2E8F0']],
                'alignment' => ['horizontal' => 'center'],
                'borders' => [
                    'allBorders' => ['borderStyle' => 'thin'],
                ],
            ],
        ];
    }
}
