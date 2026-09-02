<?php

namespace App\Exports;

use App\Models\CourseOffering;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminAttendanceExcelExport implements FromCollection, WithDrawings, WithStyles
{
    protected CourseOffering $courseOffering;

    protected Collection $studentAttendance;

    protected array $stats;

    public function __construct(CourseOffering $courseOffering, Collection $studentAttendance, array $stats)
    {
        $this->courseOffering = $courseOffering;
        $this->studentAttendance = $studentAttendance;
        $this->stats = $stats;
    }

    public function collection(): Collection
    {
        return collect([]);
    }

    public function drawings(): array
    {
        $logoPath = public_path('assets/image/nmu_Logo.png');
        if (! file_exists($logoPath)) {
            return [];
        }

        $logo = new Drawing;
        $logo->setName('NMU Logo');
        $logo->setDescription('National Meanchey University Logo');
        $logo->setPath($logoPath);
        $logo->setCoordinates('A1');
        $logo->setOffsetX(0);
        $logo->setOffsetY(0);
        $logo->setResizeProportional(false);
        $logo->setWidth(80);
        $logo->setHeight(80);

        return [$logo];
    }

    protected function colLetter(int $zeroBasedIndex): string
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($zeroBasedIndex + 1);
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = 'G';
        $khmerFont = 'Khmer OS Battambang';

        $sheet->getColumnDimension('A')->setWidth(5.55);
        $sheet->getColumnDimension('B')->setWidth(22.66);
        $sheet->getColumnDimension('C')->setWidth(19.66);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(10);

        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(20);
        $sheet->getRowDimension(4)->setRowHeight(20);

        $sheet->mergeCells("A3:{$lastCol}3");
        $sheet->setCellValue('A3', 'ព្រះរាជាណាចក្រកម្ពុជា');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        $sheet->mergeCells("A4:{$lastCol}4");
        $sheet->setCellValue('A4', 'ជាតិ សាសនា ព្រះមហាក្សត្រ');
        $sheet->getStyle('A4')->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        $sheet->mergeCells('A7:B7');
        $facultyName = $this->courseOffering->course->department->faculty->name_km ?? 'មហាវិទ្យាល័យ';
        $sheet->setCellValue('A7', 'ទីតាំង៖ '.$facultyName);
        $sheet->getStyle('A7')->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 8],
            'alignment' => ['horizontal' => 'left'],
        ]);

        $sheet->mergeCells("A8:{$lastCol}8");
        $courseName = $this->courseOffering->course->title_km ?? $this->courseOffering->course->title_en ?? '';
        $academicYear = $this->courseOffering->academic_year ?? '';
        $semester = $this->courseOffering->semester ?? '';
        $sheet->setCellValue('A8', "របាយការណ៍វត្តមានសិស្ស មុខវិជ្ជា {$courseName} ឆមាស {$semester} ឆ្នាំសិក្សា {$academicYear}");
        $sheet->getStyle('A8')->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 9, 'bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(8)->setRowHeight(18);

        $sheet->mergeCells("A9:{$lastCol}9");
        $lecturerName = $this->courseOffering->lecturer->name ?? '';
        $phone = $this->courseOffering->lecturer->phone_number ?? '';
        $sheet->setCellValue('A9', "បង្រៀនដោយលោក/អ្នកគ្រូ {$lecturerName} លេខទូរស័ព្ទ {$phone}");
        $sheet->getStyle('A9')->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 9],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(9)->setRowHeight(18);

        $headers = ['ល.រ', 'គោត្តនាម និងនាម', 'ឈ្មោះអង់គ្លេស', 'សរុប', 'មានវត្តមាន', 'អវត្តមាន', 'អនុគ្រោះ'];

        foreach ($headers as $colIndex => $header) {
            $colLetter = $this->colLetter($colIndex);
            $sheet->mergeCells("{$colLetter}10:{$colLetter}11");
            $sheet->setCellValue("{$colLetter}10", $header);
        }

        $sheet->getStyle("A10:{$lastCol}11")->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
            'borders' => [
                'allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => '000000']],
            ],
        ]);
        $sheet->getRowDimension(10)->setRowHeight(25);
        $sheet->getRowDimension(11)->setRowHeight(25);

        $dataStartRow = 12;
        $borderStyle = [
            'borders' => [
                'allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => '000000']],
            ],
        ];

        foreach ($this->studentAttendance as $index => $data) {
            $rowNum = $dataStartRow + $index;

            $sheet->setCellValue("A{$rowNum}", $index + 1);
            $sheet->setCellValue("B{$rowNum}", $data['student']->studentProfile->full_name_km ?? $data['student']->name ?? '');
            $sheet->setCellValue("C{$rowNum}", $data['student']->studentProfile->full_name_en ?? '');
            $sheet->setCellValue("D{$rowNum}", $data['total_days']);
            $sheet->setCellValue("E{$rowNum}", $data['present_days']);
            $sheet->setCellValue("F{$rowNum}", $data['absent_days']);
            $sheet->setCellValue("G{$rowNum}", $data['permission_days']);

            $sheet->getStyle("A{$rowNum}:{$lastCol}{$rowNum}")->applyFromArray([
                'font' => ['name' => $khmerFont, 'size' => 10],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ]);
            $sheet->getStyle("A{$rowNum}:{$lastCol}{$rowNum}")->applyFromArray($borderStyle);

            $sheet->getStyle("B{$rowNum}")->getAlignment()->setHorizontal('left');
            $sheet->getStyle("C{$rowNum}")->getAlignment()->setHorizontal('left');

            $sheet->getRowDimension($rowNum)->setRowHeight(21.75);
        }

        $dataEndRow = $dataStartRow + $this->studentAttendance->count() - 1;
        $footerRow = $dataEndRow + 2;

        $sheet->mergeCells("A{$footerRow}:C{$footerRow}");
        $sheet->setCellValue("A{$footerRow}", 'សរុប');
        $sheet->getStyle("A{$footerRow}")->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => 'right', 'vertical' => 'center'],
        ]);

        $sheet->setCellValue("D{$footerRow}", $this->stats['total_records']);
        $sheet->setCellValue("E{$footerRow}", $this->stats['present_total']);
        $sheet->setCellValue("F{$footerRow}", $this->stats['absent_total']);
        $sheet->setCellValue("G{$footerRow}", $this->stats['overall_rate'].'%');
        $sheet->getStyle("D{$footerRow}:{$lastCol}{$footerRow}")->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => 'center'],
        ]);
        $sheet->getStyle("A{$footerRow}:{$lastCol}{$footerRow}")->applyFromArray($borderStyle);

        $sigRow = $footerRow + 2;
        $sheet->mergeCells("A{$sigRow}:C{$sigRow}");
        $sheet->setCellValue("A{$sigRow}", 'ហត្ថលេខារបស់អ្នករៀបចំ');
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
