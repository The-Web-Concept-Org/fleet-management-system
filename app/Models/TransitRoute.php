<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransitRoute extends Model
{
    use HasFactory;

    protected $table = 'transit_routes';

    protected $fillable = [
        'route_code',
        'name',
        'origin',
        'destination',
        'origin_lat',
        'origin_lng',
        'destination_lat',
        'destination_lng',
        'distance_km',
        'estimated_minutes',
        'waypoints',
        'path_coordinates',
        'status',
        'notes',
    ];

    protected $casts = [
        'origin_lat' => 'float',
        'origin_lng' => 'float',
        'destination_lat' => 'float',
        'destination_lng' => 'float',
        'distance_km' => 'decimal:2',
        'estimated_minutes' => 'integer',
        'waypoints' => 'array',
        'path_coordinates' => 'array',
    ];

    public function buses(): HasMany
    {
        return $this->hasMany(Bus::class, 'transit_route_id');
    }

    public function getWaypointsListAttribute(): array
    {
        return [
            [
                'name' => $this->origin,
                'lat' => $this->origin_lat !== null ? (float) $this->origin_lat : null,
                'lng' => $this->origin_lng !== null ? (float) $this->origin_lng : null,
                'type' => 'origin',
            ],
            [
                'name' => $this->destination,
                'lat' => $this->destination_lat !== null ? (float) $this->destination_lat : null,
                'lng' => $this->destination_lng !== null ? (float) $this->destination_lng : null,
                'type' => 'destination',
            ],
        ];
    }

    public function getPathListAttribute(): array
    {
        return is_array($this->path_coordinates) ? $this->path_coordinates : [];
    }

    public function getAssignedBusesCountAttribute(): int
    {
        return $this->buses()->count();
    }

    public function getCorridorNameAttribute(): string
    {
        return $this->name ?? '';
    }

    public function getOriginTerminalAttribute(): string
    {
        return $this->origin ?? '';
    }

    public function getDestTerminalAttribute(): string
    {
        return $this->destination ?? '';
    }

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'route_code' => $this->route_code,
            'name' => $this->name,
            'origin' => $this->origin,
            'destination' => $this->destination,
            'origin_coordinates' => [
                'latitude' => $this->origin_lat,
                'longitude' => $this->origin_lng,
            ],
            'destination_coordinates' => [
                'latitude' => $this->destination_lat,
                'longitude' => $this->destination_lng,
            ],
            'distance_km' => (float) $this->distance_km,
            'estimated_minutes' => $this->estimated_minutes,
            'waypoints' => $this->waypoints_list,
            'path_coordinates' => $this->path_list,
            'status' => $this->status,
            'notes' => $this->notes,
            'assigned_buses_count' => $this->assigned_buses_count,
        ];
    }
}
