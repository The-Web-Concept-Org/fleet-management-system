<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Driver;
use App\Models\MaintenanceLog;
use App\Services\DocumentCompressionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function __construct(private readonly DocumentCompressionService $compressor)
    {
    }

    /**
     * Maintenance audit board: every repair / service logged from the app, with
     * the workshop bill photo, spend totals and the vehicle, driver and trip.
     *
     * GET /maintenance
     */
    public function index(Request $request): View
    {
        $busId = $request->input('bus_id');
        $driverId = $request->input('driver_id');
        $from = $request->input('from');
        $to = $request->input('to');

        $query = MaintenanceLog::with(['bus', 'driver.user', 'trip']);

        if ($busId !== null && $busId !== '') {
            $query->where('bus_id', (int) $busId);
        }

        if ($driverId !== null && $driverId !== '') {
            $query->where('driver_id', (int) $driverId);
        }

        if ($from !== null && $from !== '') {
            $query->whereDate('performed_at', '>=', $from);
        }

        if ($to !== null && $to !== '') {
            $query->whereDate('performed_at', '<=', $to);
        }

        $maintenanceLogs = $query->orderByDesc('performed_at')->orderByDesc('id')->get();

        // Totals reflect the current filter so the header always matches the rows.
        $totalLogs = $maintenanceLogs->count();
        $totalCost = (float) $maintenanceLogs->sum(fn (MaintenanceLog $log) => (float) $log->price);
        $averageCost = $totalLogs > 0 ? $totalCost / $totalLogs : 0.0;
        $pendingCount = $maintenanceLogs->where('status', 'pending')->count();
        $flaggedCount = $maintenanceLogs->filter(fn (MaintenanceLog $log) => $log->flagged_reason !== null)->count();
        $withBillCount = $maintenanceLogs->filter(fn (MaintenanceLog $log) => $log->image !== null)->count();

        $buses = Bus::orderBy('bus_number')->get();
        $drivers = Driver::with('user')->get()
            ->sortBy(fn (Driver $driver) => $driver->user->name ?? '')
            ->values();

        return view('maintenance', compact(
            'maintenanceLogs',
            'totalLogs',
            'totalCost',
            'averageCost',
            'pendingCount',
            'flaggedCount',
            'withBillCount',
            'buses',
            'drivers',
            'busId',
            'driverId',
            'from',
            'to'
        ));
    }

    /**
     * Store a vehicle maintenance record sent from the app.
     *
     * POST /api/maintenance
     *  image|bill_image|receipt_image|photo, price|amount|cost, note|notes|description,
     *  bus_id, driver_id, trip_id, performed_at|maintenance_date|date
     */
    public function store(Request $request): JsonResponse
    {
        $this->normalizeRequest($request);

        $validated = $request->validate([
            'bus_id' => ['nullable', 'integer', 'exists:buses,id'],
            'driver_id' => ['nullable', 'integer', 'exists:drivers,id'],
            'trip_id' => ['nullable', 'integer', 'exists:trips,id'],
            'price' => ['required', 'numeric', 'min:0', 'max:100000000'],
            'note' => ['nullable', 'string', 'max:2000'],
            'service_type' => ['nullable', 'string', 'max:100'],
            'performed_at' => ['nullable', function (string $attribute, mixed $value, \Closure $fail): void {
                if ($value !== null && $value !== '' && $this->parseDateTime($value) === null) {
                    $fail('The performed_at value is not a valid date/time.');
                }
            }],
            'image' => ['required', 'image', 'max:10240'],
        ]);

        // The vehicle may be sent explicitly or derived from the logged-in
        // driver's currently assigned bus.
        $busId = $validated['bus_id'] ?? $request->user()?->driver?->bus?->id;

        if (!$busId) {
            throw ValidationException::withMessages([
                'bus_id' => 'A vehicle (bus_id) is required for a maintenance record.',
            ]);
        }

        $bus = Bus::find($busId);
        $imagePath = $this->storeImage($request->file('image'), (int) $busId);

        $performedAt = $this->parseDateTime($validated['performed_at'] ?? null);

        $log = MaintenanceLog::create([
            'reference' => $this->generateReference(),
            'bus_id' => $busId,
            'driver_id' => $validated['driver_id'] ?? $bus?->driver_id ?? $request->user()?->driver?->id,
            'trip_id' => $validated['trip_id'] ?? null,
            'price' => $validated['price'],
            'service_type' => $validated['service_type'] ?? null,
            'note' => $validated['note'] ?? null,
            'image' => $imagePath,
            'status' => 'pending',
            // Use the datetime sent by the app; fall back to now() only when absent.
            'performed_at' => $performedAt ?? now(),
        ]);

        $response = [
            'success' => true,
            'message' => 'Maintenance record added successfully.',
            'maintenance' => $log->load(['bus', 'driver.user', 'trip'])->toApiArray(),
        ];

        // Diagnostic: when the app did not send a usable datetime, surface the
        // field names that actually arrived so a naming mismatch is obvious.
        if ($performedAt === null) {
            $received = array_keys($request->except(['image']));
            $response['warning'] = 'No valid performed_at date was received, so the current time was stored. '
                . 'Received fields: ' . (empty($received) ? '(none)' : implode(', ', $received))
                . '. Accepted names include performed_at, maintenance_date, date, added_at, datetime.';
        }

        return response()->json($response, 201);
    }

    /**
     * Accept the friendlier field names the app may send and normalise them onto
     * the canonical names used by validation and persistence (including the
     * uploaded bill image, so any accepted field name satisfies the "image" rule).
     */
    private function normalizeRequest(Request $request): void
    {
        // Resolve the uploaded bill image first, before anything reads (and
        // caches) the file bag, so any accepted field name satisfies the
        // "image" rule.
        $this->aliasImage($request);

        $aliases = [];

        $price = $this->findInput($request, ['price', 'amount', 'cost', 'total_cost', 'totalCost', 'total_price', 'totalPrice', 'bill_amount', 'billAmount']);
        if ($price !== null) {
            $aliases['price'] = $price;
        }

        $note = $this->findInput($request, ['note', 'notes', 'description', 'details', 'remark', 'remarks', 'comment', 'comments']);
        if ($note !== null) {
            $aliases['note'] = $note;
        }

        $serviceType = $this->findInput($request, ['service_type', 'serviceType', 'type', 'category', 'work_type', 'workType']);
        if ($serviceType !== null) {
            $aliases['service_type'] = $serviceType;
        }

        $busId = $this->findInput($request, ['bus_id', 'busId', 'vehicle_id', 'vehicleId', 'bus', 'vehicle']);
        if ($busId !== null) {
            $aliases['bus_id'] = $busId;
        }

        $driverId = $this->findInput($request, ['driver_id', 'driverId', 'driver']);
        if ($driverId !== null) {
            $aliases['driver_id'] = $driverId;
        }

        $tripId = $this->findInput($request, ['trip_id', 'tripId', 'trip']);
        if ($tripId !== null) {
            $aliases['trip_id'] = $tripId;
        }

        $performedAt = $this->findInput($request, [
            'performed_at', 'performedAt', 'maintenance_date', 'maintenanceDate',
            'service_date', 'serviceDate', 'added_at', 'addedAt', 'added_datetime',
            'addedDatetime', 'datetime', 'date_time', 'date', 'recorded_at', 'timestamp',
        ]);
        if ($performedAt !== null) {
            $aliases['performed_at'] = $performedAt;
        }

        $request->merge($aliases);
    }

    /**
     * Copy an uploaded bill image sent under any accepted field name onto the
     * canonical "image" key. Request::file() memoises the converted file bag, so
     * the cache is cleared after the copy to make the alias visible everywhere.
     */
    private function aliasImage(Request $request): void
    {
        $file = $request->file('image');

        foreach (['bill_image', 'billImage', 'bill', 'receipt_image', 'receiptImage', 'invoice_image', 'photo'] as $alias) {
            $file ??= $request->file($alias);
        }

        if (!$file) {
            return;
        }

        \Closure::bind(function () use ($file): void {
            $this->files->set('image', $file);
            $this->convertedFiles = null;
        }, $request, $request)();
    }

    /**
     * Find an input value by any of the accepted key names. Matching is lenient:
     * extra underscores, spaces or casing in the key (e.g. "price_") still match,
     * which avoids silent fall-backs caused by a stray character.
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
     * Store and compress a bill image, returning its public disk path.
     */
    private function storeImage(UploadedFile $file, int $busId): string
    {
        $result = $this->compressor->storeAndCompress($file, "maintenance/bus-{$busId}", 'public');

        return $result['path'];
    }

    /**
     * Sequential human readable maintenance reference (MNT-0001, MNT-0002, ...).
     */
    private function generateReference(): string
    {
        $next = (int) (MaintenanceLog::max('id') ?? 0) + 1;

        do {
            $candidate = 'MNT-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
            $next++;
        } while (MaintenanceLog::where('reference', $candidate)->exists());

        return $candidate;
    }
}
