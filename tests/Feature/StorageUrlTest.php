<?php

use App\Models\Bus;
use App\Models\Driver;
use App\Models\ShiftLog;
use App\Models\User;
use App\Support\StorageUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('builds storage urls from the current request host and scheme, not APP_URL', function () {
    Storage::fake('public');

    $admin = User::create([
        'name' => 'Fleet Admin',
        'email' => 'urlhost-' . uniqid() . '@example.com',
        'password' => Hash::make('password123'),
        'role' => 'super_admin',
        'status' => 'active',
    ]);

    $driverUser = User::create([
        'name' => 'David Lee',
        'email' => 'urlhost-driver-' . uniqid() . '@example.com',
        'password' => Hash::make('password123'),
        'role' => 'driver',
        'status' => 'active',
    ]);

    $driver = Driver::create([
        'user_id' => $driverUser->id,
        'badge_id' => 'BDG-' . uniqid(),
        'duty_status' => 'available',
    ]);

    $bus = Bus::create([
        'bus_number' => 'B' . uniqid(),
        'plate' => 'PLT-' . uniqid(),
        'status' => 'active',
        'driver_id' => $driver->id,
        'odometer' => 100000,
    ]);

    $path = 'shifts/bus-' . $bus->id . '/meter.png';
    Storage::disk('public')->put($path, 'fake-bytes');

    $log = ShiftLog::create([
        'shift_number' => 'SHF-TEST',
        'bus_id' => $bus->id,
        'driver_id' => $driver->id,
        'start_km' => 100000,
        'start_meter_image' => $path,
        'status' => ShiftLog::STATUS_COMPLETED,
        'started_at' => now(),
        'ended_at' => now()->addHour(),
    ]);

    // Request the detail partial over a spoofed https production host.
    $response = $this->actingAs($admin)
        ->get('https://fleet.thewebconcept.com/shifts/' . $log->id);

    $response->assertOk();
    $response->assertSee('https://fleet.thewebconcept.com/storage/' . $path, false);
    $response->assertDontSee('127.0.0.1:8000/storage', false);
    $response->assertDontSee('localhost/storage', false);

    // The helper tolerates a leading slash and returns null for a missing path.
    expect(StorageUrl::for('/' . $path))->toContain('/storage/' . $path)
        ->and(StorageUrl::for(null))->toBeNull();
});
