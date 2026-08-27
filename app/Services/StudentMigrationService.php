<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentClassHistory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StudentMigrationService
{
    /**
     * Bulk migrate students from one class to another.
     *
     * Records history entries for each student and updates their current class.
     *
     * @param  Collection|array  $studentIds
     */
    public function migrateStudents(
        array|Collection $studentIds,
        SchoolClass $targetClass,
        AcademicYear $academicYear,
    ): int {
        return DB::transaction(function () use ($studentIds, $targetClass, $academicYear) {
            $students = Student::whereIn('id', $studentIds)->get();
            $movedAt = now();
            $count = 0;

            foreach ($students as $student) {
                // Record history
                StudentClassHistory::create([
                    'student_id' => $student->id,
                    'class_id' => $targetClass->id,
                    'academic_year_id' => $academicYear->id,
                    'moved_at' => $movedAt,
                ]);

                // Update current class
                $student->update(['class_id' => $targetClass->id]);
                $count++;
            }

            return $count;
        });
    }

    /**
     * Bulk promote an entire class to a new class (annual promotion).
     */
    public function promoteClass(
        SchoolClass $fromClass,
        SchoolClass $toClass,
        AcademicYear $academicYear,
    ): int {
        $studentIds = $fromClass->students()->pluck('id');

        return $this->migrateStudents($studentIds, $toClass, $academicYear);
    }
}
