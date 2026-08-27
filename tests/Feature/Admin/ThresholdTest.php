<?php
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('admin can view thresholds', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->get('/admin/rule-thresholds')->assertStatus(200);
});

test('admin can create threshold', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->post('/admin/rule-thresholds', [
        'min_points' => 300,
        'action' => 'call_parent',
    ])->assertRedirect('/admin/rule-thresholds')->assertSessionHas('success');
    
    $this->assertDatabaseHas('rule_thresholds', ['min_points' => 300]);
});
