<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Driver;
use App\Models\ShiftLog;
use App\Models\Trip;
use Illuminate\Database\Seeder;

/**
 * Demo shift logs for the meter audit board. Records are keyed on the human
 * readable shift number so re-running the seeder updates them in place rather
 * than duplicating rows. Fuel detail lives in the separate fuel_logs table.
 */
class ShiftLogSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = [
            [
                'shift_number' => 'SHF-0002',
                'bus_number' => '1',
                'driver_name' => 'David Lee',
                'trip_number' => 'TRP-0001',
                'start_km' => 89204,
                'end_km' => 89422,
                'started_at' => now()->setTime(5, 45),
                'ended_at' => now()->setTime(14, 2),
                'status' => ShiftLog::STATUS_COMPLETED,
                'start_notes' => 'Depot Gate 4 departure, start meter photo clear.',
                'end_notes' => 'Returned to Bay 12 without incident.',
            ],
            [
                'shift_number' => 'SHF-0003',
                'bus_number' => '1',
                'driver_name' => 'David Lee',
                'trip_number' => 'TRP-0003',
                'start_km' => 88900,
                'end_km' => 89088,
                'started_at' => now()->subDay()->setTime(5, 45),
                'ended_at' => now()->subDay()->setTime(13, 30),
                'status' => ShiftLog::STATUS_COMPLETED,
                'start_notes' => null,
                'end_notes' => 'Campus shuttle contract run.',
            ],
            [
                'shift_number' => 'SHF-0004',
                'bus_number' => '2',
                'driver_name' => 'Muhammad Ahsan',
                'trip_number' => 'TRP-0002',
                'start_km' => 20554,
                'end_km' => null,
                'started_at' => now()->setTime(7, 30),
                'ended_at' => null,
                'status' => ShiftLog::STATUS_IN_PROGRESS,
                'start_notes' => 'Harbour to airport express, currently in route.',
                'end_notes' => null,
            ],
            [
                'shift_number' => 'SHF-0005',
                'bus_number' => '2',
                'driver_name' => 'Muhammad Ahsan',
                'trip_number' => 'TRP-0004',
                'start_km' => 20142,
                'end_km' => 20360,
                'started_at' => now()->subDays(3)->setTime(5, 50),
                'ended_at' => now()->subDays(3)->setTime(13, 45),
                'status' => ShiftLog::STATUS_COMPLETED,
                'start_notes' => null,
                'end_notes' => 'Downtown loop to industrial park.',
            ],
            [
                // Completed but closed without an end meter reading: the board
                // surfaces this as a flagged log that still needs an auditor.
                'shift_number' => 'SHF-0006',
                'bus_number' => '2',
                'driver_name' => 'Muhammad Ahsan',
                'trip_number' => null,
                'start_km' => 20360,
                'end_km' => null,
                'started_at' => now()->subDays(2)->setTime(6, 0),
                'ended_at' => now()->subDays(2)->setTime(15, 20),
                'status' => ShiftLog::STATUS_COMPLETED,
                'start_notes' => 'End meter photo was not captured before the driver signed off.',
                'end_notes' => null,
            ],
        ];

        $fallbackBusId = Bus::orderBy('id')->value('id');
        $fallbackDriverId = Driver::orderBy('id')->value('id');

        foreach ($definitions as $definition) {
            $busId = Bus::where('bus_number', $definition['bus_number'])->value('id') ?? $fallbackBusId;
            $driverId = Driver::whereHas('user', fn ($query) => $query->where('name', $definition['driver_name']))
                ->value('id') ?? $fallbackDriverId;
            $tripId = $definition['trip_number']
                ? Trip::where('trip_number', $definition['trip_number'])->value('id')
                : null;

            $distance = $definition['end_km'] !== null
                ? $definition['end_km'] - $definition['start_km']
                : null;

            ShiftLog::updateOrCreate(
                ['shift_number' => $definition['shift_number']],
                [
                    'bus_id' => $busId,
                    'driver_id' => $driverId,
                    'trip_id' => $tripId,
                    'start_km' => $definition['start_km'],
                    'end_km' => $definition['end_km'],
                    'distance_km' => $distance,
                    'start_notes' => $definition['start_notes'],
                    'end_notes' => $definition['end_notes'],
                    'status' => $definition['status'],
                    'started_at' => $definition['started_at'],
                    'ended_at' => $definition['ended_at'],
                ]
            );
        }
    }
}
