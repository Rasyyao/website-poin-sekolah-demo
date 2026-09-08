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

test('admin can view dashboard with student distribution chart', function () {
    $admin = User::where('role', 'admin')->first();

    $response = $this->actingAs($admin)->get('/admin/reports/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Persebaran Siswa');
    $response->assertSee('classDistributionChart');
    $response->assertSee('Total Siswa');
    $response->assertViewHas('classDistributionData', function ($data) {
        return isset($data['labels'], $data['data'], $data['total_students'])
            && count($data['labels']) === 3
            && $data['total_students'] === 30;
    });
});

