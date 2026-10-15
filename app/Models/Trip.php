<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    public const STATUSES = ['scheduled', 'in_progress', 'completed', 'cancelled'];

    protected $fillable = [
        'trip_number',
        'origin',
        'destination',
        'trip_date',
        'departure_time',
        'arrival_time',
        'distance_km',
        'status',
        'notes',
        'bus_id',
    ];

    protected $casts = [
        'trip_date' => 'date',
        'distance_km' => 'decimal:2',
    ];

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    /**
     * Driver-logged shifts that were run against this scheduled trip.
     */
    public function shiftLogs(): HasMany
    {
        return $this->hasMany(ShiftLog::class);
    }

    /**
     * The trip's driver is never stored on the trip itself; it always comes
     * from the assigned bus, which owns the single driver link.
     */
    public function getDriverAttribute(): ?Driver
    {
        return $this->bus?->driver;
    }

    /**
     * Human readable route label, e.g. "Central Metro -> Airport".
     */
    public function getRouteLabelAttribute(): string
    {
        return trim($this->origin) . ' -> ' . trim($this->destination);
    }

    public function getIsAssignedAttribute(): bool
    {
        return $this->bus_id !== null;
    }
}
