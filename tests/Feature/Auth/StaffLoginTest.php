<?php
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('staff login page is accessible', function () {
    $this->get('/auth/login')->assertStatus(200);
});

test('staff can login with correct credentials', function () {
    $user = User::where('email', 'admin@smpn1demo.sch.id')->first();
    $this->post('/auth/login', [
        'email' => 'admin@smpn1demo.sch.id',
        'password' => 'password',
    ])->assertRedirect(route('admin.reports.dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('staff login fails with wrong password', function () {
    $this->post('/auth/login', [
        'email' => 'admin@smpn1demo.sch.id',
        'password' => 'wrong',
    ])->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('staff can logout', function () {
    $user = User::where('email', 'admin@smpn1demo.sch.id')->first();
    $this->actingAs($user)->post('/auth/logout')->assertRedirect('/auth/login');
    $this->assertGuest();
});
