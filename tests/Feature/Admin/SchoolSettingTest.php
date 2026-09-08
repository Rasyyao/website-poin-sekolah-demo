<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('admin can update school name and website name', function () {
    $admin = User::where('role', 'admin')->first();

    $this->actingAs($admin)->put('/admin/school/update', [
        'name' => 'SMK Bina Informatika',
        'website_name' => 'Sistem Poin Prestasi',
    ])->assertRedirect()->assertSessionHas('success');

    $admin->school->refresh();
    expect($admin->school->name)->toBe('SMK Bina Informatika');
    expect($admin->school->settings['website_name'])->toBe('Sistem Poin Prestasi');
    expect(website_name())->toBe('Sistem Poin Prestasi');
});

test('website name reflects in view', function () {
    $admin = User::where('role', 'admin')->first();

    $admin->school->update([
        'settings' => ['website_name' => 'Portal Disiplin Siswa']
    ]);

    $this->actingAs($admin)
        ->get('/admin/academic-years')
        ->assertStatus(200)
        ->assertSee('Portal Disiplin Siswa');
});
