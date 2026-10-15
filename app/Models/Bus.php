<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_number',
        'plate',
        'model',
        'operator',
        'driver_id',
        'status',
        'odometer',
        'fuel_month_liters',
        'fuel_cost',
        'km_per_liter',
        'next_service_km',
        'route',
    ];

    protected $casts = [
        'odometer' => 'integer',
        'fuel_month_liters' => 'integer',
        'fuel_cost' => 'decimal:2',
        'km_per_liter' => 'decimal:2',
        'next_service_km' => 'integer',
    ];

    /**
     * The driver currently responsible for this bus (bus side owns the link).
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
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
