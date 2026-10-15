<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\ShiftLog;
use App\Models\Trip;
use App\Services\DocumentCompressionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ShiftController extends Controller
{
    public function __construct(private readonly DocumentCompressionService $compressor)
    {
    }

    /**
     * Start a shift for a vehicle: capture the meter photo, the starting meter
     * reading (KM) and the fuel detail taken from the dashboard.
     *
     * POST /api/shifts/start
     *  bus_id, trip_id, meter_image|image, start_km|meter_reading,
     *  fuel_level, fuel_liters, notes
     */
    public function start(Request $request): JsonResponse
    {
        $this->mergeAliases($request);

        $validated = $request->validate([
            'bus_id' => ['required', 'integer', 'exists:buses,id'],
            'trip_id' => ['nullable', 'integer', 'exists:trips,id'],
            'start_km' => ['required', 'integer', 'min:0', 'max:99999999'],
            'fuel_level' => ['nullable', 'string', Rule::in(ShiftLog::FUEL_LEVELS)],
            'fuel_liters' => ['nullable', 'numeric', 'min:0', 'max:100000'],
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
                'message' => "Bus #{$bus->bus_number} already has an active shift ({$active->shift_number}). End it before starting a new one.",
                'shift' => $active->toApiArray(),
            ], 422);
        }

        $trip = null;
        if (!empty($validated['trip_id'])) {
            $trip = Trip::find($validated['trip_id']);

            if (in_array($trip->status, ['completed', 'cancelled'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => "Trip {$trip->trip_number} is already {$trip->status} and cannot be started.",
                ], 422);
            }

            if ($trip->bus_id !== null && (int) $trip->bus_id !== (int) $bus->id) {
                return response()->json([
                    'success' => false,
                    'message' => "Trip {$trip->trip_number} is assigned to a different vehicle (Bus #{$trip->bus->bus_number}).",
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
                'start_fuel_level' => $validated['fuel_level'] ?? null,
                'start_fuel_liters' => $validated['fuel_liters'] ?? null,
                'start_meter_image' => $imagePath,
                'start_notes' => $validated['start_notes'] ?? null,
                'status' => ShiftLog::STATUS_IN_PROGRESS,
                'started_at' => now(),
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

            return $log;
        });

        return response()->json([
            'success' => true,
            'message' => "Shift {$log->shift_number} started for Bus #{$bus->bus_number}.",
            'shift' => $log->toApiArray(),
        ]);
    }

    /**
     * End a vehicle's active shift: capture the closing meter photo, the ending
     * meter reading (KM) and the closing fuel detail. The covered distance is
     * calculated automatically, the vehicle odometer is updated and any linked
     * trip is marked completed.
     *
     * POST /api/shifts/end
     *  bus_id, trip_id, meter_image|image, end_km|meter_reading,
     *  fuel_level, fuel_liters, notes
     */
    public function end(Request $request): JsonResponse
    {
        $this->mergeAliases($request, end: true);

        $validated = $request->validate([
            'bus_id' => ['required', 'integer', 'exists:buses,id'],
            'trip_id' => ['nullable', 'integer', 'exists:trips,id'],
            'end_km' => ['required', 'integer', 'min:0', 'max:99999999'],
            'fuel_level' => ['nullable', 'string', Rule::in(ShiftLog::FUEL_LEVELS)],
            'fuel_liters' => ['nullable', 'numeric', 'min:0', 'max:100000'],
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
                'message' => "No active shift found for Bus #{$bus->bus_number}. Start a shift before ending one.",
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
                'end_fuel_level' => $validated['fuel_level'] ?? null,
                'end_fuel_liters' => $validated['fuel_liters'] ?? null,
                'end_meter_image' => $imagePath,
                'end_notes' => $validated['end_notes'] ?? null,
                'status' => ShiftLog::STATUS_COMPLETED,
                'ended_at' => now(),
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
        });

        return response()->json([
            'success' => true,
            'message' => "Shift {$log->shift_number} ended. Distance covered: {$log->distance_km} KM.",
            'shift' => $log->toApiArray(),
        ]);
    }

    /**
     * Accept the friendlier client field names (meter_reading and start_/end_
     * prefixed fuel + notes) and normalise them onto the canonical field names
     * used by validation and persistence.
     */
    private function mergeAliases(Request $request, bool $end = false): void
    {
        $kmKey = $end ? 'end_km' : 'start_km';
        $fuelLevelKey = $end ? 'end_fuel_level' : 'start_fuel_level';
        $fuelLitersKey = $end ? 'end_fuel_liters' : 'start_fuel_liters';
        $notesKey = $end ? 'end_notes' : 'start_notes';

        $request->merge([
            $kmKey => $request->input($kmKey, $request->input('meter_reading')),
            'fuel_level' => $request->input('fuel_level', $request->input($fuelLevelKey)),
            'fuel_liters' => $request->input('fuel_liters', $request->input($fuelLitersKey)),
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
