<?php

use App\Models\User;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('admin can export dashboard to excel without fatal error', function () {
    $admin = User::where('role', 'admin')->first();

    $response = $this->actingAs($admin)->get('/admin/reports/dashboard/export?filter=last_30_days');

    $response->assertStatus(200);
    $response->assertHeader('content-disposition');
});

test('admin can export student excel without fatal error', function () {
    $admin = User::where('role', 'admin')->first();
    $student = Student::where('school_id', $admin->school_id)->first();

    $response = $this->actingAs($admin)->get('/admin/reports/student/' . $student->id . '/export/excel');

    $response->assertStatus(200);
    $response->assertHeader('content-disposition');
});
