<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Requests\BulkMigrateStudentsRequest;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\StudentMigrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function __construct(
        private StudentMigrationService $migrationService,
    ) {}

    public function index(Request $request)
    {
        $classes = SchoolClass::orderBy('name')->get();
        $students = Student::query()
            ->with('currentClass:id,name')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('nisn', 'like', "%{$s}%"))
            ->when($request->class_id, fn ($q, $id) => $q->where('class_id', $id))
            ->orderBy('name')
            ->paginate(10);

        return view('admin.students.index', compact('students', 'classes'));
    }

    public function create()
    {
        $classes = SchoolClass::orderBy('name')->get();

        return view('admin.students.create', compact('classes'));
    }

    public function store(StoreStudentRequest $request)
    {
        $data = $request->validated();
        $data['school_id'] = $request->user()->school_id;
        Student::create($data);

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function show(Student $student)
    {
        $logs = $student->pointsLogs()->with(['rule', 'reporter'])->orderByDesc('occurred_at')->paginate(5);

        return view('admin.students.show', [
            'student' => $student->load(['currentClass', 'classHistories.schoolClass', 'classHistories.academicYear']),
            'totalPoints' => $student->totalPoints(),
            'violationPoints' => $student->totalViolationPoints(),
            'achievementPoints' => $student->totalAchievementPoints(),
            'logs' => $logs,
        ]);
    }

    public function edit(Student $student)
    {
        $classes = SchoolClass::orderBy('name')->get();

        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $student->update($request->validated());

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil dihapus.');
    }

    public function generateAccessCode(Student $student)
    {
        $plainCode = Str::upper(Str::random(8));
        $student->update(['access_code' => $plainCode]);

        return redirect()->route('admin.students.show', $student)->with('success', 'Kode akses berhasil di-generate.')->with('access_code', $plainCode);
    }

    public function bulkMigrate(BulkMigrateStudentsRequest $request)
    {
        $targetClass = SchoolClass::findOrFail($request->target_class_id);
        $academicYear = AcademicYear::findOrFail($request->academic_year_id);
        $count = $this->migrationService->migrateStudents($request->student_ids, $targetClass, $academicYear);

        return redirect()->route('admin.students.index')->with('success', "{$count} siswa berhasil dipindahkan ke kelas {$targetClass->name}.");
    }
}
