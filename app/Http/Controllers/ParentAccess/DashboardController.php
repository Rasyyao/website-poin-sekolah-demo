<?php

namespace App\Http\Controllers\ParentAccess;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $studentId = $request->session()->get('parent_student_id');

        if (! $studentId) {
            return redirect()->route('parent.login.form');
        }

        $student = Student::withoutGlobalScopes()->findOrFail($studentId);

        $recentLogs = $student->pointsLogs()
            ->withoutGlobalScopes()
            ->with(['rule:id,name,type,category', 'reporter:id,name'])
            ->where('status', 'approved')
            ->orderByDesc('occurred_at')
            ->limit(20)
            ->get();

        return view('parent.dashboard', [
            'student' => $student,
            'stats' => [
                'total_points' => $student->totalPoints(),
                'total_violation_points' => $student->totalViolationPoints(),
                'total_achievement_points' => $student->totalAchievementPoints(),
            ],
            'recentLogs' => $recentLogs,
        ]);
    }

    public function exportPdf(Request $request, \App\Services\ReportService $reportService)
    {
        $studentId = $request->session()->get('parent_student_id');
        if (! $studentId) return redirect()->route('parent.login.form');
        
        $student = Student::withoutGlobalScopes()->findOrFail($studentId);
        $report = $reportService->studentBehaviorReport($student, $request->from, $request->to);
        $logs = $student->pointsLogs()->withoutGlobalScopes()->with(['rule', 'reporter'])->orderByDesc('occurred_at')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.pdf.student-report', [
            'student' => $student,
            'report' => $report,
            'logs' => $logs
        ]);

        return $pdf->download('laporan-anak-' . $student->nisn . '.pdf');
    }
}
