<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Driver;
use Illuminate\Database\Seeder;

class BusSeeder extends Seeder
{
    public function run(): void
    {
        $buses = [
            [
                'bus_number' => '42',
                'plate' => 'EX-482-TX',
                'model' => 'Scania K310 City',
                'operator' => 'Marcus Vance',
                'status' => 'active',
                'odometer' => 142760,
                'fuel_month_liters' => 1240,
                'fuel_cost' => 2108.00,
                'km_per_liter' => 3.60,
                'next_service_km' => 7240,
                'route' => 'Route 14: Central Metro → North Highlands',
            ],
            [
                'bus_number' => '08',
                'plate' => 'KA-991-BV',
                'model' => 'Volvo B8RLE Low-Floor',
                'operator' => 'Sarah Jenkins',
                'status' => 'active',
                'odometer' => 198340,
                'fuel_month_liters' => 1410,
                'fuel_cost' => 2397.00,
                'km_per_liter' => 3.50,
                'next_service_km' => 3400,
                'route' => 'Line 08 Express: Harbour → Airport',
            ],
            [
                'bus_number' => '17',
                'plate' => 'TR-312-MM',
                'model' => 'Mercedes Citaro C2',
                'operator' => null,
                'status' => 'out_of_service',
                'odometer' => 245890,
                'fuel_month_liters' => 410,
                'fuel_cost' => 697.00,
                'km_per_liter' => 2.90,
                'next_service_km' => -380,
                'route' => null,
            ],
            [
                'bus_number' => '23',
                'plate' => 'PL-554-QW',
                'model' => "MAN Lion's City 12G",
                'operator' => 'David Chen',
                'status' => 'depot',
                'odometer' => 87410,
                'fuel_month_liters' => 980,
                'fuel_cost' => 1666.00,
                'km_per_liter' => 3.70,
                'next_service_km' => 1120,
                'route' => null,
            ],
            [
                'bus_number' => '31',
                'plate' => 'FR-781-OP',
                'model' => 'Scania K310 City',
                'operator' => 'Carlos Mendez',
                'status' => 'active',
                'odometer' => 162110,
                'fuel_month_liters' => 1310,
                'fuel_cost' => 2227.00,
                'km_per_liter' => 3.40,
                'next_service_km' => 5890,
                'route' => 'Route 14: Central Metro → North Highlands',
            ],
        ];

        foreach ($buses as $bus) {
            $operatorName = $bus['operator'] ?? null;
            unset($bus['operator']);

            $record = Bus::updateOrCreate(
                ['bus_number' => $bus['bus_number']],
                $bus
            );

            if (!$operatorName) {
                continue;
            }

            // Assign the bus to the matching driver (bus side owns the link).
            $driverId = Driver::whereHas('user', fn ($q) => $q->where('name', $operatorName))->value('id');

            if ($driverId && !Bus::where('driver_id', $driverId)->whereKeyNot($record->id)->exists()) {
                $record->update(['driver_id' => $driverId]);
            }
        }
    }
}
