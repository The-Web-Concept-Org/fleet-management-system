<?php

use App\Models\Bus;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function createActiveDriver(): Driver
{
    $user = User::create([
        'name' => 'Test Driver',
        'email' => 'driver-' . uniqid() . '@example.com',
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

it('allows a driver with an assigned vehicle to log in', function (): void {
    $driver = createActiveDriver();

    Bus::create([
        'bus_number' => 'BUS-001',
        'plate' => 'ABC-123',
        'driver_id' => $driver->id,
    ]);

    $this->postJson('/api/login', [
        'email' => $driver->user->email,
        'password' => 'password123',
    ])
        ->assertOk()
        ->assertJsonStructure([
            'user' => [
                'driver' => ['id', 'badge_id', 'duty_status'],
                'bus' => ['id', 'bus_number', 'plate', 'status', 'route'],
            ],
            'accessToken',
            'refreshToken',
        ])
        ->assertJsonMissingPath('user.driver.bus')
        ->assertJsonMissingPath('user.driver.assigned_bus')
        ->assertJsonPath('user.bus.bus_number', 'BUS-001')
        ->assertJsonPath('user.bus.plate', 'ABC-123');
});

it('blocks a driver who has no vehicle assigned', function (): void {
    $driver = createActiveDriver();

    $this->postJson('/api/login', [
        'email' => $driver->user->email,
        'password' => 'password123',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('email')
        ->assertJsonPath('errors.email.0', 'No vehicle is assigned to your account yet. Please contact dispatch to assign a vehicle before signing in.');
});

it('blocks non-driver accounts from the API login', function (): void {
    $admin = User::create([
        'name' => 'Super Admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
        'role' => 'super_admin',
        'status' => 'active',
    ]);

    $this->postJson('/api/login', [
        'email' => $admin->email,
        'password' => 'password123',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('email')
        ->assertJsonPath('errors.email.0', 'Only drivers can sign in to this app. Please use the fleet web console instead.');
});

it('blocks refreshing a token once the driver loses their vehicle', function (): void {
    $driver = createActiveDriver();
    $user = $driver->user;

    $refreshToken = $user->createToken('refresh-' . $user->id)->plainTextToken;

    $this->postJson('/api/auth/refresh', [
        'refreshToken' => $refreshToken,
    ])
        ->assertStatus(403)
        ->assertJsonPath('message', 'No vehicle is assigned to your account yet. Please contact dispatch to assign a vehicle before signing in.');
});

it('allows refreshing a token while the driver still has a vehicle', function (): void {
    $driver = createActiveDriver();
    $user = $driver->user;

    Bus::create([
        'bus_number' => 'BUS-002',
        'plate' => 'XYZ-789',
        'driver_id' => $driver->id,
    ]);

    $refreshToken = $user->createToken('refresh-' . $user->id)->plainTextToken;

    $this->postJson('/api/auth/refresh', [
        'refreshToken' => $refreshToken,
    ])
        ->assertOk()
        ->assertJsonStructure(['user', 'accessToken', 'refreshToken']);
});
