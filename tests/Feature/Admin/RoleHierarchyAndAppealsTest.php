<?php

use App\Enums\AppealStatus;
use App\Enums\UserRole;
use App\Models\Appeal;
use App\Models\PointsLog;
use App\Models\Rule;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->seed());

test('kesiswaan can access operational pages but cannot access settings or staff management', function () {
    $kesiswaan = User::where('email', 'kesiswaan@smpn1demo.sch.id')->firstOrFail();

    // Allowed for Kesiswaan
    $this->actingAs($kesiswaan)->get(route('admin.reports.dashboard'))->assertStatus(200);
    $this->actingAs($kesiswaan)->get(route('admin.reports.ranking'))->assertStatus(200);
    $this->actingAs($kesiswaan)->get(route('admin.students.index'))->assertStatus(200);
    $this->actingAs($kesiswaan)->get(route('admin.classes.index'))->assertStatus(200);
    $this->actingAs($kesiswaan)->get(route('admin.rules.index'))->assertStatus(200);
    $this->actingAs($kesiswaan)->get(route('admin.points-log.index'))->assertStatus(200);
    $this->actingAs($kesiswaan)->get(route('admin.appeals.index'))->assertStatus(200);
    $this->actingAs($kesiswaan)->get(route('teacher.points.index'))->assertStatus(200);

    // PROHIBITED for Kesiswaan (Settings & Staff)
    $this->actingAs($kesiswaan)->get(route('admin.staff.index'))->assertStatus(403);
    $this->actingAs($kesiswaan)->get(route('admin.academic-years.index'))->assertStatus(403);
    $this->actingAs($kesiswaan)->get(route('admin.rule-thresholds.index'))->assertStatus(403);
    $this->actingAs($kesiswaan)->put(route('admin.school.update'), ['name' => 'New Name'])->assertStatus(403);
});

test('admin can access everything including settings and staff management', function () {
    $admin = User::where('email', 'admin@smpn1demo.sch.id')->firstOrFail();

    $this->actingAs($admin)->get(route('admin.reports.dashboard'))->assertStatus(200);
    $this->actingAs($admin)->get(route('admin.staff.index'))->assertStatus(200);
    $this->actingAs($admin)->get(route('admin.academic-years.index'))->assertStatus(200);
    $this->actingAs($admin)->get(route('admin.rule-thresholds.index'))->assertStatus(200);
    $this->actingAs($admin)->get(route('admin.appeals.index'))->assertStatus(200);
});

test('guru cannot access settings or staff but can access rekap and input points', function () {
    $guru = User::where('email', 'siti@smpn1demo.sch.id')->firstOrFail();

    $this->actingAs($guru)->get(route('teacher.points.index'))->assertStatus(200);
    $this->actingAs($guru)->get(route('teacher.my-students'))->assertStatus(200);
    $this->actingAs($guru)->get(route('admin.reports.dashboard'))->assertStatus(200);

    // Prohibited
    $this->actingAs($guru)->get(route('admin.staff.index'))->assertStatus(403);
    $this->actingAs($guru)->get(route('admin.academic-years.index'))->assertStatus(403);
    $this->actingAs($guru)->get(route('admin.rule-thresholds.index'))->assertStatus(403);
});

test('appeal is only visible and resolvable by the reporting teacher/kesiswaan and admin', function () {
    $school = School::firstOrFail();
    $student = Student::firstOrFail();
    $rule = Rule::firstOrFail();

    $kesiswaanA = User::where('email', 'kesiswaan@smpn1demo.sch.id')->firstOrFail();
    $guruB = User::where('email', 'siti@smpn1demo.sch.id')->firstOrFail();
    $admin = User::where('email', 'admin@smpn1demo.sch.id')->firstOrFail();

    // Violation reported by Kesiswaan A
    $logA = PointsLog::create([
        'school_id' => $school->id,
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'reported_by' => $kesiswaanA->id,
        'points' => 15,
        'occurred_at' => now(),
    ]);

    // Student appeals violation A
    $appealA = Appeal::create([
        'points_log_id' => $logA->id,
        'submitter_type' => Student::class,
        'submitter_id' => $student->id,
        'reason' => 'Banding atas poin yang diberikan Kesiswaan A',
        'status' => AppealStatus::Pending,
        'evidence_url' => '/storage/evidence/test.jpg',
    ]);

    // 1. Kesiswaan A CAN see Appeal A
    $responseKesiswaan = $this->actingAs($kesiswaanA)->get(route('admin.appeals.index'));
    $responseKesiswaan->assertStatus(200);
    $responseKesiswaan->assertSee('Banding atas poin yang diberikan Kesiswaan A');

    // 2. Guru B CANNOT see Appeal A (it was reported by Kesiswaan A, not Guru B)
    $responseGuru = $this->actingAs($guruB)->get(route('admin.appeals.index'));
    $responseGuru->assertStatus(200);
    $responseGuru->assertDontSee('Banding atas poin yang diberikan Kesiswaan A');

    // 3. Admin CAN see Appeal A
    $responseAdmin = $this->actingAs($admin)->get(route('admin.appeals.index'));
    $responseAdmin->assertStatus(200);
    $responseAdmin->assertSee('Banding atas poin yang diberikan Kesiswaan A');

    // 4. Guru B cannot accept or reject Appeal A (403 Forbidden)
    $this->actingAs($guruB)->post(route('admin.appeals.accept', $appealA))->assertStatus(403);
    $this->actingAs($guruB)->post(route('admin.appeals.reject', $appealA))->assertStatus(403);

    // 5. Kesiswaan A can accept Appeal A
    $this->actingAs($kesiswaanA)->post(route('admin.appeals.accept', $appealA), [
        'resolution_note' => 'Diterima oleh pelapor kesiswaan.',
    ])->assertRedirect(route('admin.appeals.index'));

    expect($appealA->fresh()->status)->toBe(AppealStatus::Accepted);
});
