<?php
use App\Models\User;
use App\Models\Student;
use App\Models\Rule;
use App\Models\PointsLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('teacher can view points page', function () {
    $teacher = User::where('role', 'teacher')->first();

    $this->actingAs($teacher)->get('/teacher/points')
        ->assertOk()
        ->assertSee('Input Poin Siswa');
});

test('teacher can input points', function () {
    $teacher = User::where('role', 'teacher')->first();
    $student = Student::first();
    $rule = Rule::first();
    
    $this->actingAs($teacher)->post('/teacher/points', [
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'occurred_at' => now()->format('Y-m-d\TH:i:s'),
        'note' => 'Test test',
    ])->assertRedirect('/teacher/points')->assertSessionHas('success');
    
    $this->assertDatabaseHas('points_log', [
        'student_id' => $student->id,
        'rule_id' => $rule->id,
    ]);
});

test('points input fails validation with future date', function () {
    $teacher = User::where('role', 'teacher')->first();
    $student = Student::first();
    $rule = Rule::first();
    
    $this->actingAs($teacher)->post('/teacher/points', [
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'occurred_at' => now()->addDays(2)->format('Y-m-d\TH:i:s'),
    ])->assertSessionHasErrors('occurred_at');
});

test('double input while submitting is prevented', function () {
    $teacher = User::where('role', 'teacher')->first();
    $student = Student::first();
    $rule = Rule::first();
    $now = now()->format('Y-m-d\TH:i:s');
    
    // First submit succeeds
    $this->actingAs($teacher)->post('/teacher/points', [
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'occurred_at' => $now,
        'note' => 'First submit',
    ])->assertRedirect('/teacher/points')->assertSessionHas('success');

    // Immediate second submit with same data is caught and prevented
    $this->actingAs($teacher)->post('/teacher/points', [
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'occurred_at' => $now,
        'note' => 'Double click submit',
    ])->assertRedirect('/teacher/points')->assertSessionHas('warning');

    // Exactly one log was created
    $count = PointsLog::where('student_id', $student->id)
        ->where('rule_id', $rule->id)
        ->where('reported_by', $teacher->id)
        ->count();

    expect($count)->toBe(1);
});

test('teacher can edit points log they reported', function () {
    $teacher = User::where('role', 'teacher')->first();
    $student = Student::first();
    $rule1 = Rule::first();
    $rule2 = Rule::skip(1)->first();

    $log = PointsLog::create([
        'school_id' => $student->school_id,
        'student_id' => $student->id,
        'rule_id' => $rule1->id,
        'reported_by' => $teacher->id,
        'points' => $rule1->points,
        'occurred_at' => now(),
        'note' => 'Original note',
    ]);

    $this->actingAs($teacher)->put("/teacher/points/{$log->id}", [
        'rule_id' => $rule2->id,
        'note' => 'Updated note',
        'occurred_at' => now()->subDay()->format('Y-m-d\TH:i:s'),
    ])->assertRedirect('/teacher/points')->assertSessionHas('success');

    $log->refresh();
    expect($log->rule_id)->toBe($rule2->id);
    expect($log->points)->toBe($rule2->points);
    expect($log->note)->toBe('Updated note');
});

test('teacher can delete points log on false click', function () {
    $teacher = User::where('role', 'teacher')->first();
    $student = Student::first();
    $rule = Rule::first();

    $log = PointsLog::create([
        'school_id' => $student->school_id,
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'reported_by' => $teacher->id,
        'points' => $rule->points,
        'occurred_at' => now(),
    ]);

    $this->actingAs($teacher)->delete("/teacher/points/{$log->id}")
        ->assertRedirect('/teacher/points')
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('points_log', ['id' => $log->id]);
});

test('teacher cannot delete points log reported by another teacher', function () {
    $teacher1 = User::where('role', 'teacher')->first();
    $teacher2 = User::where('role', 'homeroom')->first();
    $student = Student::first();
    $rule = Rule::first();

    $log = PointsLog::create([
        'school_id' => $student->school_id,
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'reported_by' => $teacher1->id,
        'points' => $rule->points,
        'occurred_at' => now(),
    ]);

    $this->actingAs($teacher2)->delete("/teacher/points/{$log->id}")
        ->assertStatus(403);

    $this->assertDatabaseHas('points_log', ['id' => $log->id]);
});

test('teacher points page categorizes rules into violation and achievement', function () {
    $teacher = User::where('role', 'teacher')->first();

    $response = $this->actingAs($teacher)->get('/teacher/points');

    $response->assertOk()
        ->assertSee('PELANGGARAN')
        ->assertSee('PRESTASI')
        ->assertSee('Semua')
        ->assertSee('Pelanggaran')
        ->assertSee('Prestasi');
});

test('teacher can filter points log history by type', function () {
    $teacher = User::where('role', 'teacher')->first();
    $student = Student::first();
    $violationRule = Rule::where('type', \App\Enums\RuleType::Violation)->first();
    $achievementRule = Rule::where('type', \App\Enums\RuleType::Achievement)->first();

    $logViolation = PointsLog::create([
        'school_id' => $student->school_id,
        'student_id' => $student->id,
        'rule_id' => $violationRule->id,
        'reported_by' => $teacher->id,
        'points' => $violationRule->points,
        'occurred_at' => now(),
        'note' => 'Catatan Pelanggaran Unik 123',
    ]);

    $logAchievement = PointsLog::create([
        'school_id' => $student->school_id,
        'student_id' => $student->id,
        'rule_id' => $achievementRule->id,
        'reported_by' => $teacher->id,
        'points' => $achievementRule->points,
        'occurred_at' => now(),
        'note' => 'Catatan Prestasi Unik 456',
    ]);

    // Filter by violation
    $this->actingAs($teacher)->get('/teacher/points?type=violation')
        ->assertOk()
        ->assertSee('Catatan Pelanggaran Unik 123')
        ->assertDontSee('Catatan Prestasi Unik 456');

    // Filter by achievement
    $this->actingAs($teacher)->get('/teacher/points?type=achievement')
        ->assertOk()
        ->assertSee('Catatan Prestasi Unik 456')
        ->assertDontSee('Catatan Pelanggaran Unik 123');
});

test('rules are ordered by weight: ringan, sedang, berat', function () {
    $teacher = User::where('role', 'teacher')->first();

    $response = $this->actingAs($teacher)->get('/teacher/points');
    $rules = $response->viewData('rules');

    $violationCategories = $rules->where('type', \App\Enums\RuleType::Violation)
        ->pluck('category')
        ->map(fn($c) => $c?->value)
        ->values()
        ->toArray();

    $expectedOrder = ['ringan', 'sedang', 'berat'];
    $lastRank = 0;
    foreach ($violationCategories as $cat) {
        $rank = array_search($cat, $expectedOrder);
        expect($rank)->toBeGreaterThanOrEqual($lastRank);
        $lastRank = $rank;
    }
});

test('teacher can upload photo evidence when recording points', function () {
    \Illuminate\Support\Facades\Storage::fake('public');
    $teacher = User::where('role', 'teacher')->first();
    $student = Student::first();
    $rule = Rule::first();
    
    $file = \Illuminate\Http\UploadedFile::fake()->image('bukti.jpg', 600, 600);

    $response = $this->actingAs($teacher)->post('/teacher/points', [
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'occurred_at' => now()->format('Y-m-d\TH:i:s'),
        'note' => 'Upload bukti test',
        'evidence' => $file,
    ]);

    $response->assertRedirect('/teacher/points')->assertSessionHas('success');

    $log = PointsLog::where('student_id', $student->id)->where('note', 'Upload bukti test')->first();
    expect($log)->not->toBeNull();
    expect($log->evidence_url)->toStartWith('/storage/evidence/');
    
    $path = str_replace('/storage/', '', $log->evidence_url);
    \Illuminate\Support\Facades\Storage::disk('public')->assertExists($path);
});

test('teacher can replace and remove photo evidence when updating', function () {
    \Illuminate\Support\Facades\Storage::fake('public');
    $teacher = User::where('role', 'teacher')->first();
    $student = Student::first();
    $rule = Rule::first();

    $file1 = \Illuminate\Http\UploadedFile::fake()->image('bukti1.jpg');
    $this->actingAs($teacher)->post('/teacher/points', [
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'occurred_at' => now()->format('Y-m-d\TH:i:s'),
        'note' => 'Initial upload',
        'evidence' => $file1,
    ]);

    $log = PointsLog::where('note', 'Initial upload')->first();
    $oldPath = str_replace('/storage/', '', $log->evidence_url);
    \Illuminate\Support\Facades\Storage::disk('public')->assertExists($oldPath);

    // Replace with new image
    $file2 = \Illuminate\Http\UploadedFile::fake()->image('bukti2.jpg');
    $this->actingAs($teacher)->put('/teacher/points/' . $log->id, [
        'rule_id' => $rule->id,
        'note' => 'Updated with replacement image',
        'evidence' => $file2,
    ])->assertRedirect('/teacher/points')->assertSessionHas('success');

    $log->refresh();
    expect($log->note)->toBe('Updated with replacement image');
    expect($log->evidence_url)->toStartWith('/storage/evidence/');
    $newPath = str_replace('/storage/', '', $log->evidence_url);
    expect($newPath)->not->toBe($oldPath);
    \Illuminate\Support\Facades\Storage::disk('public')->assertMissing($oldPath);
    \Illuminate\Support\Facades\Storage::disk('public')->assertExists($newPath);

    // Remove image
    $this->actingAs($teacher)->put('/teacher/points/' . $log->id, [
        'rule_id' => $rule->id,
        'note' => 'Updated without image',
        'remove_evidence' => '1',
    ])->assertRedirect('/teacher/points')->assertSessionHas('success');

    $log->refresh();
    expect($log->evidence_url)->toBeNull();
    \Illuminate\Support\Facades\Storage::disk('public')->assertMissing($newPath);
});

test('evidence upload validates file type and size', function () {
    $teacher = User::where('role', 'teacher')->first();
    $student = Student::first();
    $rule = Rule::first();

    // Invalid non-image file
    $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
    $this->actingAs($teacher)->post('/teacher/points', [
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'evidence' => $file,
    ])->assertSessionHasErrors('evidence');

    // Too large file (> 5MB)
    $largeFile = \Illuminate\Http\UploadedFile::fake()->create('large.jpg', 6000, 'image/jpeg');
    $this->actingAs($teacher)->post('/teacher/points', [
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'evidence' => $largeFile,
    ])->assertSessionHasErrors('evidence');
});


