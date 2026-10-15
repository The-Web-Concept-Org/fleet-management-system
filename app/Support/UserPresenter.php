<?php

namespace App\Support;

use App\Models\Trip;
use App\Models\User;

/**
 * Serializes a user into the shape returned by the driver API.
 *
 * Drivers are enriched with their driver record and currently assigned bus so
 * every endpoint (login, me, profile update) returns an identical payload.
 */
class UserPresenter
{
    public static function format(User $user): array
    {
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
            'last_login_at' => $user->last_login_at?->toIso8601String(),
        ];

        if ($user->role !== 'driver') {
            return $data;
        }

        $driver = $user->driver;

        if (!$driver) {
            return $data;
        }

        $data['driver'] = [
            'id' => $driver->id,
            'user_id' => $driver->user_id,
            'badge_id' => $driver->badge_id,
            'phone' => $driver->phone,
            'license_number' => $driver->license_number,
            'license_type' => $driver->license_type,
            'license_expiry' => $driver->license_expiry?->format('Y-m-d'),
            'duty_status' => $driver->duty_status,
            'experience_years' => $driver->experience_years,
            'notes' => $driver->notes,
        ];

        $bus = $driver->bus;

        $data['bus'] = $bus ? [
            'id' => $bus->id,
            'bus_number' => $bus->bus_number,
            'plate' => $bus->plate,
            'model' => $bus->model,
            'status' => $bus->status,
            'odometer' => $bus->odometer,
            'fuel_month_liters' => $bus->fuel_month_liters,
            'fuel_cost' => $bus->fuel_cost,
            'km_per_liter' => $bus->km_per_liter,
            'next_service_km' => $bus->next_service_km,
            'route' => $bus->route,
        ] : null;

        // The driver's current itinerary is sent straight from the login
        // response: the active trip (if started) plus the upcoming scheduled
        // ones. Completed and cancelled trips are left out.
        $data['trips'] = $bus
            ? $bus->trips()
                ->whereIn('status', ['scheduled', 'in_progress'])
                ->orderBy('trip_date')
                ->orderBy('departure_time')
                ->get()
                ->map(fn (Trip $trip) => [
                    'id' => $trip->id,
                    'trip_number' => $trip->trip_number,
                    'origin' => $trip->origin,
                    'destination' => $trip->destination,
                    'trip_date' => $trip->trip_date?->format('Y-m-d'),
                    'departure_time' => $trip->departure_time ? substr($trip->departure_time, 0, 5) : null,
                    'arrival_time' => $trip->arrival_time ? substr($trip->arrival_time, 0, 5) : null,
                    'distance_km' => $trip->distance_km,
                    'status' => $trip->status,
                    'notes' => $trip->notes,
                ])
                ->values()
                ->all()
            : [];

        return $data;
    }
}
