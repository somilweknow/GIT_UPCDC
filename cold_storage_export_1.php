<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();
include("scripts/settings.php");

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set Title Information
$sheet->mergeCells('A1:R1');
$sheet->setCellValue('A1', 'निष्क्रिय शीतगृहों की सूचना एवं कार्ययोजना (Information regarding Inactive Cold Storages)');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Row 2 & 3: Headers with Rowspan/Colspan replication
// Row 2 starts at A3

// Rowspan cols
$headers = [
    'A' => 'क्र.सं. (S.No)',
    'B' => 'शीतगृह का नाम एवं जनपद (Cold Storage Name & District)',
    'C' => 'भण्डारण क्षमता (Capacity)',
    'D' => 'बंद होने का कारण (Reason)',
    'E' => 'बंद होने का वर्ष (Year)',
    'F' => 'भारी वाहन ट्रक हेतु सड़क (Road)',
    'G' => 'कुल भूमि का क्षेत्रफल (Area)',
    'H' => 'भूमि मूल्य (Value)',
    'I' => 'अन्य परिसम्पत्तियों का विवरण (Other Assets)',
    'J' => 'बकाया बिजली बिल (Elec Bill)',
    'O' => 'न्यायालय में लम्बित वाद (Court Case)',
    'P' => 'भवन की हालत (Building Condition)',
    'Q' => 'कर्मचारियों की संख्या (Employees)',
    'R' => 'कार्ययोजना (Action Plan)'
];

foreach ($headers as $col => $text) {
    $sheet->setCellValue($col . '3', $text);
    $sheet->mergeCells($col . '3:' . $col . '4');
}

// Colspan col (Loan Details)
$sheet->mergeCells('K3:N3');
$sheet->setCellValue('K3', 'लगे ऋण का विवरण (Loan Details)');
$sheet->setCellValue('K4', 'एन.सी.डी.सी. (NCDC)');
$sheet->setCellValue('L4', 'व्यावसायिक बैंक');
$sheet->setCellValue('M4', 'यू.पी.सी.बी.');
$sheet->setCellValue('N4', 'डी.सी.बी.');

// Row 5: Numbering
for ($c = 0; $c < 18; $c++) {
    $colName = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c + 1);
    $sheet->setCellValue($colName . '5', $c + 1);
}

// Styling headers
$headerStyle = [
    'font' => ['bold' => false, 'size' => 12],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true,
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'FFCD85'],
    ],
];
$sheet->getStyle('A3:R5')->applyFromArray($headerStyle);

// Fetch Data
$sql = "SELECT * FROM cold_storage_entries ORDER BY created_at DESC";
$res = execute_query($sql);

$rowNum = 6;
$sno = 1;
while ($row = mysqli_fetch_assoc($res)) {
    $sheet->setCellValue('A' . $rowNum, $sno++);
    $sheet->setCellValue('B' . $rowNum, $row['cs_name'] . "\n" . $row['district_name']);
    $sheet->getStyle('B' . $rowNum)->getAlignment()->setWrapText(true);
    $sheet->setCellValue('C' . $rowNum, $row['capacity']);
    $sheet->setCellValue('D' . $rowNum, $row['closure_reason']);
    $sheet->setCellValue('E' . $rowNum, $row['closure_year']);
    $sheet->setCellValue('F' . $rowNum, $row['road_access']);
    $sheet->setCellValue('G' . $rowNum, $row['land_area']);
    $sheet->setCellValue('H' . $rowNum, $row['land_value']);
    $sheet->setCellValue('I' . $rowNum, $row['other_assets']);
    $sheet->setCellValue('J' . $rowNum, $row['elec_bill']);
    $sheet->setCellValue('K' . $rowNum, $row['ncdc_loan']);
    $sheet->setCellValue('L' . $rowNum, $row['bank_loan']);
    $sheet->setCellValue('M' . $rowNum, $row['upcb_loan']);
    $sheet->setCellValue('N' . $rowNum, $row['dcb_loan']);
    $sheet->setCellValue('O' . $rowNum, $row['court_case']);
    $sheet->setCellValue('P' . $rowNum, $row['building_cond']);
    $sheet->setCellValue('Q' . $rowNum, $row['employees']);
    $sheet->setCellValue('R' . $rowNum, $row['action_plan']);

    // Alignment for numbers
    $sheet->getStyle('C'.$rowNum.':E'.$rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('G'.$rowNum.':N'.$rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('Q'.$rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

    $rowNum++;
}

// Table borders
$sheet->getStyle('A6:R' . ($rowNum - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Auto size columns
foreach (range('A', 'R') as $col) {
    if ($col != 'B' && $col != 'I' && $col != 'O' && $col != 'R') { // Skip heavy text columns for auto size or set max width
        $sheet->getColumnDimension($col)->setAutoSize(true);
    } else {
        $sheet->getColumnDimension($col)->setWidth(30);
    }
}

$fileName = 'Cold_Storage_Report_' . date('Y-m-d') . '.xlsx';

$writer = new Xlsx($spreadsheet);

// Use temporary file to avoid stream corruption
$tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
$writer->save($tempFile);

if (ob_get_length()) ob_end_clean();

header('Content-Description: File Transfer');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($tempFile));

readfile($tempFile);
unlink($tempFile);
exit;
