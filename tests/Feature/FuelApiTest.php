<?php

use App\Models\Bus;
use App\Models\Driver;
use App\Models\FuelLog;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function fuelAdmin(): User
{
    return User::create([
        'name' => 'Fleet Admin',
        'email' => 'fuel-admin-' . uniqid() . '@example.com',
        'password' => Hash::make('password123'),
        'role' => 'super_admin',
        'status' => 'active',
    ]);
}

function fuelDriver(): Driver
{
    $user = User::create([
        'name' => 'Fuel Driver',
        'email' => 'fuel-driver-' . uniqid() . '@example.com',
        'password' => Hash::make('password123'),
        'role' => 'driver',
        'status' => 'active',
    ]);

    return Driver::create([
        'user_id' => $user->id,
        'badge_id' => 'FBDG-' . uniqid(),
        'duty_status' => 'available',
    ]);
}

function fuelBus(?int $driverId = null, int $odometer = 10000): Bus
{
    return Bus::create([
        'bus_number' => 'FB' . uniqid(),
        'plate' => 'FPL-' . uniqid(),
        'status' => 'active',
        'driver_id' => $driverId,
        'odometer' => $odometer,
    ]);
}

function fuelTrip(?int $busId = null): Trip
{
    return Trip::create([
        'trip_number' => 'TRP-' . strtoupper(uniqid()),
        'origin' => 'Depot',
        'destination' => 'Terminal',
        'trip_date' => now()->toDateString(),
        'status' => 'scheduled',
        'bus_id' => $busId,
    ]);
}

beforeEach(function (): void {
    Storage::fake('public');
});

it('requires authentication to log fuel', function (): void {
    $this->postJson('/api/fuel', [])->assertUnauthorized();
});

it('stores a fuel record received from the app', function (): void {
    $bus = fuelBus();
    $trip = fuelTrip($bus->id);

    Sanctum::actingAs(fuelAdmin());

    $this->postJson('/api/fuel', [
        'bus_id' => $bus->id,
        'trip_id' => $trip->id,
        'type' => 'diesel',
        'liters' => 45.5,
        'price' => 12500.75,
        'added_datetime' => '2026-10-14 09:30:00',
        'image' => UploadedFile::fake()->image('receipt.jpg'),
    ])
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('fuel.reference', 'FUL-0001')
        ->assertJsonPath('fuel.type', 'diesel')
        ->assertJsonPath('fuel.liters', 45.5)
        ->assertJsonPath('fuel.price', 12500.75)
        ->assertJsonPath('fuel.vehicle.id', $bus->id)
        ->assertJsonPath('fuel.trip.id', $trip->id);

    $log = FuelLog::first();

    expect($log)->not->toBeNull()
        ->and($log->bus_id)->toBe($bus->id)
        ->and($log->trip_id)->toBe($trip->id)
        ->and($log->fuel_type)->toBe('diesel')
        ->and((float) $log->liters)->toBe(45.5)
        ->and((float) $log->total_cost)->toBe(12500.75)
        ->and((float) $log->unit_price)->toBe(round(12500.75 / 45.5, 2))
        ->and($log->image)->not->toBeNull()
        ->and($log->filled_at->format('Y-m-d H:i:s'))->toBe('2026-10-14 09:30:00')
        // created_at and updated_at are stored as two separate columns and set.
        ->and($log->created_at)->not->toBeNull()
        ->and($log->updated_at)->not->toBeNull();

    Storage::disk('public')->assertExists($log->image);
});

it('derives the vehicle and driver from the authenticated driver', function (): void {
    $driver = fuelDriver();
    $bus = fuelBus($driver->id);

    Sanctum::actingAs($driver->user);

    $this->postJson('/api/fuel', [
        'type' => 'cng',
        'liters' => 12,
        'price' => 1800,
        'image' => UploadedFile::fake()->image('pump.png'),
    ])->assertCreated();

    $log = FuelLog::first();
    expect($log->bus_id)->toBe($bus->id)
        ->and($log->driver_id)->toBe($driver->id);
});

it('requires a vehicle when none can be derived', function (): void {
    Sanctum::actingAs(fuelAdmin());

    $this->postJson('/api/fuel', [
        'type' => 'diesel',
        'liters' => 10,
        'price' => 3000,
        'image' => UploadedFile::fake()->image('pump.png'),
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('bus_id');

    expect(FuelLog::count())->toBe(0);
});

it('normalises an aliased fuel type and defaults added_at to now', function (): void {
    $bus = fuelBus();

    Sanctum::actingAs(fuelAdmin());

    $this->postJson('/api/fuel', [
        'bus_id' => $bus->id,
        'fuel_type' => 'Hi-Octane',
        'quantity' => 20,
        'amount' => 6000,
        'image' => UploadedFile::fake()->image('pump.png'),
    ])->assertCreated()->assertJsonPath('fuel.type', 'hi_octane');

    $log = FuelLog::first();
    expect($log->filled_at)->not->toBeNull()
        ->and($log->created_at->toDateString())->toBe(now()->toDateString());
});

it('validates the required fuel fields', function (): void {
    Sanctum::actingAs(fuelAdmin());

    $this->postJson('/api/fuel', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['type', 'liters', 'price', 'image']);

    expect(FuelLog::count())->toBe(0);
});

it('rejects an unknown trip id', function (): void {
    $bus = fuelBus();

    Sanctum::actingAs(fuelAdmin());

    $this->postJson('/api/fuel', [
        'bus_id' => $bus->id,
        'trip_id' => 999999,
        'type' => 'diesel',
        'liters' => 10,
        'price' => 3000,
        'image' => UploadedFile::fake()->image('pump.png'),
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('trip_id');
});
