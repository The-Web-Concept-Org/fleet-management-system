<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'badge_id',
        'phone',
        'license_number',
        'license_type',
        'license_expiry',
        'duty_status',
        'experience_years',
        'notes',
    ];

    protected $casts = [
        'license_expiry' => 'date',
        'experience_years' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The bus currently assigned to this driver (owned by the buses table).
     */
    public function bus(): HasOne
    {
        return $this->hasOne(Bus::class, 'driver_id');
    }

    public function getNameAttribute(): ?string
    {
        return $this->user?->name;
    }

    public function getEmailAttribute(): ?string
    {
        return $this->user?->email;
    }

    public function getStatusAttribute(): string
    {
        return $this->user?->status ?? 'active';
    }

    /**
     * Read-only view of the assigned bus, derived from the bus side.
     */
    public function getAssignedBusAttribute(): ?string
    {
        $bus = $this->bus;

        return $bus?->bus_number ? 'Bus #' . $bus->bus_number : null;
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->bus === null;
    }
}
