<?php
require_once FCPATH . 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();

// Set document properties
$spreadsheet->getProperties()
    ->setCreator("Koperasi")
    ->setLastModifiedBy("Koperasi")
    ->setTitle("Laporan Slip Gaji")
    ->setSubject("Laporan Slip Gaji")
    ->setDescription("Laporan Slip Gaji Koperasi");

// Add header
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', 'No')
    ->setCellValue('B1', 'Nama Pekerja')
    ->setCellValue('C1', 'Periode')
    ->setCellValue('D1', 'Total Pendapatan')
    ->setCellValue('E1', 'Uang Makan')
    ->setCellValue('F1', 'Total Kasbon')
    ->setCellValue('G1', 'Total Tabungan')
    ->setCellValue('H1', 'Pemasukan BPJS')
    ->setCellValue('I1', 'Gaji Bersih');

// Style the header
$styleArray = [
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
];
$spreadsheet->getActiveSheet()->getStyle('A1:I1')->applyFromArray($styleArray);

// Add data
$row = 2;
$no = 1;
foreach ($slip_gaji as $slip) {
    $total_pendapatan = $slip->total_pendapatan + $slip->uang_makan + $slip->pemasukan_bpjs;
    $total_potongan = $slip->total_kasbon + $slip->total_tabungan;
    $gaji_bersih = $total_pendapatan - $total_potongan;

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A' . $row, $no++)
        ->setCellValue('B' . $row, $slip->nama_karyawan)
        ->setCellValue('C' . $row, date('d/m/Y', strtotime($slip->bulan)))
        ->setCellValue('D' . $row, $slip->total_pendapatan)
        ->setCellValue('E' . $row, $slip->uang_makan)
        ->setCellValue('F' . $row, $slip->total_kasbon)
        ->setCellValue('G' . $row, $slip->total_tabungan)
        ->setCellValue('H' . $row, $slip->pemasukan_bpjs)
        ->setCellValue('I' . $row, $gaji_bersih);

    // Format currency columns
    $columns = ['D', 'E', 'F', 'G', 'H', 'I'];
    foreach ($columns as $col) {
        $spreadsheet->getActiveSheet()
            ->getStyle($col.$row)
            ->getNumberFormat()
            ->setFormatCode('#,##0');
    }
    
    $row++;
}

// Autosize columns
foreach(range('A','I') as $columnID) {
    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
}

// Set active sheet index to the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Set the header type and filename
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Laporan_Slip_Gaji_'.date('d-m-Y').'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;