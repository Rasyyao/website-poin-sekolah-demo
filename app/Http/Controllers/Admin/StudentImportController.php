<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Exports\StudentTemplateExport;
use App\Imports\StudentsPreviewImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;

class StudentImportController extends Controller
{
    public function template()
    {
        return Excel::download(new StudentTemplateExport, 'Template_Import_Siswa.xlsx');
    }

    public function parse(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $data = Excel::toArray(new StudentsPreviewImport, $request->file('file'));
            
            if (empty($data) || empty($data[0])) {
                return response()->json(['error' => 'File kosong atau format tidak sesuai.'], 400);
            }

            $rows = $data[0];
            $schoolId = $request->user()->school_id;
            
            // Get all existing classes for mapping
            $classes = SchoolClass::where('school_id', $schoolId)->get();
            $classMap = $classes->mapWithKeys(fn($c) => [strtolower(trim($c->name)) => $c->id])->toArray();
            
            $previewData = [];
            $stats = [
                'total' => 0,
                'valid' => 0,
                'invalid' => 0,
                'class_counts' => []
            ];

            foreach ($rows as $index => $row) {
                // Skip empty rows
                if (empty(array_filter($row))) continue;
                
                $stats['total']++;

                // Map row keys safely
                $nisn = $row['nisn'] ?? null;
                $name = $row['nama_lengkap'] ?? null;
                $className = $row['kelas'] ?? null;
                
                // Parse Date
                $birthDateRaw = $row['tanggal_lahir_yyyy_mm_dd'] ?? null;
                $birthDate = null;
                if ($birthDateRaw) {
                    try {
                        if (is_numeric($birthDateRaw)) {
                            $birthDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($birthDateRaw)->format('Y-m-d');
                        } else {
                            $birthDate = Carbon::parse($birthDateRaw)->format('Y-m-d');
                        }
                    } catch (\Exception $e) {
                        // Invalid date, ignore or handle
                    }
                }

                $parentContact = $row['kontak_orang_tua'] ?? null;

                // Validate
                $errors = [];
                if (!$nisn) $errors[] = 'NISN kosong';
                if (!$name) $errors[] = 'Nama kosong';
                
                // Check if NISN exists
                $exists = Student::where('nisn', $nisn)->exists();
                if ($exists) {
                    $errors[] = 'NISN sudah terdaftar';
                }

                // Map Class
                $classId = null;
                $matchedClassName = '-';
                if ($className) {
                    $searchClass = strtolower(trim($className));
                    if (isset($classMap[$searchClass])) {
                        $classId = $classMap[$searchClass];
                        $matchedClassName = $classes->firstWhere('id', $classId)->name;
                        
                        if (empty($errors)) {
                            if (!isset($stats['class_counts'][$matchedClassName])) {
                                $stats['class_counts'][$matchedClassName] = 0;
                            }
                            $stats['class_counts'][$matchedClassName]++;
                        }
                    } else {
                        $errors[] = "Kelas '$className' tidak ditemukan";
                    }
                }

                if (empty($errors)) {
                    $stats['valid']++;
                } else {
                    $stats['invalid']++;
                }

                $previewData[] = [
                    'id' => Str::uuid()->toString(), // Temporary ID for frontend
                    'nisn' => $nisn,
                    'name' => $name,
                    'class_id' => $classId,
                    'class_name_raw' => $className,
                    'matched_class_name' => $matchedClassName,
                    'birth_date' => $birthDate,
                    'parent_contact' => $parentContact,
                    'errors' => $errors,
                    'is_valid' => empty($errors),
                    'import' => empty($errors) // Default to true if valid
                ];
            }

            return response()->json([
                'rows' => $previewData,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memproses file: ' . $e->getMessage()], 500);
        }
    }

    public function process(Request $request)
    {
        $request->validate([
            'students' => 'required|array',
            'students.*.nisn' => 'required',
            'students.*.name' => 'required|string',
            'students.*.class_id' => 'nullable|exists:classes,id',
            'students.*.import' => 'required|boolean'
        ]);

        $studentsToImport = collect($request->students)->filter(fn($s) => $s['import'] == true);
        
        if ($studentsToImport->isEmpty()) {
            return response()->json(['error' => 'Tidak ada siswa yang dipilih untuk diimport.'], 400);
        }

        $schoolId = $request->user()->school_id;
        $inserted = 0;

        foreach ($studentsToImport as $data) {
            // Check again to avoid duplicates
            if (Student::where('nisn', $data['nisn'])->exists()) {
                continue;
            }

            Student::create([
                'school_id' => $schoolId,
                'nisn' => $data['nisn'],
                'name' => $data['name'],
                'class_id' => $data['class_id'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'parent_contact' => $data['parent_contact'] ?? null,
            ]);

            $inserted++;
        }

        session()->flash('success', "Berhasil mengimport {$inserted} siswa.");

        return response()->json(['success' => true, 'redirect' => route('admin.students.index')]);
    }
}
