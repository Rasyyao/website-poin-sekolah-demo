<?php

use App\Models\Rule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('admin can view rules', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->get('/admin/rules')->assertStatus(200);
});

test('admin can create a violation rule', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->post('/admin/rules', [
        'name' => 'Test Violation Rule',
        'type' => 'violation',
        'category' => 'ringan',
        'points' => '10',
        'is_active' => '1',
    ])->assertRedirect('/admin/rules')->assertSessionHas('success');
    
    $this->assertDatabaseHas('rules', [
        'name' => 'Test Violation Rule',
        'type' => 'violation',
        'category' => 'ringan',
    ]);
});

test('admin can create an achievement rule with category', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->post('/admin/rules', [
        'name' => 'Juara 1 Lomba Coding',
        'type' => 'achievement',
        'category' => 'akademik',
        'points' => '50',
        'is_active' => '1',
    ])->assertRedirect('/admin/rules')->assertSessionHas('success');
    
    $this->assertDatabaseHas('rules', [
        'name' => 'Juara 1 Lomba Coding',
        'type' => 'achievement',
        'category' => 'akademik',
    ]);
});

test('admin can create a rule with a custom category', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->post('/admin/rules', [
        'name' => 'Tahfidz Juz 30',
        'type' => 'achievement',
        'category' => '__custom__',
        'custom_category' => 'Keagamaan & Tahfidz',
        'points' => '40',
        'is_active' => '1',
    ])->assertRedirect('/admin/rules')->assertSessionHas('success');
    
    $this->assertDatabaseHas('rules', [
        'name' => 'Tahfidz Juz 30',
        'type' => 'achievement',
        'category' => 'Keagamaan & Tahfidz',
    ]);
});

test('admin can update a rule and its category', function () {
    $admin = User::where('role', 'admin')->first();
    $rule = Rule::first();

    $this->actingAs($admin)->put("/admin/rules/{$rule->id}", [
        'name' => 'Updated Rule Name',
        'type' => 'violation',
        'category' => 'sedang',
        'points' => '25',
    ])->assertRedirect('/admin/rules')->assertSessionHas('success');

    $this->assertDatabaseHas('rules', [
        'id' => $rule->id,
        'name' => 'Updated Rule Name',
        'category' => 'sedang',
        'points' => 25,
    ]);
});

test('kesiswaan cannot create or modify rules', function () {
    $kesiswaan = User::where('role', 'kesiswaan')->first();
    
    // Kesiswaan can view
    $this->actingAs($kesiswaan)->get('/admin/rules')->assertStatus(200);

    // Kesiswaan cannot create
    $this->actingAs($kesiswaan)->post('/admin/rules', [
        'name' => 'Rule by Kesiswaan',
        'type' => 'violation',
        'category' => 'ringan',
        'points' => '10',
    ])->assertStatus(403);
});

