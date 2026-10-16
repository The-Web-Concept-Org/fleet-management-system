<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\FuelLog;
use App\Services\DocumentCompressionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FuelController extends Controller
{
    public function __construct(private readonly DocumentCompressionService $compressor)
    {
    }

    /**
     * Store a fuel record sent from the app.
     *
     * POST /api/fuel
     *  image|fuel_image|receipt_image|photo, liters, type|fuel_type, price,
     *  added_at|added_datetime|datetime, bus_id, trip_id
     */
    public function store(Request $request): JsonResponse
    {
        $this->normalizeRequest($request);

        $validated = $request->validate([
            'bus_id' => ['nullable', 'integer', 'exists:buses,id'],
            'trip_id' => ['nullable', 'integer', 'exists:trips,id'],
            'type' => ['required', 'string', 'max:50', Rule::in(FuelLog::TYPES)],
            'liters' => ['required', 'numeric', 'gt:0', 'max:100000'],
            'price' => ['required', 'numeric', 'min:0', 'max:100000000'],
            'added_at' => ['nullable', function (string $attribute, mixed $value, \Closure $fail): void {
                if ($value !== null && $value !== '' && $this->parseDateTime($value) === null) {
                    $fail('The added_at value is not a valid date/time.');
                }
            }],
            'image' => ['required', 'image', 'max:10240'],
        ]);

        // The vehicle may be sent explicitly or derived from the logged-in
        // driver's currently assigned bus.
        $busId = $validated['bus_id'] ?? $request->user()?->driver?->bus?->id;

        if (!$busId) {
            throw ValidationException::withMessages([
                'bus_id' => 'A vehicle (bus_id) is required for a fuel record.',
            ]);
        }

        $bus = Bus::find($busId);
        $imagePath = $this->storeImage($request->file('image'), (int) $busId);

        $filledAt = $this->parseDateTime($validated['added_at'] ?? null);

        $log = FuelLog::create([
            'reference' => $this->generateReference(),
            'bus_id' => $busId,
            'driver_id' => $bus?->driver_id ?? $request->user()?->driver?->id,
            'trip_id' => $validated['trip_id'] ?? null,
            'fuel_type' => $this->normalizeType($validated['type']),
            'liters' => $validated['liters'],
            'total_cost' => $validated['price'],
            'unit_price' => round((float) $validated['price'] / (float) $validated['liters'], 2),
            'image' => $imagePath,
            'status' => 'pending',
            // Use the datetime sent by the app; fall back to now() only when absent.
            'filled_at' => $filledAt ?? now(),
        ]);

        $response = [
            'success' => true,
            'message' => 'Fuel record added successfully.',
            'fuel' => $log->load(['bus', 'driver.user', 'trip'])->toApiArray(),
        ];

        // Diagnostic: when the app did not send a usable datetime, surface the
        // field names that actually arrived so a naming mismatch is obvious.
        if ($filledAt === null) {
            $received = array_keys($request->except(['image']));
            $response['warning'] = 'No valid added_at date was received, so the current time was stored. '
                . 'Received fields: ' . (empty($received) ? '(none)' : implode(', ', $received))
                . '. Accepted names include added_at, added_datetime, datetime, date_time, date, filled_at.';
        }

        return response()->json($response, 201);
    }

    /**
     * Accept the friendlier field names the app may send and normalise them onto
     * the canonical names used by validation and persistence (including the
     * uploaded image, so any accepted field name satisfies the "image" rule).
     */
    private function normalizeRequest(Request $request): void
    {
        $aliases = [];

        $type = $this->findInput($request, ['type', 'fuel_type', 'fuelType']);
        if ($type !== null) {
            $aliases['type'] = $this->normalizeType((string) $type);
        }

        $liters = $this->findInput($request, ['liters', 'litres', 'quantity', 'volume']);
        if ($liters !== null) {
            $aliases['liters'] = $liters;
        }

        $price = $this->findInput($request, ['price', 'amount', 'total_cost', 'totalCost', 'total_price', 'totalPrice', 'cost']);
        if ($price !== null) {
            $aliases['price'] = $price;
        }

        $addedAt = $this->findInput($request, [
            'added_at', 'addedAt', 'added_datetime', 'addedDatetime',
            'datetime', 'date_time', 'date', 'filled_at', 'filledAt',
            'fuel_date', 'fill_date', 'added_on', 'filled_on', 'timestamp',
        ]);
        if ($addedAt !== null) {
            $aliases['added_at'] = $addedAt;
        }

        $request->merge($aliases);

        $file = $request->file('image')
            ?? $request->file('fuel_image')
            ?? $request->file('receipt_image')
            ?? $request->file('photo');

        if ($file) {
            $request->files->set('image', $file);
        }
    }

    /**
     * Find an input value by any of the accepted key names. Matching is lenient:
     * extra underscores, spaces or casing in the key (e.g. "added_at_") still
     * match, which avoids silent fall-backs caused by a stray character.
     */
    private function findInput(Request $request, array $keys): mixed
    {
        foreach ($keys as $key) {
            $value = $request->input($key);
            if ($value !== null && $value !== '' && !is_array($value)) {
                return $value;
            }
        }

        $normalize = static fn ($key): string => preg_replace('/[^a-z0-9]/', '', strtolower((string) $key));
        $targets = array_map($normalize, $keys);

        foreach ($request->keys() as $key) {
            if (!in_array($normalize($key), $targets, true)) {
                continue;
            }

            $value = $request->input($key);
            if ($value !== null && $value !== '' && !is_array($value)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Parse the datetime sent by the app. Accepts plain dates, ISO strings with
     * or without timezone, and unix timestamps in seconds or milliseconds. The
     * value is kept as-is (no timezone shifting) so the app gets back exactly
     * the moment it recorded.
     */
    private function parseDateTime(mixed $value): ?\Illuminate\Support\Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            if (is_numeric($value)) {
                $number = (float) $value;
                // Milliseconds since epoch are far larger than seconds.
                if ($number > 9999999999) {
                    $number /= 1000;
                }

                return \Illuminate\Support\Carbon::createFromTimestamp((int) $number);
            }

            return \Illuminate\Support\Carbon::parse((string) $value);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Store and compress a fuel image, returning its public disk path.
     */
    private function storeImage(UploadedFile $file, int $busId): string
    {
        $result = $this->compressor->storeAndCompress($file, "fuel/bus-{$busId}", 'public');

        return $result['path'];
    }

    /**
     * Normalise a fuel type to a lower case, underscored token.
     */
    private function normalizeType(string $type): string
    {
        return strtolower(trim(preg_replace('/[\s\-]+/', '_', trim($type))));
    }

    /**
     * Sequential human readable fuel reference (FUL-0001, FUL-0002, ...).
     */
    private function generateReference(): string
    {
        $next = (int) (FuelLog::max('id') ?? 0) + 1;

        do {
            $candidate = 'FUL-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
            $next++;
        } while (FuelLog::where('reference', $candidate)->exists());

        return $candidate;
    }
}
