<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\TransitRoute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransitRouteController extends Controller
{
    /**
     * Web view: Dedicated Transit Routes Board with interactive Leaflet Route Map.
     */
    public function index(Request $request): View
    {
        $query = TransitRoute::with(['buses.driver.user']);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('route_code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('origin', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        $routes = $query->orderBy('route_code')->get();

        $totalRoutes = TransitRoute::count();
        $activeRoutes = TransitRoute::where('status', 'active')->count();
        $totalDistance = round((float) TransitRoute::sum('distance_km'), 1);
        $assignedBusesCount = Bus::whereNotNull('transit_route_id')->count();

        $allBuses = Bus::with('driver.user')->orderBy('bus_number')->get();

        return view('routes', compact(
            'routes',
            'totalRoutes',
            'activeRoutes',
            'totalDistance',
            'assignedBusesCount',
            'allBuses'
        ));
    }

    /**
     * API: List all active routes for the mobile application.
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $routes = TransitRoute::with('buses')
            ->where('status', 'active')
            ->orderBy('route_code')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $routes->map(fn (TransitRoute $r) => $r->toApiArray())->values(),
        ]);
    }

    /**
     * API: Get single route with detailed waypoints & path for mobile map rendering.
     */
    public function apiShow(TransitRoute $route): JsonResponse
    {
        $route->load('buses.driver.user');

        return response()->json([
            'success' => true,
            'data' => $route->toApiArray(),
        ]);
    }

    /**
     * Store a newly created route.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'route_code' => ['required', 'string', 'max:30', 'unique:transit_routes,route_code'],
            'name' => ['required', 'string', 'max:150'],
            'origin' => ['required', 'string', 'max:150'],
            'destination' => ['required', 'string', 'max:150'],
            'origin_lat' => ['nullable', 'numeric'],
            'origin_lng' => ['nullable', 'numeric'],
            'destination_lat' => ['nullable', 'numeric'],
            'destination_lng' => ['nullable', 'numeric'],
            'distance_km' => ['nullable', 'numeric', 'min:0'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'waypoints_raw' => ['nullable', 'string'],
        ]);

        $originLat = $validated['origin_lat'] ?? null;
        $originLng = $validated['origin_lng'] ?? null;
        $destLat = $validated['destination_lat'] ?? null;
        $destLng = $validated['destination_lng'] ?? null;

        $waypoints = [
            ['name' => trim($validated['origin']), 'lat' => $originLat !== null ? (float) $originLat : null, 'lng' => $originLng !== null ? (float) $originLng : null, 'type' => 'origin'],
            ['name' => trim($validated['destination']), 'lat' => $destLat !== null ? (float) $destLat : null, 'lng' => $destLng !== null ? (float) $destLng : null, 'type' => 'destination'],
        ];

        // Generate synthetic path coordinates if not provided
        $pathCoordinates = [];
        if (!empty($originLat) && !empty($originLng) && !empty($destLat) && !empty($destLng)) {
            $pathCoordinates = [
                [(float) $originLat, (float) $originLng],
                [(float) $destLat, (float) $destLng],
            ];
        }

        // Check if path_coordinates were provided as JSON or array
        if ($request->filled('path_coordinates')) {
            $parsedPath = is_string($request->input('path_coordinates'))
                ? json_decode($request->input('path_coordinates'), true)
                : $request->input('path_coordinates');
            if (is_array($parsedPath) && count($parsedPath) > 0) {
                $pathCoordinates = $parsedPath;
            }
        }

        $route = TransitRoute::create([
            'route_code' => strtoupper(trim($validated['route_code'])),
            'name' => trim($validated['name']),
            'origin' => trim($validated['origin']),
            'destination' => trim($validated['destination']),
            'origin_lat' => $validated['origin_lat'] ?? null,
            'origin_lng' => $validated['origin_lng'] ?? null,
            'destination_lat' => $validated['destination_lat'] ?? null,
            'destination_lng' => $validated['destination_lng'] ?? null,
            'distance_km' => $validated['distance_km'] ?? 0,
            'estimated_minutes' => $validated['estimated_minutes'] ?? 45,
            'status' => $validated['status'] ?? 'active',
            'notes' => $validated['notes'] ?? null,
            'waypoints' => $waypoints,
            'path_coordinates' => $pathCoordinates,
        ]);

        // Assign buses if selected in form
        if ($request->has('bus_ids') && is_array($request->input('bus_ids'))) {
            Bus::whereIn('id', $request->input('bus_ids'))->update([
                'transit_route_id' => $route->id,
                'route' => $route->name,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Route {$route->route_code} created successfully.",
                'route' => $route->toApiArray(),
            ], 201);
        }

        return redirect()->route('routes.index')->with('success', "Route {$route->route_code} created successfully.");
    }

    /**
     * Update an existing route.
     */
    public function update(Request $request, TransitRoute $route): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'route_code' => ['required', 'string', 'max:30', 'unique:transit_routes,route_code,' . $route->id],
            'name' => ['required', 'string', 'max:150'],
            'origin' => ['required', 'string', 'max:150'],
            'destination' => ['required', 'string', 'max:150'],
            'origin_lat' => ['nullable', 'numeric'],
            'origin_lng' => ['nullable', 'numeric'],
            'destination_lat' => ['nullable', 'numeric'],
            'destination_lng' => ['nullable', 'numeric'],
            'distance_km' => ['nullable', 'numeric', 'min:0'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $updateData = [
            'route_code' => strtoupper(trim($validated['route_code'])),
            'name' => trim($validated['name']),
            'origin' => trim($validated['origin']),
            'destination' => trim($validated['destination']),
            'origin_lat' => $validated['origin_lat'] ?? $route->origin_lat,
            'origin_lng' => $validated['origin_lng'] ?? $route->origin_lng,
            'destination_lat' => $validated['destination_lat'] ?? $route->destination_lat,
            'destination_lng' => $validated['destination_lng'] ?? $route->destination_lng,
            'distance_km' => $validated['distance_km'] ?? $route->distance_km,
            'estimated_minutes' => $validated['estimated_minutes'] ?? $route->estimated_minutes,
            'status' => $validated['status'] ?? $route->status,
            'notes' => $validated['notes'] ?? $route->notes,
        ];

        if ($request->filled('path_coordinates')) {
            $parsedPath = is_string($request->input('path_coordinates'))
                ? json_decode($request->input('path_coordinates'), true)
                : $request->input('path_coordinates');
            if (is_array($parsedPath) && count($parsedPath) > 0) {
                $updateData['path_coordinates'] = $parsedPath;
            }
        }

        $originLat = $updateData['origin_lat'];
        $originLng = $updateData['origin_lng'];
        $destLat = $updateData['destination_lat'];
        $destLng = $updateData['destination_lng'];

        $updateData['waypoints'] = [
            ['name' => $updateData['origin'], 'lat' => $originLat !== null ? (float) $originLat : null, 'lng' => $originLng !== null ? (float) $originLng : null, 'type' => 'origin'],
            ['name' => $updateData['destination'], 'lat' => $destLat !== null ? (float) $destLat : null, 'lng' => $destLng !== null ? (float) $destLng : null, 'type' => 'destination'],
        ];

        $route->update($updateData);

        // Sync associated buses
        if ($request->has('bus_ids') && is_array($request->input('bus_ids'))) {
            // Unassign buses that were on this route but no longer selected
            Bus::where('transit_route_id', $route->id)
                ->whereNotIn('id', $request->input('bus_ids'))
                ->update(['transit_route_id' => null]);

            // Assign selected buses
            Bus::whereIn('id', $request->input('bus_ids'))->update([
                'transit_route_id' => $route->id,
                'route' => $route->name,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Route {$route->route_code} updated successfully.",
                'route' => $route->fresh(['buses'])->toApiArray(),
            ]);
        }

        return redirect()->route('routes.index')->with('success', "Route {$route->route_code} updated successfully.");
    }

    /**
     * Delete route.
     */
    public function destroy(Request $request, TransitRoute $route): JsonResponse|RedirectResponse
    {
        $code = $route->route_code;
        Bus::where('transit_route_id', $route->id)->update(['transit_route_id' => null]);
        $route->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Route {$code} deleted.",
            ]);
        }

        return redirect()->route('routes.index')->with('success', "Route {$code} deleted.");
    }

    /**
     * Assign one or multiple buses to this route.
     */
    public function assignBuses(Request $request, TransitRoute $route): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'bus_ids' => ['required', 'array'],
            'bus_ids.*' => ['integer', 'exists:buses,id'],
        ]);

        Bus::whereIn('id', $validated['bus_ids'])->update([
            'transit_route_id' => $route->id,
            'route' => $route->name,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => count($validated['bus_ids']) . " buses assigned to {$route->route_code}.",
            ]);
        }

        return redirect()->route('routes.index')->with('success', "Buses assigned to {$route->route_code}.");
    }
}
