<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StaffExport implements FromCollection, WithHeadings, WithMapping
{
    protected $schoolId;

    public function __construct($schoolId)
    {
        $this->schoolId = $schoolId;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        return User::where('school_id', $this->schoolId)
            ->orderBy('name')
            ->get();
    }

    public function map($staff): array
    {
        return [
            $staff->name,
            $staff->email,
            $staff->role->label(),
            $staff->created_at->format('d M Y')
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'Email Akses',
            'Peran',
            'Tanggal Bergabung',
        ];
    }
}
