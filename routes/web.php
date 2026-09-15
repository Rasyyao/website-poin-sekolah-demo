<?php

use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\AppealController as AdminAppealController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\PointsLogController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\RuleController;
use App\Http\Controllers\Admin\RuleThresholdController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\SchoolSettingController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StudentImportController;
use App\Http\Controllers\Auth\DemoLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ParentAccessController;
use App\Http\Controllers\ParentAccess\DashboardController as ParentDashboardController;
use App\Http\Controllers\ParentAccess\ReportController as ParentReportController;
use App\Http\Controllers\Student\AppealController as StudentAppealController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\RuleListController;
use App\Http\Controllers\Teacher\PointsController;
use App\Http\Controllers\Teacher\StudentMonitorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// ── Authentication ──────────────────────────────────────────────────────

Route::prefix('auth')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Parent access (via student NISN + access code)
    Route::get('parent/login', [ParentAccessController::class, 'showLoginForm'])->name('parent.login.form');
    Route::post('parent/login', [ParentAccessController::class, 'login'])->middleware('throttle:5,1')->name('parent.login');
    Route::post('parent/logout', [ParentAccessController::class, 'logout'])->name('parent.logout');

    // Demo One-Click Login
    Route::get('demo-login/{role}', [DemoLoginController::class, 'login'])->name('demo.login');
});

// ── Super Admin Routes ──────────────────────────────────────────────────

Route::prefix('super-admin')
    ->middleware(['auth', 'role:super_admin'])
    ->name('super-admin.')
    ->group(function () {
        Route::resource('schools', SchoolController::class);
    });

// ── Admin Sekolah & Kesiswaan & Guru Shared Routes ──────────────────────

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'school.active'])
    ->group(function () {
        // Settings & Staff Management (Super Admin & Admin Sekolah ONLY - Kesiswaan & Guru prohibited)
        Route::middleware(['role:super_admin,admin'])->group(function () {
            Route::put('school/update', [SchoolSettingController::class, 'update'])->name('school.update');
            Route::put('school/signatories', [SchoolSettingController::class, 'updateSignatories'])->name('school.signatories.update');
            Route::resource('staff', StaffController::class)->except(['show', 'create']);
            Route::resource('academic-years', AcademicYearController::class);
            Route::post('academic-years/{academicYear}/activate', [AcademicYearController::class, 'activate'])->name('academic-years.activate');
            Route::resource('rule-thresholds', RuleThresholdController::class)->except(['show']);
            Route::get('exports/staff/{format}', [ExportController::class, 'staff'])->name('exports.staff');
            Route::get('exports/thresholds/{format}', [ExportController::class, 'thresholds'])->name('exports.thresholds');

            // Manage Rules (Super Admin & Admin Sekolah ONLY)
            Route::resource('rules', RuleController::class)->except(['index', 'show']);
            Route::post('rules/{rule}/toggle-active', [RuleController::class, 'toggleActive'])->name('rules.toggle-active');
        });

        // Master Data & Operasional Poin (Super Admin, Admin Sekolah, & Kesiswaan)
        Route::middleware(['role:super_admin,admin,kesiswaan'])->group(function () {
            Route::resource('classes', ClassController::class);
            Route::get('students/import/template', [StudentImportController::class, 'template'])->name('students.import.template');
            Route::post('students/import/parse', [StudentImportController::class, 'parse'])->name('students.import.parse');
            Route::post('students/import/process', [StudentImportController::class, 'process'])->name('students.import.process');
            Route::get('students/migration', [StudentController::class, 'migration'])->name('students.migration');
            Route::post('students/bulk-migrate', [StudentController::class, 'bulkMigrate'])->name('students.bulk-migrate');
            Route::resource('students', StudentController::class);
            Route::post('students/{student}/generate-access-code', [StudentController::class, 'generateAccessCode'])->name('students.generate-access-code');

            // View Rules
            Route::resource('rules', RuleController::class)->only(['index', 'show']);

            // Operasional Poin
            Route::get('points-log', [PointsLogController::class, 'index'])->name('points-log.index');
            Route::get('points-log/{pointsLog}', [PointsLogController::class, 'show'])->name('points-log.show');
            Route::put('points-log/{pointsLog}', [PointsLogController::class, 'update'])->name('points-log.update');
            Route::delete('points-log/{pointsLog}', [PointsLogController::class, 'destroy'])->name('points-log.destroy');
            Route::post('points-log/{pointsLog}/approve', [PointsLogController::class, 'approve'])->name('points-log.approve');
            Route::post('points-log/{pointsLog}/reject', [PointsLogController::class, 'reject'])->name('points-log.reject');

            Route::get('exports/classes/{format}', [ExportController::class, 'classes'])->name('exports.classes');
            Route::get('exports/students/{format}', [ExportController::class, 'students'])->name('exports.students');
        });

        // Appeals (Banding) & Rekap Laporan - Accessible to Admin, Kesiswaan, and Guru (scoped to reporter in Controller)
        Route::middleware(['role:super_admin,admin,kesiswaan,teacher,homeroom,counselor'])->group(function () {
            Route::get('appeals', [AdminAppealController::class, 'index'])->name('appeals.index');
            Route::get('appeals/{appeal}', [AdminAppealController::class, 'show'])->name('appeals.show');
            Route::post('appeals/{appeal}/accept', [AdminAppealController::class, 'accept'])->name('appeals.accept');
            Route::post('appeals/{appeal}/reject', [AdminAppealController::class, 'reject'])->name('appeals.reject');

            // Reports & Dashboard & Rekap
            Route::get('reports/dashboard', [AdminReportController::class, 'dashboard'])->name('reports.dashboard');
            Route::get('reports/dashboard/export', [AdminReportController::class, 'exportDashboard'])->name('reports.dashboard.export');
            Route::get('reports/ranking', [AdminReportController::class, 'ranking'])->name('reports.ranking');
            Route::get('reports/student/{student}', [AdminReportController::class, 'studentReport'])->name('reports.student');
            Route::get('reports/student/{student}/export/pdf', [AdminReportController::class, 'exportStudentPdf'])->name('reports.student.export.pdf');
            Route::get('reports/student/{student}/export/excel', [AdminReportController::class, 'exportStudentExcel'])->name('reports.student.export.excel');
            Route::get('reports/class/{classId}', [AdminReportController::class, 'classStats'])->name('reports.class');
            Route::get('reports/class/{classId}/export/pdf', [AdminReportController::class, 'exportClassPdf'])->name('reports.class.export.pdf');
            Route::get('reports/class/{classId}/export/excel', [AdminReportController::class, 'exportClassExcel'])->name('reports.class.export.excel');

            // Sertifikat Penghargaan (auto-issued from achievement thresholds)
            Route::get('certificates', [CertificateController::class, 'index'])->name('certificates.index');
            Route::get('certificates/{certificate}/print', [CertificateController::class, 'print'])->name('certificates.print');
        });

        // Delete a wrongly-issued certificate (Super Admin & Admin Sekolah ONLY)
        Route::middleware(['role:super_admin,admin'])->group(function () {
            Route::delete('certificates/{certificate}', [CertificateController::class, 'destroy'])->name('certificates.destroy');
        });
    });

// ── Guru / Wali Kelas / BK / Kesiswaan Routes ──────────────────────────

Route::prefix('teacher')
    ->middleware(['auth', 'role:admin,kesiswaan,teacher,homeroom,counselor', 'school.active'])
    ->name('teacher.')
    ->group(function () {
        Route::middleware('can:input points')->group(function () {
            Route::get('points', [PointsController::class, 'index'])->name('points.index');
            Route::post('points', [PointsController::class, 'store'])->name('points.store');
            Route::put('points/{pointsLog}', [PointsController::class, 'update'])->name('points.update');
            Route::delete('points/{pointsLog}', [PointsController::class, 'destroy'])->name('points.destroy');
        });
        Route::get('my-students', [StudentMonitorController::class, 'myStudents'])->name('my-students');
        Route::get('students/{student}/history', [StudentMonitorController::class, 'studentHistory'])->name('students.history');
        Route::get('class/{class}/summary', [StudentMonitorController::class, 'classSummary'])->name('class.summary');
    });

// ── Siswa Routes ────────────────────────────────────────────────────────

Route::prefix('student')
    ->name('student.')
    ->middleware('parent.auth')
    ->group(function () {
        Route::get('dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('export-pdf', [StudentDashboardController::class, 'exportPdf'])->name('export.pdf');
        Route::get('certificates/{certificate}/print', [StudentDashboardController::class, 'printCertificate'])->name('certificates.print');
        Route::get('rules', [RuleListController::class, 'index'])->name('rules');
        Route::get('appeals', [StudentAppealController::class, 'index'])->name('appeals.index');
        Route::post('appeals', [StudentAppealController::class, 'store'])->name('appeals.store');
    });

Route::prefix('parent')
    ->name('parent.')
    ->middleware('parent.auth')
    ->group(function () {
        Route::get('dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');
        Route::get('export-pdf', [ParentDashboardController::class, 'exportPdf'])->name('export.pdf');
        Route::get('certificates/{certificate}/print', [ParentDashboardController::class, 'printCertificate'])->name('certificates.print');
        Route::get('report', [ParentReportController::class, 'behaviorReport'])->name('report');
    });
