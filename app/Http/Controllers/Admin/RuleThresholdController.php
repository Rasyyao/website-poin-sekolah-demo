<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRuleThresholdRequest;
use App\Models\RuleThreshold;

class RuleThresholdController extends Controller
{
    public function index()
    {
        $thresholds = RuleThreshold::orderBy('min_points')->paginate(15);

        return view('admin.thresholds.index', compact('thresholds'));
    }

    public function create()
    {
        return view('admin.thresholds.create');
    }

    public function store(StoreRuleThresholdRequest $request)
    {
        $data = $request->validated();
        $data['school_id'] = $request->user()->school_id;
        RuleThreshold::create($data);

        return redirect()->route('admin.rule-thresholds.index')->with('success', 'Threshold berhasil dibuat.');
    }

    public function edit(RuleThreshold $ruleThreshold)
    {
        return view('admin.thresholds.edit', compact('ruleThreshold'));
    }

    public function update(StoreRuleThresholdRequest $request, RuleThreshold $ruleThreshold)
    {
        $ruleThreshold->update($request->validated());

        return redirect()->route('admin.rule-thresholds.index')->with('success', 'Threshold berhasil diperbarui.');
    }

    public function destroy(RuleThreshold $ruleThreshold)
    {
        $ruleThreshold->delete();

        return redirect()->route('admin.rule-thresholds.index')->with('success', 'Threshold berhasil dihapus.');
    }
}
