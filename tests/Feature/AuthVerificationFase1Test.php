<?php

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;

test('newly registered user has unverified email', function (): void {
    $response = $this->post(route('register.store'), [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $user = User::where('email', 'newuser@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->hasVerifiedEmail())->toBeFalse();
});

test('unverified user cannot access dashboard and is redirected to verification notice', function (): void {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));
    $response->assertRedirect(route('verification.notice'));
});

test('verified user can access dashboard', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));
    $response->assertOk();
});

test('email verification notice can be rendered for unverified user', function (): void {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get(route('verification.notice'));
    $response->assertOk();
});

test('verified user is redirected from verification notice to dashboard', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('verification.notice'));
    $response->assertRedirect(route('dashboard'));
});

test('user model implements MustVerifyEmail', function (): void {
    $user = new User;
    expect($user instanceof MustVerifyEmail)->toBeTrue();
});

test('user model has HasRoles trait', function (): void {
    $user = new User;
    expect(method_exists($user, 'assignRole'))->toBeTrue();
    expect(method_exists($user, 'hasRole'))->toBeTrue();
});
