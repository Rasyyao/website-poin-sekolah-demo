<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AchievementCategory;
use App\Enums\ViolationCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRuleRequest;
use App\Models\Rule;
use Illuminate\Http\Request;

class RuleController extends Controller
{
    private function authorizeAdmin(): void
    {
        if (! in_array(request()->user()?->role?->value, ['super_admin', 'admin'])) {
            abort(403, 'Hanya admin yang dapat mengelola peraturan.');
        }
    }

    public function index(Request $request)
    {
        $allRules = Rule::query()
            ->when($request->category, fn ($q, $cat) => $q->where('category', $cat))
            ->ordered()
            ->get();

        $grouped = $allRules->groupBy(fn ($r) => $r->type->value);

        $violationCategories = ViolationCategory::cases();
        $achievementCategories = AchievementCategory::cases();

        return view('admin.rules.index', compact('grouped', 'violationCategories', 'achievementCategories'));
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('admin.rules.create');
    }

    public function store(StoreRuleRequest $request)
    {
        $this->authorizeAdmin();

        $data = $request->validated();
        $data['school_id'] = $request->user()->school_id;
        Rule::create($data);

        return redirect()->route('admin.rules.index')->with('success', 'Peraturan berhasil dibuat.');
    }

    public function show(Rule $rule)
    {
        return redirect()->route('admin.rules.index');
    }

    public function edit(Rule $rule)
    {
        $this->authorizeAdmin();

        return view('admin.rules.edit', compact('rule'));
    }

    public function update(StoreRuleRequest $request, Rule $rule)
    {
        $this->authorizeAdmin();

        $rule->update($request->validated());

        return redirect()->route('admin.rules.index')->with('success', 'Peraturan berhasil diperbarui.');
    }

    public function toggleActive(Rule $rule)
    {
        $this->authorizeAdmin();

        $rule->update(['is_active' => ! $rule->is_active]);
        $status = $rule->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.rules.index')->with('success', "Peraturan berhasil {$status}.");
    }

    public function destroy(Rule $rule)
    {
        $this->authorizeAdmin();

        $rule->delete();

        return redirect()->route('admin.rules.index')->with('success', "Peraturan \"{$rule->name}\" berhasil dihapus.");
    }
}
