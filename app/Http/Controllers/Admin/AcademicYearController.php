<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAcademicYearRequest;
use App\Models\AcademicYear;

class AcademicYearController extends Controller
{
    public function index()
    {
        $school = auth()->user()->school ?? \App\Models\School::first();
        $activeYear = $school?->activeAcademicYear() ?? AcademicYear::where('is_active', true)->first();
        $years = AcademicYear::orderByDesc('year_label')->orderByDesc('semester')->paginate(15);

        return view('admin.academic-years.index', compact('school', 'activeYear', 'years'));
    }

    public function create()
    {
        return view('admin.academic-years.create');
    }

    public function store(StoreAcademicYearRequest $request)
    {
        $schoolId = auth()->user()->school_id ?? \App\Models\School::first()?->id;

        $year = AcademicYear::firstOrCreate(
            [
                'school_id' => $schoolId,
                'year_label' => $request->year_label,
                'semester' => $request->semester,
            ],
            [
                'is_active' => true,
            ]
        );

        $year->activate();

        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun ajaran berhasil disimpan dan diaktifkan.');
    }

    public function show(AcademicYear $academicYear)
    {
        return redirect()->route('admin.academic-years.edit', $academicYear);
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-years.edit', compact('academicYear'));
    }

    public function update(StoreAcademicYearRequest $request, AcademicYear $academicYear)
    {
        $academicYear->update($request->validated());
        if ($request->boolean('is_active')) {
            $academicYear->activate();
        }

        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function activate(AcademicYear $academicYear)
    {
        $academicYear->activate();

        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun ajaran berhasil diaktifkan.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();

        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
