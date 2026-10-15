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

        // Fuel spend is tracked per bus for the current month.
        $totalFuelCost = (float) Bus::sum('fuel_cost');
        $totalFuelLiters = (int) Bus::sum('fuel_month_liters');
        $avgFuelPrice = $totalFuelLiters > 0
            ? round($totalFuelCost / $totalFuelLiters, 2)
            : 0.0;

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
            'totalOdometer',
            'avgOdometerPerBus',
            'avgEfficiency',
            'criticalBuses',
            'alertCount',
            'primaryAlert',
            'nextServiceBus'
        ));
    }
}
