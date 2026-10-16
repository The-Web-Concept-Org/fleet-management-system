<?php

use App\Models\Bus;
use App\Models\Driver;
use App\Models\MaintenanceLog;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function maintenanceAdmin(): User
{
    return User::create([
        'name' => 'Fleet Admin',
        'email' => 'maintenance-admin-' . uniqid() . '@example.com',
        'password' => Hash::make('password123'),
        'role' => 'super_admin',
        'status' => 'active',
    ]);
}

function maintenanceDriver(): Driver
{
    $user = User::create([
        'name' => 'Maintenance Driver',
        'email' => 'maintenance-driver-' . uniqid() . '@example.com',
        'password' => Hash::make('password123'),
        'role' => 'driver',
        'status' => 'active',
    ]);

    return Driver::create([
        'user_id' => $user->id,
        'badge_id' => 'MBDG-' . uniqid(),
        'duty_status' => 'available',
    ]);
}

function maintenanceBus(?int $driverId = null): Bus
{
    return Bus::create([
        'bus_number' => 'MB' . uniqid(),
        'plate' => 'MPL-' . uniqid(),
        'status' => 'active',
        'driver_id' => $driverId,
    ]);
}

function maintenanceTrip(?int $busId = null): Trip
{
    return Trip::create([
        'trip_number' => 'MTRP-' . strtoupper(uniqid()),
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

it('requires authentication to log maintenance', function (): void {
    $this->postJson('/api/maintenance', [])->assertUnauthorized();
});

it('stores a maintenance record received from the app', function (): void {
    $bus = maintenanceBus();
    $driver = maintenanceDriver();
    $trip = maintenanceTrip($bus->id);

    Sanctum::actingAs(maintenanceAdmin());

    $this->postJson('/api/maintenance', [
        'bus_id' => $bus->id,
        'driver_id' => $driver->id,
        'trip_id' => $trip->id,
        'price' => 8500.50,
        'note' => 'Replaced brake pads and front discs.',
        'performed_at' => '2026-10-14 09:30:00',
        'image' => UploadedFile::fake()->image('bill.jpg'),
    ])
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('maintenance.reference', 'MNT-0001')
        ->assertJsonPath('maintenance.price', 8500.5)
        ->assertJsonPath('maintenance.note', 'Replaced brake pads and front discs.')
        ->assertJsonPath('maintenance.vehicle.id', $bus->id)
        ->assertJsonPath('maintenance.driver_id', $driver->id)
        ->assertJsonPath('maintenance.trip.id', $trip->id)
        ->assertJsonMissingPath('warning');

    $log = MaintenanceLog::first();

    expect($log)->not->toBeNull()
        ->and($log->bus_id)->toBe($bus->id)
        ->and($log->driver_id)->toBe($driver->id)
        ->and($log->trip_id)->toBe($trip->id)
        ->and((float) $log->price)->toBe(8500.5)
        ->and($log->note)->toBe('Replaced brake pads and front discs.')
        ->and($log->image)->not->toBeNull()
        ->and($log->performed_at->format('Y-m-d H:i:s'))->toBe('2026-10-14 09:30:00')
        ->and($log->created_at)->not->toBeNull()
        ->and($log->updated_at)->not->toBeNull();

    Storage::disk('public')->assertExists($log->image);
});

it('derives the vehicle and driver from the authenticated driver', function (): void {
    $driver = maintenanceDriver();
    $bus = maintenanceBus($driver->id);

    Sanctum::actingAs($driver->user);

    $this->postJson('/api/maintenance', [
        'price' => 1200,
        'note' => 'Oil change',
        'image' => UploadedFile::fake()->image('bill.png'),
    ])->assertCreated();

    $log = MaintenanceLog::first();
    expect($log->bus_id)->toBe($bus->id)
        ->and($log->driver_id)->toBe($driver->id);
});

it('requires a vehicle when none can be derived', function (): void {
    Sanctum::actingAs(maintenanceAdmin());

    $this->postJson('/api/maintenance', [
        'price' => 500,
        'note' => 'Tyre repair',
        'image' => UploadedFile::fake()->image('bill.png'),
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('bus_id');

    expect(MaintenanceLog::count())->toBe(0);
});

it('accepts alternate field names for price, note and the bill image', function (): void {
    $bus = maintenanceBus();

    Sanctum::actingAs(maintenanceAdmin());

    $this->postJson('/api/maintenance', [
        'bus_id' => $bus->id,
        'amount' => 4200,
        'description' => 'Radiator flush',
        'bill_image' => UploadedFile::fake()->image('bill.jpg'),
    ])
        ->assertCreated()
        ->assertJsonPath('maintenance.price', fn ($value) => (float) $value === 4200.0)
        ->assertJsonPath('maintenance.note', 'Radiator flush');

    $log = MaintenanceLog::first();
    expect((float) $log->price)->toBe(4200.0)
        ->and($log->note)->toBe('Radiator flush')
        ->and($log->image)->not->toBeNull();
});

it('defaults performed_at to now when the app sends no date', function (): void {
    $bus = maintenanceBus();

    Sanctum::actingAs(maintenanceAdmin());

    $this->postJson('/api/maintenance', [
        'bus_id' => $bus->id,
        'price' => 900,
        'image' => UploadedFile::fake()->image('bill.png'),
    ])
        ->assertCreated()
        ->assertJsonPath('maintenance.note', null);

    $log = MaintenanceLog::first();
    expect($log->performed_at)->not->toBeNull()
        ->and($log->created_at->toDateString())->toBe(now()->toDateString());
});

it('validates the required maintenance fields', function (): void {
    Sanctum::actingAs(maintenanceAdmin());

    $this->postJson('/api/maintenance', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['price', 'image']);

    expect(MaintenanceLog::count())->toBe(0);
});

it('rejects an unknown trip id', function (): void {
    $bus = maintenanceBus();

    Sanctum::actingAs(maintenanceAdmin());

    $this->postJson('/api/maintenance', [
        'bus_id' => $bus->id,
        'trip_id' => 999999,
        'price' => 3000,
        'image' => UploadedFile::fake()->image('bill.png'),
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('trip_id');
});

it('shows maintenance records on the maintenance screen', function (): void {
    $bus = maintenanceBus();

    MaintenanceLog::create([
        'reference' => 'MNT-0001',
        'bus_id' => $bus->id,
        'price' => 8500.50,
        'note' => 'Replaced brake pads and front discs.',
        'service_type' => 'brakes',
        'status' => 'pending',
        'image' => 'maintenance/bus-1/bill.jpg',
        'performed_at' => now(),
    ]);

    $this->actingAs(maintenanceAdmin())
        ->get('/maintenance')
        ->assertOk()
        ->assertSee('Maintenance Logs')
        ->assertSee('MNT-0001')
        ->assertSee('Replaced brake pads and front discs.')
        ->assertSee('8,500.50');
});
