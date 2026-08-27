<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'subscription_status',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'subscription_status' => SubscriptionStatus::class,
            'settings' => 'array',
        ];
    }

    // ── Relationships ───────────────────────────────────────

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function rules(): HasMany
    {
        return $this->hasMany(Rule::class);
    }

    public function pointsLogs(): HasMany
    {
        return $this->hasMany(PointsLog::class);
    }

    public function ruleThresholds(): HasMany
    {
        return $this->hasMany(RuleThreshold::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    // ── Helpers ─────────────────────────────────────────────

    public function isAccessible(): bool
    {
        return $this->subscription_status->isAccessible();
    }

    public function activeAcademicYear(): ?AcademicYear
    {
        return $this->academicYears()->where('is_active', true)->first();
    }
}
