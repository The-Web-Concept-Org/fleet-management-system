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
            'added_at' => ['nullable', 'date'],
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
            // Falls back to "now" only when the app does not send its own time.
            'filled_at' => $validated['added_at'] ?? now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fuel record added successfully.',
            'fuel' => $log->load(['bus', 'driver.user', 'trip'])->toApiArray(),
        ], 201);
    }

    /**
     * Accept the friendlier field names the app may send and normalise them onto
     * the canonical names used by validation and persistence (including the
     * uploaded image, so any accepted field name satisfies the "image" rule).
     */
    private function normalizeRequest(Request $request): void
    {
        $aliases = [];

        $type = $request->input('type', $request->input('fuel_type'));
        if ($type !== null && $type !== '') {
            $aliases['type'] = $this->normalizeType((string) $type);
        }

        $liters = $request->input('liters', $request->input('litres', $request->input('quantity')));
        if ($liters !== null && $liters !== '') {
            $aliases['liters'] = $liters;
        }

        $price = $request->input('price', $request->input('amount', $request->input('total_cost', $request->input('total_price'))));
        if ($price !== null && $price !== '') {
            $aliases['price'] = $price;
        }

        $addedAt = $request->input('added_at', $request->input('added_datetime', $request->input('datetime')));
        if ($addedAt !== null && $addedAt !== '') {
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
