<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassRequest;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $classes = SchoolClass::query()
            ->with(['homeroomTeacher:id,name', 'academicYear:id,year_label,semester'])
            ->when($request->academic_year_id, fn ($q, $id) => $q->where('academic_year_id', $id))
            ->withCount('students')
            ->orderBy('name')
            ->paginate(15);

        $teachers = User::where('school_id', auth()->user()->school_id)->whereIn('role', ['teacher', 'homeroom', 'counselor'])->orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('year_label')->get();

        return view('admin.classes.index', compact('classes', 'teachers', 'academicYears'));
    }

    public function create()
    {
        $teachers = User::where('school_id', auth()->user()->school_id)->whereIn('role', ['teacher', 'homeroom', 'counselor'])->orderBy('name')->paginate(15);
        $academicYears = AcademicYear::orderByDesc('year_label')->paginate(15);

        return view('admin.classes.create', compact('teachers', 'academicYears'));
    }

    public function store(StoreClassRequest $request)
    {
        SchoolClass::create($request->validated());

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil dibuat.');
    }

    public function show(SchoolClass $class)
    {
        $class->load(['homeroomTeacher', 'academicYear', 'students']);

        return view('admin.classes.show', compact('class'));
    }

    public function edit(SchoolClass $class)
    {
        $teachers = User::where('school_id', auth()->user()->school_id)->whereIn('role', ['teacher', 'homeroom', 'counselor'])->orderBy('name')->paginate(15);
        $academicYears = AcademicYear::orderByDesc('year_label')->paginate(15);

        return view('admin.classes.edit', compact('class', 'teachers', 'academicYears'));
    }

    public function update(StoreClassRequest $request, SchoolClass $class)
    {
        $class->update($request->validated());

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(SchoolClass $class)
    {
        if ($class->students()->exists()) {
            return redirect()->route('admin.classes.index')->with('error', 'Tidak dapat menghapus kelas yang masih memiliki siswa.');
        }

        $class->delete();

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
