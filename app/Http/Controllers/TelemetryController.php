<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Driver;
use App\Models\ShiftLocation;
use App\Models\ShiftLog;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelemetryController extends Controller
{
    /**
     * Submit GPS location update(s) from the driver's mobile app.
     * Supports both single real-time updates and batch offline synced points.
     *
     * POST /api/telemetry/location
     */
    public function updateLocation(Request $request): JsonResponse
    {
        $user = $request->user();
        $driver = $user?->driver;

        // If user is driver, locate assigned bus
        $bus = $driver?->bus;
        if (!$bus && $request->filled('bus_id')) {
            $bus = Bus::find($request->input('bus_id'));
        }
        if (!$bus && $driver) {
            $bus = Bus::where('driver_id', $driver->id)->first();
        }
        if (!$bus) {
            $bus = Bus::first();
        }

        // Active shift
        $activeShift = $driver
            ? ShiftLog::where('driver_id', $driver->id)
                ->where('status', ShiftLog::STATUS_IN_PROGRESS)
                ->latest('id')
                ->first()
            : null;

        if (!$activeShift && $request->filled('shift_id')) {
            $activeShift = ShiftLog::find($request->input('shift_id'));
        }

        $incomingLocations = [];

        // Check if batch payload or single payload
        if ($request->has('locations') && is_array($request->input('locations'))) {
            $incomingLocations = $request->input('locations');
        } elseif ($request->filled('latitude') && $request->filled('longitude')) {
            $incomingLocations = [[
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'speed' => $request->input('speed'),
                'heading' => $request->input('heading'),
                'accuracy' => $request->input('accuracy'),
                'timestamp' => $request->input('timestamp') ?? now()->toIso8601String(),
                'is_offline_sync' => (bool) $request->input('is_offline_sync', false),
            ]];
        }

        if (empty($incomingLocations)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid location coordinates provided.',
            ], 422);
        }

        $recordedCount = 0;
        $latestPoint = null;

        foreach ($incomingLocations as $loc) {
            $lat = (float) ($loc['latitude'] ?? 0);
            $lng = (float) ($loc['longitude'] ?? 0);

            if ($lat === 0.0 && $lng === 0.0) {
                continue;
            }

            $speed = isset($loc['speed']) ? (float) $loc['speed'] : null;
            $heading = isset($loc['heading']) ? (float) $loc['heading'] : null;
            $accuracy = isset($loc['accuracy']) ? (float) $loc['accuracy'] : null;
            $isOffline = !empty($loc['is_offline_sync']);

            $recordedAt = !empty($loc['timestamp'])
                ? Carbon::parse($loc['timestamp'])
                : now();

            if ($activeShift) {
                ShiftLocation::create([
                    'shift_log_id' => $activeShift->id,
                    'bus_id' => $bus?->id,
                    'driver_id' => $driver?->id,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'speed' => $speed,
                    'heading' => $heading,
                    'accuracy' => $accuracy,
                    'recorded_at' => $recordedAt,
                    'is_offline_sync' => $isOffline,
                ]);
                $recordedCount++;
            }

            $latestPoint = [
                'lat' => $lat,
                'lng' => $lng,
                'speed' => $speed,
                'heading' => $heading,
                'recorded_at' => $recordedAt,
            ];
        }

        // Update the vehicle's last known position and set online status
        if ($bus && $latestPoint) {
            $bus->update([
                'last_latitude' => $latestPoint['lat'],
                'last_longitude' => $latestPoint['lng'],
                'speed' => $latestPoint['speed'] ?? $bus->speed,
                'heading' => $latestPoint['heading'] ?? $bus->heading,
                'is_online' => true,
                'last_location_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully.',
            'synced_points' => $recordedCount,
            'bus_id' => $bus?->id,
            'is_online' => true,
            'last_location' => $latestPoint,
        ]);
    }

    /**
     * Provide real-time live fleet positions for both web admin and mobile.
     * Includes online status, speed, active route, and driver info.
     *
     * GET /api/telemetry/buses
     * GET /telemetry/live
     */
    public function getLiveBuses(): JsonResponse
    {
        $buses = Bus::with(['driver.user', 'transitRoute'])
            ->orderBy('bus_number')
            ->get();

        $activeShiftLogs = ShiftLog::where('status', ShiftLog::STATUS_IN_PROGRESS)
            ->with('driver.user')
            ->get()
            ->keyBy('bus_id');

        $fiveMinutesAgo = now()->subMinutes(5);

        $data = $buses->map(function (Bus $bus) use ($activeShiftLogs, $fiveMinutesAgo) {
            $activeShift = $activeShiftLogs->get($bus->id);

            // A bus is considered online if marked online AND updated in last 5 minutes
            $isOnline = $bus->is_online && $bus->last_location_at && $bus->last_location_at->gt($fiveMinutesAgo);

            return [
                'id' => $bus->id,
                'bus_number' => $bus->bus_number,
                'display_number' => $bus->display_number,
                'display_id' => $bus->display_id,
                'plate' => $bus->plate,
                'model' => $bus->model,
                'status' => $bus->status,
                'is_online' => $isOnline,
                'latitude' => $bus->last_latitude ? (float) $bus->last_latitude : null,
                'longitude' => $bus->last_longitude ? (float) $bus->last_longitude : null,
                'last_latitude' => $bus->last_latitude ? (float) $bus->last_latitude : null,
                'last_longitude' => $bus->last_longitude ? (float) $bus->last_longitude : null,
                'speed' => $bus->speed !== null ? (float) $bus->speed : 0,
                'heading' => $bus->heading !== null ? (float) $bus->heading : 0,
                'last_location_at' => $bus->last_location_at?->toIso8601String(),
                'last_seen_human' => $bus->last_seen_human,
                'driver' => $bus->driver ? [
                    'id' => $bus->driver->id,
                    'name' => $bus->driver->user?->name,
                    'avatar_url' => $bus->driver->avatar_url,
                    'phone' => $bus->driver->phone,
                    'badge_number' => $bus->driver->badge_number,
                ] : null,
                'transit_route' => $bus->transitRoute ? [
                    'id' => $bus->transitRoute->id,
                    'route_code' => $bus->transitRoute->route_code,
                    'name' => $bus->transitRoute->name,
                    'origin' => $bus->transitRoute->origin,
                    'destination' => $bus->transitRoute->destination,
                ] : null,
                'route' => $bus->transitRoute ? [
                    'id' => $bus->transitRoute->id,
                    'code' => $bus->transitRoute->route_code,
                    'name' => $bus->transitRoute->name,
                    'origin' => $bus->transitRoute->origin,
                    'destination' => $bus->transitRoute->destination,
                ] : ($bus->route ? ['name' => $bus->route] : null),
                'active_shift' => $activeShift ? [
                    'id' => $activeShift->id,
                    'shift_number' => $activeShift->shift_number,
                    'display_id' => $activeShift->display_id,
                    'driver_name' => $activeShift->driver?->user?->name ?? 'Driver',
                    'started_at' => $activeShift->started_at?->toIso8601String(),
                    'start_km' => $activeShift->start_km,
                ] : null,
            ];
        });

        $onlineCount = $data->where('is_online', true)->count();
        $offlineCount = $data->where('is_online', false)->count();

        return response()->json([
            'success' => true,
            'timestamp' => now()->toIso8601String(),
            'total_buses' => $buses->count(),
            'online_count' => $onlineCount,
            'offline_count' => $offlineCount,
            'buses' => $data->values(),
        ]);
    }
}
