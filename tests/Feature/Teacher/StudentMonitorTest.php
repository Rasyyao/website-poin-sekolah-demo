<?php
use App\Models\User;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('teacher can view my students page', function () {
    $teacher = User::where('role', 'teacher')->first();
    $this->actingAs($teacher)->get('/teacher/my-students')->assertStatus(200);
});

test('teacher can view student history', function () {
    $teacher = User::where('role', 'teacher')->first();
    $student = Student::first();
    $this->actingAs($teacher)->get("/teacher/students/{$student->id}/history")->assertStatus(200);
});
