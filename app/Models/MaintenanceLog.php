<?php

namespace App\Models;

use App\Support\StorageUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A vehicle maintenance record received from the driver / fleet app: the bill /
 * workshop invoice photo, the amount spent, a free-text note and the vehicle,
 * driver and trip it belongs to. created_at and updated_at are kept as two
 * separate columns.
 */
class MaintenanceLog extends Model
{
    protected $fillable = [
        'reference',
        'bus_id',
        'driver_id',
        'trip_id',
        'shift_id',
        'price',
        'currency',
        'service_type',
        'note',
        'status',
        'flagged_reason',
        'image',
        'odometer',
        'client_uuid',
        'performed_at',
        'recorded_at',
        'synced_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'odometer' => 'decimal:2',
        'performed_at' => 'datetime',
        'recorded_at' => 'datetime',
        'synced_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    public function getImageUrlAttribute(): ?string
    {
        return StorageUrl::for($this->image);
    }

    public function getDisplayIdAttribute(): string
    {
        if ($this->reference) {
            return $this->reference;
        }

        return 'MN-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Serialize the maintenance record into the shape returned by the API.
     */
    public function toApiArray(): array
    {
        $bus = $this->bus;
        $driver = $this->driver;
        $trip = $this->trip;

        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'bus_id' => $this->bus_id,
            'vehicle' => $bus ? [
                'id' => $bus->id,
                'bus_number' => $bus->bus_number,
                'plate' => $bus->plate,
                'model' => $bus->model,
            ] : null,
            'driver_id' => $this->driver_id,
            'driver' => $driver ? [
                'id' => $driver->id,
                'name' => $driver->user?->name,
                'badge_id' => $driver->badge_id,
            ] : null,
            'trip_id' => $this->trip_id,
            'trip' => $trip ? [
                'id' => $trip->id,
                'trip_number' => $trip->trip_number,
                'origin' => $trip->origin,
                'destination' => $trip->destination,
                'status' => $trip->status,
            ] : null,
            'price' => $this->price !== null ? (float) $this->price : null,
            'currency' => $this->currency,
            'service_type' => $this->service_type,
            'note' => $this->note,
            'status' => $this->status,
            'image' => $this->formatImage($this->image),
            'odometer' => $this->odometer !== null ? (float) $this->odometer : null,
            'performed_at' => $this->performed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
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
