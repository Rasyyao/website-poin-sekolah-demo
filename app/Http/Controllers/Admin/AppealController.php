<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appeal;
use Illuminate\Http\Request;

class AppealController extends Controller
{
    public function index(Request $request)
    {
        $appeals = Appeal::query()
            ->with(['pointsLog.student:id,name,nisn', 'pointsLog.rule:id,name', 'submitter'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.appeals.index', compact('appeals'));
    }

    public function show(Appeal $appeal)
    {
        return view('admin.appeals.show', ['appeal' => $appeal->load(['pointsLog.student', 'pointsLog.rule', 'submitter', 'resolver'])]);
    }

    public function accept(Request $request, Appeal $appeal)
    {
        if (! $appeal->isPending()) {
            return back()->with('error', 'Banding ini sudah diputuskan.');
        }

        $appeal->accept($request->user()->id, $request->input('resolution_note'));
        $appeal->pointsLog->reject();

        return redirect()->route('admin.appeals.index')->with('success', 'Banding diterima. Poin terkait dibatalkan.');
    }

    public function reject(Request $request, Appeal $appeal)
    {
        if (! $appeal->isPending()) {
            return back()->with('error', 'Banding ini sudah diputuskan.');
        }

        $appeal->reject($request->user()->id, $request->input('resolution_note'));

        return redirect()->route('admin.appeals.index')->with('success', 'Banding ditolak.');
    }
}
