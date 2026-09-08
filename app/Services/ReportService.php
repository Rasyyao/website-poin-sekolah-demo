<?php

namespace App\Services;

use App\Models\PointsLog;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get dashboard statistics for a school.
     */
    public function dashboardStats(int $schoolId, ?string $from = null, ?string $to = null): array
    {
        $query = PointsLog::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('status', 'approved');

        if ($from && $to) {
            $query->whereBetween('occurred_at', [$from, $to]);
        }

        return [
            'total_violations' => (clone $query)->whereHas('rule', fn($q) => $q->where('type', 'violation'))->count(),
            'total_achievements' => (clone $query)->whereHas('rule', fn($q) => $q->where('type', 'achievement'))->count(),
            'total_violation_points' => (int) (clone $query)->whereHas('rule', fn($q) => $q->where('type', 'violation'))->sum('points'),
            'total_achievement_points' => (int) (clone $query)->whereHas('rule', fn($q) => $q->where('type', 'achievement'))->sum('points'),
            'students_with_violations' => (clone $query)->whereHas('rule', fn($q) => $q->where('type', 'violation'))->distinct('student_id')->count('student_id'),
        ];
    }

    /**
     * Get top violators for a school.
     */
    public function topViolators(int $schoolId, int $limit = 10, ?string $from = null, ?string $to = null): Collection
    {
        $query = PointsLog::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('status', 'approved')
            ->whereHas('rule', fn($q) => $q->where('type', 'violation'))
            ->select('student_id', DB::raw('SUM(points) as total_points'), DB::raw('COUNT(*) as violation_count'))
            ->groupBy('student_id')
            ->orderByDesc('total_points')
            ->limit($limit);

        if ($from && $to) {
            $query->whereBetween('occurred_at', [$from, $to]);
        }

        return $query->get()->map(function ($row) {
            $student = Student::withoutGlobalScopes()->find($row->student_id);

            return [
                'student' => $student,
                'total_points' => $row->total_points,
                'violation_count' => $row->violation_count,
            ];
        });
    }

    /**
     * Get most common violations for a school.
     */
    public function mostCommonViolations(int $schoolId, int $limit = 10): Collection
    {
        return PointsLog::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('status', 'approved')
            ->whereHas('rule', fn($q) => $q->where('type', 'violation'))
            ->select('rule_id', DB::raw('COUNT(*) as occurrence_count'))
            ->groupBy('rule_id')
            ->orderByDesc('occurrence_count')
            ->limit($limit)
            ->with('rule')
            ->get();
    }

    /**
     * Get student behavior report data (for PDF generation).
     */
    public function studentBehaviorReport(Student $student, ?string $from = null, ?string $to = null): array
    {
        $query = $student->pointsLogs()->where('status', 'approved');

        if ($from && $to) {
            $query->whereBetween('occurred_at', [$from, $to]);
        }

        $logs = $query->with('rule', 'reporter')->orderBy('occurred_at', 'desc')->get();

        return [
            'student' => $student->load('currentClass', 'school'),
            'period' => ['from' => $from, 'to' => $to],
            'logs' => $logs,
            'summary' => [
                'total_violations' => $logs->filter(fn($log) => $log->rule->type->value === 'violation')->count(),
                'total_achievements' => $logs->filter(fn($log) => $log->rule->type->value === 'achievement')->count(),
                'total_violation_points' => $logs->filter(fn($log) => $log->rule->type->value === 'violation')->sum('points'),
                'total_achievement_points' => $logs->filter(fn($log) => $log->rule->type->value === 'achievement')->sum('points'),
            ],
        ];
    }

    /**
     * Get statistics per class.
     */
    public function classStats(int $schoolId, int $classId): array
    {
        $students = Student::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->get();

        $studentIds = $students->pluck('id');

        $violationData = PointsLog::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereIn('student_id', $studentIds)
            ->where('status', 'approved')
            ->whereHas('rule', fn($q) => $q->where('type', 'violation'))
            ->select('student_id', DB::raw('SUM(points) as total_points'))
            ->groupBy('student_id')
            ->pluck('total_points', 'student_id');

        $achievementData = PointsLog::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereIn('student_id', $studentIds)
            ->where('status', 'approved')
            ->whereHas('rule', fn($q) => $q->where('type', 'achievement'))
            ->select('student_id', DB::raw('SUM(points) as total_points'))
            ->groupBy('student_id')
            ->pluck('total_points', 'student_id');

        $thresholds = \App\Models\RuleThreshold::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->orderByDesc('min_points')
            ->get();

        return [
            'total_students' => $students->count(),
            'students_with_violations' => $violationData->count(),
            'total_violation_points' => $violationData->sum(),
            'students' => $students->map(function (Student $s) use ($violationData, $achievementData, $thresholds) {
                $v = $violationData->get($s->id, 0);
                $a = $achievementData->get($s->id, 0);
                
                $actionRequired = null;
                foreach ($thresholds as $t) {
                    if ($v >= $t->min_points) {
                        $actionRequired = 'Butuh Tindakan: ' . $t->action;
                        break;
                    }
                }

                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'nisn' => $s->nisn,
                    'total_violation_points' => $v,
                    'total_achievement_points' => $a,
                    'action_required' => $actionRequired,
                ];
            })->sortBy('name')->values(),
        ];
    }

    /**
     * Get chart data for the last 14 days.
     */
    public function dashboardChartData(int $schoolId, int $days = 30): array
    {
        $startDate = now()->subDays($days - 1)->startOfDay();
        
        $logs = PointsLog::withoutGlobalScopes()
            ->with('rule:id,type')
            ->where('school_id', $schoolId)
            ->where('status', 'approved')
            ->where('occurred_at', '>=', $startDate)
            ->select('id', 'occurred_at', 'points', 'rule_id')
            ->get();

        $chartData = [
            'labels' => [],
            'achievements' => [],
            'violations' => [],
        ];

        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($days - 1 - $i);
            $dateString = $date->format('Y-m-d');
            $label = $date->format('d M');
            
            $dayLogs = $logs->filter(fn($log) => $log->occurred_at->format('Y-m-d') === $dateString);
            
            $chartData['labels'][] = $label;
            $chartData['achievements'][] = (int) $dayLogs->filter(fn($log) => $log->rule->type->value === 'achievement')->sum('points');
            $chartData['violations'][] = (int) $dayLogs->filter(fn($log) => $log->rule->type->value === 'violation')->sum('points');
        }

        return $chartData;
    }

    /**
     * Get class distribution with student count.
     */
    public function dashboardClassDistribution(int $schoolId): array
    {
        $classes = \App\Models\SchoolClass::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->withCount(['students' => function ($query) use ($schoolId) {
                $query->withoutGlobalScopes()->where('school_id', $schoolId);
            }])
            ->orderBy('name')
            ->get();

        return [
            'labels' => $classes->pluck('name')->toArray(),
            'data' => $classes->pluck('students_count')->map(fn($v) => (int) $v)->toArray(),
            'total_students' => (int) $classes->sum('students_count'),
            'total_classes' => $classes->count(),
        ];
    }

    /**
     * Get student rankings based on total violation points.
     */
    public function studentsByThreshold(int $schoolId)
    {
        $thresholds = \App\Models\RuleThreshold::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->orderByDesc('min_points')
            ->get();

        if ($thresholds->isEmpty()) {
            return collect();
        }

        $minThreshold = $thresholds->last()->min_points;

        $students = Student::withoutGlobalScopes()
            ->with(['currentClass'])
            ->where('students.school_id', $schoolId)
            ->selectRaw('students.*, COALESCE((
                SELECT SUM(points_log.points) 
                FROM points_log 
                INNER JOIN rules ON points_log.rule_id = rules.id
                WHERE points_log.student_id = students.id 
                AND points_log.status = "approved" 
                AND rules.type = "violation"
            ), 0) as total_violation_points')
            ->orderByDesc('total_violation_points')
            ->get();
            
        $students = $students->filter(function ($s) use ($minThreshold) {
            return $s->total_violation_points >= $minThreshold;
        });

        $grouped = collect();

        foreach ($thresholds as $threshold) {
            $matchedStudents = $students->filter(function ($s) use ($threshold) {
                return $s->total_violation_points >= $threshold->min_points;
            });
            
            if ($matchedStudents->isNotEmpty()) {
                $grouped->push([
                    'threshold' => $threshold,
                    'students' => $matchedStudents->values()
                ]);
            }

            $students = $students->reject(function ($s) use ($threshold) {
                return $s->total_violation_points >= $threshold->min_points;
            });
        }

        return $grouped;
    }
}
