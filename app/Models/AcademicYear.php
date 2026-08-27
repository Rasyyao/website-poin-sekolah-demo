<?php

namespace App\Models;

use App\Enums\Semester;
use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'year_label',
        'semester',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'semester' => Semester::class,
            'is_active' => 'boolean',
        ];
    }

    // ── Relationships ───────────────────────────────────────

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function studentClassHistories(): HasMany
    {
        return $this->hasMany(StudentClassHistory::class);
    }

    // ── Helpers ─────────────────────────────────────────────

    /**
     * Deactivate all other academic years for this school and activate this one.
     */
    public function activate(): void
    {
        static::where('school_id', $this->school_id)
            ->where('id', '!=', $this->id)
            ->update(['is_active' => false]);

        $this->update(['is_active' => true]);
    }

    public function displayLabel(): string
    {
        return $this->year_label . ' - Semester ' . $this->semester->label();
    }
}
