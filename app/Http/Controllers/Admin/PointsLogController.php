<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointsLog;
use App\Services\PointsService;
use Illuminate\Http\Request;

class PointsLogController extends Controller
{
    public function __construct(
        private PointsService $pointsService,
    ) {}

    public function index(Request $request)
    {
        $logs = PointsLog::query()
            ->with(['student:id,name,nisn', 'rule:id,name,type,category', 'reporter:id,name'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->type, fn ($q, $t) => $t === 'violation' ? $q->where('points', '<', 0) : $q->where('points', '>', 0))
            ->orderByDesc('occurred_at')
            ->paginate(20);

        return view('admin.points-log.index', compact('logs'));
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
}
