<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Trip;
use Illuminate\Database\Seeder;

class TripSeeder extends Seeder
{
    public function run(): void
    {
        $trips = [
            [
                'trip_number' => 'TRP-0001',
                'origin' => 'Central Metro Terminal',
                'destination' => 'North Highlands Depot',
                'trip_date' => now()->toDateString(),
                'departure_time' => '06:15',
                'arrival_time' => '09:45',
                'distance_km' => 42.5,
                'status' => 'in_progress',
                'bus_number' => '42',
                'notes' => 'Morning express service on Line 14.',
            ],
            [
                'trip_number' => 'TRP-0002',
                'origin' => 'Harbour Terminal',
                'destination' => 'International Airport',
                'trip_date' => now()->toDateString(),
                'departure_time' => '07:30',
                'arrival_time' => '10:00',
                'distance_km' => 38.2,
                'status' => 'scheduled',
                'bus_number' => '08',
                'notes' => null,
            ],
            [
                'trip_number' => 'TRP-0003',
                'origin' => 'Riverside Station',
                'destination' => 'University Campus',
                'trip_date' => now()->addDay()->toDateString(),
                'departure_time' => '08:00',
                'arrival_time' => '11:30',
                'distance_km' => 51.0,
                'status' => 'scheduled',
                'bus_number' => '31',
                'notes' => 'Reserved for campus shuttle contract.',
            ],
            [
                'trip_number' => 'TRP-0004',
                'origin' => 'Downtown Loop',
                'destination' => 'Industrial Park',
                'trip_date' => now()->subDay()->toDateString(),
                'departure_time' => '13:00',
                'arrival_time' => '15:20',
                'distance_km' => 29.4,
                'status' => 'completed',
                'bus_number' => '23',
                'notes' => null,
            ],
            [
                'trip_number' => 'TRP-0005',
                'origin' => 'West Gate Depot',
                'destination' => 'Summit Mall',
                'trip_date' => now()->addDay()->toDateString(),
                'departure_time' => '16:00',
                'arrival_time' => '18:10',
                'distance_km' => 33.8,
                'status' => 'scheduled',
                'bus_number' => null,
                'notes' => 'Awaiting a bus to be assigned.',
            ],
        ];

        $busPool = Bus::orderBy('bus_number')->pluck('id');

        foreach ($trips as $index => $item) {
            $busId = null;
            if (!empty($item['bus_number'])) {
                $busId = Bus::where('bus_number', $item['bus_number'])->value('id');
            }

            // Fall back to whatever fleet exists so the demo board always shows
            // a populated assignment, regardless of the seeded bus data.
            if ($busId === null && $item['bus_number'] !== null && $busPool->isNotEmpty()) {
                $busId = $busPool[$index % $busPool->count()];
            }

            Trip::updateOrCreate(
                ['trip_number' => $item['trip_number']],
                [
                    'origin' => $item['origin'],
                    'destination' => $item['destination'],
                    'trip_date' => $item['trip_date'],
                    'departure_time' => $item['departure_time'],
                    'arrival_time' => $item['arrival_time'],
                    'distance_km' => $item['distance_km'],
                    'status' => $item['status'],
                    'bus_id' => $busId,
                    'notes' => $item['notes'],
                ]
            );
        }
    }
}
