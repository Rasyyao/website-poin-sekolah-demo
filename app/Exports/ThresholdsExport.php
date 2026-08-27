<?php

namespace App\Exports;

use App\Models\Student;
use App\Models\RuleThreshold;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ThresholdsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $schoolId;

    public function __construct($schoolId)
    {
        $this->schoolId = $schoolId;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        $thresholds = RuleThreshold::withoutGlobalScopes()
            ->where('school_id', $this->schoolId)
            ->orderByDesc('min_points')
            ->get();

        if ($thresholds->isEmpty()) {
            return collect();
        }

        $minThreshold = $thresholds->last()->min_points;

        $students = Student::withoutGlobalScopes()
            ->with(['currentClass'])
            ->where('students.school_id', $this->schoolId)
            ->selectRaw('students.*, COALESCE((
                SELECT SUM(ABS(points)) 
                FROM points_log 
                WHERE points_log.student_id = students.id 
                AND points_log.status = "approved" 
                AND points_log.points < 0
            ), 0) as total_violation_points')
            ->orderByDesc('total_violation_points')
            ->get();

        $students = $students->filter(function ($s) use ($minThreshold) {
            return $s->total_violation_points >= $minThreshold;
        });

        // Map to flat list but with threshold action included
        $exportData = collect();

        foreach ($thresholds as $threshold) {
            $matchedStudents = $students->filter(function ($s) use ($threshold) {
                return $s->total_violation_points >= $threshold->min_points;
            });
            
            foreach ($matchedStudents as $student) {
                $exportData->push([
                    'student' => $student,
                    'threshold_action' => $threshold->action
                ]);
            }

            $students = $students->reject(function ($s) use ($threshold) {
                return $s->total_violation_points >= $threshold->min_points;
            });
        }

        return $exportData;
    }

    public function map($row): array
    {
        $student = $row['student'];
        return [
            $student->nisn,
            $student->name,
            $student->currentClass ? $student->currentClass->name : '-',
            $student->total_violation_points,
            $row['threshold_action']
        ];
    }

    public function headings(): array
    {
        return [
            'NISN',
            'Nama Siswa',
            'Kelas',
            'Total Poin Pelanggaran',
            'Tindakan Diperlukan',
        ];
    }
}
