<?php

use App\Models\Appeal;
use App\Models\PointsLog;
use App\Models\Rule;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('student dashboard renders properly with mobile menu logout and evidence upload modal', function () {
    $student = Student::first();
    $response = $this->withSession([
        'parent_student_id' => $student->id,
        'parent_school_id' => $student->school_id,
    ])->get('/student/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Keluar / Logout');
    $response->assertSee('hidden md:block'); // Header logout is hidden on mobile
    $response->assertSee('Foto Bukti Pendukung');
    $response->assertSee('appeal_evidence_input');
    $response->assertSee('Ambil dari Kamera atau Pilih dari Galeri');
});

test('student can submit appeal with evidence image', function () {
    Storage::fake('public');

    $student = Student::first();
    $teacher = User::factory()->create([
        'school_id' => $student->school_id,
        'role' => 'teacher',
    ]);
    $rule = Rule::first();

    $log = PointsLog::create([
        'school_id' => $student->school_id,
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'reported_by' => $teacher->id,
        'points' => 10,
        'occurred_at' => now(),
    ]);

    $file = UploadedFile::fake()->image('bukti_sakit.jpg', 600, 600);

    $response = $this->withSession([
        'parent_student_id' => $student->id,
        'parent_school_id' => $student->school_id,
    ])->post('/student/appeals', [
        'points_log_id' => $log->id,
        'reason' => 'Saya sedang izin sakit pada saat itu dan memiliki surat dokter yang sah.',
        'evidence' => $file,
    ]);

    $response->assertRedirect('/student/appeals');
    $response->assertSessionHas('success', 'Banding berhasil diajukan.');

    $appeal = Appeal::where('points_log_id', $log->id)->first();
    expect($appeal)->not->toBeNull();
    expect($appeal->evidence_url)->not->toBeNull();
    expect($appeal->evidence_url)->toContain('/storage/evidence/');
    expect($appeal->reason)->toBe('Saya sedang izin sakit pada saat itu dan memiliki surat dokter yang sah.');

    // Assert file was stored on disk
    $storedPath = str_replace('/storage/', '', $appeal->evidence_url);
    Storage::disk('public')->assertExists($storedPath);
});

test('student cannot submit appeal without evidence image', function () {
    Storage::fake('public');

    $student = Student::first();
    $teacher = User::factory()->create([
        'school_id' => $student->school_id,
        'role' => 'teacher',
    ]);
    $rule = Rule::first();

    $log = PointsLog::create([
        'school_id' => $student->school_id,
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'reported_by' => $teacher->id,
        'points' => 10,
        'occurred_at' => now(),
    ]);

    $response = $this->withSession([
        'parent_student_id' => $student->id,
        'parent_school_id' => $student->school_id,
    ])->post('/student/appeals', [
        'points_log_id' => $log->id,
        'reason' => 'Saya sedang izin sakit pada saat itu.',
    ]);

    $response->assertSessionHasErrors('evidence');
    expect(Appeal::where('points_log_id', $log->id)->exists())->toBeFalse();
});
