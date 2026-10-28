<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Driver;
use App\Models\TransitRoute;
use App\Support\DriverMailer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BusController extends Controller
{
    /**
     * Display a listing of buses with fleet stats and filters.
     */
    public function index(Request $request): View
    {
        $query = Bus::with(['driver.user', 'transitRoute']);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search): void {
                $q->where('bus_number', 'like', "%{$search}%")
                    ->orWhere('plate', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('operator', 'like', "%{$search}%")
                    ->orWhere('route', 'like', "%{$search}%")
                    ->orWhereHas('driver.user', function ($uq) use ($search): void {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('model') && $request->input('model') !== 'all') {
            $query->where('model', $request->input('model'));
        }

        if ($request->filled('route') && $request->input('route') !== 'all') {
            if ($request->input('route') === 'unassigned') {
                $query->where(function ($q) {
                    $q->whereNull('route')->orWhere('route', '');
                });
            } else {
                $query->where('route', $request->input('route'));
            }
        }

        $buses = $query->orderByDesc('created_at')->orderByDesc('id')->get();

        $totalBuses = Bus::count();
        $activeBuses = Bus::where('status', 'active')->count();
        $depotBuses = Bus::whereIn('status', ['depot', 'maintenance'])->count();
        $outOfServiceBuses = Bus::where('status', 'out_of_service')->count();
        $avgEfficiency = round((float) Bus::whereNotNull('km_per_liter')->avg('km_per_liter'), 1);

        // Distinct existing routes for filter & suggestions
        $routes = Bus::query()->whereNotNull('route')->where('route', '!=', '')->distinct()->orderBy('route')->pluck('route')->all();

        // Every driver plus their current bus, so the UI can show only the
        // available ones (drivers with no bus) while still displaying the
        // driver already assigned to a bus being edited.
        $drivers = Driver::with(['user', 'bus'])->get()
            ->sortBy(fn (Driver $driver) => $driver->user->name ?? '')
            ->values();

        // All managed transit routes from database
        $transitRoutes = TransitRoute::where('status', 'active')->orderBy('route_code')->get();

        return view('buses', compact(
            'buses',
            'totalBuses',
            'activeBuses',
            'depotBuses',
            'outOfServiceBuses',
            'avgEfficiency',
            'drivers',
            'routes',
            'transitRoutes'
        ));
    }

    /**
     * Store a newly created bus in the fleet.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'bus_number' => ['required', 'string', 'max:50'],
            'plate' => ['required', 'string', 'max:50', 'unique:buses,plate'],
            'model' => ['nullable', 'string', 'max:100'],
            'driver_id' => ['nullable', 'integer', 'exists:drivers,id', Rule::unique('buses', 'driver_id')],
            'status' => ['required', 'string', 'in:active,standby,depot,maintenance,out_of_service'],
            'odometer' => ['nullable', 'integer', 'min:0'],
            'fuel_month_liters' => ['nullable', 'integer', 'min:0'],
            'km_per_liter' => ['nullable', 'numeric', 'min:0', 'max:99.99'],
            'next_service_km' => ['nullable', 'integer'],
            'transit_route_id' => ['nullable', 'integer', 'exists:transit_routes,id'],
            'route' => ['nullable', 'string', 'max:150'],
            'operator' => ['nullable', 'string', 'max:100'],
        ]);

        $busNumber = $this->normalizeBusNumber($validated['bus_number']);

        if (Bus::where('bus_number', $busNumber)->exists()) {
            return $this->duplicateBusResponse($request, $busNumber);
        }

        $transitRouteId = !empty($validated['transit_route_id']) ? (int) $validated['transit_route_id'] : null;
        $routeName = null;
        if ($transitRouteId) {
            $tr = TransitRoute::find($transitRouteId);
            if ($tr) {
                $routeName = "{$tr->route_code} - {$tr->corridor_name}";
            }
        } elseif (!empty($validated['route'])) {
            $routeName = trim($validated['route']);
        }

        $bus = Bus::create([
            'bus_number' => $busNumber,
            'plate' => strtoupper(trim($validated['plate'])),
            'model' => $validated['model'] ?? null,
            'operator' => $validated['operator'] ?? null,
            'driver_id' => $validated['driver_id'] ?? null,
            'status' => $validated['status'],
            'odometer' => $validated['odometer'] ?? 0,
            'fuel_month_liters' => $validated['fuel_month_liters'] ?? 0,
            'fuel_cost' => 0,
            'km_per_liter' => $validated['km_per_liter'] ?? null,
            'next_service_km' => $validated['next_service_km'] ?? 10000,
            'transit_route_id' => $transitRouteId,
            'route' => $routeName,
        ]);

        $this->notifyAssignment($bus, null);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$bus->display_id} added to the fleet successfully.",
                'bus' => $bus,
            ]);
        }

        return redirect()->route('buses')->with('success', "{$bus->display_id} added to the fleet successfully.");
    }

    /**
     * Update the specified bus.
     */
    public function update(Request $request, Bus $bus): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'bus_number' => ['required', 'string', 'max:50'],
            'plate' => ['required', 'string', 'max:50', 'unique:buses,plate,' . $bus->id],
            'model' => ['nullable', 'string', 'max:100'],
            'driver_id' => ['nullable', 'integer', 'exists:drivers,id', Rule::unique('buses', 'driver_id')->ignore($bus->id)],
            'status' => ['required', 'string', 'in:active,standby,depot,maintenance,out_of_service'],
            'odometer' => ['nullable', 'integer', 'min:0'],
            'fuel_month_liters' => ['nullable', 'integer', 'min:0'],
            'km_per_liter' => ['nullable', 'numeric', 'min:0', 'max:99.99'],
            'next_service_km' => ['nullable', 'integer'],
            'transit_route_id' => ['nullable'],
            'route' => ['nullable', 'string', 'max:150'],
            'operator' => ['nullable', 'string', 'max:100'],
        ]);

        $busNumber = $this->normalizeBusNumber($validated['bus_number']);

        if (Bus::where('bus_number', $busNumber)->whereKeyNot($bus->id)->exists()) {
            return $this->duplicateBusResponse($request, $busNumber);
        }

        $previousDriverId = $bus->driver_id;

        $transitRouteId = !empty($validated['transit_route_id']) ? (int) $validated['transit_route_id'] : null;
        $routeName = null;
        if ($transitRouteId) {
            $tr = TransitRoute::find($transitRouteId);
            if ($tr) {
                $routeName = "{$tr->route_code} - {$tr->corridor_name}";
            }
        } elseif (array_key_exists('route', $validated)) {
            $routeName = !empty($validated['route']) ? trim($validated['route']) : null;
        } else {
            $routeName = $bus->route;
        }

        $bus->update([
            'bus_number' => $busNumber,
            'plate' => strtoupper(trim($validated['plate'])),
            'model' => $validated['model'] ?? null,
            'operator' => $validated['operator'] ?? $bus->operator,
            'driver_id' => $validated['driver_id'] ?? null,
            'status' => $validated['status'],
            'odometer' => array_key_exists('odometer', $validated) && $validated['odometer'] !== null ? $validated['odometer'] : $bus->odometer,
            'fuel_month_liters' => $validated['fuel_month_liters'] ?? $bus->fuel_month_liters,
            'km_per_liter' => $validated['km_per_liter'] ?? $bus->km_per_liter,
            'next_service_km' => array_key_exists('next_service_km', $validated) && $validated['next_service_km'] !== null ? $validated['next_service_km'] : $bus->next_service_km,
            'transit_route_id' => $transitRouteId,
            'route' => $routeName,
        ]);

        $this->notifyAssignment($bus, $previousDriverId);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$bus->display_id} updated successfully.",
                'bus' => $bus->fresh(['driver.user', 'transitRoute']),
            ]);
        }

        return redirect()->route('buses')->with('success', "{$bus->display_id} updated successfully.");
    }

    /**
     * Remove the specified bus from the fleet.
     */
    public function destroy(Request $request, Bus $bus): JsonResponse|RedirectResponse
    {
        $busDisplayId = $bus->display_id;
        $bus->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$busDisplayId} removed from the fleet.",
            ]);
        }

        return redirect()->route('buses')->with('success', "{$busDisplayId} removed from the fleet.");
    }

    /**
     * Send the assignment email only when a driver is newly assigned to the bus.
     */
    private function notifyAssignment(Bus $bus, ?int $previousDriverId): void
    {
        if (!$bus->driver_id || $bus->driver_id === $previousDriverId) {
            return;
        }

        $driver = Driver::with('user')->find($bus->driver_id);

        if ($driver) {
            DriverMailer::sendBusAssignment($bus, $driver);
        }
    }

    private function duplicateBusResponse(Request $request, string $busNumber): JsonResponse|RedirectResponse
    {
        $message = 'Bus #' . str_pad($busNumber, 2, '0', STR_PAD_LEFT) . ' already exists in the fleet.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }

        return back()->withErrors(['bus_number' => $message])->withInput();
    }

    /**
     * Fast update for assigning or modifying a bus service route.
     *
     * POST /buses/{bus}/route
     */
    public function updateRoute(Request $request, Bus $bus): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'transit_route_id' => ['nullable'],
            'route' => ['nullable', 'string', 'max:150'],
        ]);

        $transitRouteId = !empty($validated['transit_route_id']) ? (int) $validated['transit_route_id'] : null;
        $routeName = null;

        if ($transitRouteId) {
            $tr = TransitRoute::find($transitRouteId);
            if ($tr) {
                $routeName = "{$tr->route_code} - {$tr->corridor_name}";
            }
        } elseif (!empty($validated['route'])) {
            $routeName = trim($validated['route']);
        }

        $bus->update([
            'transit_route_id' => $transitRouteId,
            'route' => $routeName,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Service route updated for {$bus->display_id}.",
                'route' => $bus->route,
                'transit_route_id' => $bus->transit_route_id,
            ]);
        }

        return redirect()->route('buses')->with('success', "Service route updated for {$bus->display_id}.");
    }

    /**
     * Strip a leading "Bus" prefix / "#" so identifiers stay consistent.
     */
    private function normalizeBusNumber(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/^bus\s*#?\s*/i', '', $value) ?? $value;
        $value = ltrim($value, '#');

        return trim($value);
    }
}
