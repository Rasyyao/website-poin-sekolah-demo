<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    use BelongsToSchool;

    protected $table = 'classes';

    protected $fillable = [
        'school_id',
        'name',
        'homeroom_teacher_id',
        'academic_year_id',
    ];

    // ── Relationships ───────────────────────────────────────

    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function classHistories(): HasMany
    {
        return $this->hasMany(StudentClassHistory::class, 'class_id');
    }

    // ── Helpers ─────────────────────────────────────────────

    public function studentCount(): int
    {
        return $this->students()->count();
    }

    /**
     * Parse the leading grade number from the class name (e.g. "7A" -> 7).
     * Returns null when the name has no leading number.
     */
    public function gradeLevel(): ?int
    {
        if (preg_match('/^(\d+)/', $this->name, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
