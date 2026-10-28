<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Driver;
use App\Models\ShiftLog;
use App\Models\Trip;
use App\Services\DocumentCompressionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ShiftController extends Controller
{
    public function __construct(private readonly DocumentCompressionService $compressor)
    {
    }

    /**
     * Shift audit board: the daily meter log roster plus the inspection desk
     * detail for the shift currently in focus.
     *
     * GET /shifts
     */
    public function index(Request $request): View
    {
        return $this->renderShiftsBoard($request);
    }

    /**
     * Shift detail for the inspection desk. Returns the full shifts board with
     * the requested shift in focus, or the rendered detail partial / json for AJAX.
     *
     * GET /shifts/{shift}
     */
    public function show(Request $request, ShiftLog $shift): View|HttpResponse|JsonResponse
    {
        $shift->load(['bus.transitRoute', 'driver.user', 'trip', 'locations']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'shift' => array_merge($shift->toApiArray(), [
                    'locations' => $shift->locations->map(fn (\App\Models\ShiftLocation $loc) => [
                        'latitude' => $loc->latitude,
                        'longitude' => $loc->longitude,
                        'speed' => $loc->speed,
                        'heading' => $loc->heading,
                        'accuracy' => $loc->accuracy,
                        'recorded_at' => $loc->recorded_at?->toIso8601String(),
                        'is_offline_sync' => $loc->is_offline_sync,
                    ])->values(),
                ]),
            ]);
        }

        if ($request->ajax()) {
            return response()->view('partials.shift-detail', ['shift' => $shift]);
        }

        if ($request->has('board')) {
            return $this->renderShiftsBoard($request, $shift);
        }

        // Dedicated Shift Detail separate page with full breadcrumbs and map!
        return view('shift-detail-page', compact('shift'));
    }

    /**
     * Approve and verify a shift's mileage & telemetry from the Inspection Desk.
     * Requires both start and end meter readings (i.e. shift completed).
     *
     * POST /shifts/{shift}/verify
     */
    public function verify(Request $request, ShiftLog $shift): JsonResponse
    {
        if ($shift->status === ShiftLog::STATUS_IN_PROGRESS || $shift->end_km === null) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot verify shift: both start and end shift meter readings are required before approval.',
            ], 422);
        }

        $shift->update([
            'status' => ShiftLog::STATUS_VERIFIED,
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$shift->display_id} has been verified and approved by the inspection desk.",
            'shift' => $shift->fresh(['bus', 'driver.user', 'trip'])->toApiArray(),
            'status' => ShiftLog::STATUS_VERIFIED,
        ]);
    }

    /**
     * Flag a shift for supervisor audit or meter recount.
     *
     * POST /shifts/{shift}/flag
     */
    public function flag(Request $request, ShiftLog $shift): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $shift->update([
            'status' => ShiftLog::STATUS_FLAGGED,
            'end_notes' => !empty($validated['reason'])
                ? ($shift->end_notes ? $shift->end_notes . ' | Flagged: ' . $validated['reason'] : 'Flagged: ' . $validated['reason'])
                : $shift->end_notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$shift->display_id} has been flagged for audit review.",
            'shift' => $shift->fresh(['bus', 'driver.user', 'trip'])->toApiArray(),
            'status' => ShiftLog::STATUS_FLAGGED,
        ]);
    }

    /**
     * Update shift status directly (e.g. revert from verified to completed).
     *
     * POST /shifts/{shift}/status
     */
    public function updateStatus(Request $request, ShiftLog $shift): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:verified,completed,flagged'],
        ]);

        if ($validated['status'] === ShiftLog::STATUS_VERIFIED && ($shift->status === ShiftLog::STATUS_IN_PROGRESS || $shift->end_km === null)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot verify shift: both start and end shift meter readings are required before approval.',
            ], 422);
        }

        $shift->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$shift->display_id} status updated to {$validated['status']}.",
            'shift' => $shift->fresh(['bus', 'driver.user', 'trip'])->toApiArray(),
            'status' => $validated['status'],
        ]);
    }

    /**
     * Manually adjust shift mileage range (start_km / end_km) from the inspection desk.
     *
     * POST /shifts/{shift}/mileage
     */
    public function adjustMileage(Request $request, ShiftLog $shift): JsonResponse
    {
        $validated = $request->validate([
            'start_km' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'end_km' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'adjustment_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $startKm = (float) $validated['start_km'];
        $endKm = $validated['end_km'] !== null ? (float) $validated['end_km'] : null;

        if ($endKm !== null && $endKm < $startKm) {
            return response()->json([
                'success' => false,
                'message' => "The ending meter reading ({$endKm} km) cannot be lower than the starting reading ({$startKm} km).",
            ], 422);
        }

        $distance = $endKm !== null ? round($endKm - $startKm, 2) : null;
        $reason = $validated['adjustment_reason'] ?? 'Manual odometer calibration by supervisor';

        $shift->update([
            'start_km' => $startKm,
            'end_km' => $endKm,
            'distance_km' => $distance,
            'end_notes' => $shift->end_notes ? $shift->end_notes . ' | Adjusted: ' . $reason : 'Adjusted: ' . $reason,
        ]);

        if ($endKm !== null && $shift->bus && $endKm > $shift->bus->odometer) {
            $shift->bus->update(['odometer' => (int) $endKm]);
        }

        if ($shift->trip && $distance !== null) {
            $shift->trip->update(['distance_km' => $distance]);
        }

        return response()->json([
            'success' => true,
            'message' => "Mileage calibrated: {$startKm} km – " . ($endKm ? "{$endKm} km ({$distance} km)" : 'In Progress'),
            'shift' => $shift->fresh(['bus', 'driver.user', 'trip'])->toApiArray(),
            'distance_km' => $distance,
        ]);
    }

    private function renderShiftsBoard(Request $request, ?ShiftLog $selectedShift = null): View
    {
        $status = (string) $request->input('status', 'all');
        $busId = $request->input('bus_id');
        $driverId = $request->input('driver_id');

        $query = ShiftLog::with(['bus', 'driver.user', 'trip']);

        if ($status === 'verified') {
            $query->where('status', ShiftLog::STATUS_VERIFIED);
        } elseif ($status === 'completed') {
            $query->where('status', ShiftLog::STATUS_COMPLETED)->whereNotNull('end_km');
        } elseif ($status === 'in_progress') {
            $query->where('status', ShiftLog::STATUS_IN_PROGRESS);
        } elseif ($status === 'flagged') {
            $query->where(function ($q) {
                $q->where('status', ShiftLog::STATUS_FLAGGED)
                  ->orWhere(function ($sub) {
                      $sub->where('status', ShiftLog::STATUS_COMPLETED)->whereNull('end_km');
                  });
            });
        } elseif (in_array($status, ShiftLog::STATUSES, true)) {
            $query->where('status', $status);
        }

        if ($busId !== null && $busId !== '') {
            $query->where('bus_id', (int) $busId);
        }

        if ($driverId !== null && $driverId !== '') {
            $query->where('driver_id', (int) $driverId);
        }

        $shifts = $query->orderByDesc('started_at')->orderByDesc('id')->get();

        // The inspection desk always shows a shift: the requested shift (e.g. /shifts/{shift}),
        // or the shift specified by shift_id, or the first row of the current view, or the newest log.
        $selected = $selectedShift
            ?? ($request->input('shift_id') ? ShiftLog::with(['bus', 'driver.user', 'trip'])->find($request->input('shift_id')) : null)
            ?? $shifts->first()
            ?? ShiftLog::with(['bus', 'driver.user', 'trip'])->orderByDesc('id')->first();

        if ($selected && !$selected->relationLoaded('bus')) {
            $selected->load(['bus', 'driver.user', 'trip']);
        }

        $countAll = ShiftLog::count();
        $countOpen = ShiftLog::where('status', ShiftLog::STATUS_IN_PROGRESS)->count();
        $countCompleted = ShiftLog::where('status', ShiftLog::STATUS_COMPLETED)->whereNotNull('end_km')->count();
        $countVerified = ShiftLog::where('status', ShiftLog::STATUS_VERIFIED)->count();
        $countFlagged = ShiftLog::where(function ($q) {
            $q->where('status', ShiftLog::STATUS_FLAGGED)
              ->orWhere(function ($sub) {
                  $sub->where('status', ShiftLog::STATUS_COMPLETED)->whereNull('end_km');
              });
        })->count();

        $totalBuses = Bus::count();
        $activeBuses = Bus::where('status', 'active')->count();
        $dayMileage = (float) ShiftLog::whereDate('started_at', now()->toDateString())->sum('distance_km');

        $buses = Bus::orderBy('bus_number')->get();
        $drivers = Driver::with('user')->get()
            ->sortBy(fn (Driver $driver) => $driver->user->name ?? '')
            ->values();

        return view('shifts', compact(
            'shifts',
            'selected',
            'countAll',
            'countOpen',
            'countCompleted',
            'countVerified',
            'countFlagged',
            'totalBuses',
            'activeBuses',
            'dayMileage',
            'buses',
            'drivers',
            'status',
            'busId',
            'driverId'
        ));
    }

    /**
     * API list of shift records for the driver app or fleet dashboard.
     *
     * GET /api/shifts
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $user = $request->user();
        $busId = $request->input('bus_id') ?? $user?->driver?->bus?->id;
        $driverId = $request->input('driver_id') ?? $user?->driver?->id;

        $query = ShiftLog::with(['bus', 'driver.user', 'trip']);

        if ($busId && $user?->role === 'driver') {
            $query->where('bus_id', $busId);
        } elseif ($busId) {
            $query->where('bus_id', (int) $busId);
        }

        if ($driverId && $user?->role === 'driver') {
            $query->orWhere('driver_id', $driverId);
        }

        $shifts = $query->orderByDesc('started_at')->orderByDesc('id')->paginate((int) $request->input('limit', 20));

        return response()->json([
            'success' => true,
            'data' => collect($shifts->items())->map(fn (ShiftLog $s) => $s->toApiArray())->values(),
            'pagination' => [
                'total' => $shifts->total(),
                'page' => $shifts->currentPage(),
                'last_page' => $shifts->lastPage(),
            ],
        ]);
    }

    /**
     * Return the currently active in_progress shift for the bus/driver.
     *
     * GET /api/shifts/current
     */
    public function current(Request $request): JsonResponse
    {
        $user = $request->user();
        $busId = $request->input('bus_id') ?? $user?->driver?->bus?->id;

        if (!$busId) {
            return response()->json([
                'success' => true,
                'active' => false,
                'shift' => null,
            ]);
        }

        $shift = ShiftLog::with(['bus', 'driver.user', 'trip'])
            ->where('bus_id', $busId)
            ->where('status', ShiftLog::STATUS_IN_PROGRESS)
            ->latest('id')
            ->first();

        return response()->json([
            'success' => true,
            'active' => $shift !== null,
            'shift' => $shift?->toApiArray(),
        ]);
    }

    /**
     * Start a shift for a vehicle: capture the meter photo, the starting meter
     * reading (KM) and the start timestamp sent by the frontend.
     *
     * POST /api/shifts/start
     *  bus_id, trip_id, meter_image|image, start_km|meter_reading,
     *  started_at|start_datetime, notes
     */
    public function start(Request $request): JsonResponse
    {
        $this->mergeAliases($request);

        $validated = $request->validate([
            'bus_id' => ['required', 'integer', 'exists:buses,id'],
            'trip_id' => ['nullable', 'integer', 'exists:trips,id'],
            'start_km' => ['required', 'integer', 'min:0', 'max:99999999'],
            'started_at' => ['nullable', 'date'],
            'start_notes' => ['nullable', 'string', 'max:1000'],
            'meter_image' => ['nullable', 'image', 'max:10240'],
            'image' => ['nullable', 'image', 'max:10240'],
        ]);

        $meterImage = $this->resolveMeterImage($request, true);

        $bus = Bus::with('driver.user')->findOrFail($validated['bus_id']);

        $active = ShiftLog::query()
            ->where('bus_id', $bus->id)
            ->where('status', ShiftLog::STATUS_IN_PROGRESS)
            ->latest('id')
            ->first();

        if ($active) {
            return response()->json([
                'success' => false,
                'message' => "{$bus->display_id} already has an active shift ({$active->display_id}). End it before starting a new one.",
                'shift' => $active->toApiArray(),
            ], 422);
        }

        $trip = null;
        if (!empty($validated['trip_id'])) {
            $trip = Trip::find($validated['trip_id']);

            if (in_array($trip->status, ['completed', 'cancelled'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => "{$trip->display_id} is already {$trip->status} and cannot be started.",
                ], 422);
            }

            if ($trip->bus_id !== null && (int) $trip->bus_id !== (int) $bus->id) {
                return response()->json([
                    'success' => false,
                    'message' => "{$trip->display_id} is assigned to a different vehicle ({$trip->bus->display_id}).",
                ], 422);
            }
        }

        $imagePath = $this->storeMeterImage($meterImage, $bus->id);

        $log = DB::transaction(function () use ($validated, $bus, $trip, $imagePath): ShiftLog {
            $log = ShiftLog::create([
                'shift_number' => $this->generateShiftNumber(),
                'bus_id' => $bus->id,
                'driver_id' => $bus->driver_id,
                'trip_id' => $trip?->id,
                'start_km' => $validated['start_km'],
                'start_meter_image' => $imagePath,
                'start_notes' => $validated['start_notes'] ?? null,
                'status' => ShiftLog::STATUS_IN_PROGRESS,
                'started_at' => (!empty($validated['started_at']) && !Carbon::parse($validated['started_at'])->isFuture())
                    ? Carbon::parse($validated['started_at'])
                    : now(),
            ]);

            // Reflect that this vehicle is now out on the road.
            $bus->update(['status' => 'active']);

            if ($bus->driver) {
                $bus->driver->update(['duty_status' => 'on_route']);
            }

            // The linked scheduled trip is now running.
            if ($trip) {
                $trip->update([
                    'status' => 'in_progress',
                    'bus_id' => $trip->bus_id ?? $bus->id,
                    'departure_time' => $trip->departure_time ?? now()->format('H:i'),
                ]);
            }

            // Record initial Start Location checkpoint
            $startLat = (float) ($request->input('latitude') ?? $bus->last_latitude);
            $startLng = (float) ($request->input('longitude') ?? $bus->last_longitude);
            if ($startLat !== 0.0 || $startLng !== 0.0) {
                \App\Models\ShiftLocation::create([
                    'shift_log_id' => $log->id,
                    'bus_id' => $bus->id,
                    'driver_id' => $bus->driver_id,
                    'latitude' => $startLat,
                    'longitude' => $startLng,
                    'speed' => (float) ($request->input('speed') ?? 0),
                    'heading' => (float) ($request->input('heading') ?? 0),
                    'accuracy' => (float) ($request->input('accuracy') ?? 10),
                    'recorded_at' => $log->started_at ?? now(),
                    'is_offline_sync' => false,
                ]);
            }

            return $log;
        });

        // Notify admins and driver of started shift
        $recipients = \App\Models\User::whereIn('role', ['admin', 'super_admin'])->pluck('id')->all();
        if ($bus->driver?->user_id) {
            $recipients[] = $bus->driver->user_id;
        }
        if ($request->user()) {
            $recipients[] = $request->user()->id;
        }
        $recipients = array_values(array_unique(array_filter($recipients)));

        if (!empty($recipients)) {
            $driverName = $bus->driver?->user?->name ?? 'Driver';
            \App\Support\Notifier::send(
                $recipients,
                'shift',
                'Shift Started',
                "{$driverName} started shift on {$bus->display_id} at {$log->start_km} KM.",
                ['shift_id' => $log->id, 'bus_id' => $bus->id, 'start_km' => $log->start_km],
                route('shifts')
            );
        }

        return response()->json([
            'success' => true,
            'message' => "{$log->display_id} started for {$bus->display_id}.",
            'shift' => $log->toApiArray(),
        ]);
    }

    /**
     * End a vehicle's active shift: capture the closing meter photo, the ending
     * meter reading (KM) and the end timestamp sent by the frontend. The covered
     * distance is calculated automatically, the vehicle odometer is updated and
     * any linked trip is marked completed.
     *
     * POST /api/shifts/end
     *  bus_id, trip_id, meter_image|image, end_km|meter_reading,
     *  ended_at|end_datetime, notes
     */
    public function end(Request $request): JsonResponse
    {
        $this->mergeAliases($request, end: true);

        $validated = $request->validate([
            'bus_id' => ['required', 'integer', 'exists:buses,id'],
            'trip_id' => ['nullable', 'integer', 'exists:trips,id'],
            'end_km' => ['required', 'integer', 'min:0', 'max:99999999'],
            'ended_at' => ['nullable', 'date'],
            'end_notes' => ['nullable', 'string', 'max:1000'],
            'meter_image' => ['nullable', 'image', 'max:10240'],
            'image' => ['nullable', 'image', 'max:10240'],
        ]);

        $meterImage = $this->resolveMeterImage($request, true);

        $bus = Bus::with('driver.user')->findOrFail($validated['bus_id']);

        $log = ShiftLog::query()
            ->where('bus_id', $bus->id)
            ->where('status', ShiftLog::STATUS_IN_PROGRESS)
            ->latest('id')
            ->first();

        if (!$log) {
            return response()->json([
                'success' => false,
                'message' => "No active shift found for {$bus->display_id}. Start a shift before ending one.",
            ], 422);
        }

        if ($validated['end_km'] < $log->start_km) {
            throw ValidationException::withMessages([
                'end_km' => "The ending meter reading ({$validated['end_km']} KM) cannot be lower than the starting reading ({$log->start_km} KM).",
            ]);
        }

        $distance = $validated['end_km'] - $log->start_km;
        $imagePath = $this->storeMeterImage($meterImage, $bus->id);

        // Prefer the trip linked at start; fall back to one sent with the end call.
        $tripId = $log->trip_id ?? $validated['trip_id'] ?? null;
        $trip = $tripId ? Trip::find($tripId) : null;

        DB::transaction(function () use ($log, $bus, $validated, $imagePath, $distance, $trip, $tripId): void {
            $log->update([
                'end_km' => $validated['end_km'],
                'distance_km' => $distance,
                'trip_id' => $tripId,
                'end_meter_image' => $imagePath,
                'end_notes' => $validated['end_notes'] ?? null,
                'status' => ShiftLog::STATUS_COMPLETED,
                'ended_at' => !empty($validated['ended_at']) ? Carbon::parse($validated['ended_at']) : now(),
            ]);

            // Keep the vehicle's odometer in sync with the latest reading.
            if ($validated['end_km'] > $bus->odometer) {
                $bus->update(['odometer' => $validated['end_km']]);
            }

            if ($bus->driver) {
                $bus->driver->update(['duty_status' => 'available']);
            }

            // Close out the linked scheduled trip with the measured distance.
            if ($trip) {
                $trip->update([
                    'status' => 'completed',
                    'arrival_time' => now()->format('H:i'),
                    'distance_km' => $distance,
                    'bus_id' => $trip->bus_id ?? $bus->id,
                ]);
            }

            // Enforce clean 2-checkpoint GPS policy: Start & End only
            $shiftLocs = $log->locations()->orderBy('id', 'asc')->get();
            if ($shiftLocs->count() > 2) {
                $firstId = $shiftLocs->first()->id;
                $lastId = $shiftLocs->last()->id;
                $log->locations()->whereNotIn('id', [$firstId, $lastId])->delete();
            } elseif ($shiftLocs->count() === 1 && ($bus->last_latitude || $request->filled('latitude'))) {
                $endLat = (float) ($request->input('latitude') ?? $bus->last_latitude);
                $endLng = (float) ($request->input('longitude') ?? $bus->last_longitude);
                if ($endLat !== 0.0 || $endLng !== 0.0) {
                    \App\Models\ShiftLocation::create([
                        'shift_log_id' => $log->id,
                        'bus_id' => $bus->id,
                        'driver_id' => $bus->driver?->id,
                        'latitude' => $endLat,
                        'longitude' => $endLng,
                        'speed' => (float) ($request->input('speed') ?? 0),
                        'heading' => (float) ($request->input('heading') ?? 0),
                        'accuracy' => (float) ($request->input('accuracy') ?? 10),
                        'recorded_at' => now(),
                        'is_offline_sync' => false,
                    ]);
                }
            }
        });

        // Notify admins and driver of completed shift awaiting inspection audit
        $adminUserIds = \App\Models\User::whereIn('role', ['admin', 'super_admin'])->pluck('id')->all();
        if ($bus->driver?->user_id) {
            $adminUserIds[] = $bus->driver->user_id;
        }
        if ($request->user()) {
            $adminUserIds[] = $request->user()->id;
        }
        $adminUserIds = array_values(array_unique(array_filter($adminUserIds)));

        if (!empty($adminUserIds)) {
            $driverName = $bus->driver?->user?->name ?? 'Driver';
            \App\Support\Notifier::send(
                $adminUserIds,
                'shift',
                'Shift Closed — Mileage Recorded',
                "{$driverName} closed shift on {$bus->display_id} ({$distance} km). Awaiting inspection desk review.",
                ['shift_id' => $log->id, 'bus_id' => $bus->id, 'distance_km' => $distance],
                route('shifts')
            );
        }

        return response()->json([
            'success' => true,
            'message' => "{$log->display_id} ended. Distance covered: {$log->distance_km} KM.",
            'shift' => $log->toApiArray(),
        ]);
    }

    /**
     * Accept the friendlier client field names (meter_reading, start_datetime /
     * end_datetime) and normalise them onto the canonical field names used by
     * validation and persistence.
     */
    private function mergeAliases(Request $request, bool $end = false): void
    {
        $kmKey = $end ? 'end_km' : 'start_km';
        $timeKey = $end ? 'ended_at' : 'started_at';
        $timeAlias = $end ? 'end_datetime' : 'start_datetime';
        $notesKey = $end ? 'end_notes' : 'start_notes';

        $request->merge([
            $kmKey => $request->input($kmKey, $request->input('meter_reading')),
            $timeKey => $request->input($timeKey, $request->input($timeAlias)),
            $notesKey => $request->input($notesKey, $request->input('notes')),
        ]);
    }

    /**
     * Resolve the uploaded meter photo, accepting either `meter_image` or the
     * shorter `image` field name.
     */
    private function resolveMeterImage(Request $request, bool $required): ?UploadedFile
    {
        $file = $request->file('meter_image') ?? $request->file('image');

        if (!$file || !$file->isValid()) {
            if ($required) {
                throw ValidationException::withMessages([
                    'meter_image' => 'A clear photo of the vehicle meter reading (meter_image) is required.',
                ]);
            }

            return null;
        }

        return $file;
    }

    /**
     * Store and compress a meter photo, returning its public disk path.
     */
    private function storeMeterImage(UploadedFile $file, int $busId): string
    {
        $result = $this->compressor->storeAndCompress($file, "shifts/bus-{$busId}", 'public');

        return $result['path'];
    }

    /**
     * Sequential human readable shift reference (SHF-0001, SHF-0002, ...).
     */
    private function generateShiftNumber(): string
    {
        $next = (int) (ShiftLog::max('id') ?? 0) + 1;

        do {
            $candidate = 'SHF-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
            $next++;
        } while (ShiftLog::where('shift_number', $candidate)->exists());

        return $candidate;
    }
}
