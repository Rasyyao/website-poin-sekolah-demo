<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePointsLogRequest;
use App\Http\Requests\UpdatePointsLogRequest;
use App\Models\PointsLog;
use App\Models\Rule;
use App\Models\Student;
use App\Services\PointsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PointsController extends Controller
{
    public function __construct(
        private PointsService $pointsService,
    ) {}

    public function index(Request $request)
    {
        $rules = Rule::active()->ordered()->get();
        $students = Student::orderBy('name')->get(['id', 'name', 'nisn', 'class_id']);
        
        $query = PointsLog::with(['student', 'rule'])
            ->where('reported_by', auth()->id());

        if ($request->filled('type')) {
            $query->whereHas('rule', function ($q) use ($request) {
                $q->where('type', $request->type);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->latest()->paginate(15);

        return view('teacher.points.index', compact('rules', 'students', 'logs'));
    }

    public function store(StorePointsLogRequest $request)
    {
        $student = Student::findOrFail($request->student_id);
        $rule = Rule::findOrFail($request->rule_id);

        // Prevent duplicate submission within a short window (5 seconds)
        $lockKey = "record_points_u{$request->user()->id}_s{$student->id}_r{$rule->id}";
        $lock = Cache::lock($lockKey, 5);

        if (! $lock->get()) {
            return redirect()->route('teacher.points.index')->with('warning', 'Poin sedang diproses atau baru saja dicatat.');
        }

        try {
            // Check for identical recent entry (within last 3 seconds)
            $recent = PointsLog::where('school_id', $student->school_id)
                ->where('student_id', $student->id)
                ->where('rule_id', $rule->id)
                ->where('reported_by', $request->user()->id)
                ->where('created_at', '>=', now()->subSeconds(3))
                ->first();

            if ($recent) {
                return redirect()->route('teacher.points.index')->with('warning', 'Poin yang sama baru saja dicatat.');
            }

            $evidenceUrl = $request->evidence_url;
            if ($request->hasFile('evidence')) {
                $path = $request->file('evidence')->store('evidence', 'public');
                $evidenceUrl = Storage::url($path);
            }

            $pointsLog = $this->pointsService->recordPoints(
                student: $student,
                rule: $rule,
                reporter: $request->user(),
                note: $request->note,
                evidenceUrl: $evidenceUrl,
                occurredAt: $request->occurred_at ? new \DateTime($request->occurred_at) : null,
            );

            $msg = $pointsLog->isPending()
                ? 'Pelanggaran berat berhasil dilaporkan. Menunggu approval admin.'
                : 'Poin berhasil dicatat.';

            return redirect()->route('teacher.points.index')->with('success', $msg);
        } finally {
            optional($lock)->release();
        }
    }

    public function update(UpdatePointsLogRequest $request, PointsLog $pointsLog)
    {
        if ($pointsLog->reported_by !== $request->user()->id && ! $request->user()->isAdmin() && ! $request->user()->isSuperAdmin()) {
            abort(403, 'Anda hanya dapat mengubah poin yang Anda laporkan.');
        }

        $rule = Rule::findOrFail($request->rule_id);

        $evidenceUrl = $pointsLog->evidence_url;

        if ($request->boolean('remove_evidence')) {
            if ($evidenceUrl && str_starts_with($evidenceUrl, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $evidenceUrl));
            }
            $evidenceUrl = null;
        }

        if ($request->hasFile('evidence')) {
            if ($evidenceUrl && str_starts_with($evidenceUrl, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $evidenceUrl));
            }
            $path = $request->file('evidence')->store('evidence', 'public');
            $evidenceUrl = Storage::url($path);
        }

        $this->pointsService->correctPoints($pointsLog, [
            'rule_id' => $rule->id,
            'points' => $rule->points,
            'note' => $request->note,
            'evidence_url' => $evidenceUrl,
            'occurred_at' => $request->occurred_at ? new \DateTime($request->occurred_at) : $pointsLog->occurred_at,
        ]);

        return redirect()->route('teacher.points.index')->with('success', 'Log poin berhasil diperbarui.');
    }

    public function destroy(Request $request, PointsLog $pointsLog)
    {
        if ($pointsLog->reported_by !== $request->user()->id && ! $request->user()->isAdmin() && ! $request->user()->isSuperAdmin()) {
            abort(403, 'Anda hanya dapat menghapus poin yang Anda laporkan.');
        }

        $this->pointsService->deletePoints($pointsLog);

        return redirect()->route('teacher.points.index')->with('success', 'Log poin berhasil dihapus.');
    }
}
