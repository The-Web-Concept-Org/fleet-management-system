<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function logoutDriver(): User
{
    return User::create([
        'name' => 'Logout Driver',
        'email' => 'logout-' . uniqid() . '@example.com',
        'password' => Hash::make('password123'),
        'role' => 'driver',
        'status' => 'active',
    ]);
}

it('requires authentication to log out', function (): void {
    $this->postJson('/api/auth/logout')->assertUnauthorized();
});

it('logs out and revokes the access and refresh tokens', function (): void {
    $user = logoutDriver();

    $access = $user->createToken('access-' . $user->id);
    $user->createToken('refresh-' . $user->id);

    $this->withHeader('Authorization', 'Bearer ' . $access->plainTextToken)
        ->postJson('/api/auth/logout')
        ->assertOk()
        ->assertJsonPath('message', 'Logged out successfully.');

    expect($user->tokens()->count())->toBe(0);

    // Drop the guard's cached user so the next call re-reads the token.
    $this->app['auth']->forgetGuards();

    // The revoked access token can no longer be used.
    $this->withHeader('Authorization', 'Bearer ' . $access->plainTextToken)
        ->getJson('/api/auth/me')
        ->assertUnauthorized();
});

it('leaves other users tokens untouched', function (): void {
    $driver = logoutDriver();
    $other = logoutDriver();

    $access = $driver->createToken('access-' . $driver->id);
    $driver->createToken('refresh-' . $driver->id);

    $otherAccess = $other->createToken('access-' . $other->id);
    $other->createToken('refresh-' . $other->id);

    $this->withHeader('Authorization', 'Bearer ' . $access->plainTextToken)
        ->postJson('/api/auth/logout')
        ->assertOk();

    expect($driver->tokens()->count())->toBe(0)
        ->and($other->tokens()->count())->toBe(2);

    $this->withHeader('Authorization', 'Bearer ' . $otherAccess->plainTextToken)
        ->getJson('/api/auth/me')
        ->assertOk();
});
