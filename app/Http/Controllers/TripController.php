<?php

namespace App\Http\Controllers;

use App\Models\Bus;
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
                'message' => "Trip {$trip->trip_number} scheduled successfully.",
                'trip' => $trip->load('bus.driver.user'),
            ]);
        }

        return redirect()->route('trips')->with('success', "Trip {$trip->trip_number} scheduled successfully.");
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
                'message' => "Trip {$trip->trip_number} updated successfully.",
                'trip' => $trip->fresh('bus.driver.user'),
            ]);
        }

        return redirect()->route('trips')->with('success', "Trip {$trip->trip_number} updated successfully.");
    }

    /**
     * Remove a trip from the board.
     */
    public function destroy(Request $request, Trip $trip): JsonResponse|RedirectResponse
    {
        $tripNumber = $trip->trip_number;
        $trip->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Trip {$tripNumber} deleted successfully.",
            ]);
        }

        return redirect()->route('trips')->with('success', "Trip {$tripNumber} deleted successfully.");
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
        $label = $bus ? 'Bus #' . $bus->bus_number : 'This bus';

        return "{$label} is already assigned to trip {$clash->trip_number} ({$this->formatTime($clash->departure_time)} - {$this->formatTime($clash->arrival_time)}) on that date.";
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
}
