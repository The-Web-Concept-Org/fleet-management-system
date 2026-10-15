<?php

use App\Models\Bus;
use App\Models\Driver;
use App\Models\ShiftLog;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function shiftAdmin(): User
{
    return User::create([
        'name' => 'Fleet Admin',
        'email' => 'shift-admin-' . uniqid() . '@example.com',
        'password' => Hash::make('password123'),
        'role' => 'super_admin',
        'status' => 'active',
    ]);
}

function shiftDriver(string $name = 'Shift Driver'): Driver
{
    $user = User::create([
        'name' => $name,
        'email' => 'shift-driver-' . uniqid() . '@example.com',
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

function shiftBus(?int $driverId = null, int $odometer = 100000): Bus
{
    return Bus::create([
        'bus_number' => 'B' . uniqid(),
        'plate' => 'PLT-' . uniqid(),
        'status' => 'active',
        'driver_id' => $driverId,
        'odometer' => $odometer,
    ]);
}

function shiftTrip(?int $busId = null, string $status = 'scheduled'): Trip
{
    return Trip::create([
        'trip_number' => 'TRP-' . strtoupper(uniqid()),
        'origin' => 'Central Metro',
        'destination' => 'North Highlands',
        'trip_date' => now()->toDateString(),
        'status' => $status,
        'bus_id' => $busId,
    ]);
}

beforeEach(function (): void {
    Storage::fake('public');
});

it('requires authentication to start a shift', function (): void {
    $this->postJson('/api/shifts/start', [])->assertUnauthorized();
});

it('starts a shift with a meter image, km, start time and linked trip', function (): void {
    $driver = shiftDriver('Ali Raza');
    $bus = shiftBus($driver->id, 142760);
    $trip = shiftTrip($bus->id);

    Sanctum::actingAs($driver->user);

    $this->postJson('/api/shifts/start', [
        'bus_id' => $bus->id,
        'trip_id' => $trip->id,
        'meter_reading' => 142760,
        'started_at' => '2026-10-15 06:30:00',
        'meter_image' => UploadedFile::fake()->image('start-meter.jpg'),
    ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('shift.status', 'in_progress')
        ->assertJsonPath('shift.start_km', 142760)
        ->assertJsonPath('shift.vehicle.id', $bus->id)
        ->assertJsonPath('shift.trip.id', $trip->id);

    $log = ShiftLog::first();

    expect($log)->not->toBeNull()
        ->and($log->shift_number)->toBe('SHF-0001')
        ->and($log->bus_id)->toBe($bus->id)
        ->and($log->driver_id)->toBe($driver->id)
        ->and($log->trip_id)->toBe($trip->id)
        ->and($log->end_km)->toBeNull()
        ->and($log->start_meter_image)->not->toBeNull()
        ->and($log->started_at->format('Y-m-d H:i'))->toBe('2026-10-15 06:30');

    Storage::disk('public')->assertExists($log->start_meter_image);
    expect($driver->fresh()->duty_status)->toBe('on_route');
    expect($trip->fresh()->status)->toBe('in_progress');
});

it('requires a meter image to start a shift', function (): void {
    $bus = shiftBus();

    Sanctum::actingAs(shiftAdmin());

    $this->postJson('/api/shifts/start', [
        'bus_id' => $bus->id,
        'start_km' => 100,
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('meter_image');

    expect(ShiftLog::count())->toBe(0);
});

it('blocks a second active shift for the same vehicle', function (): void {
    $bus = shiftBus();

    Sanctum::actingAs(shiftAdmin());

    $this->postJson('/api/shifts/start', [
        'bus_id' => $bus->id,
        'start_km' => 5000,
        'meter_image' => UploadedFile::fake()->image('start-one.jpg'),
    ])->assertOk();

    $this->postJson('/api/shifts/start', [
        'bus_id' => $bus->id,
        'start_km' => 5100,
        'meter_image' => UploadedFile::fake()->image('start-two.jpg'),
    ])
        ->assertStatus(422)
        ->assertJsonPath('success', false);

    expect(ShiftLog::count())->toBe(1);
});

it('rejects a trip that belongs to a different vehicle', function (): void {
    $bus = shiftBus();
    $otherBus = shiftBus();
    $trip = shiftTrip($otherBus->id);

    Sanctum::actingAs(shiftAdmin());

    $this->postJson('/api/shifts/start', [
        'bus_id' => $bus->id,
        'trip_id' => $trip->id,
        'start_km' => 1000,
        'meter_image' => UploadedFile::fake()->image('start.jpg'),
    ])
        ->assertStatus(422)
        ->assertJsonPath('success', false);

    expect(ShiftLog::count())->toBe(0);
});

it('ends a shift by vehicle id, computes distance and completes the linked trip', function (): void {
    $bus = shiftBus(null, 50000);
    $trip = shiftTrip($bus->id);

    Sanctum::actingAs(shiftAdmin());

    $this->postJson('/api/shifts/start', [
        'bus_id' => $bus->id,
        'trip_id' => $trip->id,
        'start_km' => 50000,
        'meter_image' => UploadedFile::fake()->image('start.jpg'),
    ])->assertOk();

    $this->postJson('/api/shifts/end', [
        'bus_id' => $bus->id,
        'end_km' => 50137,
        'ended_at' => '2026-10-15 18:45:00',
        'meter_image' => UploadedFile::fake()->image('end.jpg'),
    ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('shift.status', 'completed')
        ->assertJsonPath('shift.distance_km', 137);

    $log = ShiftLog::first();

    expect($log->end_km)->toBe(50137)
        ->and((float) $log->distance_km)->toBe(137.0)
        ->and($log->is_active)->toBeFalse()
        ->and($log->ended_at->format('Y-m-d H:i'))->toBe('2026-10-15 18:45');

    Storage::disk('public')->assertExists($log->end_meter_image);
    expect($bus->fresh()->odometer)->toBe(50137);

    $trip->refresh();
    expect($trip->status)->toBe('completed')
        ->and((float) $trip->distance_km)->toBe(137.0)
        ->and($trip->arrival_time)->not->toBeNull();
});

it('accepts a trip id on the end call and completes that trip', function (): void {
    $bus = shiftBus();
    $trip = shiftTrip($bus->id);

    Sanctum::actingAs(shiftAdmin());

    // Shift started without a trip link.
    $this->postJson('/api/shifts/start', [
        'bus_id' => $bus->id,
        'start_km' => 8000,
        'meter_image' => UploadedFile::fake()->image('start.jpg'),
    ])->assertOk();

    $this->postJson('/api/shifts/end', [
        'bus_id' => $bus->id,
        'trip_id' => $trip->id,
        'end_km' => 8080,
        'meter_image' => UploadedFile::fake()->image('end.jpg'),
    ])->assertOk();

    expect($trip->fresh()->status)->toBe('completed')
        ->and(ShiftLog::first()->trip_id)->toBe($trip->id);
});

it('requires an active shift before it can be ended', function (): void {
    $bus = shiftBus();

    Sanctum::actingAs(shiftAdmin());

    $this->postJson('/api/shifts/end', [
        'bus_id' => $bus->id,
        'end_km' => 1234,
        'meter_image' => UploadedFile::fake()->image('end.jpg'),
    ])
        ->assertStatus(422)
        ->assertJsonPath('success', false);
});

it('rejects an ending reading lower than the starting reading', function (): void {
    $bus = shiftBus();

    Sanctum::actingAs(shiftAdmin());

    $this->postJson('/api/shifts/start', [
        'bus_id' => $bus->id,
        'start_km' => 9000,
        'meter_image' => UploadedFile::fake()->image('start.jpg'),
    ])->assertOk();

    $this->postJson('/api/shifts/end', [
        'bus_id' => $bus->id,
        'end_km' => 8500,
        'meter_image' => UploadedFile::fake()->image('end.jpg'),
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('end_km');

    expect(ShiftLog::first()->status)->toBe('in_progress');
});
