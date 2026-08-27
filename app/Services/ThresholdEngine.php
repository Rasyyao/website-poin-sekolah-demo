<?php

namespace App\Services;

use App\Models\NotificationLog;
use App\Models\RuleThreshold;
use App\Models\Student;
use Illuminate\Support\Facades\Log;

class ThresholdEngine
{
    /**
     * Evaluate a student's accumulated violation points against school thresholds.
     *
     * Checks all configured thresholds for the student's school and triggers
     * appropriate actions when thresholds are crossed.
     */
    public function evaluate(Student $student): array
    {
        $totalViolationPoints = $student->totalViolationPoints();

        $thresholds = RuleThreshold::withoutGlobalScopes()
            ->where('school_id', $student->school_id)
            ->where('min_points', '<=', $totalViolationPoints)
            ->orderBy('min_points', 'asc')
            ->get();

        $triggeredActions = [];

        foreach ($thresholds as $threshold) {
            $triggeredActions[] = $this->triggerAction($student, $threshold, $totalViolationPoints);
        }

        return $triggeredActions;
    }

    /**
     * Trigger the action defined by a threshold.
     *
     * Currently logs the action and creates a notification record.
     * Actual notification sending (WA/email) to be implemented with external service integration.
     */
    private function triggerAction(Student $student, RuleThreshold $threshold, int $currentPoints): array
    {
        $action = $threshold->action;
        $message = $this->buildMessage($student, $threshold, $currentPoints);

        // Log the notification (actual sending deferred to notification service integration)
        NotificationLog::withoutGlobalScopes()->create([
            'school_id' => $student->school_id,
            'student_id' => $student->id,
            'channel' => 'email', // default channel
            'message' => $message,
            'sent_at' => now(),
            'status' => 'sent',
        ]);

        Log::info("Threshold triggered for student {$student->id}", [
            'action' => $action,
            'threshold_points' => $threshold->min_points,
            'current_points' => $currentPoints,
        ]);

        return [
            'action' => $action,
            'threshold' => $threshold,
            'message' => $message,
        ];
    }

    private function buildMessage(Student $student, RuleThreshold $threshold, int $currentPoints): string
    {
        return sprintf(
            '[%s] Siswa %s (NISN: %s) telah mencapai %d poin pelanggaran (ambang batas: %d). Tindakan: %s. %s',
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
