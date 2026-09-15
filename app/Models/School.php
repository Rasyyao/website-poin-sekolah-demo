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

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
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

    // ── Certificate Signatories ─────────────────────────────

    public function principalName(): ?string
    {
        return $this->settings['principal_name'] ?? null;
    }

    public function principalSignaturePath(): ?string
    {
        return $this->settings['principal_signature'] ?? null;
    }

    public function principalSignatureUrl(): ?string
    {
        $path = $this->principalSignaturePath();

        return $path ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : null;
    }

    public function kesiswaanName(): ?string
    {
        return $this->settings['kesiswaan_name'] ?? null;
    }

    public function kesiswaanSignaturePath(): ?string
    {
        return $this->settings['kesiswaan_signature'] ?? null;
    }

    public function kesiswaanSignatureUrl(): ?string
    {
        $path = $this->kesiswaanSignaturePath();

        return $path ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : null;
    }
}
