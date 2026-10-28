<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\TransitRoute;
use Illuminate\Database\Seeder;

class TransitRouteSeeder extends Seeder
{
    public function run(): void
    {
        $routes = [
            [
                'route_code' => 'RT-101',
                'name' => 'Airport Express Corridor',
                'origin' => 'Central Metro Terminal',
                'destination' => 'International Airport Terminal 2',
                'origin_lat' => 24.8607,
                'origin_lng' => 67.0011,
                'destination_lat' => 24.9065,
                'destination_lng' => 67.1608,
                'distance_km' => 28.5,
                'estimated_minutes' => 45,
                'status' => 'active',
                'notes' => 'Dedicated express transit route with rapid depot bypass.',
                'waypoints' => [
                    ['name' => 'Central Metro Terminal', 'lat' => 24.8607, 'lng' => 67.0011, 'type' => 'origin'],
                    ['name' => 'International Airport Terminal 2', 'lat' => 24.9065, 'lng' => 67.1608, 'type' => 'destination'],
                ],
                'path_coordinates' => [
                    [24.8607, 67.0011],
                    [24.8650, 67.0180],
                    [24.8720, 67.0450],
                    [24.8790, 67.0720],
                    [24.8850, 67.0980],
                    [24.8920, 67.1180],
                    [24.8990, 67.1350],
                    [24.9030, 67.1480],
                    [24.9065, 67.1608],
                ],
            ],
            [
                'route_code' => 'RT-204',
                'name' => 'Coastal Harbour Line',
                'origin' => 'South Dockside Bay',
                'destination' => 'North Horizon Depot',
                'origin_lat' => 24.8350,
                'origin_lng' => 67.0250,
                'destination_lat' => 24.9350,
                'destination_lng' => 67.0850,
                'distance_km' => 19.8,
                'estimated_minutes' => 38,
                'status' => 'active',
                'notes' => 'High-frequency urban service connecting passenger harbor to industrial zone.',
                'waypoints' => [
                    ['name' => 'South Dockside Bay', 'lat' => 24.8350, 'lng' => 67.0250, 'type' => 'origin'],
                    ['name' => 'North Horizon Depot', 'lat' => 24.9350, 'lng' => 67.0850, 'type' => 'destination'],
                ],
                'path_coordinates' => [
                    [24.8350, 67.0250],
                    [24.8420, 67.0310],
                    [24.8520, 67.0380],
                    [24.8650, 67.0470],
                    [24.8780, 67.0550],
                    [24.8920, 67.0640],
                    [24.9050, 67.0720],
                    [24.9200, 67.0790],
                    [24.9350, 67.0850],
                ],
            ],
            [
                'route_code' => 'RT-305',
                'name' => 'Metro Ring Circle Line',
                'origin' => 'West Boulevard Station',
                'destination' => 'East Park Transit Mall',
                'origin_lat' => 24.8820,
                'origin_lng' => 66.9850,
                'destination_lat' => 24.8890,
                'destination_lng' => 67.1420,
                'distance_km' => 22.4,
                'estimated_minutes' => 50,
                'status' => 'active',
                'notes' => 'Cross-city arterial connecting eastern and western transit hubs.',
                'waypoints' => [
                    ['name' => 'West Boulevard Station', 'lat' => 24.8820, 'lng' => 66.9850, 'type' => 'origin'],
                    ['name' => 'East Park Transit Mall', 'lat' => 24.8890, 'lng' => 67.1420, 'type' => 'destination'],
                ],
                'path_coordinates' => [
                    [24.8820, 66.9850],
                    [24.8830, 67.0080],
                    [24.8840, 67.0320],
                    [24.8850, 67.0550],
                    [24.8860, 67.0750],
                    [24.8870, 67.0950],
                    [24.8875, 67.1120],
                    [24.8882, 67.1280],
                    [24.8890, 67.1420],
                ],
            ],
        ];

        foreach ($routes as $routeData) {
            $route = TransitRoute::updateOrCreate(
                ['route_code' => $routeData['route_code']],
                $routeData
            );
        }

        // Assign routes and initial GPS coordinates to existing buses
        $buses = Bus::all();
        $createdRoutes = TransitRoute::all();

        if ($createdRoutes->count() > 0) {
            foreach ($buses as $index => $bus) {
                $assignedRoute = $createdRoutes[$index % $createdRoutes->count()];
                $waypoints = $assignedRoute->waypoints_list;
                $activeWaypoint = $waypoints[$index % count($waypoints)] ?? null;

                $lat = $activeWaypoint['lat'] ?? $assignedRoute->origin_lat;
                $lng = $activeWaypoint['lng'] ?? $assignedRoute->origin_lng;

                // Alternate some buses as online (active) vs offline
                $isOnline = ($index % 2 === 0);

                $bus->update([
                    'transit_route_id' => $assignedRoute->id,
                    'route' => $assignedRoute->name,
                    'last_latitude' => $lat,
                    'last_longitude' => $lng,
                    'last_location_at' => $isOnline ? now()->subMinutes(rand(1, 3)) : now()->subHours(rand(1, 12)),
                    'is_online' => $isOnline,
                    'speed' => $isOnline ? rand(25, 62) : 0,
                    'heading' => rand(0, 359),
                ]);
            }
        }
    }
}
