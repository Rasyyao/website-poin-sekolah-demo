<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppealRequest;
use App\Models\Appeal;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppealController extends Controller
{
    public function index(Request $request)
    {
        $studentId = $request->session()->get('parent_student_id');

        $appeals = Appeal::where('submitter_type', Student::class)
            ->where('submitter_id', $studentId)
            ->with(['pointsLog.rule:id,name', 'resolver:id,name'])
            ->orderByDesc('created_at')
            ->get();

        return view('student.appeals', compact('appeals'));
    }

    public function store(StoreAppealRequest $request)
    {
        $studentId = $request->session()->get('parent_student_id');

        $existing = Appeal::where('points_log_id', $request->points_log_id)
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return back()->with('error', 'Sudah ada banding yang sedang diproses untuk poin ini.');
        }

        $evidenceUrl = null;
        if ($request->hasFile('evidence')) {
            $path = $request->file('evidence')->store('evidence', 'public');
            $evidenceUrl = Storage::url($path);
        }

        Appeal::create([
            'points_log_id' => $request->points_log_id,
            'submitter_type' => Student::class,
            'submitter_id' => $studentId,
            'reason' => $request->reason,
            'evidence_url' => $evidenceUrl,
            'status' => 'pending',
        ]);

        return redirect()->route('student.appeals.index')->with('success', 'Banding berhasil diajukan.');
    }
}
