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


    public function migration(Request $request)
    {
        $classes = SchoolClass::orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('year_label')->get();

        $sourceClass = $request->filled('class_id') ? $classes->firstWhere('id', (int) $request->class_id) : null;

        $targetClasses = $classes
            ->reject(fn (SchoolClass $c) => $sourceClass && $c->id === $sourceClass->id)
            ->filter(function (SchoolClass $c) use ($sourceClass) {
                if (! $sourceClass) {
                    return true;
                }

                $sourceGrade = $sourceClass->gradeLevel();
                $targetGrade = $c->gradeLevel();

                return $sourceGrade === null || $targetGrade === null || $targetGrade >= $sourceGrade;
            })
            ->values();

        $students = Student::query()
            ->with('currentClass:id,name')
            ->when($sourceClass, fn ($q) => $q->where('class_id', $sourceClass->id))
            ->when($request->search, fn ($q, $s) => $q->where(fn ($q2) => $q2->where('name', 'like', "%{$s}%")->orWhere('nisn', 'like', "%{$s}%")))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.students.migration', compact('students', 'classes', 'academicYears', 'sourceClass', 'targetClasses'));
    }

    public function bulkMigrate(BulkMigrateStudentsRequest $request)
    {
        $targetClass = SchoolClass::findOrFail($request->target_class_id);
        $academicYear = AcademicYear::findOrFail($request->academic_year_id);
        $targetGrade = $targetClass->gradeLevel();

        if ($targetGrade !== null) {
            $students = Student::with('currentClass:id,name')->whereIn('id', $request->student_ids)->get();
            $downgraded = $students->first(function (Student $student) use ($targetGrade) {
                $currentGrade = $student->currentClass?->gradeLevel();

                return $currentGrade !== null && $targetGrade < $currentGrade;
            });

            if ($downgraded) {
                return back()->withErrors([
                    'target_class_id' => "Tidak bisa memindahkan siswa dari kelas {$downgraded->currentClass->name} turun ke kelas {$targetClass->name}.",
                ])->withInput();
            }
        }

        $count = $this->migrationService->migrateStudents($request->student_ids, $targetClass, $academicYear);

        return redirect()->route('admin.students.migration')->with('success', "{$count} siswa berhasil dipindahkan ke kelas {$targetClass->name}.");
    }
}
