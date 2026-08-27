<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRuleRequest;
use App\Models\Rule;
use Illuminate\Http\Request;

class RuleController extends Controller
{
    public function index(Request $request)
    {
        $allRules = Rule::query()
            ->when($request->category, fn ($q, $cat) => $q->where('category', $cat))
            ->orderBy('type')->orderBy('category')->orderBy('name')
            ->get();

        $grouped = $allRules->groupBy(fn ($r) => $r->type->value);

        return view('admin.rules.index', compact('grouped'));
    }

    public function create()
    {
        return view('admin.rules.create');
    }

    public function store(StoreRuleRequest $request)
    {
        $data = $request->validated();
        $data['school_id'] = $request->user()->school_id;
        Rule::create($data);

        return redirect()->route('admin.rules.index')->with('success', 'Peraturan berhasil dibuat.');
    }

    public function show(Rule $rule)
    {
        return redirect()->route('admin.rules.edit', $rule);
    }

    public function edit(Rule $rule)
    {
        return view('admin.rules.edit', compact('rule'));
    }

    public function update(StoreRuleRequest $request, Rule $rule)
    {
        $rule->update($request->validated());

        return redirect()->route('admin.rules.index')->with('success', 'Peraturan berhasil diperbarui.');
    }

    public function toggleActive(Rule $rule)
    {
        $rule->update(['is_active' => ! $rule->is_active]);
        $status = $rule->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.rules.index')->with('success', "Peraturan berhasil {$status}.");
    }

    public function destroy(Rule $rule)
    {
        $rule->delete();

        return redirect()->route('admin.rules.index')->with('success', "Peraturan \"{$rule->name}\" berhasil dihapus.");
    }
}
