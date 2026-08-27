<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth;
use App\Http\Controllers\ParentAccess;
use App\Http\Controllers\Student;
use App\Http\Controllers\Teacher;
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
    Route::get('login', [Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [Auth\LoginController::class, 'login'])->name('login.submit');
    Route::post('logout', [Auth\LoginController::class, 'logout'])->name('logout');

    // Parent access (via student NISN + access code)
    Route::get('parent/login', [Auth\ParentAccessController::class, 'showLoginForm'])->name('parent.login.form');
    Route::post('parent/login', [Auth\ParentAccessController::class, 'login'])->name('parent.login');
    Route::post('parent/logout', [Auth\ParentAccessController::class, 'logout'])->name('parent.logout');

    // Demo One-Click Login
    Route::get('demo-login/{role}', [Auth\DemoLoginController::class, 'login'])->name('demo.login');
});

// ── Super Admin Routes ──────────────────────────────────────────────────

Route::prefix('super-admin')
    ->middleware(['auth', 'role:super_admin'])
    ->name('super-admin.')
    ->group(function () {
        Route::resource('schools', Admin\SchoolController::class);
    });

// ── Admin Sekolah Routes ────────────────────────────────────────────────

Route::prefix('admin')
    ->middleware(['auth', 'role:super_admin,admin', 'school.active'])
    ->name('admin.')
    ->group(function () {
        // School Settings
        Route::put('school/update', [Admin\SchoolSettingController::class, 'update'])->name('school.update');

        // Staff Management
        Route::resource('staff', Admin\StaffController::class)->except(['show', 'create']);

        // Master Data
        Route::resource('academic-years', Admin\AcademicYearController::class);
        Route::post('academic-years/{academicYear}/activate', [Admin\AcademicYearController::class, 'activate'])->name('academic-years.activate');

        Route::resource('classes', Admin\ClassController::class);

        Route::resource('students', Admin\StudentController::class);
        Route::post('students/{student}/generate-access-code', [Admin\StudentController::class, 'generateAccessCode'])->name('students.generate-access-code');
        Route::post('students/bulk-migrate', [Admin\StudentController::class, 'bulkMigrate'])->name('students.bulk-migrate');

        Route::resource('rules', Admin\RuleController::class);
        Route::post('rules/{rule}/toggle-active', [Admin\RuleController::class, 'toggleActive'])->name('rules.toggle-active');

        Route::resource('rule-thresholds', Admin\RuleThresholdController::class)->except(['show']);

        // Operasional Poin
        Route::get('points-log', [Admin\PointsLogController::class, 'index'])->name('points-log.index');
        Route::get('points-log/{pointsLog}', [Admin\PointsLogController::class, 'show'])->name('points-log.show');
        Route::post('points-log/{pointsLog}/approve', [Admin\PointsLogController::class, 'approve'])->name('points-log.approve');
        Route::post('points-log/{pointsLog}/reject', [Admin\PointsLogController::class, 'reject'])->name('points-log.reject');

        // Appeals
        Route::get('appeals', [Admin\AppealController::class, 'index'])->name('appeals.index');
        Route::get('appeals/{appeal}', [Admin\AppealController::class, 'show'])->name('appeals.show');
        Route::post('appeals/{appeal}/accept', [Admin\AppealController::class, 'accept'])->name('appeals.accept');
        Route::post('appeals/{appeal}/reject', [Admin\AppealController::class, 'reject'])->name('appeals.reject');

        // Reports & Dashboard
        Route::get('reports/dashboard', [Admin\ReportController::class, 'dashboard'])->name('reports.dashboard');
        Route::get('reports/ranking', [Admin\ReportController::class, 'ranking'])->name('reports.ranking');
        Route::get('reports/student/{student}', [Admin\ReportController::class, 'studentReport'])->name('reports.student');
        Route::get('reports/student/{student}/export/pdf', [Admin\ReportController::class, 'exportStudentPdf'])->name('reports.student.export.pdf');
        Route::get('reports/student/{student}/export/excel', [Admin\ReportController::class, 'exportStudentExcel'])->name('reports.student.export.excel');
        Route::get('reports/class/{classId}', [Admin\ReportController::class, 'classStats'])->name('reports.class');
        Route::get('reports/class/{classId}/export/pdf', [Admin\ReportController::class, 'exportClassPdf'])->name('reports.class.export.pdf');
        Route::get('reports/class/{classId}/export/excel', [Admin\ReportController::class, 'exportClassExcel'])->name('reports.class.export.excel');
        // Export Routes
        Route::get('exports/classes/{format}', [Admin\ExportController::class, 'classes'])->name('exports.classes');
        Route::get('exports/staff/{format}', [Admin\ExportController::class, 'staff'])->name('exports.staff');
        Route::get('exports/students/{format}', [Admin\ExportController::class, 'students'])->name('exports.students');
        Route::get('exports/thresholds/{format}', [Admin\ExportController::class, 'thresholds'])->name('exports.thresholds');
    });

// ── Guru / Wali Kelas / BK Routes ──────────────────────────────────────

Route::prefix('teacher')
    ->middleware(['auth', 'role:admin,teacher,homeroom,counselor', 'school.active'])
    ->name('teacher.')
    ->group(function () {
        Route::middleware('can:input points')->group(function () {
            Route::get('points', [Teacher\PointsController::class, 'index'])->name('points.index');
            Route::post('points', [Teacher\PointsController::class, 'store'])->name('points.store');
        });
        Route::get('my-students', [Teacher\StudentMonitorController::class, 'myStudents'])->name('my-students');
        Route::get('students/{student}/history', [Teacher\StudentMonitorController::class, 'studentHistory'])->name('students.history');
        Route::get('class/{class}/summary', [Teacher\StudentMonitorController::class, 'classSummary'])->name('class.summary');
    });

// ── Siswa Routes ────────────────────────────────────────────────────────

Route::prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('dashboard', [Student\DashboardController::class, 'index'])->name('dashboard');
        Route::get('export-pdf', [Student\DashboardController::class, 'exportPdf'])->name('export.pdf');
        Route::get('rules', [Student\RuleListController::class, 'index'])->name('rules');
        Route::get('appeals', [Student\AppealController::class, 'index'])->name('appeals.index');
        Route::post('appeals', [Student\AppealController::class, 'store'])->name('appeals.store');
    });

// ── Orang Tua Routes ───────────────────────────────────────────────────

Route::prefix('parent')
    ->name('parent.')
    ->group(function () {
        Route::get('dashboard', [ParentAccess\DashboardController::class, 'index'])->name('dashboard');
        Route::get('export-pdf', [ParentAccess\DashboardController::class, 'exportPdf'])->name('export.pdf');
        Route::get('report', [ParentAccess\ReportController::class, 'behaviorReport'])->name('report');
    });
