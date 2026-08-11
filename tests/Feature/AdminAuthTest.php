<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the admin login page', function () {
    $this->get(route('admin.login'))
        ->assertSuccessful()
        ->assertSee('Admin Login');
});

it('redirects guests from the admin dashboard to login', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('admin.login'));
});

it('allows an admin to log in with phone and password', function () {
    $admin = User::factory()->admin()->create([
        'phone' => '0911111111',
        'password' => 'secret123',
    ]);

    $this->post(route('admin.login.store'), [
        'phone' => $admin->phone,
        'password' => 'secret123',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($admin);
});

it('rejects non-admin users from logging into admin', function () {
    $user = User::factory()->create([
        'phone' => '0922222222',
        'password' => 'secret123',
        'is_admin' => false,
    ]);

    $this->post(route('admin.login.store'), [
        'phone' => $user->phone,
        'password' => 'secret123',
    ])->assertRedirect()
        ->assertSessionHasErrors('phone');

    $this->assertGuest();
});

it('blocks non-admin users from the dashboard', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});
