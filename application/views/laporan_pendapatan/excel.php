<?php
require_once FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

// Clear any previous output
ob_end_clean();

// Set memory limit and execution time
ini_set('memory_limit', '512M');
set_time_limit(300);

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set document properties
$spreadsheet->getProperties()
    ->setCreator('Koperasi')
    ->setLastModifiedBy('Koperasi')
    ->setTitle('Laporan Pendapatan')
    ->setSubject('Laporan Pendapatan')
    ->setDescription('Laporan Pendapatan Koperasi');

// Add header
$sheet->setCellValue('A1', 'Laporan Pendapatan');
$sheet->mergeCells('A1:F1');

// Style the header
$sheet->getStyle('A1:F1')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 14
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER
    ]
]);

// Add table headers
$headers = ['No', 'Tanggal', 'Nama Karyawan', 'Pekerjaan', 'Banyak', 'Harga Koperasi', 'Total Koperasi', 'Harga Karyawan', 'Total Karyawan', 'Status'];
$col = 'A';
$row = 3;
foreach ($headers as $header) {
    $sheet->setCellValue($col . $row, $header);
    $sheet->getStyle($col . $row)->applyFromArray([
        'font' => ['bold' => true],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'E2E2E2']
        ]
    ]);
    $col++;
}

// Add data
$row = 4;
$no = 1;
foreach ($pendapatan as $p) {
    $sheet->setCellValue('A' . $row, $no++);
    $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($p->tanggal)));
    $sheet->setCellValue('C' . $row, $p->nama_karyawan);
    $sheet->setCellValue('D' . $row, $p->nama_pekerjaan);
    $sheet->setCellValue('E' . $row, $p->banyak);
    $sheet->setCellValue('F' . $row, $p->harga_koperasi);
    $sheet->setCellValue('G' . $row, $p->total_koperasi);
    $sheet->setCellValue('H' . $row, $p->harga_karyawan);
    $sheet->setCellValue('I' . $row, $p->total_karyawan);
    $sheet->setCellValue('J' . $row, $p->status ?? '');
    // Format currency columns
    foreach(['F','G','H','I'] as $colCur) {
        $sheet->getStyle($colCur . $row)->getNumberFormat()->setFormatCode('#,##0');
    }
    $row++;
}

// Auto-size columns
foreach (range('A', 'J') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Clean output buffer
ob_end_clean();

// Set content type and headers
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Laporan_Pendapatan.xlsx"');
header('Cache-Control: max-age=0');

// Create Excel file
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;