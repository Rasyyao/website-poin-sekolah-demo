<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\ReportService;
use Illuminate\Http\Request;

class StudentMonitorController extends Controller
{
    public function __construct(
        private ReportService $reportService,
    ) {}

    public function myStudents(Request $request)
    {
        $user = $request->user();
        $classIds = SchoolClass::where('homeroom_teacher_id', $user->id)->pluck('id');

        $students = Student::whereIn('class_id', $classIds)
            ->with('currentClass:id,name')
            ->orderBy('name')
            ->get()
            ->map(fn (Student $s) => [
                'id' => $s->id,
                'name' => $s->name,
                'nisn' => $s->nisn,
                'class' => $s->currentClass?->name,
                'total_points' => $s->totalPoints(),
                'violation_points' => $s->totalViolationPoints(),
                'achievement_points' => $s->totalAchievementPoints(),
            ]);

        return view('teacher.my-students', compact('students'));
    }

    public function studentHistory(Request $request, Student $student)
    {
        $logs = $student->pointsLogs()
            ->with(['rule:id,name,type,category', 'reporter:id,name'])
            ->orderByDesc('occurred_at')
            ->paginate(20);

        return view('teacher.student-history', compact('student', 'logs'));
    }

    public function classSummary(Request $request, SchoolClass $class)
    {
        $stats = $this->reportService->classStats($request->user()->school_id, $class->id);

        return view('admin.classes.show', compact('class', 'stats'));
    }
}
