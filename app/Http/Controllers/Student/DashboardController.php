<?php

namespace App\Http\Controllers\Student;

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

        $certificates = $student->certificates()
            ->withoutGlobalScopes()
            ->with('ruleThreshold')
            ->orderByDesc('issued_at')
            ->get();

        return view('student.dashboard', [
            'student' => $student,
            'stats' => [
                'total_points' => $student->totalPoints(),
                'total_violation_points' => $student->totalViolationPoints(),
                'total_achievement_points' => $student->totalAchievementPoints(),
            ],
            'recentLogs' => $recentLogs,
            'certificates' => $certificates,
        ]);
    }

    public function printCertificate(Request $request, \App\Models\Certificate $certificate)
    {
        $studentId = $request->session()->get('parent_student_id');
        if (! $studentId || $certificate->student_id != $studentId) {
            abort(403);
        }

        $certificate->load(['student.currentClass', 'ruleThreshold', 'school']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.pdf.certificate', [
            'certificate' => $certificate,
        ]);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download($certificate->downloadFilename());
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

        return $pdf->download('laporan-siswa-' . $student->nisn . '.pdf');
    }
}
