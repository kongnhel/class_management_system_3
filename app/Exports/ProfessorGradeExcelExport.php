<?php

namespace App\Exports;

use App\Models\Assignment;
use App\Models\CourseOffering;
use App\Models\Exam;
use App\Models\Quiz;
use App\Services\GradingService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfessorGradeExcelExport implements FromCollection, WithStyles
{
    protected CourseOffering $courseOffering;
    protected Collection $students;
    protected Collection $assessments;
    protected array $gradebook;

    public function __construct(CourseOffering $courseOffering, Collection $students, Collection $assessments, array $gradebook)
    {
        $this->courseOffering = $courseOffering;
        $this->students = $students;
        $this->assessments = $assessments;
        $this->gradebook = $gradebook;
    }

    public function collection(): Collection
    {
        return collect([]);
    }

    protected function getLastColLetter(): string
    {
        $assessmentCount = $this->assessments->count();
        $totalCols = 4 + $assessmentCount + 3; // 4 info + assessments + attendance + total + grade
        return $this->colLetter($totalCols - 1);
    }

    protected function colLetter(int $zeroBasedIndex): string
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($zeroBasedIndex + 1);
    }

    public function styles(Worksheet $sheet): array
    {
        $assessmentCount = $this->assessments->count();
        $lastCol = $this->getLastColLetter();
        $khmerFont = 'Khmer OS Battambang';

        // ── Column widths (matching template proportions) ──
        $sheet->getColumnDimension('A')->setWidth(5.55);
        $sheet->getColumnDimension('B')->setWidth(22.66);
        $sheet->getColumnDimension('C')->setWidth(19.66);
        $sheet->getColumnDimension('D')->setWidth(6);
        for ($i = 0; $i < $assessmentCount; $i++) {
            $sheet->getColumnDimension($this->colLetter(4 + $i))->setWidth(14);
        }
        $sheet->getColumnDimension($this->colLetter(4 + $assessmentCount))->setWidth(10);     // Attendance
        $sheet->getColumnDimension($this->colLetter(5 + $assessmentCount))->setWidth(10);     // Total
        $sheet->getColumnDimension($this->colLetter(6 + $assessmentCount))->setWidth(12);     // Grade

        // ── Row 1: ព្រះរាជាណាចក្រកម្ពុជា ──
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'ព្រះរាជាណាចក្រកម្ពុជា');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(20);

        // ── Row 2: ជាតិ សាសនា ព្រះមហាក្សត្រ ──
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2', 'ជាតិ សាសនា ព្រះមហាក្សត្រ');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // ── Row 3-4: Empty ──

        // ── Row 5: Location (merged A5:B5 like template) ──
        $sheet->mergeCells('A5:B5');
        $facultyName = $this->courseOffering->course->department->faculty->name_km
            ?? 'មហាវិទ្យាល័យ';
        $sheet->setCellValue('A5', 'ទីតាំង៖ ' . $facultyName);
        $sheet->getStyle('A5')->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 8],
            'alignment' => ['horizontal' => 'left'],
        ]);

        // ── Row 6: Title ──
        $sheet->mergeCells("A6:{$lastCol}6");
        $courseName = $this->courseOffering->course->title_km ?? $this->courseOffering->course->title_en ?? '';
        $academicYear = $this->courseOffering->academic_year ?? '';
        $semester = $this->courseOffering->semester ?? '';
        $section = $this->courseOffering->section ?? '';
        $sectionText = $section ? " ផ្នែក{$section}" : '';
        $sheet->setCellValue('A6', "បញ្ជីស្រង់ពិន្ទុប្រចាំ{$semester} ឆ្នាំសិក្សា {$academicYear}{$sectionText}");
        $sheet->getStyle('A6')->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 9, 'bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(6)->setRowHeight(18);

        // ── Row 7: Subject + Lecturer info ──
        $sheet->mergeCells("A7:{$lastCol}7");
        $lecturerName = $this->courseOffering->lecturer->name ?? '';
        $phone = $this->courseOffering->lecturer->phone_number ?? '';
        $sheet->setCellValue('A7', "មុខវិជ្ជា {$courseName} បង្រៀនដោយលោក/អ្នកគ្រូ {$lecturerName} លេខទូរស័ព្ទ {$phone}");
        $sheet->getStyle('A7')->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 9],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(7)->setRowHeight(18);

        // ── Rows 8-9: Column headers (merged vertically like template) ──
        $headers = ['ល.រ', 'គោត្តនាម និងនាម', 'ឈ្មោះអង់គ្លេស', 'ភេទ'];
        foreach ($this->assessments as $a) {
            $name = $a->title_km ?? $a->title_en ?? 'Assessment';
            $type = ($a instanceof Assignment) ? 'កិច្ចការ' : (($a instanceof Quiz) ? 'ឃ្វីស' : 'ប្រឡង');
            $maxScore = $a->max_score ?? $a->max_points ?? 100;
            $headers[] = $name . "\n(" . $maxScore . " ពិន្ទុ)";
        }
        $headers[] = 'វត្តមាន';
        $headers[] = 'ពិន្ទុសរុប';
        $headers[] = 'ចំណាត់ថ្នាក់';

        foreach ($headers as $colIndex => $header) {
            $colLetter = $this->colLetter($colIndex);
            $sheet->mergeCells("{$colLetter}8:{$colLetter}9");
            $sheet->setCellValue("{$colLetter}8", $header);
        }

        // Style header rows 8-9
        $sheet->getStyle("A8:{$lastCol}9")->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
            'borders' => [
                'allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => '000000']],
            ],
        ]);
        $sheet->getRowDimension(8)->setRowHeight(25);
        $sheet->getRowDimension(9)->setRowHeight(25);

        // ── Data Rows (start at row 10) ──
        $dataStartRow = 10;
        $borderStyle = [
            'borders' => [
                'allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => '000000']],
            ],
        ];

        foreach ($this->students as $index => $student) {
            $rowNum = $dataStartRow + $index;

            // ល.រ
            $sheet->setCellValue("A{$rowNum}", $index + 1);
            // គោត្តនាម និងនាម
            $sheet->setCellValue("B{$rowNum}", $student->studentProfile->full_name_km ?? $student->name ?? '');
            // ឈ្មោះអង់គ្លេស
            $sheet->setCellValue("C{$rowNum}", $student->studentProfile->full_name_en ?? '');
            // ភេទ
            $gender = $student->studentProfile->gender ?? '';
            $sheet->setCellValue("D{$rowNum}", $gender === 'male' ? 'ប' : ($gender === 'female' ? 'ស' : ''));

            // Assessment scores
            $colOffset = 4;
            $attendanceScore = $student->getAttendanceScoreByCourse($this->courseOffering->id);
            $baseScore = $attendanceScore;
            $quizBonus = 0;

            foreach ($this->assessments as $a) {
                $type = ($a instanceof Assignment) ? 'assignment' : (($a instanceof Quiz) ? 'quiz' : 'exam');
                $score = $this->gradebook[$student->id][$type . '_' . $a->id] ?? 0;
                $sheet->setCellValue($this->colLetter($colOffset) . $rowNum, $score > 0 ? $score : '');
                $colOffset++;
                if ($type === 'quiz') {
                    $quizBonus += $score;
                } else {
                    $baseScore += $score;
                }
            }

            // វត្តមាន (Attendance)
            $sheet->setCellValue($this->colLetter($colOffset) . $rowNum, $attendanceScore > 0 ? $attendanceScore : '');
            $colOffset++;
            // ពិន្ទុសរុប (Total)
            $total = min($baseScore + $quizBonus, 100);
            $sheet->setCellValue($this->colLetter($colOffset) . $rowNum, round($total, 1));
            $colOffset++;
            // ចំណាត់ថ្នាក់ (Grade)
            $sheet->setCellValue($this->colLetter($colOffset) . $rowNum, GradingService::getLetterGrade($total));

            // Style data row
            $sheet->getStyle("A{$rowNum}:{$lastCol}{$rowNum}")->applyFromArray([
                'font' => ['name' => $khmerFont, 'size' => 10],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ]);
            $sheet->getStyle("A{$rowNum}:{$lastCol}{$rowNum}")->applyFromArray($borderStyle);

            // Name columns left-aligned
            $sheet->getStyle("B{$rowNum}")->getAlignment()->setHorizontal('left');
            $sheet->getStyle("C{$rowNum}")->getAlignment()->setHorizontal('left');

            $sheet->getRowDimension($rowNum)->setRowHeight(21.75);
        }

        // ── Summary / Footer rows ──
        $dataEndRow = $dataStartRow + $this->students->count() - 1;
        $footerRow = $dataEndRow + 2;
        $totalCol = $this->colLetter(5 + $assessmentCount);
        $gradeCol = $this->colLetter(6 + $assessmentCount);

        // Calculate stats
        $totals = $this->students->map(function ($s) {
            $att = $s->getAttendanceScoreByCourse($this->courseOffering->id);
            $base = $att;
            $quiz = 0;
            foreach ($this->assessments as $a) {
                $type = ($a instanceof Assignment) ? 'assignment' : (($a instanceof Quiz) ? 'quiz' : 'exam');
                $score = $this->gradebook[$s->id][$type . '_' . $a->id] ?? 0;
                if ($type === 'quiz') {
                    $quiz += $score;
                } else {
                    $base += $score;
                }
            }
            return min($base + $quiz, 100);
        });

        $avgTotal = $totals->count() > 0 ? round($totals->avg(), 1) : 0;
        $passCount = $totals->filter(fn ($t) => $t >= 45)->count();
        $failCount = $totals->count() - $passCount;

        // Average row
        $sheet->mergeCells("A{$footerRow}:D{$footerRow}");
        $sheet->setCellValue("A{$footerRow}", 'មធ្យមភាគរួម');
        $sheet->getStyle("A{$footerRow}")->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => 'right', 'vertical' => 'center'],
        ]);
        $sheet->setCellValue("{$totalCol}{$footerRow}", $avgTotal);
        $sheet->getStyle("A{$footerRow}:{$lastCol}{$footerRow}")->applyFromArray($borderStyle);
        $sheet->getStyle("{$totalCol}{$footerRow}")->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Pass/Fail row
        $pfRow = $footerRow + 1;
        $sheet->mergeCells("A{$pfRow}:D{$pfRow}");
        $sheet->setCellValue("A{$pfRow}", 'អ្នកប្រឡងជាប់ / ធ្លាក់');
        $sheet->getStyle("A{$pfRow}")->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => 'right', 'vertical' => 'center'],
        ]);
        $sheet->setCellValue("{$totalCol}{$pfRow}", "{$passCount} / {$failCount}");
        $sheet->getStyle("A{$pfRow}:{$lastCol}{$pfRow}")->applyFromArray($borderStyle);
        $sheet->getStyle("{$totalCol}{$pfRow}")->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Signature row
        $sigRow = $pfRow + 2;
        $sheet->mergeCells("A{$sigRow}:C{$sigRow}");
        $sheet->setCellValue("A{$sigRow}", 'ហត្ថលេខារបស់លោក/អ្នកគ្រូ');
        $sheet->getStyle("A{$sigRow}")->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 10],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->mergeCells("E{$sigRow}:{$lastCol}{$sigRow}");
        $sheet->setCellValue("E{$sigRow}", 'ហត្ថលេខារបស់នាយកសាលា');
        $sheet->getStyle("E{$sigRow}")->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 10],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // Signature space row
        $sigRow2 = $sigRow + 3;
        $sheet->mergeCells("A{$sigRow2}:C{$sigRow2}");
        $sheet->setCellValue("A{$sigRow2}", $lecturerName);
        $sheet->getStyle("A{$sigRow2}")->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['top' => ['borderStyle' => 'thin', 'color' => ['rgb' => '000000']]],
        ]);
        $sheet->mergeCells("E{$sigRow2}:{$lastCol}{$sigRow2}");
        $sheet->setCellValue("E{$sigRow2}", 'នាយកសាលា');
        $sheet->getStyle("E{$sigRow2}")->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['top' => ['borderStyle' => 'thin', 'color' => ['rgb' => '000000']]],
        ]);

        // ── Print settings ──
        $sheet->getPageSetup()->setOrientation('landscape');
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setBottom(0.5);
        $sheet->getPageMargins()->setLeft(0.3);
        $sheet->getPageMargins()->setRight(0.3);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        return [];
    }
}
