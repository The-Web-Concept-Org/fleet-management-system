<?php

use App\Models\Bus;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function tripAdmin(): User
{
    return User::create([
        'name' => 'Fleet Admin',
        'email' => 'admin-' . uniqid() . '@example.com',
        'password' => Hash::make('password123'),
        'role' => 'super_admin',
        'status' => 'active',
    ]);
}

function tripDriver(string $name = 'Trip Driver'): Driver
{
    $user = User::create([
        'name' => $name,
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

function tripBus(string $number, ?int $driverId = null): Bus
{
    return Bus::create([
        'bus_number' => $number,
        'plate' => 'PLT-' . uniqid(),
        'status' => 'active',
        'driver_id' => $driverId,
    ]);
}

it('requires authentication to view the trip board', function (): void {
    $this->get('/trips')->assertRedirect('/login');
});

it('renders the trip board for an authenticated user', function (): void {
    $this->actingAs(tripAdmin())
        ->get('/trips')
        ->assertOk()
        ->assertSee('Trip Scheduler');
});

it('sends the active and scheduled trips in the driver login response after the bus', function (): void {
    $driver = tripDriver('Login Driver');
    $bus = tripBus('99', $driver->id);

    // Active + upcoming trips are sent; a completed trip is not.
    Trip::create([
        'trip_number' => 'TRP-0001',
        'origin' => 'Central Metro',
        'destination' => 'North Highlands',
        'trip_date' => '2026-10-20',
        'departure_time' => '06:15',
        'arrival_time' => '09:45',
        'distance_km' => 42.5,
        'status' => 'in_progress',
        'bus_id' => $bus->id,
    ]);

    Trip::create([
        'trip_number' => 'TRP-0002',
        'origin' => 'Harbour',
        'destination' => 'Airport',
        'trip_date' => '2026-10-21',
        'status' => 'scheduled',
        'bus_id' => $bus->id,
    ]);

    Trip::create([
        'trip_number' => 'TRP-0003',
        'origin' => 'Old Town',
        'destination' => 'Depot',
        'trip_date' => '2026-10-19',
        'status' => 'completed',
        'bus_id' => $bus->id,
    ]);

    $this->postJson('/api/login', [
        'email' => $driver->user->email,
        'password' => 'password123',
    ])
        ->assertOk()
        ->assertJsonPath('user.bus.bus_number', '99')
        ->assertJsonCount(2, 'user.trips')
        ->assertJsonPath('user.trips.0.trip_number', 'TRP-0001')
        ->assertJsonPath('user.trips.0.origin', 'Central Metro')
        ->assertJsonPath('user.trips.0.destination', 'North Highlands')
        ->assertJsonPath('user.trips.0.departure_time', '06:15')
        ->assertJsonPath('user.trips.0.arrival_time', '09:45')
        ->assertJsonPath('user.trips.0.status', 'in_progress')
        ->assertJsonPath('user.trips.1.trip_number', 'TRP-0002')
        ->assertJsonPath('user.trips.1.status', 'scheduled');
});

it('sends no trips when the bus has no scheduled or active trip', function (): void {
    $driver = tripDriver('Idle Driver');
    $bus = tripBus('70', $driver->id);

    Trip::create([
        'trip_number' => 'TRP-0001',
        'origin' => 'Central Metro',
        'destination' => 'North Highlands',
        'trip_date' => '2026-10-21',
        'status' => 'completed',
        'bus_id' => $bus->id,
    ]);

    $this->postJson('/api/login', [
        'email' => $driver->user->email,
        'password' => 'password123',
    ])
        ->assertOk()
        ->assertJsonCount(0, 'user.trips');
});

it('schedules a trip and assigns a bus, deriving the driver from the bus', function (): void {
    $driver = tripDriver('Marcus Vance');
    $bus = tripBus('77', $driver->id);

    $response = $this->actingAs(tripAdmin())->postJson('/trips', [
        'origin' => 'Central Metro',
        'destination' => 'North Highlands',
        'trip_date' => '2026-10-20',
        'departure_time' => '06:15',
        'arrival_time' => '09:45',
        'distance_km' => 42.5,
        'status' => 'scheduled',
        'bus_id' => $bus->id,
    ]);

    $response->assertOk()->assertJsonPath('success', true);

    $trip = Trip::firstWhere('origin', 'Central Metro');

    expect($trip)->not->toBeNull()
        ->and($trip->trip_number)->toBe('TRP-0001')
        ->and($trip->bus_id)->toBe($bus->id)
        ->and($trip->driver?->id)->toBe($driver->id)
        ->and($trip->is_assigned)->toBeTrue();
});

it('updates a trip and reassigns the bus including its driver', function (): void {
    $trip = Trip::create([
        'trip_number' => 'TRP-0001',
        'origin' => 'A',
        'destination' => 'B',
        'trip_date' => '2026-10-20',
        'status' => 'scheduled',
    ]);

    $driver = tripDriver('Sarah Jenkins');
    $newBus = tripBus('88', $driver->id);

    $this->actingAs(tripAdmin())->putJson("/trips/{$trip->id}", [
        'origin' => 'A1',
        'destination' => 'B1',
        'trip_date' => '2026-10-21',
        'status' => 'in_progress',
        'bus_id' => $newBus->id,
    ])->assertOk()->assertJsonPath('success', true);

    $trip->refresh();

    expect($trip->origin)->toBe('A1')
        ->and($trip->status)->toBe('in_progress')
        ->and($trip->bus_id)->toBe($newBus->id)
        ->and($trip->driver?->id)->toBe($driver->id);
});

it('deletes a trip', function (): void {
    $trip = Trip::create([
        'trip_number' => 'TRP-0001',
        'origin' => 'A',
        'destination' => 'B',
        'trip_date' => '2026-10-20',
        'status' => 'scheduled',
    ]);

    $this->actingAs(tripAdmin())
        ->deleteJson("/trips/{$trip->id}")
        ->assertOk()
        ->assertJsonPath('success', true);

    expect(Trip::find($trip->id))->toBeNull();
});

it('blocks a double-booked bus on an overlapping trip', function (): void {
    $bus = tripBus('55');

    Trip::create([
        'trip_number' => 'TRP-0001',
        'origin' => 'A',
        'destination' => 'B',
        'trip_date' => '2026-10-20',
        'departure_time' => '08:00',
        'arrival_time' => '10:00',
        'status' => 'scheduled',
        'bus_id' => $bus->id,
    ]);

    $this->actingAs(tripAdmin())
        ->postJson('/trips', [
            'origin' => 'C',
            'destination' => 'D',
            'trip_date' => '2026-10-20',
            'departure_time' => '09:00',
            'arrival_time' => '11:00',
            'status' => 'scheduled',
            'bus_id' => $bus->id,
        ])
        ->assertStatus(422)
        ->assertJsonPath('success', false);

    expect(Trip::count())->toBe(1);
});

it('allows reusing a bus for a non-overlapping trip', function (): void {
    $bus = tripBus('56');

    Trip::create([
        'trip_number' => 'TRP-0001',
        'origin' => 'A',
        'destination' => 'B',
        'trip_date' => '2026-10-20',
        'departure_time' => '08:00',
        'arrival_time' => '10:00',
        'status' => 'scheduled',
        'bus_id' => $bus->id,
    ]);

    $this->actingAs(tripAdmin())
        ->postJson('/trips', [
            'origin' => 'C',
            'destination' => 'D',
            'trip_date' => '2026-10-20',
            'departure_time' => '10:30',
            'arrival_time' => '12:00',
            'status' => 'scheduled',
            'bus_id' => $bus->id,
        ])
        ->assertOk()
        ->assertJsonPath('success', true);

    expect(Trip::count())->toBe(2);
});
