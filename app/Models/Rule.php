<?php

namespace App\Models;

use App\Enums\RuleType;
use App\Enums\ViolationCategory;
use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rule extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'name',
        'type',
        'category',
        'points',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => RuleType::class,
            'category' => ViolationCategory::class,
            'points' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // ── Relationships ───────────────────────────────────────

    public function pointsLogs(): HasMany
    {
        return $this->hasMany(PointsLog::class);
    }

    // ── Scopes ──────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeViolations(Builder $query): Builder
    {
        return $query->where('type', RuleType::Violation);
    }

    public function scopeAchievements(Builder $query): Builder
    {
        return $query->where('type', RuleType::Achievement);
    }

    // ── Helpers ─────────────────────────────────────────────

    public function requiresApproval(): bool
    {
        return $this->type === RuleType::Violation
            && $this->category?->requiresApproval();
    }
}
