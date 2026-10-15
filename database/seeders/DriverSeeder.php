<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = [
            [
                'name' => 'Marcus Vance',
                'email' => 'marcus.vance@fleet.local',
                'password' => Hash::make('12345678'),
                'role' => 'driver',
                'status' => 'active',
                'badge_id' => 'DRV-8802',
                'phone' => '+1 (555) 234-8802',
                'license_number' => 'CDL-8802-TX',
                'license_type' => 'Class A CDL',
                'license_expiry' => '2028-06-15',
                'duty_status' => 'on_route',
                'experience_years' => 6,
                'notes' => 'Senior line driver, certified for double-decker and articulated routes.',
            ],
            [
                'name' => 'Sarah Jenkins',
                'email' => 'sarah.jenkins@fleet.local',
                'password' => Hash::make('12345678'),
                'role' => 'driver',
                'status' => 'active',
                'badge_id' => 'DRV-7401',
                'phone' => '+1 (555) 492-7401',
                'license_number' => 'CDL-7401-CA',
                'license_type' => 'Class B CDL',
                'license_expiry' => '2027-11-20',
                'duty_status' => 'on_route',
                'experience_years' => 4,
                'notes' => 'City Line specialist with clean safety records.',
            ],
            [
                'name' => 'David Lee',
                'email' => 'david.lee@fleet.local',
                'password' => Hash::make('12345678'),
                'role' => 'driver',
                'status' => 'active',
                'badge_id' => 'DRV-3199',
                'phone' => '+1 (555) 812-3199',
                'license_number' => 'CDL-3199-NY',
                'license_type' => 'Class A CDL',
                'license_expiry' => '2029-01-10',
                'duty_status' => 'available',
                'experience_years' => 8,
                'notes' => 'Available for depot standby and express shuttle duties.',
            ],
            [
                'name' => 'Rachel Chen',
                'email' => 'rachel.chen@fleet.local',
                'password' => Hash::make('12345678'),
                'role' => 'driver',
                'status' => 'active',
                'badge_id' => 'DRV-5210',
                'phone' => '+1 (555) 304-5210',
                'license_number' => 'CDL-5210-FL',
                'license_type' => 'Class B CDL',
                'license_expiry' => '2026-12-30',
                'duty_status' => 'on_route',
                'experience_years' => 5,
                'notes' => 'Scheduled for routine medical card update before expiry.',
            ],
        ];

        foreach ($drivers as $item) {
            $user = User::updateOrCreate(
                ['email' => $item['email']],
                [
                    'name' => $item['name'],
                    'password' => $item['password'],
                    'role' => 'driver',
                    'status' => $item['status'],
                ]
            );

            Driver::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'badge_id' => $item['badge_id'],
                    'phone' => $item['phone'],
                    'license_number' => $item['license_number'],
                    'license_type' => $item['license_type'],
                    'license_expiry' => $item['license_expiry'],
                    'duty_status' => $item['duty_status'],
                    'experience_years' => $item['experience_years'],
                    'notes' => $item['notes'],
                ]
            );
        }
    }
}
