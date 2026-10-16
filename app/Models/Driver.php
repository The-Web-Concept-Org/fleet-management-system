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
        'shift',
        'avatar',
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

    public function getAvatarUrlAttribute(): ?string
    {
        $path = $this->avatar ?: $this->user?->avatar;
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return \App\Support\StorageUrl::for($path);
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
        return $this->bus?->display_id;
    }

    /**
     * Canonical driver identifier shown on every screen, e.g. "Driver #8802".
     * Stored badges look like "DRV-8802"; only the sequence is displayed.
     */
    public function getDisplayIdAttribute(): string
    {
        $badge = trim((string) $this->badge_id);

        if ($badge === '') {
            return 'Driver #—';
        }

        if (preg_match('/^(?:[A-Za-z]+[-\s_]*)?(\d+)$/', $badge, $matches)) {
            return 'Driver #' . $matches[1];
        }

        return 'Driver #' . $badge;
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->bus === null;
    }
}
