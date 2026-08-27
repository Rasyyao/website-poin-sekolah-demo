<?php

namespace App\Models;

use App\Enums\PointsLogStatus;
use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PointsLog extends Model
{
    use BelongsToSchool;

    protected $table = 'points_log';

    protected $fillable = [
        'school_id',
        'student_id',
        'rule_id',
        'reported_by',
        'points',
        'evidence_url',
        'note',
        'status',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'status' => PointsLogStatus::class,
            'occurred_at' => 'datetime',
        ];
    }

    // ── Relationships ───────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function appeals(): HasMany
    {
        return $this->hasMany(Appeal::class, 'points_log_id');
    }

    // ── Scopes ──────────────────────────────────────────────

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', PointsLogStatus::Approved);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', PointsLogStatus::Pending);
    }

    public function scopeViolations(Builder $query): Builder
    {
        return $query->whereHas('rule', function ($q) {
            $q->where('type', 'violation');
        });
    }

    public function scopeAchievements(Builder $query): Builder
    {
        return $query->whereHas('rule', function ($q) {
            $q->where('type', 'achievement');
        });
    }

    public function scopeDateRange(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('occurred_at', [$from, $to]);
    }

    // ── Helpers ─────────────────────────────────────────────

    public function isApproved(): bool
    {
        return $this->status === PointsLogStatus::Approved;
    }

    public function isPending(): bool
    {
        return $this->status === PointsLogStatus::Pending;
    }

    public function approve(): void
    {
        $this->update(['status' => PointsLogStatus::Approved]);
    }

    public function reject(): void
    {
        $this->update(['status' => PointsLogStatus::Rejected]);
    }
}
