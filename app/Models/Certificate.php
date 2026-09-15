<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'student_id',
        'rule_threshold_id',
        'certificate_number',
        'points_at_issue',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'points_at_issue' => 'integer',
            'issued_at' => 'datetime',
        ];
    }

    // ── Relationships ───────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function ruleThreshold(): BelongsTo
    {
        return $this->belongsTo(RuleThreshold::class);
    }

    /**
     * Filesystem/header-safe filename for the downloaded PDF.
     * certificate_number contains "/" (e.g. SERT/0001/IX/2026), which is
     * invalid in a Content-Disposition filename.
     */
    public function downloadFilename(): string
    {
        return 'sertifikat-'.str_replace(['/', '\\'], '-', $this->certificate_number).'.pdf';
    }
}
