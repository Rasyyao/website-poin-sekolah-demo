<?php
use App\Models\User;
use App\Models\Student;
use App\Models\Rule;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

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
