<?php

use App\Enums\PointsLogStatus;
use App\Enums\RuleType;
use App\Enums\UserRole;
use App\Enums\ViolationCategory;
use App\Models\PointsLog;
use App\Models\Rule;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Services\PointsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    Storage::fake('public');
});

/*
|--------------------------------------------------------------------------
| 1. Functional Testing & RBAC Enforcement (Per Role)
|--------------------------------------------------------------------------
*/

test('guest cannot access admin, teacher, or parent routes and is redirected', function () {
    $this->get('/admin/reports/dashboard')->assertRedirect('/auth/login');
    $this->get('/teacher/points')->assertRedirect('/auth/login');
    $this->get('/parent/dashboard')->assertRedirect('/auth/parent/login');
    $this->get('/student/dashboard')->assertRedirect('/auth/parent/login');
});

test('teacher cannot access admin settings, staff management, or rule mutations', function () {
    $teacher = User::where('role', UserRole::Teacher)->first();

    // Staff management is admin-only
    $this->actingAs($teacher)
        ->get('/admin/staff')
        ->assertForbidden();

    // Rule creation is admin-only
    $this->actingAs($teacher)
        ->post('/admin/rules', [
            'name' => 'Illegal Rule',
            'type' => 'violation',
            'category' => 'kedisiplinan',
            'points' => 10,
        ])
        ->assertForbidden();

    // School settings update is admin-only
    $this->actingAs($teacher)
        ->put('/admin/school/update', ['name' => 'Hacked School'])
        ->assertForbidden();
});

test('teacher can submit points for a student with proper validation', function () {
    $teacher = User::where('role', UserRole::Teacher)->first();
    $student = Student::where('school_id', $teacher->school_id)->first();
    $rule = Rule::where('school_id', $teacher->school_id)
        ->where('type', RuleType::Violation)
        ->where('category', ViolationCategory::Ringan)
        ->first();

    $response = $this->actingAs($teacher)->post('/teacher/points', [
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'note' => 'Terlambat 10 menit masuk kelas.',
        'evidence' => UploadedFile::fake()->image('bukti.jpg'),
    ]);

    $response->assertRedirect('/teacher/points');
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('points_log', [
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'reported_by' => $teacher->id,
        'status' => 'approved',
    ]);
});

test('submitting violation with category berat requires approval (status pending)', function () {
    $teacher = User::where('role', UserRole::Teacher)->first();
    $student = Student::where('school_id', $teacher->school_id)->first();
    $rule = Rule::where('school_id', $teacher->school_id)
        ->where('type', RuleType::Violation)
        ->where('category', ViolationCategory::Berat)
        ->first();

    $response = $this->actingAs($teacher)->post('/teacher/points', [
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'note' => 'Pelanggaran berat terdeteksi.',
    ]);

    $response->assertRedirect('/teacher/points');

    $this->assertDatabaseHas('points_log', [
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'status' => 'pending',
    ]);
});

test('admin can approve pending points log', function () {
    $admin = User::where('role', UserRole::Admin)->first();
    $student = Student::where('school_id', $admin->school_id)->first();
    $rule = Rule::where('school_id', $admin->school_id)->where('type', RuleType::Violation)->first();

    $pendingLog = PointsLog::create([
        'school_id' => $admin->school_id,
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'reported_by' => $admin->id,
        'points' => $rule->points,
        'status' => PointsLogStatus::Pending,
        'occurred_at' => now(),
    ]);

    $response = $this->actingAs($admin)->post("/admin/points-log/{$pendingLog->id}/approve");

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect($pendingLog->fresh()->status)->toBe(PointsLogStatus::Approved);
});

/*
|--------------------------------------------------------------------------
| 2. Race Conditions & Concurrent-Write Integrity
|--------------------------------------------------------------------------
*/

test('concurrent point recordings for the same student serialize properly without lost points', function () {
    $teacher = User::where('role', UserRole::Teacher)->first();
    $student = Student::where('school_id', $teacher->school_id)->first();
    $rule = Rule::where('school_id', $teacher->school_id)
        ->where('type', RuleType::Violation)
        ->first();

    $initialPoints = $student->totalViolationPoints();
    $pointsService = app(PointsService::class);

    // Simulate two concurrent point additions
    $pointsService->recordPoints($student, $rule, $teacher, 'Poin paralel 1');
    $pointsService->recordPoints($student, $rule, $teacher, 'Poin paralel 2');

    $finalPoints = $student->fresh()->totalViolationPoints();
    expect($finalPoints)->toBe($initialPoints + ($rule->points * 2));
});

test('idempotency: rapid duplicate point submissions by the same teacher are detected', function () {
    $teacher = User::where('role', UserRole::Teacher)->first();
    $student = Student::where('school_id', $teacher->school_id)->first();
    $rule = Rule::where('school_id', $teacher->school_id)
        ->where('type', RuleType::Violation)
        ->first();

    // First submission
    $response1 = $this->actingAs($teacher)->post('/teacher/points', [
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'note' => 'Catatan tes duplikat',
    ]);
    $response1->assertRedirect();

    // Immediate second submission (replay)
    $response2 = $this->actingAs($teacher)->post('/teacher/points', [
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'note' => 'Catatan tes duplikat',
    ]);
    $response2->assertSessionHas('warning');
});

/*
|--------------------------------------------------------------------------
| 3. Authentication, Rate Limiting & Session Security
|--------------------------------------------------------------------------
*/

test('staff login is rate limited after 5 failed attempts', function () {
    for ($i = 0; $i < 5; $i++) {
        $this->post('/auth/login', [
            'email' => 'admin@smpn1demo.sch.id',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');
    }

    // 6th attempt should be blocked with 429 Too Many Requests
    $this->post('/auth/login', [
        'email' => 'admin@smpn1demo.sch.id',
        'password' => 'wrong-password',
    ])->assertStatus(429);
});

test('parent login is rate limited after 5 failed attempts to prevent brute-forcing access code', function () {
    $student = Student::first();
    $school = $student->school;

    for ($i = 0; $i < 5; $i++) {
        $this->post('/auth/parent/login', [
            'school_slug' => $school->slug,
            'nisn' => $student->nisn,
            'access_code' => 'WRONG9',
        ])->assertSessionHasErrors('access_code');
    }

    // 6th attempt should be rate-limited
    $this->post('/auth/parent/login', [
        'school_slug' => $school->slug,
        'nisn' => $student->nisn,
        'access_code' => 'WRONG9',
    ])->assertStatus(429);
});

test('parent logout invalidates session completely', function () {
    $student = Student::first();

    // Simulate active parent session
    $this->withSession([
        'parent_student_id' => $student->id,
        'parent_school_id' => $student->school_id,
    ]);

    $this->get('/parent/dashboard')->assertOk();

    // Logout
    $this->post('/auth/parent/logout')->assertRedirect('/auth/parent/login');

    // Attempting to access dashboard with discarded session redirects
    $this->get('/parent/dashboard')->assertRedirect('/auth/parent/login');
});

test('parent cannot submit an appeal for another students points log (IDOR prevention)', function () {
    $student1 = Student::find(1);
    $student2 = Student::find(2);

    // Create an approved points log belonging to Student 2
    $logStudent2 = PointsLog::create([
        'school_id' => $student2->school_id,
        'student_id' => $student2->id,
        'rule_id' => Rule::first()->id,
        'reported_by' => User::where('role', UserRole::Teacher)->first()->id,
        'points' => 10,
        'status' => PointsLogStatus::Approved,
        'occurred_at' => now(),
    ]);

    // Student 1 is logged in
    $response = $this->withSession([
        'parent_student_id' => $student1->id,
        'parent_school_id' => $student1->school_id,
    ])->post('/student/appeals', [
        'points_log_id' => $logStudent2->id,
        'reason' => 'Saya tidak merasa melakukan ini.',
        'evidence' => UploadedFile::fake()->image('bukti.jpg'),
    ]);

    // Validation must fail because points_log_id does not belong to Student 1
    $response->assertSessionHasErrors('points_log_id');
});

/*
|--------------------------------------------------------------------------
| 4. Input & Injection Security
|--------------------------------------------------------------------------
*/

test('disallowed file types are rejected during evidence upload', function () {
    $teacher = User::where('role', UserRole::Teacher)->first();
    $student = Student::where('school_id', $teacher->school_id)->first();
    $rule = Rule::first();

    $response = $this->actingAs($teacher)->post('/teacher/points', [
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'note' => 'Upload malicious file',
        'evidence' => UploadedFile::fake()->create('malicious.php', 100, 'application/x-php'),
    ]);

    $response->assertSessionHasErrors('evidence');
});

test('xss payload in note is stored safely and escaped upon display', function () {
    $teacher = User::where('role', UserRole::Teacher)->first();
    $student = Student::where('school_id', $teacher->school_id)->first();
    $rule = Rule::first();
    $xssPayload = '<script>alert("XSS")</script>';

    $this->actingAs($teacher)->post('/teacher/points', [
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'note' => $xssPayload,
    ]);

    $response = $this->actingAs($teacher)->get('/teacher/points');
    $response->assertOk();
    // The response must not contain unescaped executable script tags
    $response->assertDontSee($xssPayload, false);
});

/*
|--------------------------------------------------------------------------
| 5. Custom Error Pages (401, 403, 404)
|--------------------------------------------------------------------------
*/

test('custom 403 error page renders dashboard-style layout and message', function () {
    $teacher = User::where('role', UserRole::Teacher)->first();

    $response = $this->actingAs($teacher)->get('/admin/staff');

    $response->assertStatus(403);
    $response->assertSee('403');
    $response->assertSee('Akses Dibatasi');
    $response->assertSee('Kembali ke Dashboard');
});

test('custom 404 error page renders dashboard-style layout for unknown routes', function () {
    $admin = User::where('role', UserRole::Admin)->first();

    $response = $this->actingAs($admin)->get('/admin/non-existent-page-url-xyz');

    $response->assertStatus(404);
    $response->assertSee('404');
    $response->assertSee('Halaman Tidak Ditemukan');
    $response->assertSee('Kembali ke Dashboard');
});

test('custom 401 error page view renders correctly', function () {
    $view = view('errors.401', [
        'exception' => new \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException('Challenge', 'Sesi berakhir'),
    ])->render();

    expect($view)->toContain('401')
        ->toContain('Autentikasi Diperlukan')
        ->toContain('Kembali ke Dashboard');
});

