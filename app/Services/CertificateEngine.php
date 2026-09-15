<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\NotificationLog;
use App\Models\RuleThreshold;
use App\Models\Student;
use Illuminate\Support\Facades\Log;

class CertificateEngine
{
    /**
     * Evaluate a student's accumulated achievement points against school
     * achievement thresholds, issuing a Certificate of Appreciation for
     * every threshold crossed that hasn't already been issued.
     *
     * Unlike ThresholdEngine, this is idempotent per (student, threshold) —
     * a certificate is only ever issued once.
     */
    public function evaluate(Student $student): array
    {
        $totalAchievementPoints = $student->totalAchievementPoints();

        $thresholds = RuleThreshold::withoutGlobalScopes()
            ->where('school_id', $student->school_id)
            ->achievements()
            ->where('min_points', '<=', $totalAchievementPoints)
            ->orderBy('min_points', 'asc')
            ->get();

        $issued = [];

        foreach ($thresholds as $threshold) {
            $certificate = $this->issueCertificate($student, $threshold, $totalAchievementPoints);
            if ($certificate !== null) {
                $issued[] = $certificate;
            }
        }

        return $issued;
    }

    /**
     * Issue a certificate for the given threshold if one hasn't already
     * been issued to this student for it. Returns null when a certificate
     * already existed (no-op).
     */
    private function issueCertificate(Student $student, RuleThreshold $threshold, int $currentPoints): ?Certificate
    {
        $existing = Certificate::withoutGlobalScopes()
            ->where('student_id', $student->id)
            ->where('rule_threshold_id', $threshold->id)
            ->first();

        if ($existing !== null) {
            return null;
        }

        $certificate = Certificate::withoutGlobalScopes()->create([
            'school_id' => $student->school_id,
            'student_id' => $student->id,
            'rule_threshold_id' => $threshold->id,
            'certificate_number' => $this->generateCertificateNumber($student->school_id),
            'points_at_issue' => $currentPoints,
            'issued_at' => now(),
        ]);

        $message = $this->buildMessage($student, $threshold, $currentPoints);

        NotificationLog::withoutGlobalScopes()->create([
            'school_id' => $student->school_id,
            'student_id' => $student->id,
            'channel' => 'email',
            'message' => $message,
            'sent_at' => now(),
            'status' => 'sent',
        ]);

        Log::info("Certificate issued for student {$student->id}", [
            'threshold_points' => $threshold->min_points,
            'current_points' => $currentPoints,
            'certificate_number' => $certificate->certificate_number,
        ]);

        return $certificate;
    }

    private function generateCertificateNumber(int $schoolId): string
    {
        $sequence = Certificate::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereYear('created_at', now()->year)
            ->count() + 1;

        $romanMonth = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ][now()->month];

        return sprintf('SERT/%04d/%s/%d', $sequence, $romanMonth, now()->year);
    }

    private function buildMessage(Student $student, RuleThreshold $threshold, int $currentPoints): string
    {
        return sprintf(
            '[%s] Siswa %s (NISN: %s) telah mencapai %d poin prestasi (ambang batas: %d) dan menerima sertifikat penghargaan: %s. %s',
            now()->format('d/m/Y H:i'),
            $student->name,
            $student->nisn,
            $currentPoints,
            $threshold->min_points,
            $threshold->action,
            $threshold->description ?? '',
        );
    }
}
