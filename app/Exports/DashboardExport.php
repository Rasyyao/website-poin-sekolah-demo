<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DashboardExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $logs;
    protected $from;
    protected $to;

    public function __construct($logs, $from, $to)
    {
        $this->logs = $logs;
        $this->from = $from;
        $this->to = $to;
    }

    public function collection(): Collection
    {
        return $this->logs instanceof Collection ? $this->logs : collect($this->logs);
    }

    public function headings(): array
    {
        return [
            ['Data Diekspor Dari:', \Carbon\Carbon::parse($this->from)->format('d M Y'), 'Hingga:', \Carbon\Carbon::parse($this->to)->format('d M Y')],
            [],
            [
                'Tanggal & Waktu',
                'Nama Siswa',
                'Kelas',
                'Jenis Aturan',
                'Nama Aturan',
                'Poin',
                'Pelapor'
            ]
        ];
    }

    public function map($log): array
    {
        return [
            $log->occurred_at ? $log->occurred_at->format('d/m/Y H:i') : '-',
            $log->student->name ?? '-',
            $log->student->currentClass->name ?? '-',
            $log->rule->type->label() ?? '-',
            $log->rule->name ?? '-',
            ($log->rule && $log->rule->type->value === 'violation') ? "-{$log->points}" : "+{$log->points}",
            $log->reporter->name ?? 'Sistem',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
        ];
    }
}
