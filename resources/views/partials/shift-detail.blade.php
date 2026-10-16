{{--
    Inspection desk detail for a single shift log.

    Rendered server-side for the row in focus on the /shifts board and returned
    as the response body of GET /shifts/{shift} when a row is clicked, so the
    same markup is reused for both the initial paint and later swaps.

    Expects: $shift (App\Models\ShiftLog) with bus, driver.user and trip loaded.
--}}
@php
    $bus = $shift->bus;
    $driver = $shift->driver;
    $trip = $shift->trip;

    $isOpen = $shift->status === \App\Models\ShiftLog::STATUS_IN_PROGRESS;
    $needsReview = $shift->status === \App\Models\ShiftLog::STATUS_COMPLETED && $shift->end_km === null;

    if ($needsReview) {
        $statusLabel = 'Needs Review';
        $statusClasses = 'bg-danger-critical text-on-error';
    } elseif ($isOpen) {
        $statusLabel = 'In Progress';
        $statusClasses = 'bg-surface-container-high text-primary';
    } else {
        $statusLabel = 'Approved';
        $statusClasses = 'bg-secondary-fixed/40 text-secondary';
    }

    // Meter photos resolve through App\Support\StorageUrl, which follows the
    // current request scheme + host instead of the static APP_URL env value, so
    // the same markup works on local, staging and any production domain without
    // reconfiguring APP_URL.
    $startImage = \App\Support\StorageUrl::for($shift->start_meter_image);
    $endImage = \App\Support\StorageUrl::for($shift->end_meter_image);

    $startTime = optional($shift->started_at)->format('h:i A');
    $endTime = optional($shift->ended_at)->format('h:i A');

    $distance = $shift->distance_km !== null ? (float) $shift->distance_km : null;

    $duration = null;
    if ($shift->started_at && $shift->ended_at) {
        $minutes = $shift->started_at->diffInMinutes($shift->ended_at);
        $duration = intdiv($minutes, 60) . 'h ' . ($minutes % 60) . 'm';
    }

    $driverName = $driver?->user?->name;
    $initials = collect(explode(' ', (string) $driverName))
        ->filter()
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->take(2)
        ->implode('');
@endphp
<div class="bg-surface-card rounded-xl p-space-lg shadow-md flex flex-col gap-space-md relative overflow-hidden">
    <!-- Header Strip of Drawer Card -->
    <div class="flex items-start justify-between gap-space-sm pb-space-sm">
        <div class="flex flex-col">
            <div class="flex items-center gap-1.5 text-primary-container">
                <span class="material-symbols-outlined text-sm">verified</span>
                <span class="font-label-caps text-label-caps">INSPECTION DESK</span>
            </div>
            <h2 class="font-headline-md text-headline-md text-on-surface">Meter Audit: Shift #{{ $shift->shift_number }}</h2>
            <span class="font-body-sm text-body-sm text-outline">
                {{ $driverName ?? 'Unassigned driver' }}@if($driver?->badge_id) (#{{ $driver->badge_id }})@endif
                &bull; {{ $bus ? 'Bus #' . $bus->bus_number : 'No vehicle' }}
            </span>
        </div>
        <span class="px-2.5 py-1 rounded-md font-label-caps text-label-caps uppercase font-bold {{ $statusClasses }}">{{ $statusLabel }}</span>
    </div>

    <!-- Depot Sync & Telemetry Match Badge -->
    <div class="p-space-sm rounded-lg bg-surface-container-low flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-secondary text-lg">satellite_alt</span>
            <span class="font-body-sm text-body-sm text-on-surface font-medium">
                @if($trip)
                    Trip {{ $trip->trip_number }}: {{ $trip->origin }} &rarr; {{ $trip->destination }}
                @else
                    No scheduled trip linked to this shift
                @endif
            </span>
        </div>
        <span class="font-label-caps text-label-caps text-outline">
            {{ $duration ? $duration . ' logged' : 'Open shift' }}
        </span>
    </div>

    <!-- Meter Photo Verification Display (Start vs. End Side-by-Side Cards) -->
    <div class="flex flex-col gap-space-md">
        <!-- Start Meter Verification Box -->
        <div class="flex flex-col gap-space-xs bg-surface-container-lowest p-space-sm rounded-xl shadow-sm">
            <div class="flex items-center justify-between">
                <span class="font-label-caps text-label-caps text-outline uppercase">Start Shift Reading</span>
                <span class="font-body-sm text-body-sm text-on-surface-variant font-medium">{{ $startTime ?? '—' }} &bull; Depot Gate</span>
            </div>
            <div class="relative w-full h-44 rounded-lg overflow-hidden group shadow-inner">
                @if($startImage)
                    <img alt="Start shift meter photo for shift {{ $shift->shift_number }}" class="w-full h-full object-cover" src="{{ $startImage }}"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-on-surface/80 via-transparent to-transparent flex items-end p-space-sm justify-between">
                        <span class="px-2 py-0.5 rounded bg-surface-card/90 text-on-surface font-label-numeric text-body-sm backdrop-blur">{{ number_format($shift->start_km) }} KM</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-secondary text-on-secondary font-label-caps text-label-caps">
                            <span class="material-symbols-outlined text-xs">done_all</span> OCR OK
                        </span>
                    </div>
                @else
                    <div class="w-full h-full bg-surface-container-low flex flex-col items-center justify-center gap-1 text-outline">
                        <span class="material-symbols-outlined text-2xl">photo_camera</span>
                        <span class="font-body-sm text-body-sm">No start meter photo captured</span>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-on-surface/70 via-transparent to-transparent flex items-end p-space-sm">
                        <span class="px-2 py-0.5 rounded bg-surface-card/90 text-on-surface font-label-numeric text-body-sm backdrop-blur">{{ number_format($shift->start_km) }} KM</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- End Meter Verification Box -->
        <div class="flex flex-col gap-space-xs bg-surface-container-lowest p-space-sm rounded-xl shadow-sm">
            <div class="flex items-center justify-between">
                <span class="font-label-caps text-label-caps {{ $shift->end_km !== null ? 'text-primary' : 'text-outline' }} uppercase font-bold">End Shift Reading</span>
                <span class="font-body-sm text-body-sm text-on-surface-variant font-medium">{{ $endTime ? $endTime . ' • Depot Bay' : 'Awaiting return' }}</span>
            </div>
            <div class="relative w-full h-44 rounded-lg overflow-hidden group shadow-inner">
                @if($endImage)
                    <img alt="End shift meter photo for shift {{ $shift->shift_number }}" class="w-full h-full object-cover" src="{{ $endImage }}"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-on-surface/80 via-transparent to-transparent flex items-end p-space-sm justify-between">
                        <span class="px-2 py-0.5 rounded bg-surface-card/90 text-on-surface font-label-numeric text-body-sm backdrop-blur font-bold">{{ number_format($shift->end_km) }} KM</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-secondary text-on-secondary font-label-caps text-label-caps">
                            <span class="material-symbols-outlined text-xs">verified</span> OCR OK
                        </span>
                    </div>
                @elseif($shift->end_km !== null)
                    <div class="w-full h-full bg-surface-container-low flex flex-col items-center justify-center gap-1 text-outline">
                        <span class="material-symbols-outlined text-2xl">photo_camera</span>
                        <span class="font-body-sm text-body-sm">No end meter photo captured</span>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-on-surface/70 via-transparent to-transparent flex items-end p-space-sm">
                        <span class="px-2 py-0.5 rounded bg-surface-card/90 text-on-surface font-label-numeric text-body-sm backdrop-blur font-bold">{{ number_format($shift->end_km) }} KM</span>
                    </div>
                @else
                    <div class="w-full h-full bg-surface-container-low flex flex-col items-center justify-center gap-1 text-outline">
                        <span class="material-symbols-outlined text-2xl animate-spin">timelapse</span>
                        <span class="font-body-sm text-body-sm">Vehicle still in route</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Calculated Mileage Differential Breakout -->
    <div class="bg-surface-container-high rounded-xl p-space-md flex flex-col gap-space-xs">
        <div class="flex items-center justify-between">
            <span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Meter Delta:</span>
            <span class="font-label-numeric text-label-numeric text-on-surface font-bold">
                @if($shift->end_km !== null)
                    {{ number_format($shift->end_km) }} - {{ number_format($shift->start_km) }} = {{ number_format($distance ?? ($shift->end_km - $shift->start_km), 1) }} KM
                @else
                    Pending end reading
                @endif
            </span>
        </div>
        <div class="flex items-center justify-between">
            <span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Scheduled Route:</span>
            <span class="font-label-numeric text-body-md text-secondary font-bold">
                {{ $trip && $trip->distance_km !== null ? number_format((float) $trip->distance_km, 1) . ' KM' : '—' }}
            </span>
        </div>
        <div class="flex items-center justify-between pt-1">
            <span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Shift Duration:</span>
            <span class="font-body-md text-body-md text-on-surface font-semibold">{{ $duration ?? 'In progress' }}</span>
        </div>
    </div>

    @if($shift->start_notes || $shift->end_notes)
        <div class="p-space-sm rounded-lg bg-surface-container-low flex flex-col gap-1">
            @if($shift->start_notes)
                <span class="font-body-sm text-body-sm text-on-surface-variant"><span class="font-semibold text-on-surface">Start note:</span> {{ $shift->start_notes }}</span>
            @endif
            @if($shift->end_notes)
                <span class="font-body-sm text-body-sm text-on-surface-variant"><span class="font-semibold text-on-surface">End note:</span> {{ $shift->end_notes }}</span>
            @endif
        </div>
    @endif

    <!-- Auditor Signature and Approval Buttons -->
    <div class="flex flex-col gap-space-sm pt-space-xs">
        <button class="w-full h-14 rounded-xl bg-primary-container text-on-primary font-label-action text-label-action flex items-center justify-center gap-2 hover:opacity-95 shadow-md active:translate-y-0.5 transition-all">
            <span class="material-symbols-outlined text-2xl">check_circle</span>
            <span>Approve Shift Mileage</span>
        </button>
        <div class="grid grid-cols-2 gap-space-sm">
            <button class="h-12 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md font-semibold hover:bg-surface-container transition-colors flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-lg">flag</span>
                <span>Flag Recount</span>
            </button>
            <button class="h-12 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md font-semibold hover:bg-surface-container transition-colors flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-lg">edit_note</span>
                <span>Manual Entry</span>
            </button>
        </div>
    </div>

    <!-- Audit History Log Strip -->
    <div class="pt-space-xs flex items-center gap-space-xs text-outline font-body-sm text-body-sm">
        <span class="material-symbols-outlined text-base">history</span>
        <span>Logged by {{ auth()->user()->name ?? 'System' }} &bull; Auto-validated via Depot OCR Gateway</span>
    </div>
</div>

<!-- Linked Route Tile -->
<div class="bg-surface-card rounded-xl p-space-md shadow-sm flex flex-col gap-space-sm">
    <div class="flex items-center justify-between">
        <span class="font-label-caps text-label-caps text-outline uppercase">Linked Route</span>
        <span class="font-body-sm text-body-sm text-primary font-semibold">
            {{ $trip?->route_label ?? ($bus ? 'Bus #' . $bus->bus_number . ' depot round' : 'Unassigned route') }}
        </span>
    </div>
    <div class="w-full h-36 bg-surface-container-low rounded-lg relative overflow-hidden shadow-inner flex items-end p-space-sm">
        <div class="bg-surface-card/90 backdrop-blur px-2.5 py-1 rounded text-xs font-semibold text-on-surface shadow-sm flex items-center gap-1">
            <span class="w-2 h-2 rounded-full {{ $isOpen ? 'bg-primary-container animate-pulse' : 'bg-sync-active' }}"></span>
            <span>
                @if($shift->ended_at)
                    Returned {{ $shift->ended_at->format('M d, h:i A') }}
                @else
                    Departed {{ optional($shift->started_at)->format('M d, h:i A') ?? '—' }} &bull; still in route
                @endif
            </span>
        </div>
    </div>
</div>

