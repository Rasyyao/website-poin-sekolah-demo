<?php
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
        'name' => 'Test Rule',
        'type' => 'violation',
        'category' => 'ringan',
        'points' => '-10',
        'is_active' => '1',
    ])->assertRedirect('/admin/rules')->assertSessionHas('success');
    
    $this->assertDatabaseHas('rules', ['name' => 'Test Rule']);
});
