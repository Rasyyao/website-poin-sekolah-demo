<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Student extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'class_id',
        'nisn',
        'name',
        'birth_date',
        'parent_contact',
        'access_code',
    ];

    protected $hidden = [
        'access_code',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'access_code' => 'hashed',
        ];
    }

    // ── Relationships ───────────────────────────────────────

    public function currentClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function classHistories(): HasMany
    {
        return $this->hasMany(StudentClassHistory::class);
    }

    public function pointsLogs(): HasMany
    {
        return $this->hasMany(PointsLog::class);
    }

    public function appeals(): MorphMany
    {
        return $this->morphMany(Appeal::class, 'submitter');
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    // ── Computed Attributes ─────────────────────────────────

    /**
     * Get total accumulated net points (achievements - violations).
     */
    public function totalPoints(): int
    {
        return $this->totalViolationPoints() - $this->totalAchievementPoints();
    }

    /**
     * Get total violation points.
     */
    public function totalViolationPoints(): int
    {
        return (int) $this->pointsLogs()
            ->where('status', 'approved')
            ->violations()
            ->sum('points');
    }

    /**
     * Get total achievement points.
     */
    public function totalAchievementPoints(): int
    {
        return (int) $this->pointsLogs()
            ->where('status', 'approved')
            ->achievements()
            ->sum('points');
    }
}
