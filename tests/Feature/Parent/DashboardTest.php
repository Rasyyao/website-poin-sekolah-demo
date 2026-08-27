<?php
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('parent can view dashboard after login', function () {
    $student = Student::first();
    $this->withSession([
        'parent_student_id' => $student->id,
        'parent_school_id' => $student->school_id,
    ])->get('/parent/dashboard')->assertStatus(200);
});

test('guest parent cannot view dashboard', function () {
    $this->get('/parent/dashboard')->assertRedirect('/auth/parent/login');
});
