<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Shift Detail: {{ $shift->display_id }} &bull; {{ config('app.name', 'Lancotri') }}</title>
    @include('partials.brand-styles')
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/lancotri-mark.svg') }}">
    <!-- Leaflet CSS for Map rendering -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        .leaflet-container {
            font-family: inherit;
            border-radius: 0.75rem;
            z-index: 10;
        }
        .bus-marker-pulse {
            width: 14px;
            height: 14px;
            background-color: #10B981;
            border-radius: 50%;
            border: 2px solid #FFFFFF;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.4);
            animation: pulse-ring 2s infinite;
        }
        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6); }
            70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        .stop-marker {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background-color: #3B82F6;
            color: #FFFFFF;
            font-size: 10px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #FFFFFF;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body class="bg-background text-foreground antialiased min-h-screen">
    @include('partials.sidebar')

    <div class="pl-64 flex flex-col min-h-screen">
        @include('partials.topbar', [
            'topbarSearchPlaceholder' => 'Search shift records...',
            'topbarSearchId' => 'shiftNavSearch',
            'topbarStatus' => 'Shift Inspection Desk',
        ])

        @php
            $bus = $shift->bus;
            $route = $bus?->transitRoute;
            $driver = $shift->driver;
            $driverUser = $driver?->user;
            $trip = $shift->trip;

            $statusBadgeClass = match($shift->status) {
                'verified' => 'border-emerald-500/30 bg-emerald-500/15 text-emerald-600 dark:text-emerald-400',
                'in_progress' => 'border-amber-500/30 bg-amber-500/15 text-amber-600 dark:text-amber-400',
                'completed' => ($shift->end_km === null ? 'border-destructive/30 bg-destructive/15 text-destructive' : 'border-blue-500/30 bg-blue-500/15 text-blue-600 dark:text-blue-400'),
                'flagged' => 'border-destructive/30 bg-destructive/15 text-destructive',
                default => 'border-transparent bg-secondary text-secondary-foreground'
            };
            $statusLabel = match($shift->status) {
                'verified' => 'Verified & Approved',
                'in_progress' => 'Active On-Shift',
                'completed' => ($shift->end_km === null ? 'Flagged (Missing Meter)' : 'Completed &bull; Pending Audit'),
                'flagged' => 'Flagged for Supervisor',
                default => ucfirst($shift->status)
            };

            $startImage = \App\Support\StorageUrl::for($shift->start_meter_image);
            $endImage = \App\Support\StorageUrl::for($shift->end_meter_image);

            $duration = '—';
            if ($shift->started_at && $shift->ended_at) {
                $mins = (int) round($shift->started_at->diffInMinutes($shift->ended_at));
                $duration = intdiv($mins, 60) . 'h ' . ($mins % 60) . 'm';
            } elseif ($shift->started_at) {
                $mins = (int) round($shift->started_at->diffInMinutes(now()));
                $duration = intdiv($mins, 60) . 'h ' . ($mins % 60) . 'm (elapsed)';
            }

            $locationsCount = $shift->locations->count();
            $offlineSyncedCount = $shift->locations->where('is_offline_sync', true)->count();
            $lastLocation = $shift->locations->last();

            // Prepare Map Data Payload
            $routeData = $route ? [
                'id' => $route->id,
                'code' => $route->route_code,
                'name' => $route->name,
                'origin' => $route->origin,
                'destination' => $route->destination,
                'origin_coords' => [$route->origin_lat, $route->origin_lng],
                'destination_coords' => [$route->destination_lat, $route->destination_lng],
                'waypoints' => $route->waypoints ?? [],
                'path' => $route->path_coordinates ?? [],
            ] : null;

            $breadcrumbsData = $shift->locations->map(fn($loc) => [
                'lat' => (float)$loc->latitude,
                'lng' => (float)$loc->longitude,
                'speed' => (float)($loc->speed ?? 0),
                'heading' => (float)($loc->heading ?? 0),
                'accuracy' => (float)($loc->accuracy ?? 0),
                'recorded_at' => $loc->recorded_at?->format('H:i:s'),
                'offline' => (bool)$loc->is_offline_sync,
            ])->values();
        @endphp

        <main class="w-full pt-16 flex-1 px-8 py-8 bg-background">
            <div class="flex flex-col w-full gap-6 max-w-7xl mx-auto">

                <!-- Breadcrumbs & Navigation Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b">
                    <div class="flex items-center gap-2 text-sm text-muted-foreground">
                        <a href="{{ route('shifts') }}" class="hover:text-foreground flex items-center gap-1.5 transition-colors">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            <span>Shifts Roster</span>
                        </a>
                        <span>/</span>
                        <span class="font-mono text-foreground font-semibold">{{ $shift->display_id }}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusBadgeClass }}">
                            {!! $statusLabel !!}
                        </span>
                    </div>

                    <!-- Quick Actions -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('shifts') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background shadow-xs hover:bg-accent hover:text-accent-foreground h-9 px-3 gap-1.5">
                            <i data-lucide="layout-list" class="w-4 h-4 text-muted-foreground"></i>
                            <span>All Shifts</span>
                        </a>

                        @if($shift->status !== \App\Models\ShiftLog::STATUS_VERIFIED && $shift->end_km !== null)
                            <button type="button" onclick="verifyShift({{ $shift->id }})" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium bg-emerald-600 text-white hover:bg-emerald-700 shadow-xs h-9 px-4 gap-1.5 transition-colors">
                                <i data-lucide="shield-check" class="w-4 h-4"></i>
                                <span>Approve Shift</span>
                            </button>
                        @endif

                        @if($shift->status !== \App\Models\ShiftLog::STATUS_FLAGGED)
                            <button type="button" onclick="openFlagModal({{ $shift->id }})" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-destructive/40 text-destructive hover:bg-destructive/10 shadow-xs h-9 px-3 gap-1.5 transition-colors">
                                <i data-lucide="flag" class="w-4 h-4"></i>
                                <span>Flag Audit</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Shift Overview Top Banner -->
                <div class="rounded-xl border bg-card text-card-foreground shadow-xs p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-center">
                        <!-- Driver Details -->
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-lg border border-primary/20 shrink-0">
                                {{ strtoupper(substr($driverUser->name ?? 'D', 0, 2)) }}
                            </div>
                            <div>
                                <div class="text-xs font-mono uppercase text-muted-foreground">Driver in Charge</div>
                                <div class="text-base font-bold text-foreground leading-snug">{{ $driverUser->name ?? 'Unassigned' }}</div>
                                <div class="text-xs text-muted-foreground font-mono flex items-center gap-1.5 mt-0.5">
                                    <span>Badge: {{ $driver?->display_id ?? 'DRV-—' }}</span>
                                    @if($driver?->phone)
                                        <span>&bull;</span>
                                        <span>{{ $driver->phone }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Bus / Vehicle Details -->
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-muted text-foreground flex items-center justify-center shrink-0 border">
                                <i data-lucide="bus" class="w-6 h-6 text-primary"></i>
                            </div>
                            <div>
                                <div class="text-xs font-mono uppercase text-muted-foreground">Assigned Vehicle</div>
                                <div class="text-base font-bold text-foreground leading-snug">{{ $bus?->display_id ?? 'Bus —' }}</div>
                                <div class="text-xs text-muted-foreground font-mono flex items-center gap-1.5 mt-0.5">
                                    <span>Plate: {{ $bus?->plate ?? '—' }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $bus?->model ?? 'Standard Coach' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Transit Route Details -->
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center shrink-0 border border-blue-500/20">
                                <i data-lucide="route" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <div class="text-xs font-mono uppercase text-muted-foreground">Assigned Transit Corridor</div>
                                @if($route)
                                    <div class="text-base font-bold text-foreground leading-snug flex items-center gap-1.5">
                                        <span class="font-mono text-primary">{{ $route->route_code }}</span>
                                        <span class="truncate">{{ $route->name }}</span>
                                    </div>
                                    <div class="text-xs text-muted-foreground truncate mt-0.5">
                                        {{ $route->origin }} &rarr; {{ $route->destination }}
                                    </div>
                                @else
                                    <div class="text-base font-bold text-muted-foreground leading-snug">No Route Assigned</div>
                                    <div class="text-xs text-muted-foreground">Free dispatch / On-demand</div>
                                @endif
                            </div>
                        </div>

                        <!-- Timing & Telemetry Status -->
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-500/20">
                                <i data-lucide="activity" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <div class="text-xs font-mono uppercase text-muted-foreground">Shift Duration &amp; GPS</div>
                                <div class="text-base font-bold text-foreground font-mono leading-snug">{{ $duration }}</div>
                                <div class="text-xs text-muted-foreground font-mono flex items-center gap-1.5 mt-0.5">
                                    @if($locationsCount >= 2)
                                        <span class="text-emerald-600 font-semibold">Start &amp; End GPS Checkpoints</span>
                                    @elseif($locationsCount === 1)
                                        <span class="text-emerald-600 font-semibold">1 GPS Checkpoint Recorded</span>
                                    @else
                                        <span class="text-muted-foreground">No GPS check logged</span>
                                    @endif
                                    @if($offlineSyncedCount > 0)
                                        <span>&bull;</span>
                                        <span class="text-amber-500">{{ $offlineSyncedCount }} offline buffered</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Key Metrics Strip -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="rounded-xl border bg-card text-card-foreground shadow-xs p-4">
                        <div class="flex items-center justify-between text-xs text-muted-foreground font-mono uppercase">
                            <span>Start Odometer</span>
                            <i data-lucide="gauge" class="w-4 h-4 text-muted-foreground"></i>
                        </div>
                        <div class="text-2xl font-bold font-mono text-foreground mt-2">
                            {{ number_format($shift->start_km) }} <span class="text-xs font-normal text-muted-foreground">KM</span>
                        </div>
                        <div class="text-xs text-muted-foreground mt-1">
                            {{ $shift->started_at ? $shift->started_at->format('M d, Y &bull; H:i') : '—' }}
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-xs p-4">
                        <div class="flex items-center justify-between text-xs text-muted-foreground font-mono uppercase">
                            <span>End Odometer</span>
                            <i data-lucide="flag" class="w-4 h-4 text-muted-foreground"></i>
                        </div>
                        <div class="text-2xl font-bold font-mono text-foreground mt-2">
                            @if($shift->end_km)
                                {{ number_format($shift->end_km) }} <span class="text-xs font-normal text-muted-foreground">KM</span>
                            @else
                                <span class="text-amber-500 font-sans text-base">In Progress</span>
                            @endif
                        </div>
                        <div class="text-xs text-muted-foreground mt-1">
                            {{ $shift->ended_at ? $shift->ended_at->format('M d, Y &bull; H:i') : 'Shift is ongoing' }}
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-xs p-4">
                        <div class="flex items-center justify-between text-xs text-muted-foreground font-mono uppercase">
                            <span>Audited Distance</span>
                            <i data-lucide="milestone" class="w-4 h-4 text-primary"></i>
                        </div>
                        <div class="text-2xl font-bold font-mono text-primary mt-2">
                            {{ $shift->distance_km !== null ? number_format($shift->distance_km, 1) : '—' }} <span class="text-xs font-normal text-muted-foreground">KM</span>
                        </div>
                        <div class="text-xs text-muted-foreground mt-1">
                            @if($route && $route->distance_km && $shift->distance_km)
                                Variance: {{ number_format($shift->distance_km - $route->distance_km, 1) }} KM vs Route
                            @else
                                Meter difference
                            @endif
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-xs p-4">
                        <div class="flex items-center justify-between text-xs text-muted-foreground font-mono uppercase">
                            <span>Telemetry Health</span>
                            <i data-lucide="satellite" class="w-4 h-4 text-emerald-500"></i>
                        </div>
                        <div class="text-2xl font-bold font-mono mt-2 {{ $bus?->is_live_online ? 'text-emerald-500' : 'text-muted-foreground' }}">
                            {{ $bus?->is_live_online ? 'ONLINE' : 'OFFLINE' }}
                        </div>
                        <div class="text-xs text-muted-foreground mt-1">
                            Last ping: {{ $lastLocation?->recorded_at ? $lastLocation->recorded_at->diffForHumans() : ($bus?->last_seen_human ?? 'No pings recorded') }}
                        </div>
                    </div>
                </div>

                <!-- Live Map Card: Assigned Corridor + GPS Breadcrumb Trail -->
                <div class="rounded-xl border bg-card text-card-foreground shadow-xs overflow-hidden flex flex-col">
                    <div class="p-4 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-muted/30">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-primary"></span>
                            </span>
                            <h2 class="text-base font-semibold text-foreground">Interactive Route Corridor &amp; GPS Telemetry Path</h2>
                            <span class="text-xs text-muted-foreground font-mono">&bull; OpenStreetMap Leaflet Engine</span>
                        </div>

                        <!-- Map Legend & Layer Controls -->
                        <div class="flex flex-wrap items-center gap-3 text-xs">
                            <div class="flex items-center gap-1.5 text-muted-foreground font-medium">
                                <span class="w-3.5 h-1 bg-blue-500 rounded"></span>
                                <span>Planned Corridor</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-muted-foreground font-medium">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                                <span>Start Checkpoint</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-muted-foreground font-medium">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                                <span>End / Current Position</span>
                            </div>
                            <button type="button" id="btnFitMap" class="px-2.5 py-1 text-xs rounded border bg-background hover:bg-accent text-foreground font-mono">
                                Fit View
                            </button>
                        </div>
                    </div>

                    <!-- Map Container -->
                    <div id="shiftMap" class="w-full h-[450px] relative bg-muted/20"></div>
                </div>

                <!-- Meter Photos & Physical Verification Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Start Shift Photo Card -->
                    <div class="rounded-xl border bg-card text-card-foreground shadow-xs p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between border-b pb-3 mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="p-1.5 rounded-md bg-primary/10 text-primary">
                                        <i data-lucide="log-in" class="w-4 h-4"></i>
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-semibold text-foreground">Start Shift Meter Photo</h3>
                                        <p class="text-xs text-muted-foreground">Captured at depot dispatch</p>
                                    </div>
                                </div>
                                <span class="font-mono text-sm font-bold text-foreground bg-muted px-2.5 py-1 rounded">
                                    {{ number_format($shift->start_km) }} KM
                                </span>
                            </div>

                            <div class="relative w-full h-64 rounded-lg overflow-hidden border bg-muted/40 group flex items-center justify-center">
                                @if($startImage)
                                    <img src="{{ $startImage }}" alt="Start meter reading" class="w-full h-full object-cover transition-transform group-hover:scale-105 duration-200 cursor-pointer" onclick="openPhotoModal('{{ $startImage }}', 'Start Shift Meter: {{ number_format($shift->start_km) }} KM')">
                                    <div class="absolute bottom-2 left-2 right-2 bg-background/80 backdrop-blur text-xs p-2 rounded flex items-center justify-between">
                                        <span class="text-muted-foreground font-mono">{{ $shift->started_at ? $shift->started_at->format('M d, Y H:i:s') : 'Time unrecorded' }}</span>
                                        <span class="text-primary font-medium flex items-center gap-1 cursor-pointer" onclick="openPhotoModal('{{ $startImage }}', 'Start Shift Meter: {{ number_format($shift->start_km) }} KM')">
                                            <i data-lucide="maximize-2" class="w-3.5 h-3.5"></i> Expand
                                        </span>
                                    </div>
                                @else
                                    <div class="text-center p-6 text-muted-foreground">
                                        <i data-lucide="image-off" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                        <p class="text-sm">No start meter photo recorded</p>
                                        <p class="text-xs text-muted-foreground mt-1">Manual entry or legacy upload</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t text-xs text-muted-foreground">
                            <span class="font-semibold text-foreground">Notes:</span>
                            <span>{{ $shift->start_notes ?: 'No start remarks provided.' }}</span>
                        </div>
                    </div>

                    <!-- End Shift Photo Card -->
                    <div class="rounded-xl border bg-card text-card-foreground shadow-xs p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between border-b pb-3 mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="p-1.5 rounded-md bg-emerald-500/10 text-emerald-600">
                                        <i data-lucide="log-out" class="w-4 h-4"></i>
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-semibold text-foreground">End Shift Meter Photo</h3>
                                        <p class="text-xs text-muted-foreground">Captured at shift completion</p>
                                    </div>
                                </div>
                                <span class="font-mono text-sm font-bold text-foreground bg-muted px-2.5 py-1 rounded">
                                    {{ $shift->end_km ? number_format($shift->end_km) . ' KM' : 'Pending' }}
                                </span>
                            </div>

                            <div class="relative w-full h-64 rounded-lg overflow-hidden border bg-muted/40 group flex items-center justify-center">
                                @if($endImage)
                                    <img src="{{ $endImage }}" alt="End meter reading" class="w-full h-full object-cover transition-transform group-hover:scale-105 duration-200 cursor-pointer" onclick="openPhotoModal('{{ $endImage }}', 'End Shift Meter: {{ number_format($shift->end_km) }} KM')">
                                    <div class="absolute bottom-2 left-2 right-2 bg-background/80 backdrop-blur text-xs p-2 rounded flex items-center justify-between">
                                        <span class="text-muted-foreground font-mono">{{ $shift->ended_at ? $shift->ended_at->format('M d, Y H:i:s') : 'Time unrecorded' }}</span>
                                        <span class="text-primary font-medium flex items-center gap-1 cursor-pointer" onclick="openPhotoModal('{{ $endImage }}', 'End Shift Meter: {{ number_format($shift->end_km) }} KM')">
                                            <i data-lucide="maximize-2" class="w-3.5 h-3.5"></i> Expand
                                        </span>
                                    </div>
                                @else
                                    <div class="text-center p-6 text-muted-foreground">
                                        <i data-lucide="clock" class="w-8 h-8 mx-auto mb-2 text-amber-500 opacity-60"></i>
                                        <p class="text-sm font-medium text-foreground">Awaiting end meter photo</p>
                                        <p class="text-xs text-muted-foreground mt-1">Shift is currently open or ended without photo attachment</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t text-xs text-muted-foreground">
                            <span class="font-semibold text-foreground">Notes:</span>
                            <span>{{ $shift->end_notes ?: 'No end remarks provided.' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Shift GPS Location Checkpoints (Start & End Only) -->
                <div class="rounded-xl border bg-card text-card-foreground shadow-xs overflow-hidden">
                    <div class="p-4 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-muted/20">
                        <div class="flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-4 h-4 text-primary"></i>
                            <h3 class="text-sm font-semibold text-foreground">Shift GPS Location Checkpoints ({{ $locationsCount }} Recorded)</h3>
                        </div>
                        <span class="text-xs text-muted-foreground font-mono">
                            Optimized: Preserving Driver Start &amp; End Locations Only
                        </span>
                    </div>

                    @if($locationsCount > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs font-mono">
                                <thead class="border-b bg-muted/40 text-muted-foreground uppercase text-[10px]">
                                    <tr>
                                        <th class="py-2.5 px-4 text-left">Checkpoint</th>
                                        <th class="py-2.5 px-4 text-left">Recorded Time</th>
                                        <th class="py-2.5 px-4 text-left">Coordinates (Lat, Lng)</th>
                                        <th class="py-2.5 px-4 text-left">Speed &amp; Heading</th>
                                        <th class="py-2.5 px-4 text-left">GPS Accuracy</th>
                                        <th class="py-2.5 px-4 text-right">Data Source</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border/60">
                                    @php
                                        $orderedLocs = $shift->locations->sortBy('id')->values();
                                    @endphp
                                    @foreach($orderedLocs as $index => $loc)
                                        @php
                                            $isFirst = ($index === 0);
                                            $isLast = ($index === $orderedLocs->count() - 1 && $orderedLocs->count() > 1);
                                        @endphp
                                        <tr class="hover:bg-muted/30 transition-colors">
                                            <td class="py-3 px-4 font-sans">
                                                @if($isFirst)
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-emerald-500/15 text-emerald-600 border border-emerald-500/30">
                                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                        START CHECKPOINT
                                                    </span>
                                                @elseif($isLast)
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold {{ $shift->status === 'completed' ? 'bg-blue-500/15 text-blue-600 border border-blue-500/30' : 'bg-amber-500/15 text-amber-600 border border-amber-500/30' }}">
                                                        <span class="w-2 h-2 rounded-full {{ $shift->status === 'completed' ? 'bg-blue-500' : 'bg-amber-500 animate-pulse' }}"></span>
                                                        {{ $shift->status === 'completed' ? 'END CHECKPOINT' : 'CURRENT POSITION' }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] bg-muted text-muted-foreground font-medium">
                                                        CHECKPOINT #{{ $index + 1 }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-foreground font-semibold">
                                                {{ $loc->recorded_at ? $loc->recorded_at->format('M d, Y H:i:s') : '—' }}
                                            </td>
                                            <td class="py-3 px-4 text-foreground font-mono">
                                                <a href="https://maps.google.com/?q={{ $loc->latitude }},{{ $loc->longitude }}" target="_blank" class="hover:underline hover:text-primary">
                                                    {{ number_format($loc->latitude, 6) }}, {{ number_format($loc->longitude, 6) }}
                                                </a>
                                            </td>
                                            <td class="py-3 px-4 text-muted-foreground">
                                                {{ $loc->speed ? number_format($loc->speed, 1) . ' km/h' : '0 km/h' }}
                                                @if($loc->heading)
                                                    &bull; {{ round($loc->heading) }}°
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-muted-foreground">
                                                &plusmn;{{ $loc->accuracy ? round($loc->accuracy) . 'm' : '10m' }}
                                            </td>
                                            <td class="py-3 px-4 text-right">
                                                @if($loc->is_offline_sync)
                                                    <span class="px-2 py-0.5 rounded text-[10px] bg-amber-500/15 text-amber-600 font-semibold border border-amber-500/30">Offline Synced</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded text-[10px] bg-emerald-500/15 text-emerald-600 font-semibold border border-emerald-500/30">Live Realtime</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-8 text-center text-muted-foreground text-xs">
                            <i data-lucide="map-pin-off" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                            No GPS telemetry coordinates received yet for this shift. Location checkpoints are automatically recorded at shift start and completion.
                        </div>
                    @endif
                </div>

            </div>
        </main>
    </div>

    <!-- Image Preview Modal -->
    <div id="photoModal" class="fixed inset-0 z-50 bg-black/80 hidden items-center justify-center p-4 backdrop-blur-xs" onclick="closePhotoModal()">
        <div class="relative max-w-4xl max-h-[90vh] bg-card rounded-xl p-4 shadow-2xl flex flex-col gap-3" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between pb-2 border-b">
                <h4 id="photoModalTitle" class="text-sm font-semibold text-foreground">Meter Photo</h4>
                <button type="button" onclick="closePhotoModal()" class="text-muted-foreground hover:text-foreground p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="overflow-hidden rounded-lg flex items-center justify-center max-h-[75vh]">
                <img id="photoModalImg" src="" alt="Enlarged meter photo" class="max-w-full max-h-[75vh] object-contain rounded-md"/>
            </div>
        </div>
    </div>

    <!-- Flag Shift Modal -->
    <div id="flagModal" class="fixed inset-0 z-50 bg-black/60 hidden items-center justify-center p-4 backdrop-blur-xs">
        <div class="bg-card border rounded-xl max-w-md w-full p-6 shadow-xl flex flex-col gap-4">
            <div class="flex items-center justify-between pb-2 border-b">
                <div class="flex items-center gap-2 text-destructive">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    <h3 class="text-base font-bold text-foreground">Flag Shift for Audit</h3>
                </div>
                <button type="button" onclick="closeFlagModal()" class="text-muted-foreground hover:text-foreground">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <p class="text-xs text-muted-foreground">
                Marking this shift will alert supervisor review. Please state the discrepancy (e.g. meter blur, odometer mismatch, route deviation).
            </p>
            <div>
                <label class="text-xs font-semibold uppercase text-muted-foreground mb-1 block">Audit Note / Reason</label>
                <textarea id="flagReason" rows="3" class="w-full rounded-md border border-input bg-background p-2.5 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-ring" placeholder="State reason for flagging this shift..."></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeFlagModal()" class="px-3 py-1.5 text-xs rounded border hover:bg-muted font-medium">Cancel</button>
                <button type="button" onclick="submitFlag()" class="px-3 py-1.5 text-xs rounded bg-destructive text-white hover:bg-destructive/90 font-medium">Confirm Flag</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        lucide.createIcons();

        // Data payload passed safely from PHP
        const routeData = @json($routeData);
        const breadcrumbs = @json($breadcrumbsData);
        const currentShiftId = {{ $shift->id }};

        // Initialize Leaflet Map
        let map = null;
        let mapBounds = null;

        function initMap() {
            const mapContainer = document.getElementById('shiftMap');
            if (!mapContainer) return;

            // Default fallback center: Caracas / Central hub
            let defaultLat = 10.4806;
            let defaultLng = -66.9036;

            if (breadcrumbs.length > 0) {
                defaultLat = breadcrumbs[breadcrumbs.length - 1].lat;
                defaultLng = breadcrumbs[breadcrumbs.length - 1].lng;
            } else if (routeData && routeData.origin_coords) {
                defaultLat = routeData.origin_coords[0];
                defaultLng = routeData.origin_coords[1];
            }

            map = L.map('shiftMap', {
                zoomControl: true,
                attributionControl: false
            }).setView([defaultLat, defaultLng], 13);

            // OpenStreetMap Standard Tiles (100% Free, No API Key Required)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const boundsGroup = [];

            // 1. Draw Assigned Transit Corridor (Blue Line + Stops)
            if (routeData) {
                if (routeData.path && routeData.path.length > 1) {
                    const corridorPolyline = L.polyline(routeData.path, {
                        color: '#3B82F6',
                        weight: 4,
                        opacity: 0.7,
                        dashArray: '8, 8'
                    }).addTo(map);
                    corridorPolyline.bindPopup(`<b>Corridor: ${routeData.code}</b><br>${routeData.name}`);
                    routeData.path.forEach(pt => boundsGroup.push(pt));
                }

                // Origin Stop
                if (routeData.origin_coords && routeData.origin_coords[0]) {
                    L.marker(routeData.origin_coords, {
                        icon: L.divIcon({
                            className: 'custom-icon',
                            html: '<div class="stop-marker" style="background:#2563EB;">A</div>',
                            iconSize: [22, 22],
                            iconAnchor: [11, 11]
                        })
                    }).addTo(map).bindPopup(`<b>Origin Terminal:</b><br>${routeData.origin}`);
                    boundsGroup.push(routeData.origin_coords);
                }

                // Destination Stop
                if (routeData.destination_coords && routeData.destination_coords[0]) {
                    L.marker(routeData.destination_coords, {
                        icon: L.divIcon({
                            className: 'custom-icon',
                            html: '<div class="stop-marker" style="background:#1D4ED8;">B</div>',
                            iconSize: [22, 22],
                            iconAnchor: [11, 11]
                        })
                    }).addTo(map).bindPopup(`<b>Destination Terminal:</b><br>${routeData.destination}`);
                    boundsGroup.push(routeData.destination_coords);
                }

                // Waypoints
                if (Array.isArray(routeData.waypoints)) {
                    routeData.waypoints.forEach((wp, idx) => {
                        if (wp.lat && wp.lng) {
                            L.marker([wp.lat, wp.lng], {
                                icon: L.divIcon({
                                    className: 'custom-icon',
                                    html: `<div class="stop-marker" style="background:#6B7280; font-size:9px;">${idx + 1}</div>`,
                                    iconSize: [18, 18],
                                    iconAnchor: [9, 9]
                                })
                            }).addTo(map).bindPopup(`<b>Waypoint #${idx + 1}:</b><br>${wp.name || 'En-route stop'}`);
                            boundsGroup.push([wp.lat, wp.lng]);
                        }
                    });
                }
            }

            // 2. Draw Recorded GPS Checkpoints (Start & End)
            if (breadcrumbs && breadcrumbs.length > 0) {
                const trailCoords = breadcrumbs.map(b => [b.lat, b.lng]);
                if (trailCoords.length > 1) {
                    const trailPolyline = L.polyline(trailCoords, {
                        color: '#10B981',
                        weight: 4,
                        opacity: 0.85,
                        dashArray: '6, 6',
                        lineJoin: 'round'
                    }).addTo(map);
                    trailPolyline.bindPopup(`<b>Transit Vector:</b><br>Start &amp; End Checkpoints`);
                }
                trailCoords.forEach(pt => boundsGroup.push(pt));

                // Start Checkpoint Marker
                const firstPt = breadcrumbs[0];
                L.marker([firstPt.lat, firstPt.lng], {
                    icon: L.divIcon({
                        className: 'custom-icon',
                        html: '<div class="stop-marker" style="background:#059669; font-size:8px; font-weight:bold;">START</div>',
                        iconSize: [36, 20],
                        iconAnchor: [18, 10]
                    })
                }).addTo(map).bindPopup(`<b>Shift Started Here:</b><br>Time: ${firstPt.recorded_at}<br>Coords: ${firstPt.lat.toFixed(5)}, ${firstPt.lng.toFixed(5)}`);

                // End / Latest Checkpoint Marker
                if (breadcrumbs.length > 1) {
                    const lastPt = breadcrumbs[breadcrumbs.length - 1];
                    const isCompleted = @json($shift->status === 'completed' || $shift->status === 'verified');
                    L.marker([lastPt.lat, lastPt.lng], {
                        icon: L.divIcon({
                            className: 'custom-icon',
                            html: isCompleted
                                ? '<div class="stop-marker" style="background:#2563EB; font-size:8px; font-weight:bold;">END</div>'
                                : '<div class="bus-marker-pulse"></div>',
                            iconSize: isCompleted ? [32, 20] : [14, 14],
                            iconAnchor: isCompleted ? [16, 10] : [7, 7]
                        })
                    }).addTo(map).bindPopup(`<b>${isCompleted ? 'Shift Ended Here' : 'Bus Latest Position'}:</b><br>Logged: ${lastPt.recorded_at}<br>Coords: ${lastPt.lat.toFixed(5)}, ${lastPt.lng.toFixed(5)}<br>Speed: ${lastPt.speed} km/h`);
                }
            }

            // Fit map to contain all corridor and breadcrumb points
            if (boundsGroup.length > 0) {
                mapBounds = L.latLngBounds(boundsGroup);
                map.fitBounds(mapBounds, { padding: [40, 40] });
            }

            document.getElementById('btnFitMap')?.addEventListener('click', () => {
                if (mapBounds && map) {
                    map.fitBounds(mapBounds, { padding: [40, 40] });
                }
            });
        }

        // Initialize Map once DOM is ready
        window.addEventListener('load', () => {
            initMap();
        });

        // Photo Modal
        function openPhotoModal(src, title) {
            document.getElementById('photoModalImg').src = src;
            document.getElementById('photoModalTitle').innerText = title;
            document.getElementById('photoModal').classList.remove('hidden');
            document.getElementById('photoModal').classList.add('flex');
        }
        function closePhotoModal() {
            document.getElementById('photoModal').classList.add('hidden');
            document.getElementById('photoModal').classList.remove('flex');
        }

        // Flag Modal
        let activeFlagShiftId = null;
        function openFlagModal(id) {
            activeFlagShiftId = id;
            document.getElementById('flagModal').classList.remove('hidden');
            document.getElementById('flagModal').classList.add('flex');
        }
        function closeFlagModal() {
            document.getElementById('flagModal').classList.add('hidden');
            document.getElementById('flagModal').classList.remove('flex');
            document.getElementById('flagReason').value = '';
        }
        async function submitFlag() {
            if (!activeFlagShiftId) return;
            const reason = document.getElementById('flagReason').value.trim();
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const res = await fetch(`/shifts/${activeFlagShiftId}/flag`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ reason: reason })
                });
                const data = await res.json();
                if (data.success) {
                    closeFlagModal();
                    window.location.reload();
                } else {
                    alert(data.message || 'Error flagging shift');
                }
            } catch (err) {
                console.error(err);
                alert('Network error communicating with shift server');
            }
        }

        // Verify Shift
        async function verifyShift(id) {
            if (!confirm('Are you sure you want to approve and verify this shift? This certifies meter mileage & telemetry.')) return;
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const res = await fetch(`/shifts/${id}/verify`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Cannot verify shift');
                }
            } catch (err) {
                console.error(err);
                alert('Network error verifying shift');
            }
        }
    </script>
</body>
</html>
