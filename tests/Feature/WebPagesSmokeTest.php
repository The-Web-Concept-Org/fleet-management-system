<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('renders every sidebar page for an authenticated user', function (string $path, string $expected): void {
    $admin = User::create([
        'name' => 'Fleet Admin',
        'email' => 'smoke-' . uniqid() . '@example.com',
        'password' => Hash::make('password123'),
        'role' => 'super_admin',
        'status' => 'active',
    ]);

    $this->actingAs($admin)
        ->get($path)
        ->assertOk()
        ->assertSee($expected);
})->with([
    ['/dashboard', 'Dashboard'],
    ['/buses', 'Buses'],
    ['/trips', 'Trips'],
    ['/shifts', 'Shifts'],
    ['/fuel', 'Fuel'],
    ['/maintenance', 'Maintenance'],
    ['/drivers', 'Drivers'],
    ['/settings', 'Account Settings'],
]);

it('renders live fuel and maintenance sums on dashboard and telemetry', function (): void {
    $admin = User::create([
        'name' => 'Fleet Admin',
        'email' => 'metrics-' . uniqid() . '@example.com',
        'password' => Hash::make('password123'),
        'role' => 'super_admin',
        'status' => 'active',
    ]);

    $bus = \App\Models\Bus::create([
        'bus_number' => 'BUS-99',
        'plate' => 'PLT-99',
        'status' => 'active',
        'odometer' => 12000,
    ]);

    \App\Models\FuelLog::create([
        'reference' => 'FUL-TEST',
        'bus_id' => $bus->id,
        'fuel_type' => 'diesel',
        'liters' => 75.5,
        'total_cost' => 225.50,
        'unit_price' => 2.98,
        'status' => 'approved',
    ]);

    \App\Models\MaintenanceLog::create([
        'reference' => 'MNT-TEST',
        'bus_id' => $bus->id,
        'price' => 450.75,
        'note' => 'Transmission servicing',
        'status' => 'completed',
    ]);

    $this->actingAs($admin)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('$225.50')
        ->assertSee('75.5 L Diesel logged')
        ->assertSee('$450.75')
        ->assertSeeHtml('>1</span> completed');

    $this->actingAs($admin)
        ->getJson('/telemetry/live')
        ->assertOk()
        ->assertJson([
            'success' => true,
            'stats' => [
                'totalFuelCost' => 225.50,
                'totalMaintenanceCost' => 450.75,
                'completedMaintenanceCount' => 1,
            ],
        ]);
});
