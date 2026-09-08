<?php

use App\Models\User;
use App\Models\Student;
use App\Models\Rule;
use App\Models\PointsLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('admin can view points log list with rules', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->get('/admin/points-log')->assertStatus(200);
});

test('admin can edit points log', function () {
    $admin = User::where('role', 'admin')->first();
    $student = Student::first();
    $rule1 = Rule::first();
    $rule2 = Rule::skip(1)->first();

    $log = PointsLog::create([
        'school_id' => $student->school_id,
        'student_id' => $student->id,
        'rule_id' => $rule1->id,
        'reported_by' => $admin->id,
        'points' => $rule1->points,
        'occurred_at' => now(),
        'note' => 'Old note',
    ]);

    $this->actingAs($admin)->put("/admin/points-log/{$log->id}", [
        'rule_id' => $rule2->id,
        'note' => 'Admin updated note',
        'occurred_at' => now()->format('Y-m-d\TH:i:s'),
    ])->assertRedirect('/admin/points-log')->assertSessionHas('success');

    $log->refresh();
    expect($log->rule_id)->toBe($rule2->id);
    expect($log->points)->toBe($rule2->points);
    expect($log->note)->toBe('Admin updated note');
});

test('admin can delete points log', function () {
    $admin = User::where('role', 'admin')->first();
    $student = Student::first();
    $rule = Rule::first();

    $log = PointsLog::create([
        'school_id' => $student->school_id,
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'reported_by' => $admin->id,
        'points' => $rule->points,
        'occurred_at' => now(),
    ]);

    $this->actingAs($admin)->delete("/admin/points-log/{$log->id}")
        ->assertRedirect('/admin/points-log')
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('points_log', ['id' => $log->id]);
});
