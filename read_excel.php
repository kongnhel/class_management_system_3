<?php
require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$reader = IOFactory::createReaderForFile('c:\Users\kong\Downloads\Telegram Desktop\grades_Machine Learning(AI)_2025-2026_semឆមាសទី២.xlsx');
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load('c:\Users\kong\Downloads\Telegram Desktop\grades_Machine Learning(AI)_2025-2026_semឆមាសទី២.xlsx');

$sheet = $spreadsheet->getActiveSheet();
$highestRow = $sheet->getHighestRow();
$highestColumn = $sheet->getHighestColumn();

echo "Rows: $highestRow, Columns: $highestColumn\n\n";

for ($row = 1; $row <= min($highestRow, 30); $row++) {
    $rowData = [];
    for ($col = 'A'; $col <= $highestColumn; $col++) {
        $rowData[] = $sheet->getCell($col . $row)->getValue();
    }
    echo "Row $row: " . implode(' | ', $rowData) . "\n";
}
