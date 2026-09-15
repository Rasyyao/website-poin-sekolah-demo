<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\SchoolClass;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $certificates = Certificate::with(['student.currentClass', 'ruleThreshold'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->whereHas('student', function ($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->search.'%')
                        ->orWhere('nisn', 'like', '%'.$request->search.'%');
                });
            })
            ->when($request->filled('class_id'), function ($query) use ($request) {
                $query->whereHas('student', function ($q) use ($request) {
                    $q->where('class_id', $request->class_id);
                });
            })
            ->orderByDesc('issued_at')
            ->paginate(15)
            ->withQueryString();

        $classes = SchoolClass::orderBy('name')->get();

        return view('admin.certificates.index', compact('certificates', 'classes'));
    }

    public function print(Certificate $certificate)
    {
        $certificate->load(['student.currentClass', 'ruleThreshold', 'school']);

        $pdf = Pdf::loadView('exports.pdf.certificate', [
            'certificate' => $certificate,
        ]);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download($certificate->downloadFilename());
    }

    public function destroy(Certificate $certificate)
    {
        $certificate->delete();

        return redirect()->route('admin.certificates.index')->with('success', 'Sertifikat berhasil dihapus.');
    }
}
