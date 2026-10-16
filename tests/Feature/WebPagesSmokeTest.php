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
]);
