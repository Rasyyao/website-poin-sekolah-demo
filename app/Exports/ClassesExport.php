<?php

namespace App\Exports;

use App\Models\SchoolClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClassesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $schoolId;

    public function __construct($schoolId)
    {
        $this->schoolId = $schoolId;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        return SchoolClass::where('school_id', $this->schoolId)
            ->with(['homeroomTeacher', 'students'])
            ->orderBy('name')
            ->get();
    }

    public function map($class): array
    {
        return [
            $class->name,
            $class->homeroomTeacher ? $class->homeroomTeacher->name : '-',
            $class->students->count(),
            $class->created_at->format('d M Y')
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Kelas',
            'Wali Kelas',
            'Jumlah Siswa',
            'Tanggal Dibuat',
        ];
    }
}
