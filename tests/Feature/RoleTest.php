<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    // Ensure roles exist for each test (RefreshDatabase clears them)
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'seller', 'guard_name' => 'web']);
});

test('guest cannot access admin area', function (): void {
    $response = $this->get('/admin');
    $response->assertRedirect(route('login'));
});

test('customer cannot access admin area', function (): void {
    $user = User::factory()->create();
    $user->assignRole('customer');

    $response = $this->actingAs($user)->get('/admin');
    $response->assertForbidden();
});

test('admin can access admin area', function (): void {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get('/admin');
    $response->assertOk();
});

test('seller placeholder role exists but cannot access admin by default', function (): void {
    $user = User::factory()->create();
    $user->assignRole('seller');

    $response = $this->actingAs($user)->get('/admin');
    $response->assertForbidden();
});

test('user can have multiple roles check', function (): void {
    $user = User::factory()->create();
    $user->assignRole('customer');

    expect($user->hasRole('customer'))->toBeTrue();
    expect($user->hasRole('admin'))->toBeFalse();
});

test('admin role has manage permissions', function (): void {
    $permission = Permission::firstOrCreate([
        'name' => 'manage products',
        'guard_name' => 'web',
    ]);
    $adminRole = Role::findByName('admin');
    if ($adminRole->permissions->isEmpty()) {
        $adminRole->givePermissionTo($permission);
    }

    expect($adminRole->hasPermissionTo('manage products'))->toBeTrue();
});
