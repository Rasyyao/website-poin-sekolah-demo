<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RuleThreshold extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'type',
        'min_points',
        'action',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'min_points' => 'integer',
        ];
    }

    // ── Scopes ──────────────────────────────────────────────

    public function scopeViolations(Builder $query): Builder
    {
        return $query->where('type', 'violation');
    }

    public function scopeAchievements(Builder $query): Builder
    {
        return $query->where('type', 'achievement');
    }
}
