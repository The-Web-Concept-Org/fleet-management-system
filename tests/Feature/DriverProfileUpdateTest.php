<?php

use App\Models\Driver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function makeProfileDriver(): Driver
{
    $user = User::create([
        'name' => 'Profile Driver',
        'email' => 'profile-' . uniqid() . '@example.com',
        'password' => Hash::make('password123'),
        'role' => 'driver',
        'status' => 'active',
    ]);

    return Driver::create([
        'user_id' => $user->id,
        'badge_id' => 'BDG-' . uniqid(),
        'duty_status' => 'available',
    ]);
}

it('updates the authenticated driver profile', function (): void {
    $driver = makeProfileDriver();
    Sanctum::actingAs($driver->user);

    $this->patchJson('/api/profile', [
        'name' => 'Updated Name',
        'phone' => '+92 300 1234567',
        'license_number' => 'LIC-9988',
        'license_type' => 'Class A CDL',
        'license_expiry' => '2030-01-31',
        'duty_status' => 'on_route',
        'experience_years' => 7,
        'notes' => 'Prefers night shifts.',
    ])
        ->assertOk()
        ->assertJsonPath('message', 'Profile updated successfully.')
        ->assertJsonPath('user.name', 'Updated Name')
        ->assertJsonPath('user.driver.phone', '+92 300 1234567')
        ->assertJsonPath('user.driver.license_number', 'LIC-9988')
        ->assertJsonPath('user.driver.duty_status', 'on_route')
        ->assertJsonPath('user.driver.experience_years', 7);

    expect($driver->user->fresh()->name)->toBe('Updated Name');
    expect($driver->fresh()->duty_status)->toBe('on_route');
});

it('does not allow the driver to change their email', function (): void {
    $driver = makeProfileDriver();
    $originalEmail = $driver->user->email;
    Sanctum::actingAs($driver->user);

    $this->patchJson('/api/profile', [
        'name' => 'Still The Same',
        'email' => 'hacked@example.com',
    ])->assertOk();

    expect($driver->user->fresh()->email)->toBe($originalEmail);
});

it('requires the current password to change the password', function (): void {
    $driver = makeProfileDriver();
    Sanctum::actingAs($driver->user);

    $this->patchJson('/api/profile', [
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('current_password');

    $this->patchJson('/api/profile', [
        'current_password' => 'password123',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ])->assertOk();

    expect(Hash::check('new-password-123', $driver->user->fresh()->password))->toBeTrue();
});

it('rejects profile updates from non-driver accounts', function (): void {
    $admin = User::create([
        'name' => 'Super Admin',
        'email' => 'admin-' . uniqid() . '@example.com',
        'password' => Hash::make('password123'),
        'role' => 'super_admin',
        'status' => 'active',
    ]);

    Sanctum::actingAs($admin);

    $this->patchJson('/api/profile', ['name' => 'Nope'])->assertStatus(403);
});
