<?php
use App\Models\User;
use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('super admin can view schools', function () {
    $superadmin = User::where('role', 'super_admin')->first();
    $this->actingAs($superadmin)->get('/super-admin/schools')->assertStatus(200);
});

test('super admin can create school', function () {
    $superadmin = User::where('role', 'super_admin')->first();
    $this->actingAs($superadmin)->post('/super-admin/schools', [
        'name' => 'SMP Test',
        'slug' => 'smp-test',
        'subscription_status' => 'trial',
    ])->assertRedirect('/super-admin/schools')->assertSessionHas('success');
    
    $this->assertDatabaseHas('schools', ['slug' => 'smp-test']);
});

test('school creation fails validation on missing fields', function () {
    $superadmin = User::where('role', 'super_admin')->first();
    $this->actingAs($superadmin)->post('/super-admin/schools', [
        'name' => '', // required
    ])->assertSessionHasErrors(['name', 'slug']);
});
