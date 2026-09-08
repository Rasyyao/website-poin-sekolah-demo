<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePointsLogRequest;
use App\Models\PointsLog;
use App\Models\Rule;
use App\Services\PointsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PointsLogController extends Controller
{
    public function __construct(
        private PointsService $pointsService,
    ) {}

    public function index(Request $request)
    {
        $rules = Rule::active()->ordered()->get();

        $logs = PointsLog::query()
            ->with(['student:id,name,nisn', 'rule:id,name,type,category', 'reporter:id,name'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->type, fn ($q, $t) => $t === 'violation' ? $q->violations() : $q->achievements())
            ->orderByDesc('occurred_at')
            ->paginate(20);

        return view('admin.points-log.index', compact('logs', 'rules'));
    }

    public function show(PointsLog $pointsLog)
    {
        return view('admin.points-log.show', ['log' => $pointsLog->load(['student', 'rule', 'reporter', 'appeals'])]);
    }

    public function approve(PointsLog $pointsLog)
    {
        if (! $pointsLog->isPending()) {
            return back()->with('error', 'Hanya poin dengan status pending yang bisa di-approve.');
        }

        $this->pointsService->approve($pointsLog);

        return redirect()->route('admin.points-log.index')->with('success', 'Poin berhasil di-approve.');
    }

    public function reject(PointsLog $pointsLog)
    {
        if (! $pointsLog->isPending()) {
            return back()->with('error', 'Hanya poin dengan status pending yang bisa di-reject.');
        }

        $this->pointsService->reject($pointsLog);

        return redirect()->route('admin.points-log.index')->with('success', 'Poin berhasil di-reject.');
    }

    public function update(UpdatePointsLogRequest $request, PointsLog $pointsLog)
    {
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

        return redirect()->route('admin.points-log.index')->with('success', 'Log poin berhasil diperbarui.');
    }

    public function destroy(PointsLog $pointsLog)
    {
        $this->pointsService->deletePoints($pointsLog);

        return redirect()->route('admin.points-log.index')->with('success', 'Log poin berhasil dihapus.');
    }
}
