<?php
use App\Models\User;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('admin can view students', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->get('/admin/students')->assertStatus(200);
});

test('admin can create student', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->post('/admin/students', [
        'nisn' => '999999',
        'name' => 'New Student',
        'parent_contact' => '0812345678',
    ])->assertRedirect('/admin/students')->assertSessionHas('success');
    
    $this->assertDatabaseHas('students', ['nisn' => '999999']);
});

test('admin can generate student access code', function () {
    $admin = User::where('role', 'admin')->first();
    $student = Student::first();
    $this->actingAs($admin)->post("/admin/students/{$student->id}/generate-access-code")
        ->assertSessionHas('access_code');
});
