<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePointsLogRequest;
use App\Models\Rule;
use App\Models\Student;
use App\Services\PointsService;

class PointsController extends Controller
{
    public function __construct(
        private PointsService $pointsService,
    ) {}

    public function index()
    {
        $rules = Rule::active()->orderBy('type')->orderBy('name')->get();
        $students = Student::orderBy('name')->get(['id', 'name', 'nisn', 'class_id']);
        
        $logs = \App\Models\PointsLog::with(['student', 'rule'])
            ->where('reported_by', auth()->id())
            ->latest()
            ->paginate(15);

        return view('teacher.points.index', compact('rules', 'students', 'logs'));
    }

    public function store(StorePointsLogRequest $request)
    {
        $student = Student::findOrFail($request->student_id);
        $rule = Rule::findOrFail($request->rule_id);

        $pointsLog = $this->pointsService->recordPoints(
            student: $student,
            rule: $rule,
            reporter: $request->user(),
            note: $request->note,
            evidenceUrl: $request->evidence_url,
            occurredAt: $request->occurred_at ? new \DateTime($request->occurred_at) : null,
        );

        $msg = $pointsLog->isPending()
            ? 'Pelanggaran berat berhasil dilaporkan. Menunggu approval admin.'
            : 'Poin berhasil dicatat.';

        return redirect()->route('teacher.points.index')->with('success', $msg);
    }
}
