<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ClassReportExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $stats;
    protected $className;

    public function __construct(array $stats, string $className)
    {
        $this->stats = $stats;
        $this->className = $className;
    }

    public function array(): array
    {
        $data = [];
        foreach ($this->stats['students'] as $student) {
            $data[] = [
                $student['nisn'],
                $student['name'],
                $student['total_violation_points'],
                $student['total_achievement_points'],
                $student['net_points'],
                $student['action_required'] ?? 'Aman',
            ];
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            'NISN',
            'Nama Siswa',
            'Poin Pelanggaran',
            'Poin Prestasi',
            'Total Poin',
            'Keterangan / Tindakan'
        ];
    }

    public function title(): string
    {
        return 'Laporan Kelas - ' . $this->className;
    }
}
