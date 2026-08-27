<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\Student;
use App\Services\ReportService;
use App\Exports\ClassesExport;
use App\Exports\StaffExport;
use App\Exports\StudentsExport;
use App\Exports\ThresholdsExport;

class ExportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function classes(Request $request, $format)
    {
        $schoolId = $request->user()->school_id;
        $date = now()->format('Y-md');
        
        if ($format === 'excel') {
            return Excel::download(new ClassesExport($schoolId), "Daftar_Kelas_{$date}.xlsx");
        } elseif ($format === 'pdf') {
            $classes = SchoolClass::where('school_id', $schoolId)
                ->with(['homeroomTeacher', 'students'])
                ->orderBy('name')
                ->get();
            $pdf = Pdf::loadView('exports.classes', compact('classes'));
            return $pdf->download("Daftar_Kelas_{$date}.pdf");
        }
        
        abort(404);
    }

    public function staff(Request $request, $format)
    {
        $schoolId = $request->user()->school_id;
        $date = now()->format('Y-md');
        
        if ($format === 'excel') {
            return Excel::download(new StaffExport($schoolId), "Daftar_Staf_{$date}.xlsx");
        } elseif ($format === 'pdf') {
            $staffs = User::where('school_id', $schoolId)
                ->orderBy('name')
                ->get();
            $pdf = Pdf::loadView('exports.staff', compact('staffs'));
            return $pdf->download("Daftar_Staf_{$date}.pdf");
        }
        
        abort(404);
    }

    public function students(Request $request, $format)
    {
        $schoolId = $request->user()->school_id;
        $date = now()->format('Y-md');
        
        if ($format === 'excel') {
            return Excel::download(new StudentsExport($schoolId), "Daftar_Siswa_{$date}.xlsx");
        } elseif ($format === 'pdf') {
            $students = Student::withoutGlobalScopes()
                ->with(['currentClass'])
                ->where('school_id', $schoolId)
                ->orderBy('name')
                ->get();
            $pdf = Pdf::loadView('exports.students', compact('students'));
            return $pdf->download("Daftar_Siswa_{$date}.pdf");
        }
        
        abort(404);
    }

    public function thresholds(Request $request, $format)
    {
        $schoolId = $request->user()->school_id;
        $date = now()->format('Y-md');
        
        if ($format === 'excel') {
            return Excel::download(new ThresholdsExport($schoolId), "Siswa_Melampaui_Batas_{$date}.xlsx");
        } elseif ($format === 'pdf') {
            $groupedStudents = $this->reportService->studentsByThreshold($schoolId);
            $pdf = Pdf::loadView('exports.thresholds', compact('groupedStudents'));
            return $pdf->download("Siswa_Melampaui_Batas_{$date}.pdf");
        }
        
        abort(404);
    }
}
