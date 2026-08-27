<?php
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('admin can view academic years', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->get('/admin/academic-years')->assertStatus(200);
});

test('admin can create academic year', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->post('/admin/academic-years', [
        'year_label' => '2030/2031',
        'semester' => 1,
        'is_active' => '1',
    ])->assertRedirect('/admin/academic-years')->assertSessionHas('success');
    
    $this->assertDatabaseHas('academic_years', ['year_label' => '2030/2031']);
});

test('academic year creation validation', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->post('/admin/academic-years', [])
        ->assertSessionHasErrors(['year_label', 'semester']);
});
