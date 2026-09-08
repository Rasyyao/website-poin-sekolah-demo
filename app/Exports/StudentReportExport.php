<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StudentReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    protected $student;
    protected $logs;

    public function __construct(Student $student, $logs)
    {
        $this->student = $student;
        $this->logs = $logs;
    }

    public function collection(): Collection
    {
        return $this->logs instanceof Collection ? $this->logs : collect($this->logs);
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Peraturan',
            'Kategori',
            'Poin',
            'Status',
            'Dilaporkan Oleh',
            'Catatan'
        ];
    }

    public function map($log): array
    {
        return [
            $log->occurred_at ? $log->occurred_at->format('d/m/Y H:i') : '-',
            $log->rule->name ?? '-',
            $log->rule->type->label() ?? '-',
            $log->points,
            $log->status->label() ?? '-',
            $log->reporter->name ?? '-',
            $log->notes ?? '-'
        ];
    }

    public function title(): string
    {
        return 'Riwayat Poin - ' . $this->student->name;
    }
}
