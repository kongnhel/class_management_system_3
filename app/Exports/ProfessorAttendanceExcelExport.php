<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfessorAttendanceExcelExport implements FromCollection, WithDrawings, WithStyles
{
    protected Collection $attendances;

    protected array $stats;

    protected string $professorName;

    protected ?string $semester;

    protected ?string $academicYear;

    protected ?string $dayType;

    public function __construct(Collection $attendances, array $stats, string $professorName, ?string $semester = null, ?string $academicYear = null, ?string $dayType = null)
    {
        $this->attendances = $attendances;
        $this->stats = $stats;
        $this->professorName = $professorName;
        $this->semester = $semester;
        $this->academicYear = $academicYear;
        $this->dayType = $dayType;
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
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(14);
        $sheet->getColumnDimension('F')->setWidth(14);
        $sheet->getColumnDimension('G')->setWidth(12);

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

        $sheet->mergeCells("A8:{$lastCol}8");
        $semesterLabel = $this->semester ? " ឆមាស {$this->semester}" : '';
        $yearLabel = $this->academicYear ? " ឆ្នាំសិក្សា {$this->academicYear}" : '';
        $dayTypeLabel = match ($this->dayType) {
            'weekend' => ' (ថ្ងៃសប្តាហ៍)',
            'weekday' => ' (ថ្ងៃសិក្សា)',
            default => '',
        };
        $sheet->setCellValue('A8', "របាយការណ៍វត្តមានគ្រូបង្រៀន{$semesterLabel}{$yearLabel}{$dayTypeLabel}");
        $sheet->getStyle('A8')->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 11, 'bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(8)->setRowHeight(20);

        $sheet->mergeCells("A9:{$lastCol}9");
        $sheet->setCellValue('A9', 'គ្រូបង្រៀន៖ '.$this->professorName);
        $sheet->getStyle('A9')->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 9],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(9)->setRowHeight(18);

        $sheet->mergeCells("A10:{$lastCol}10");
        $now = \Carbon\Carbon::now('Asia/Phnom_Penh');
        $sheet->setCellValue('A10', 'របាយការណ៍នេះត្រូវបានរៀបចំនៅថ្ងៃទី '.$now->format('d/m/Y').' ម៉ោង '.$now->format('H:i'));
        $sheet->getStyle('A10')->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 9],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(10)->setRowHeight(18);

        $headers = ['ល.រ', 'គ្រូបង្រៀន', 'មុខវិជ្ជា', 'ឆមាស', 'ឆ្នាំសិក្សា', 'ថ្ងៃ', 'ស្ថានភាព'];

        foreach ($headers as $colIndex => $header) {
            $colLetter = $this->colLetter($colIndex);
            $sheet->mergeCells("{$colLetter}11:{$colLetter}12");
            $sheet->setCellValue("{$colLetter}11", $header);
        }

        $sheet->getStyle("A11:{$lastCol}12")->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
            'borders' => [
                'allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => '000000']],
            ],
        ]);
        $sheet->getRowDimension(11)->setRowHeight(25);
        $sheet->getRowDimension(12)->setRowHeight(25);

        $dataStartRow = 13;
        $borderStyle = [
            'borders' => [
                'allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => '000000']],
            ],
        ];

        // Apply day_type filter
        $records = $this->attendances;
        if ($this->dayType === 'weekend') {
            $records = $records->filter(fn ($r) => $r->verified_at && Carbon::parse($r->verified_at)->isWeekend());
        } elseif ($this->dayType === 'weekday') {
            $records = $records->filter(fn ($r) => $r->verified_at && ! Carbon::parse($r->verified_at)->isWeekend());
        }

        foreach ($records as $index => $record) {
            $rowNum = $dataStartRow + $index;

            $courseTitle = $record->courseOffering->course->title_km ?? $record->courseOffering->course->title_en ?? 'N/A';
            $semester = $record->courseOffering->semester ?? '-';
            $academicYear = $record->courseOffering->academic_year ?? '-';
            $verifiedDate = $record->verified_at ? $record->verified_at->format('d M Y') : '-';
            $status = $record->verified_at ? 'វត្តមាន' : '-';

            $sheet->setCellValue("A{$rowNum}", $index + 1);
            $sheet->setCellValue("B{$rowNum}", $this->professorName);
            $sheet->setCellValue("C{$rowNum}", $courseTitle);
            $sheet->setCellValue("D{$rowNum}", $semester);
            $sheet->setCellValue("E{$rowNum}", $academicYear);
            $sheet->setCellValue("F{$rowNum}", $verifiedDate);
            $sheet->setCellValue("G{$rowNum}", $status);

            $sheet->getStyle("A{$rowNum}:{$lastCol}{$rowNum}")->applyFromArray([
                'font' => ['name' => $khmerFont, 'size' => 10],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ]);
            $sheet->getStyle("A{$rowNum}:{$lastCol}{$rowNum}")->applyFromArray($borderStyle);

            $sheet->getStyle("B{$rowNum}")->getAlignment()->setHorizontal('left');
            $sheet->getStyle("C{$rowNum}")->getAlignment()->setHorizontal('left');

            $sheet->getRowDimension($rowNum)->setRowHeight(21.75);
        }

        $dataEndRow = $dataStartRow + $records->count() - 1;
        $footerRow = $dataEndRow + 2;

        $sheet->mergeCells("A{$footerRow}:C{$footerRow}");
        $sheet->setCellValue("A{$footerRow}", 'សរុប');
        $sheet->getStyle("A{$footerRow}")->applyFromArray([
            'font' => ['name' => $khmerFont, 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => 'right', 'vertical' => 'center'],
        ]);

        $sheet->setCellValue("D{$footerRow}", $records->count());
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
        $sheet->setCellValue("A{$sigRow2}", 'អ្នករៀបចំ');
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
