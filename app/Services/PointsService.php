<?php

namespace App\Services;

use App\Enums\PointsLogStatus;
use App\Enums\RuleType;
use App\Models\PointsLog;
use App\Models\Rule;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PointsService
{
    public function __construct(
        private ThresholdEngine $thresholdEngine,
        private CertificateEngine $certificateEngine,
    ) {}

    /**
     * Record a new points entry for a student.
     *
     * If the rule is a "berat" violation, sets status to pending (requires admin approval).
     * Otherwise, auto-approves and checks thresholds.
     */
    public function recordPoints(
        Student $student,
        Rule $rule,
        User $reporter,
        ?string $note = null,
        ?string $evidenceUrl = null,
        ?\DateTimeInterface $occurredAt = null,
    ): PointsLog {
        return DB::transaction(function () use ($student, $rule, $reporter, $note, $evidenceUrl, $occurredAt) {
            // Pessimistic row locking to serialize concurrent point records and threshold evaluations
            $lockedStudent = Student::withoutGlobalScopes()->where('id', $student->id)->lockForUpdate()->firstOrFail();

            $status = $rule->requiresApproval()
                ? PointsLogStatus::Pending
                : PointsLogStatus::Approved;

            $pointsLog = PointsLog::create([
                'school_id' => $lockedStudent->school_id,
                'student_id' => $lockedStudent->id,
                'rule_id' => $rule->id,
                'reported_by' => $reporter->id,
                'points' => $rule->points, // snapshot
                'evidence_url' => $evidenceUrl,
                'note' => $note,
                'status' => $status,
                'occurred_at' => $occurredAt ?? now(),
            ]);

            // Only check thresholds for auto-approved entries
            if ($status === PointsLogStatus::Approved) {
                if ($rule->type === RuleType::Violation) {
                    $this->thresholdEngine->evaluate($lockedStudent);
                } elseif ($rule->type === RuleType::Achievement) {
                    $this->certificateEngine->evaluate($lockedStudent);
                }
            }

            return $pointsLog;
        });
    }

    /**
     * Approve a pending points log (for "berat" violations).
     */
    public function approve(PointsLog $pointsLog): void
    {
        DB::transaction(function () use ($pointsLog) {
            $lockedStudent = Student::withoutGlobalScopes()->where('id', $pointsLog->student_id)->lockForUpdate()->firstOrFail();
            $pointsLog->approve();

            if ($pointsLog->rule->type === RuleType::Violation) {
                $this->thresholdEngine->evaluate($lockedStudent);
            } elseif ($pointsLog->rule->type === RuleType::Achievement) {
                $this->certificateEngine->evaluate($lockedStudent);
            }
        });
    }

    /**
     * Reject a pending points log.
     */
    public function reject(PointsLog $pointsLog): void
    {
        DB::transaction(function () use ($pointsLog) {
            $pointsLog->reject();
        });
    }

    /**
     * Correct/update an existing points log entry.
     */
    public function correctPoints(PointsLog $pointsLog, array $data): PointsLog
    {
        return DB::transaction(function () use ($pointsLog, $data) {
            $lockedStudent = Student::withoutGlobalScopes()->where('id', $pointsLog->student_id)->lockForUpdate()->firstOrFail();

            if (isset($data['rule_id']) && ! isset($data['points'])) {
                $rule = Rule::find($data['rule_id']);
                if ($rule) {
                    $data['points'] = $rule->points;
                }
            }

            $pointsLog->update($data);

            if ($pointsLog->rule->type === RuleType::Violation) {
                $this->thresholdEngine->evaluate($lockedStudent);
            } elseif ($pointsLog->rule->type === RuleType::Achievement) {
                $this->certificateEngine->evaluate($lockedStudent);
            }

            return $pointsLog->fresh();
        });
    }

    /**
     * Delete a points log entry.
     */
    public function deletePoints(PointsLog $pointsLog): void
    {
        DB::transaction(function () use ($pointsLog) {
            if ($pointsLog->evidence_url && str_starts_with($pointsLog->evidence_url, '/storage/')) {
                $relativePath = str_replace('/storage/', '', $pointsLog->evidence_url);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($relativePath);
            }
            $pointsLog->delete();
        });
    }
}
