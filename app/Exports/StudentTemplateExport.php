<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentTemplateExport implements FromArray, WithHeadings, WithColumnWidths, WithStyles
{
    public function array(): array
    {
        return [
            ['0012340001', 'Budi Santoso', '7A', '2010-05-15', '081234567890'],
            ['0012340002', 'Ani Wijaya', '7B', '2010-08-20', '089876543210'],
        ];
    }

    public function headings(): array
    {
        return [
            'NISN',
            'Nama Lengkap',
            'Kelas',
            'Tanggal Lahir (YYYY-MM-DD)',
            'Kontak Orang Tua',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // NISN
            'B' => 30, // Nama Lengkap
            'C' => 10, // Kelas
            'D' => 30, // Tanggal Lahir
            'E' => 20, // Kontak Orang Tua
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
