<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appeal;
use Illuminate\Http\Request;

class AppealController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Appeal::query()
            ->with(['pointsLog.student:id,name,nisn', 'pointsLog.rule:id,name', 'submitter'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->orderByDesc('created_at');

        // Scoping: Guru and Kesiswaan only see appeals for violations they reported. Admin sees all.
        if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
            $query->whereHas('pointsLog', function ($q) use ($user) {
                $q->where('reported_by', $user->id);
            });
        }

        $appeals = $query->paginate(15);

        return view('admin.appeals.index', compact('appeals'));
    }

    public function show(Appeal $appeal)
    {
        $user = auth()->user();
        if (! $user->isAdmin() && ! $user->isSuperAdmin() && $appeal->pointsLog?->reported_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk melihat banding ini.');
        }

        return view('admin.appeals.show', ['appeal' => $appeal->load(['pointsLog.student', 'pointsLog.rule', 'submitter', 'resolver'])]);
    }

    public function accept(Request $request, Appeal $appeal)
    {
        $user = $request->user();
        if (! $user->isAdmin() && ! $user->isSuperAdmin() && $appeal->pointsLog?->reported_by !== $user->id) {
            abort(403, 'Anda hanya dapat memproses banding atas laporan yang Anda buat.');
        }

        if (! $appeal->isPending()) {
            return back()->with('error', 'Banding ini sudah diputuskan.');
        }

        $appeal->accept($user->id, $request->input('resolution_note'));
        $appeal->pointsLog->reject();

        return redirect()->route('admin.appeals.index')->with('success', 'Banding diterima. Poin terkait dibatalkan.');
    }

    public function reject(Request $request, Appeal $appeal)
    {
        $user = $request->user();
        if (! $user->isAdmin() && ! $user->isSuperAdmin() && $appeal->pointsLog?->reported_by !== $user->id) {
            abort(403, 'Anda hanya dapat memproses banding atas laporan yang Anda buat.');
        }

        if (! $appeal->isPending()) {
            return back()->with('error', 'Banding ini sudah diputuskan.');
        }

        $appeal->reject($user->id, $request->input('resolution_note'));

        return redirect()->route('admin.appeals.index')->with('success', 'Banding ditolak.');
    }
}
