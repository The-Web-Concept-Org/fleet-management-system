<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_number',
        'plate',
        'model',
        'operator',
        'driver_id',
        'transit_route_id',
        'status',
        'odometer',
        'fuel_month_liters',
        'fuel_cost',
        'km_per_liter',
        'next_service_km',
        'route',
        'last_latitude',
        'last_longitude',
        'last_location_at',
        'is_online',
        'speed',
        'heading',
    ];

    protected $casts = [
        'odometer' => 'integer',
        'fuel_month_liters' => 'integer',
        'fuel_cost' => 'decimal:2',
        'km_per_liter' => 'decimal:2',
        'next_service_km' => 'integer',
        'last_latitude' => 'float',
        'last_longitude' => 'float',
        'last_location_at' => 'datetime',
        'is_online' => 'boolean',
        'speed' => 'float',
        'heading' => 'float',
    ];

    /**
     * The transit route this vehicle is assigned to.
     */
    public function transitRoute(): BelongsTo
    {
        return $this->belongsTo(TransitRoute::class, 'transit_route_id');
    }

    /**
     * Check if vehicle is live online (heartbeat within 5 minutes)
     */
    public function getIsLiveOnlineAttribute(): bool
    {
        return $this->is_online && $this->last_location_at && $this->last_location_at->gt(now()->subMinutes(5));
    }

    /**
     * Human-readable last seen string
     */
    public function getLastSeenHumanAttribute(): string
    {
        if (!$this->last_location_at) {
            return 'No location recorded';
        }

        if ($this->is_live_online) {
            return 'Online now • ' . $this->last_location_at->diffForHumans();
        }

        return 'Offline • Last seen ' . $this->last_location_at->diffForHumans();
    }

    /**
     * Current active shift log if any
     */
    public function activeShift(): ?ShiftLog
    {
        return $this->shiftLogs()->where('status', ShiftLog::STATUS_IN_PROGRESS)->latest('id')->first();
    }

    /**
     * The driver currently responsible for this bus (bus side owns the link).
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Trips this bus has been scheduled on.
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Driver-logged meter / fuel shift logs for this vehicle.
     */
    public function shiftLogs(): HasMany
    {
        return $this->hasMany(ShiftLog::class);
    }

    /**
     * Fuel fill-up records logged against this vehicle.
     */
    public function fuelLogs(): HasMany
    {
        return $this->hasMany(FuelLog::class);
    }

    /**
     * Maintenance records logged against this vehicle.
     */
    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    /**
     * Display name of the assigned driver, falling back to the legacy operator field.
     */
    public function getDriverNameAttribute(): ?string
    {
        return $this->driver?->user?->name ?? $this->operator;
    }

    public function getIsAssignedAttribute(): bool
    {
        return $this->driver_id !== null;
    }

    /**
     * Zero-padded bus number used in badges and identifiers, e.g. "02".
     */
    public function getDisplayNumberAttribute(): string
    {
        return str_pad((string) $this->bus_number, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Canonical bus identifier shown on every screen, e.g. "Bus #02".
     */
    public function getDisplayIdAttribute(): string
    {
        if ($this->bus_number === null || $this->bus_number === '') {
            return 'Unassigned';
        }

        return 'Bus #' . $this->display_number;
    }

    /**
     * Service progress as a percentage against the 10,000 KM service interval.
     */
    public function getServiceProgressAttribute(): int
    {
        if ($this->next_service_km <= 0) {
            return 0;
        }

        return (int) max(0, min(100, round(($this->next_service_km / 10000) * 100)));
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->next_service_km < 0;
    }
}
