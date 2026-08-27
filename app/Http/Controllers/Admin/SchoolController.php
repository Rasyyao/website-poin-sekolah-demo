<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        $schools = School::query()
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->when($request->status, fn ($q, $status) => $q->where('subscription_status', $status))
            ->orderBy('name')
            ->paginate(15);

        return view('super-admin.schools.index', compact('schools'));
    }

    public function show(School $school)
    {
        return view('super-admin.schools.show', ['school' => $school->loadCount(['users', 'students', 'classes'])]);
    }

    public function create()
    {
        return view('super-admin.schools.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:schools,slug'],
            'subscription_status' => ['sometimes', 'in:active,trial,expired'],
        ]);

        School::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'subscription_status' => $validated['subscription_status'] ?? 'trial',
        ]);

        return redirect()->route('super-admin.schools.index')->with('success', 'Sekolah berhasil dibuat.');
    }

    public function edit(School $school)
    {
        return view('super-admin.schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'subscription_status' => ['sometimes', 'in:active,trial,expired'],
        ]);

        $school->update($validated);

        return redirect()->route('super-admin.schools.index')->with('success', 'Sekolah berhasil diperbarui.');
    }

    public function destroy(School $school)
    {
        $school->delete();

        return redirect()->route('super-admin.schools.index')->with('success', 'Sekolah berhasil dihapus.');
    }
}
