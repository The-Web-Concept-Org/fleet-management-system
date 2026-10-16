<?php

namespace App\Models;

use App\Support\StorageUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A driver-logged shift: the meter (odometer) and fuel readings taken from a
 * dashboard photo when a vehicle leaves the depot and again when it returns.
 *
 * A shift may optionally be linked to a scheduled trip (the ones returned in
 * the driver login response) so completing the shift also completes the trip.
 */
class ShiftLog extends Model
{
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    public const STATUSES = [self::STATUS_IN_PROGRESS, self::STATUS_COMPLETED];

    protected $fillable = [
        'shift_number',
        'bus_id',
        'driver_id',
        'trip_id',
        'start_km',
        'end_km',
        'distance_km',
        'start_meter_image',
        'end_meter_image',
        'start_notes',
        'end_notes',
        'status',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'start_km' => 'integer',
        'end_km' => 'integer',
        'distance_km' => 'decimal:2',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Canonical shift identifier shown on every screen, e.g. "Shift #0001".
     * Stored references look like "SHF-0001"; only the sequence is displayed.
     */
    public function getDisplayIdAttribute(): string
    {
        $number = trim((string) $this->shift_number);

        if ($number === '') {
            return 'Shift #—';
        }

        $digits = preg_replace('/\D+/', '', $number);

        return 'Shift #' . ($digits === '' ? $number : str_pad($digits, 3, '0', STR_PAD_LEFT));
    }

    /**
     * Serialize the shift into the shape returned by the shift start / end API.
     */
    public function toApiArray(): array
    {
        $bus = $this->bus;
        $driver = $this->driver;
        $trip = $this->trip;

        return [
            'id' => $this->id,
            'shift_number' => $this->shift_number,
            'status' => $this->status,
            'is_active' => $this->is_active,
            'vehicle' => $bus ? [
                'id' => $bus->id,
                'bus_number' => $bus->bus_number,
                'plate' => $bus->plate,
                'model' => $bus->model,
                'odometer' => $bus->odometer,
            ] : null,
            'driver' => $driver ? [
                'id' => $driver->id,
                'name' => $driver->user?->name,
                'badge_id' => $driver->badge_id,
            ] : null,
            'trip' => $trip ? [
                'id' => $trip->id,
                'trip_number' => $trip->trip_number,
                'origin' => $trip->origin,
                'destination' => $trip->destination,
                'status' => $trip->status,
            ] : null,
            'start_km' => $this->start_km,
            'end_km' => $this->end_km,
            'distance_km' => $this->distance_km !== null ? (float) $this->distance_km : null,
            'start_meter_image' => $this->formatImage($this->start_meter_image),
            'end_meter_image' => $this->formatImage($this->end_meter_image),
            'start_notes' => $this->start_notes,
            'end_notes' => $this->end_notes,
            'started_at' => $this->started_at?->toIso8601String(),
            'ended_at' => $this->ended_at?->toIso8601String(),
        ];
    }

    private function formatImage(?string $path): ?array
    {
        if (!$path) {
            return null;
        }

        return [
            'path' => $path,
            'url' => StorageUrl::for($path),
        ];
    }
}
