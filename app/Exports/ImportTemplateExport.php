<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ImportTemplateExport implements WithHeadings, WithStyles, WithMapping
{
    public function headings(): array
    {
        return [
            'ឈ្មោះ *',           // Name (required)
            'អ៊ីម៉ែល',           // Email (optional)
            'ឈ្មោះពេញខ្មែរ',     // Full Name KM (optional)
            'ឈ្មោះពេញអង់គ្លេស',   // Full Name EN (optional)
            'ភេទ',               // Gender (optional)
            'លេខទូរស័ព្ទ',       // Phone (optional)
            'អាសយដ្ឋាន',         // Address (optional)
            'ថ្ងៃខែឆ្នាំកំណើត',    // Date of Birth (optional)
        ];
    }

    public function map($student): array
    {
        return [
            $student['name'] ?? '',
            $student['email'] ?? '',
            $student['full_name_km'] ?? '',
            $student['full_name_en'] ?? '',
            $student['gender'] ?? '',
            $student['phone'] ?? '',
            $student['address'] ?? '',
            $student['date_of_birth'] ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Header row style
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '059669'],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(28);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(22);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(16);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(16);

        // Sample data
        $sampleData = [
            ['ញឹល កុង', '', 'ញឹល កុង', 'NHEL KONG', 'ប្រុស', '', 'ខេត្តបាត់ដំបង', ''],
            ['លី ហួ', '', 'លី ហួ', 'LY HOU', 'ប្រុស', '', 'ខេត្តសៀមរាប', ''],
            ['រ៉ន ឡាវ', '', 'រ៉ន ឡាវ', 'RON LOVE', 'ប្រុស', '', 'រាជធានីភ្នំពេញ', ''],
        ];

        foreach ($sampleData as $index => $row) {
            $rowNum = $index + 2;
            $sheet->getRowDimension($rowNum)->setRowHeight(22);
            foreach ($row as $colIndex => $value) {
                $colLetter = chr(65 + $colIndex);
                $sheet->setCellValue($colLetter.$rowNum, $value);
                $sheet->getStyle($colLetter.$rowNum)->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['vertical' => 'center'],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin',
                            'color' => ['rgb' => 'E5E7EB'],
                        ],
                    ],
                ]);
            }
        }

        return [];
    }
}
