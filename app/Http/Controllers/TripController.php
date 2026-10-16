<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\ShiftLog;
use App\Models\Trip;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TripController extends Controller
{
    /**
     * Display the trip board with fleet stats, filters and assignment data.
     */
    public function index(Request $request): View
    {
        $query = Trip::with('bus.driver.user');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search): void {
                $q->where('trip_number', 'like', "%{$search}%")
                    ->orWhere('origin', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('bus', function ($bq) use ($search): void {
                        $bq->where('bus_number', 'like', "%{$search}%")
                            ->orWhere('plate', 'like', "%{$search}%");
                    })
                    ->orWhereHas('bus.driver.user', function ($uq) use ($search): void {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('date')) {
            $query->whereDate('trip_date', $request->input('date'));
        }

        $trips = $query->orderByDesc('trip_date')
            ->orderBy('departure_time')
            ->orderByDesc('id')
            ->get();

        $totalTrips = Trip::count();
        $scheduledTrips = Trip::where('status', 'scheduled')->count();
        $inProgressTrips = Trip::where('status', 'in_progress')->count();
        $completedTrips = Trip::where('status', 'completed')->count();
        $unassignedTrips = Trip::whereNull('bus_id')->count();

        // Eager load each bus's driver so the form can show which driver comes
        // with a vehicle and the board can display it without extra queries.
        $buses = Bus::with('driver.user')->orderBy('bus_number')->get();

        return view('trips', compact(
            'trips',
            'totalTrips',
            'scheduledTrips',
            'inProgressTrips',
            'completedTrips',
            'unassignedTrips',
            'buses'
        ));
    }

    /**
     * Schedule a new trip and assign a bus and/or driver to it.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'origin' => ['required', 'string', 'max:150'],
            'destination' => ['required', 'string', 'max:150'],
            'trip_date' => ['required', 'date'],
            'departure_time' => ['nullable', 'date_format:H:i'],
            'arrival_time' => ['nullable', 'date_format:H:i', 'after_or_equal:departure_time'],
            'distance_km' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'status' => ['required', 'string', 'in:' . implode(',', Trip::STATUSES)],
            'bus_id' => ['nullable', 'integer', 'exists:buses,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($conflict = $this->findAssignmentConflict($validated)) {
            return $this->conflictResponse($request, $conflict);
        }

        $trip = Trip::create([
            'trip_number' => $this->generateTripNumber(),
            'origin' => trim($validated['origin']),
            'destination' => trim($validated['destination']),
            'trip_date' => $validated['trip_date'],
            'departure_time' => $validated['departure_time'] ?? null,
            'arrival_time' => $validated['arrival_time'] ?? null,
            'distance_km' => $validated['distance_km'] ?? null,
            'status' => $validated['status'],
            'bus_id' => $validated['bus_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$trip->display_id} scheduled successfully.",
                'trip' => $trip->load('bus.driver.user'),
            ]);
        }

        return redirect()->route('trips')->with('success', "{$trip->display_id} scheduled successfully.");
    }

    /**
     * Update an existing trip, including its bus / driver assignment.
     */
    public function update(Request $request, Trip $trip): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'origin' => ['required', 'string', 'max:150'],
            'destination' => ['required', 'string', 'max:150'],
            'trip_date' => ['required', 'date'],
            'departure_time' => ['nullable', 'date_format:H:i'],
            'arrival_time' => ['nullable', 'date_format:H:i', 'after_or_equal:departure_time'],
            'distance_km' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'status' => ['required', 'string', 'in:' . implode(',', Trip::STATUSES)],
            'bus_id' => ['nullable', 'integer', 'exists:buses,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($conflict = $this->findAssignmentConflict($validated, $trip->id)) {
            return $this->conflictResponse($request, $conflict);
        }

        $trip->update([
            'origin' => trim($validated['origin']),
            'destination' => trim($validated['destination']),
            'trip_date' => $validated['trip_date'],
            'departure_time' => $validated['departure_time'] ?? null,
            'arrival_time' => $validated['arrival_time'] ?? null,
            'distance_km' => $validated['distance_km'] ?? null,
            'status' => $validated['status'],
            'bus_id' => $validated['bus_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$trip->display_id} updated successfully.",
                'trip' => $trip->fresh('bus.driver.user'),
            ]);
        }

        return redirect()->route('trips')->with('success', "{$trip->display_id} updated successfully.");
    }

    /**
     * Remove a trip from the board.
     */
    public function destroy(Request $request, Trip $trip): JsonResponse|RedirectResponse
    {
        $tripDisplayId = $trip->display_id;
        $trip->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$tripDisplayId} deleted successfully.",
            ]);
        }

        return redirect()->route('trips')->with('success', "{$tripDisplayId} deleted successfully.");
    }

    /**
     * Detect a double-booking where the same bus is already scheduled on an
     * overlapping trip on the same date. Only meaningful when both times are
     * supplied; trips without times are treated as non-blocking.
     */
    private function findAssignmentConflict(array $data, ?int $ignoreId = null): ?string
    {
        if (empty($data['bus_id']) || empty($data['departure_time']) || empty($data['arrival_time'])) {
            return null;
        }

        $clash = Trip::query()
            ->with('bus')
            ->where('bus_id', $data['bus_id'])
            ->whereDate('trip_date', $data['trip_date'])
            ->whereNotNull('departure_time')
            ->whereNotNull('arrival_time')
            ->where('departure_time', '<', $data['arrival_time'])
            ->where('arrival_time', '>', $data['departure_time'])
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
            ->first();

        if (!$clash) {
            return null;
        }

        $bus = $clash->bus;
        $label = $bus ? $bus->display_id : 'This bus';

        return "{$label} is already assigned to {$clash->display_id} ({$this->formatTime($clash->departure_time)} - {$this->formatTime($clash->arrival_time)}) on that date.";
    }

    private function formatTime(?string $time): string
    {
        return $time ? substr($time, 0, 5) : '--:--';
    }

    private function conflictResponse(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }

        return back()->withErrors(['bus_id' => $message])->withInput();
    }

    /**
     * Sequential, human readable trip reference (TRP-0001, TRP-0002, ...).
     */
    private function generateTripNumber(): string
    {
        $next = (int) (Trip::max('id') ?? 0) + 1;

        do {
            $candidate = 'TRP-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
            $next++;
        } while (Trip::where('trip_number', $candidate)->exists());

        return $candidate;
    }

    /**
     * API list of trips for the authenticated driver's assigned bus.
     *
     * GET /api/trips
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $user = $request->user();
        $driver = $user->driver;
        $bus = $driver?->bus;

        $query = Trip::with(['bus.driver.user', 'shiftLogs']);

        if ($bus) {
            $query->where('bus_id', $bus->id);
        }

        if ($request->filled('date')) {
            $query->whereDate('trip_date', $request->input('date'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $trips = $query->orderByDesc('trip_date')
            ->orderBy('departure_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $trips->map(fn (Trip $t) => $this->formatTripForApi($t))->values(),
        ]);
    }

    /**
     * Get the active in_progress trip for the authenticated driver's bus.
     *
     * GET /api/trips/current
     */
    public function currentTrip(Request $request): JsonResponse
    {
        $user = $request->user();
        $driver = $user->driver;
        $bus = $driver?->bus;

        if (!$bus) {
            return response()->json([
                'success' => true,
                'active' => false,
                'trip' => null,
            ]);
        }

        $active = Trip::where('bus_id', $bus->id)
            ->where('status', 'in_progress')
            ->latest('id')
            ->first();

        return response()->json([
            'success' => true,
            'active' => $active !== null,
            'trip' => $active ? $this->formatTripForApi($active) : null,
        ]);
    }

    /**
     * Start a scheduled trip: marks it in_progress and links to the active shift.
     *
     * POST /api/trips/{trip}/start
     */
    public function startTrip(Request $request, Trip $trip): JsonResponse
    {
        if ($trip->status === 'in_progress') {
            return response()->json([
                'success' => true,
                'message' => "{$trip->display_id} is already in progress.",
                'trip' => $this->formatTripForApi($trip),
            ]);
        }

        if ($trip->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => "{$trip->display_id} has already been completed.",
            ], 422);
        }

        $user = $request->user();
        $bus = $user->driver?->bus ?? $trip->bus;

        // Check if another trip on this bus is currently in_progress
        if ($bus) {
            $otherInProgress = Trip::where('bus_id', $bus->id)
                ->where('status', 'in_progress')
                ->where('id', '!=', $trip->id)
                ->first();
            if ($otherInProgress) {
                return response()->json([
                    'success' => false,
                    'message' => "Another trip ({$otherInProgress->display_id}) is currently in progress. Complete it first.",
                ], 422);
            }
        }

        $trip->update([
            'status' => 'in_progress',
            'departure_time' => $trip->departure_time ?? now()->format('H:i'),
            'bus_id' => $trip->bus_id ?? $bus?->id,
        ]);

        // Link to active shift if available
        if ($bus) {
            $activeShift = ShiftLog::where('bus_id', $bus->id)
                ->where('status', ShiftLog::STATUS_IN_PROGRESS)
                ->latest('id')
                ->first();
            if ($activeShift && !$activeShift->trip_id) {
                $activeShift->update(['trip_id' => $trip->id]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$trip->display_id} started successfully. Drive safely!",
            'trip' => $this->formatTripForApi($trip->fresh('bus')),
        ]);
    }

    /**
     * Complete an in-progress trip and record arrival time & final distance.
     *
     * POST /api/trips/{trip}/complete
     */
    public function completeTrip(Request $request, Trip $trip): JsonResponse
    {
        $validated = $request->validate([
            'distance_km' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $trip->update([
            'status' => 'completed',
            'arrival_time' => $trip->arrival_time ?? now()->format('H:i'),
            'distance_km' => $validated['distance_km'] ?? $trip->distance_km,
            'notes' => !empty($validated['notes']) ? trim($trip->notes . ' ' . $validated['notes']) : $trip->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$trip->display_id} successfully marked as completed.",
            'trip' => $this->formatTripForApi($trip->fresh('bus')),
        ]);
    }

    private function formatTripForApi(Trip $trip): array
    {
        return [
            'id' => $trip->id,
            'trip_number' => $trip->trip_number,
            'display_id' => $trip->display_id,
            'origin' => $trip->origin,
            'destination' => $trip->destination,
            'route_label' => $trip->route_label,
            'trip_date' => $trip->trip_date?->format('Y-m-d'),
            'departure_time' => $trip->departure_time ? substr($trip->departure_time, 0, 5) : null,
            'arrival_time' => $trip->arrival_time ? substr($trip->arrival_time, 0, 5) : null,
            'distance_km' => (float) $trip->distance_km,
            'status' => $trip->status,
            'notes' => $trip->notes,
            'bus_id' => $trip->bus_id,
            'bus_number' => $trip->bus?->display_id,
            'plate' => $trip->bus?->plate,
        ];
    }
}
