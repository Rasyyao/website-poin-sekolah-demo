<?php

namespace App\Http\Controllers\ParentAccess;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService,
    ) {}

    public function behaviorReport(Request $request)
    {
        $studentId = $request->session()->get('parent_student_id');

        if (! $studentId) {
            return redirect()->route('parent.login.form');
        }

        $student = Student::withoutGlobalScopes()->findOrFail($studentId);
        $report = $this->reportService->studentBehaviorReport($student, $request->from, $request->to);

        return view('parent.report', compact('student', 'report'));
    }
}
