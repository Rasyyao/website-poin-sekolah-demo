<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $schoolId;

    public function __construct($schoolId)
    {
        $this->schoolId = $schoolId;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        return Student::withoutGlobalScopes()
            ->with(['currentClass'])
            ->where('school_id', $this->schoolId)
            ->orderBy('name')
            ->get();
    }

    public function map($student): array
    {
        return [
            $student->nisn,
            $student->name,
            $student->currentClass ? $student->currentClass->name : '-',
            $student->gender->label(),
            $student->created_at->format('d M Y')
        ];
    }

    public function headings(): array
    {
        return [
            'NISN',
            'Nama Siswa',
            'Kelas',
            'Jenis Kelamin',
            'Terdaftar Pada',
        ];
    }
}
