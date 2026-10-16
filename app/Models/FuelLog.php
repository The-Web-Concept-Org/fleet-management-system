<?php

namespace App\Models;

use App\Support\StorageUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A fuel fill-up received from the driver / fleet app: a receipt or pump photo,
 * the quantity in liters, the fuel type, the price paid and the moment the fuel
 * was added. created_at and updated_at are kept as two separate columns.
 *
 * The app-facing field names (type, price, added_at) map onto the underlying
 * columns (fuel_type, total_cost, filled_at).
 */
class FuelLog extends Model
{
    /**
     * Commonly used fuel types. The stored value is normalised to lower case.
     */
    public const TYPES = [
        'diesel',
        'petrol',
        'gasoline',
        'cng',
        'lpg',
        'hi_octane',
        'super',
        'electric',
        'other',
    ];

    protected $fillable = [
        'reference',
        'driver_id',
        'bus_id',
        'shift_id',
        'trip_id',
        'liters',
        'total_cost',
        'currency',
        'unit_price',
        'fuel_type',
        'station',
        'pump_number',
        'nozzle',
        'fleet_card',
        'receipt_number',
        'odometer',
        'target_cap_liters',
        'distance_since_last_fill_km',
        'efficiency_km_per_litre',
        'receipt_media_id',
        'ocr_confidence',
        'latitude',
        'longitude',
        'status',
        'flagged_reason',
        'filled_at',
        'note',
        'client_uuid',
        'recorded_at',
        'synced_at',
        'image',
    ];

    protected $casts = [
        'liters' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'efficiency_km_per_litre' => 'decimal:2',
        'ocr_confidence' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'odometer' => 'integer',
        'target_cap_liters' => 'integer',
        'distance_since_last_fill_km' => 'integer',
        'filled_at' => 'datetime',
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

        return 'FL-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Serialize the fuel record into the shape returned by the fuel API.
     * App-facing keys (type, price, added_at) are included alongside the raw ones.
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
            'type' => $this->fuel_type,
            'liters' => $this->liters !== null ? (float) $this->liters : null,
            'price' => $this->total_cost !== null ? (float) $this->total_cost : null,
            'currency' => $this->currency,
            'unit_price' => $this->unit_price !== null ? (float) $this->unit_price : null,
            'image' => $this->formatImage($this->image),
            'station' => $this->station,
            'receipt_number' => $this->receipt_number,
            'odometer' => $this->odometer,
            'status' => $this->status,
            'note' => $this->note,
            'added_at' => $this->filled_at?->toIso8601String(),
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
