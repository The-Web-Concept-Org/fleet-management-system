<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the operations dashboard backed by live fleet data.
     */
    public function index(): View
    {
        $buses = Bus::with('driver.user')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $totalBuses = $buses->count();
        $activeBuses = $buses->where('status', 'active')->count();
        $standbyBuses = $buses->where('status', 'standby')->count();
        $depotBuses = $buses->whereIn('status', ['depot', 'maintenance'])->count();
        $outOfServiceBuses = $buses->where('status', 'out_of_service')->count();

        $utilizationPct = $totalBuses > 0
            ? (int) round(($activeBuses / $totalBuses) * 100)
            : 0;

        // Fuel spend: Prefer actual FuelLog entries if any exist, fallback to Bus::sum('fuel_cost')
        $dbFuelCost = (float) \App\Models\FuelLog::sum('total_cost');
        $dbFuelLiters = (float) \App\Models\FuelLog::sum('liters');
        $totalFuelCost = $dbFuelCost > 0 ? $dbFuelCost : (float) Bus::sum('fuel_cost');
        $totalFuelLiters = $dbFuelLiters > 0 ? $dbFuelLiters : (float) Bus::sum('fuel_month_liters');
        $avgFuelPrice = $totalFuelLiters > 0
            ? round($totalFuelCost / $totalFuelLiters, 2)
            : 0.0;

        // Maintenance spend backed by MaintenanceLog
        $totalMaintenanceCost = (float) \App\Models\MaintenanceLog::sum('price');
        $completedMaintenanceCount = \App\Models\MaintenanceLog::where('status', 'completed')->count();
        $pendingMaintenanceCount = \App\Models\MaintenanceLog::whereIn('status', ['pending', 'in_progress'])->count();

        // Lifetime distance per vehicle (no per-trip odometer log exists yet).
        $totalOdometer = (int) Bus::sum('odometer');
        $avgOdometerPerBus = $totalBuses > 0
            ? (int) round($totalOdometer / $totalBuses)
            : 0;

        $avgEfficiency = round((float) Bus::whereNotNull('km_per_liter')->avg('km_per_liter'), 1);

        // Buses needing dispatcher attention: out of service or overdue a service.
        $criticalBuses = $buses
            ->filter(fn (Bus $bus) => $bus->status === 'out_of_service' || $bus->next_service_km < 0)
            ->values();
        $alertCount = $criticalBuses->count();
        $primaryAlert = $criticalBuses->first();

        // Next bus due for a scheduled service.
        $nextServiceBus = $buses
            ->filter(fn (Bus $bus) => $bus->next_service_km >= 0)
            ->sortBy('next_service_km')
            ->first();

        return view('dashboard', compact(
            'buses',
            'totalBuses',
            'activeBuses',
            'standbyBuses',
            'depotBuses',
            'outOfServiceBuses',
            'utilizationPct',
            'totalFuelCost',
            'totalFuelLiters',
            'avgFuelPrice',
            'totalMaintenanceCost',
            'completedMaintenanceCount',
            'pendingMaintenanceCount',
            'totalOdometer',
            'avgOdometerPerBus',
            'avgEfficiency',
            'criticalBuses',
            'alertCount',
            'primaryAlert',
            'nextServiceBus'
        ));
    }

    /**
     * Provide real-time live telemetry JSON for live polling & UI updates.
     */
    public function liveTelemetry(): \Illuminate\Http\JsonResponse
    {
        $buses = Bus::with('driver.user')->get();

        $totalBuses = $buses->count();
        $activeBuses = $buses->where('status', 'active')->count();
        $standbyBuses = $buses->where('status', 'standby')->count();
        $depotBuses = $buses->whereIn('status', ['depot', 'maintenance'])->count();
        $outOfServiceBuses = $buses->where('status', 'out_of_service')->count();

        $utilizationPct = $totalBuses > 0
            ? (int) round(($activeBuses / $totalBuses) * 100)
            : 0;

        $dbFuelCost = (float) \App\Models\FuelLog::sum('total_cost');
        $totalFuelCost = $dbFuelCost > 0 ? $dbFuelCost : (float) Bus::sum('fuel_cost');
        $totalMaintenanceCost = (float) \App\Models\MaintenanceLog::sum('price');
        $completedMaintenanceCount = \App\Models\MaintenanceLog::where('status', 'completed')->count();
        $pendingMaintenanceCount = \App\Models\MaintenanceLog::whereIn('status', ['pending', 'in_progress'])->count();
        $totalOdometer = (int) Bus::sum('odometer');

        $criticalBuses = $buses
            ->filter(fn (Bus $bus) => $bus->status === 'out_of_service' || $bus->next_service_km < 0)
            ->values();

        $alerts = $criticalBuses->map(function (Bus $b) {
            return [
                'id' => $b->id,
                'bus_number' => $b->bus_number,
                'plate_number' => $b->plate_number,
                'status' => $b->status,
                'type' => $b->status === 'out_of_service' ? 'out_of_service' : 'overdue_service',
                'message' => $b->status === 'out_of_service' 
                    ? "Bus #{$b->bus_number} flagged out of service" 
                    : "Bus #{$b->bus_number} service overdue by " . abs($b->next_service_km) . " km",
            ];
        });

        $unreadNotificationCount = 0;
        $recentNotifications = [];
        if (auth()->check()) {
            $userId = auth()->id();
            $unreadNotificationCount = \App\Models\AppNotification::where('user_id', $userId)->whereNull('read_at')->count();
            $recentNotifications = \App\Models\AppNotification::where('user_id', $userId)
                ->latest()
                ->take(8)
                ->get()
                ->map(fn ($n) => [
                    'id' => $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'type' => $n->type,
                    'link' => $n->link,
                    'is_read' => $n->read_at !== null,
                    'time_ago' => $n->created_at?->diffForHumans(),
                ]);
        }

        return response()->json([
            'success' => true,
            'timestamp' => now()->toIso8601String(),
            'formatted_time' => now()->format('H:i:s'),
            'unread_notifications_count' => $unreadNotificationCount,
            'recent_notifications' => $recentNotifications,
            'stats' => [
                'totalBuses' => $totalBuses,
                'activeBuses' => $activeBuses,
                'standbyBuses' => $standbyBuses,
                'depotBuses' => $depotBuses,
                'outOfServiceBuses' => $outOfServiceBuses,
                'utilizationPct' => $utilizationPct,
                'totalFuelCost' => $totalFuelCost,
                'totalMaintenanceCost' => $totalMaintenanceCost,
                'completedMaintenanceCount' => $completedMaintenanceCount,
                'pendingMaintenanceCount' => $pendingMaintenanceCount,
                'totalOdometer' => $totalOdometer,
                'alertCount' => $criticalBuses->count(),
            ],
            'alerts' => $alerts,
        ]);
    }
}
