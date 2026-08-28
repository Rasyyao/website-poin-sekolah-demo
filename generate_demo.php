<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Faker\Factory;
use App\Models\SchoolClass;

$faker = Factory::create('id_ID');

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set Header
$sheet->setCellValue('A1', 'NISN');
$sheet->setCellValue('B1', 'NAMA LENGKAP');
$sheet->setCellValue('C1', 'KELAS');
$sheet->setCellValue('D1', 'TANGGAL LAHIR (YYYY-MM-DD)');
$sheet->setCellValue('E1', 'KONTAK ORANG TUA');

// Style header
$sheet->getStyle('A1:E1')->getFont()->setBold(true);

// Get available classes
$classes = SchoolClass::where('school_id', 1)->pluck('name')->toArray();
if (empty($classes)) {
    $classes = ['7A', '7B', '8A', '9A']; // Fallback
}

$startNisn = 1000100000;
$row = 2;

// Generate 30 rows
for ($i = 0; $i < 30; $i++) {
    // Generate valid data
    $nisn = (string) ($startNisn + $i);
    $name = $faker->name();
    $class = $classes[array_rand($classes)];
    $dob = $faker->dateTimeBetween('-15 years', '-12 years')->format('Y-m-d');
    $contact = $faker->phoneNumber();

    // Introduce 2 specific errors for demonstration
    if ($i === 5) {
        // Error: duplicate NISN with the first generated one
        $nisn = (string) $startNisn;
    }
    if ($i === 12) {
        // Error: invalid class
        $class = 'Kelas Tidak Dikenal';
    }
    if ($i === 20) {
        // Error: empty NISN
        $nisn = '';
    }

    $sheet->setCellValue('A' . $row, $nisn);
    // Force NISN to string to avoid scientific notation
    $sheet->getStyle('A' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

    $sheet->setCellValue('B' . $row, $name);
    $sheet->setCellValue('C' . $row, $class);
    $sheet->setCellValue('D' . $row, $dob);
    $sheet->setCellValue('E' . $row, $contact);

    $row++;
}

// Auto size columns
foreach (range('A', 'E') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$writer = new Xlsx($spreadsheet);
$writer->save(__DIR__ . '/demo-import-siswa-30.xlsx');

echo "File demo-import-siswa-30.xlsx created successfully!\n";
