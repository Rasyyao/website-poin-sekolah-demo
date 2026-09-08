<?php

namespace App\Models;

use App\Enums\AppealStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Appeal extends Model
{
    protected $fillable = [
        'points_log_id',
        'submitter_type',
        'submitter_id',
        'reason',
        'evidence_url',
        'status',
        'resolved_by',
        'resolved_at',
        'resolution_note',
    ];

    protected function casts(): array
    {
        return [
            'status' => AppealStatus::class,
            'resolved_at' => 'datetime',
        ];
    }

    // ── Relationships ───────────────────────────────────────

    public function pointsLog(): BelongsTo
    {
        return $this->belongsTo(PointsLog::class, 'points_log_id');
    }

    /**
     * The submitter can be either a User (teacher/admin) or a Student.
     */
    public function submitter(): MorphTo
    {
        return $this->morphTo();
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // ── Helpers ─────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === AppealStatus::Pending;
    }

    public function accept(int $resolvedBy, ?string $note = null): void
    {
        $this->update([
            'status' => AppealStatus::Accepted,
            'resolved_by' => $resolvedBy,
            'resolved_at' => now(),
            'resolution_note' => $note,
        ]);
    }

    public function reject(int $resolvedBy, ?string $note = null): void
    {
        $this->update([
            'status' => AppealStatus::Rejected,
            'resolved_by' => $resolvedBy,
            'resolved_at' => now(),
            'resolution_note' => $note,
        ]);
    }
}
