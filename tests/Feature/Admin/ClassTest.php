<?php
use App\Models\User;
use App\Models\AcademicYear;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('admin can view classes', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->get('/admin/classes')->assertStatus(200);
});

test('admin can create class', function () {
    $admin = User::where('role', 'admin')->first();
    $academicYear = AcademicYear::first();
    $this->actingAs($admin)->post('/admin/classes', [
        'name' => 'Class X',
        'academic_year_id' => $academicYear->id,
        'homeroom_teacher_id' => $admin->id,
    ])->assertRedirect('/admin/classes')->assertSessionHas('success');
    
    $this->assertDatabaseHas('classes', ['name' => 'Class X']);
});
