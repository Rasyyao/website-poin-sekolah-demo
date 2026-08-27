<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointsLog;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentReportExport;
use App\Exports\ClassReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SchoolClass;
use App\Models\Student;
class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService,
    ) {}

    public function dashboard(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $stats = $this->reportService->dashboardStats($schoolId, $request->from, $request->to);
        $topViolators = $this->reportService->topViolators($schoolId, 5, $request->from, $request->to);
        $pendingCount = PointsLog::where('status', 'pending')->count();
        $recentLogs = PointsLog::with(['student:id,name', 'rule:id,name,type'])
            ->where('status', 'approved')
            ->orderByDesc('occurred_at')
            ->limit(5)
            ->get();
            
        $chartData = $this->reportService->dashboardChartData($schoolId);
        $classDistributionData = $this->reportService->dashboardClassDistribution($schoolId);

        return view('admin.dashboard', compact('stats', 'topViolators', 'pendingCount', 'recentLogs', 'chartData', 'classDistributionData'));
    }

    public function ranking(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $groupedStudents = $this->reportService->studentsByThreshold($schoolId);

        return view('admin.reports.ranking', compact('groupedStudents'));
    }

    public function studentReport(Request $request, \App\Models\Student $student)
    {
        $report = $this->reportService->studentBehaviorReport($student, $request->from, $request->to);

        return view('admin.students.show', [
            'student' => $student,
            'totalPoints' => $report['summary']['net_points'],
            'violationPoints' => $report['summary']['total_violation_points'],
            'achievementPoints' => $report['summary']['total_achievement_points'],
            'logs' => $student->pointsLogs()->with(['rule', 'reporter'])->orderByDesc('occurred_at')->paginate(20),
        ]);
    }

    public function classStats(Request $request, int $classId)
    {
        $stats = $this->reportService->classStats($request->user()->school_id, $classId);

        return view('admin.classes.show', ['classStats' => $stats]);
    }

    public function exportStudentPdf(Request $request, Student $student)
    {
        $report = $this->reportService->studentBehaviorReport($student, $request->from, $request->to);
        $logs = $student->pointsLogs()->with(['rule', 'reporter'])->orderByDesc('occurred_at')->get();

        $pdf = Pdf::loadView('exports.pdf.student-report', [
            'student' => $student,
            'report' => $report,
            'logs' => $logs
        ]);

        return $pdf->download('laporan-siswa-' . $student->nisn . '.pdf');
    }

    public function exportStudentExcel(Request $request, Student $student)
    {
        $logs = $student->pointsLogs()->with(['rule', 'reporter'])->orderByDesc('occurred_at')->get();
        return Excel::download(new StudentReportExport($student, $logs), 'laporan-siswa-' . $student->nisn . '.xlsx');
    }

    public function exportClassPdf(Request $request, int $classId)
    {
        $schoolClass = SchoolClass::findOrFail($classId);
        $schoolId = $request->user()->school_id ?? $schoolClass->school_id;
        $stats = $this->reportService->classStats($schoolId, $classId);

        $pdf = Pdf::loadView('exports.pdf.class-report', [
            'classStats' => $stats,
            'schoolClass' => $schoolClass
        ]);

        return $pdf->download('laporan-kelas-' . $schoolClass->name . '.pdf');
    }

    public function exportClassExcel(Request $request, int $classId)
    {
        $schoolClass = SchoolClass::findOrFail($classId);
        $schoolId = $request->user()->school_id ?? $schoolClass->school_id;
        $stats = $this->reportService->classStats($schoolId, $classId);

        return Excel::download(new ClassReportExport($stats, $schoolClass->name), 'laporan-kelas-' . $schoolClass->name . '.xlsx');
    }
}
