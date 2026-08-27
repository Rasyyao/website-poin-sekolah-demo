<?php
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('parent login page is accessible', function () {
    $this->get('/auth/parent/login')->assertStatus(200);
});

test('parent can login with correct credentials', function () {
    $student = Student::first();
    $student->update(['access_code' => 'DEMO1234']); // override for test
    $this->post('/auth/parent/login', [
        'school_slug' => $student->school->slug,
        'nisn' => $student->nisn,
        'access_code' => 'DEMO1234',
    ])->assertRedirect(route('parent.dashboard'))
      ->assertSessionHas('parent_student_id', $student->id);
});

test('parent login fails with wrong access code', function () {
    $student = Student::first();
    $this->post('/auth/parent/login', [
        'school_slug' => $student->school->slug,
        'nisn' => $student->nisn,
        'access_code' => 'WRONG',
    ])->assertSessionHasErrors('access_code');
});
