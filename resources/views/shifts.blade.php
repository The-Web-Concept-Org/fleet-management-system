<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Shift Management &amp; Meter Audits &bull; {{ config('app.name', 'Lancotri') }}</title>
    @include('partials.brand-styles')
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/lancotri-mark.svg') }}">
    <!-- Leaflet CSS for Map rendering -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        .leaflet-container { font-family: inherit; border-radius: 0.75rem; z-index: 10; }
        .bus-marker-online {
            width: 16px;
            height: 16px;
            background-color: #10B981;
            border-radius: 50%;
            border: 2px solid #FFFFFF;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.4);
            animation: bus-ping 2s infinite;
        }
        @keyframes bus-ping {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6); }
            70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        .bus-marker-offline {
            width: 14px;
            height: 14px;
            background-color: #6B7280;
            border-radius: 50%;
            border: 2px solid #FFFFFF;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body class="bg-background text-foreground antialiased min-h-screen">
    @include('partials.sidebar')

    <div class="pl-64 flex flex-col min-h-screen">
        @include('partials.topbar', [
            'topbarSearchPlaceholder' => 'Search shift #, driver, or vehicle...',
            'topbarSearchId' => 'shiftNavSearch',
            'topbarStatus' => $activeBuses . ' active vehicles',
        ])

        <main class="w-full pt-16 flex-1 px-8 py-8 bg-background">
            <div class="flex flex-col w-full gap-6 max-w-7xl mx-auto">

                <!-- Page Header: Title + Controls -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b">
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-xs text-muted-foreground font-mono">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-muted border">
                                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-primary"></i>
                                <span>TELEMETRY &amp; AUDIT PIPELINE</span>
                            </span>
                            <span>&bull;</span>
                            <span class="inline-flex items-center gap-1.5 text-emerald-600 font-medium">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                Daily Meter Roster
                            </span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">Shift Management &amp; Meter Audits</h1>
                            <p class="text-xs text-muted-foreground mt-2 leading-relaxed max-w-2xl">
                                Verify driver start and end shift meter photos, audit mileage accuracy, and approve logs in real time without reloading.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5">
                        <div class="relative w-64">
                            <i data-lucide="search" class="w-3.5 h-3.5 text-muted-foreground absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <input id="shiftSearch"
                                   data-shadcn-table-search="shiftsTable"
                                   placeholder="Search shift #, bus, driver..."
                                   class="flex h-9 w-full rounded-md border border-input bg-transparent pl-9 pr-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                        </div>

                        <button type="button" onclick="window.toast?.success('Shift Report Exported', 'CSV/PDF meter audit log generated successfully.')"
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background shadow-xs hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 gap-2">
                            <i data-lucide="download" class="w-3.5 h-3.5 text-muted-foreground"></i>
                            <span>Export Logs</span>
                        </button>
                    </div>
                </div>

                <!-- 5 Stat Cards (shadcn Card default) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    <div class="rounded-xl border bg-card text-card-foreground shadow-xs p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">Active Shifts</span>
                            <i data-lucide="clock" class="w-4 h-4 text-amber-500"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono text-amber-500" id="statCountOpen">{{ $countOpen }}</div>
                            <span class="text-xs text-muted-foreground">En route now</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-xs p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">Pending Audit</span>
                            <i data-lucide="inbox" class="w-4 h-4 text-blue-500"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono text-blue-600" id="statCountCompleted">{{ $countCompleted }}</div>
                            <span class="text-xs text-muted-foreground">Awaiting approval</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-xs p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">Verified Shifts</span>
                            <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono text-emerald-600" id="statCountVerified">{{ $countVerified }}</div>
                            <span class="text-xs text-muted-foreground">Approved by desk</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-xs p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-destructive">Flagged Audits</span>
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-destructive"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono text-destructive" id="statCountFlagged">{{ $countFlagged }}</div>
                            <span class="text-xs text-muted-foreground">Needs review</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-xs p-4 flex flex-col justify-between col-span-2 sm:col-span-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">Day Distance</span>
                            <i data-lucide="gauge" class="w-4 h-4 text-primary"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono">{{ number_format($dayMileage, 0) }} <span class="text-xs font-normal text-muted-foreground">KM</span></div>
                            <span class="text-xs text-muted-foreground">Logged today</span>
                        </div>
                    </div>
                </div>

                <!-- Live Fleet Telemetry & Active Bus Map (shadcn Card) -->
                <div class="rounded-xl border bg-card text-card-foreground shadow-xs overflow-hidden">
                    <div class="p-4 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-muted/20">
                        <div class="flex items-center gap-2.5">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                            </span>
                            <div>
                                <h2 class="text-sm font-bold text-foreground tracking-tight flex items-center gap-2">
                                    <span>Live Fleet GPS Map &amp; Real-Time Bus Tracker</span>
                                    <span class="text-[11px] font-normal text-muted-foreground font-mono">10s auto-refresh</span>
                                </h2>
                                <p class="text-xs text-muted-foreground">
                                    Green markers pulse for active buses broadcasting GPS; gray markers indicate last known position when offline.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-2 px-2.5 py-1 rounded-md bg-background border text-xs font-mono">
                                <div class="flex items-center gap-1.5 text-emerald-600 font-semibold">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span id="mapOnlineCount">0</span> Online
                                </div>
                                <span class="text-muted-foreground">&bull;</span>
                                <div class="flex items-center gap-1.5 text-muted-foreground">
                                    <span class="w-2 h-2 rounded-full bg-muted-foreground/60"></span>
                                    <span id="mapOfflineCount">0</span> Offline
                                </div>
                            </div>
                            <button type="button" onclick="refreshFleetMap()" class="inline-flex items-center justify-center rounded-md border border-input bg-background h-8 px-2.5 text-xs font-medium hover:bg-accent text-foreground gap-1.5 shadow-xs">
                                <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-muted-foreground"></i>
                                <span>Sync</span>
                            </button>
                            <button type="button" onclick="fitFleetMap()" class="inline-flex items-center justify-center rounded-md border border-input bg-background h-8 px-2.5 text-xs font-medium hover:bg-accent text-foreground gap-1.5 shadow-xs">
                                <i data-lucide="maximize" class="w-3.5 h-3.5 text-muted-foreground"></i>
                                <span>Center</span>
                            </button>
                        </div>
                    </div>

                    <!-- Map Canvas -->
                    <div id="fleetLiveMap" class="w-full h-80 relative bg-muted/10"></div>
                </div>

                <!-- Filter Controls (shadcn Card) -->
                <div class="rounded-xl border bg-card text-card-foreground shadow-xs p-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <form action="{{ route('shifts') }}" method="GET" class="flex flex-wrap items-center gap-2">
                            <!-- Bus Filter -->
                            <div class="w-48">
                                <select name="bus_id" onchange="this.form.submit()"
                                        class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring">
                                    <option value="">All Buses ({{ $totalBuses }})</option>
                                    @foreach($buses as $bus)
                                        <option value="{{ $bus->id }}" @selected((string) request('bus_id') === (string) $bus->id)>
                                            {{ $bus->display_id }}{{ $bus->plate ? ' · ' . $bus->plate : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Driver Filter -->
                            <div class="w-48">
                                <select name="driver_id" onchange="this.form.submit()"
                                        class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring">
                                    <option value="">All Drivers ({{ $drivers->count() }})</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}" @selected((string) request('driver_id') === (string) $driver->id)>
                                            {{ $driver->user->name ?? 'Driver' }} ({{ $driver->display_id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Reset Button -->
                            @if(request('bus_id') || request('driver_id'))
                                <a href="{{ route('shifts') }}"
                                   class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background shadow-xs hover:bg-accent hover:text-accent-foreground h-9 px-3 gap-1.5 text-muted-foreground">
                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                    <span>Reset Filters</span>
                                </a>
                            @endif
                        </form>

                        <!-- Instant Client-Side Status Filter Tabs (NO page reload) -->
                        <div class="flex items-center gap-1.5 p-1 rounded-lg bg-muted/60 border border-border/50 text-xs overflow-x-auto" data-shadcn-filter-group="shiftsTable">
                            <button type="button" data-filter="all"
                                    class="px-3 py-1.5 rounded-md text-xs font-semibold bg-foreground text-background shadow-xs transition-all cursor-pointer">
                                All ({{ $countAll }})
                            </button>
                            <button type="button" data-filter="in_progress"
                                    class="px-3 py-1.5 rounded-md text-xs font-medium text-muted-foreground hover:text-foreground transition-all cursor-pointer">
                                Active ({{ $countOpen }})
                            </button>
                            <button type="button" data-filter="completed"
                                    class="px-3 py-1.5 rounded-md text-xs font-medium text-muted-foreground hover:text-foreground transition-all cursor-pointer">
                                Pending Audit ({{ $countCompleted }})
                            </button>
                            <button type="button" data-filter="verified"
                                    class="px-3 py-1.5 rounded-md text-xs font-medium text-muted-foreground hover:text-foreground transition-all cursor-pointer">
                                Verified ({{ $countVerified }})
                            </button>
                            <button type="button" data-filter="flagged"
                                    class="px-3 py-1.5 rounded-md text-xs font-medium text-muted-foreground hover:text-foreground transition-all cursor-pointer">
                                Flagged ({{ $countFlagged }})
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Grid Layout: Shifts Table (2/3) + Detail Inspection Desk (1/3) -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                    <!-- Shifts DataTable (2/3) -->
                    <div class="lg:col-span-2 rounded-xl border bg-card text-card-foreground shadow-xs overflow-hidden">
                        <div class="w-full overflow-x-auto">
                            <table id="shiftsTable" class="w-full caption-bottom text-sm select-none">
                                <thead>
                                    <tr class="border-b bg-muted/50 font-mono text-[11px] uppercase tracking-wider text-muted-foreground">
                                        <th class="h-10 px-4 text-left align-middle font-medium">Shift #</th>
                                        <th class="h-10 px-4 text-left align-middle font-medium">Vehicle / Driver</th>
                                        <th class="h-10 px-4 text-left align-middle font-medium">Started At</th>
                                        <th class="h-10 px-4 text-left align-middle font-medium">Odometer</th>
                                        <th class="h-10 px-4 text-left align-middle font-medium">Distance</th>
                                        <th class="h-10 px-4 text-left align-middle font-medium">Status</th>
                                        <th class="h-10 px-4 text-right align-middle font-medium">Inspect</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y text-foreground" id="shiftsTableBody">
                                    @forelse($shifts as $shift)
                                        @php
                                            $isSelected = $selected && $selected->id === $shift->id;
                                            $driverUser = $shift->driver?->user;
                                            $filterStatus = match($shift->status) {
                                                'verified' => 'verified',
                                                'in_progress' => 'in_progress',
                                                'completed' => ($shift->end_km === null ? 'flagged' : 'completed'),
                                                'flagged' => 'flagged',
                                                default => $shift->status,
                                            };
                                            $shiftBadgeClass = match($shift->status) {
                                                'verified' => 'border-emerald-500/30 bg-emerald-500/15 text-emerald-600 dark:text-emerald-400',
                                                'in_progress' => 'border-amber-500/30 bg-amber-500/15 text-amber-600 dark:text-amber-400',
                                                'completed' => ($shift->end_km === null ? 'border-destructive/30 bg-destructive/15 text-destructive' : 'border-blue-500/30 bg-blue-500/15 text-blue-600 dark:text-blue-400'),
                                                'flagged' => 'border-destructive/30 bg-destructive/15 text-destructive',
                                                default => 'border-transparent bg-secondary text-secondary-foreground'
                                            };
                                            $statusLabel = match($shift->status) {
                                                'verified' => 'Verified',
                                                'in_progress' => 'Active',
                                                'completed' => ($shift->end_km === null ? 'Flagged' : 'Pending Audit'),
                                                'flagged' => 'Flagged',
                                                default => ucfirst($shift->status)
                                            };

                                            $hasStartAndEnd = ($shift->start_km !== null && $shift->end_km !== null);

                                            $shiftDataJson = [
                                                'id' => $shift->id,
                                                'display_id' => $shift->display_id,
                                                'shift_number' => $shift->shift_number,
                                                'bus_id' => $shift->bus?->display_id ?? 'Bus —',
                                                'bus_plate' => $shift->bus?->plate ?? '—',
                                                'bus_model' => $shift->bus?->model ?? 'Standard Chassis',
                                                'driver_name' => $driverUser->name ?? 'Unassigned Driver',
                                                'driver_badge' => $shift->driver?->badge_id ?? 'DRV-—',
                                                'start_km' => number_format($shift->start_km) . ' km',
                                                'end_km' => $shift->end_km ? number_format($shift->end_km) . ' km' : 'Pending Close',
                                                'distance_km' => $shift->distance_km !== null ? number_format($shift->distance_km, 1) . ' km' : '—',
                                                'status' => $shift->status,
                                                'filter_status' => $filterStatus,
                                                'status_label' => $statusLabel,
                                                'status_badge_class' => $shiftBadgeClass,
                                                'has_start_and_end' => $hasStartAndEnd,
                                                'started_at_date' => $shift->started_at?->format('M j, Y') ?? '—',
                                                'started_at_time' => $shift->started_at?->format('H:i') ?? '—',
                                                'ended_at_date' => $shift->ended_at?->format('M j, Y') ?? '—',
                                                'ended_at_time' => $shift->ended_at?->format('H:i') ?? '—',
                                                'start_notes' => $shift->start_notes ?? '',
                                                'end_notes' => $shift->end_notes ?? '',
                                                'start_meter_image_url' => $shift->start_meter_image_url ?? '',
                                                'end_meter_image_url' => $shift->end_meter_image_url ?? '',
                                                'route_label' => $shift->trip ? ($shift->trip->origin . ' → ' . $shift->trip->destination . ' (' . $shift->trip->display_id . ')') : ($shift->bus ? $shift->bus->display_id . ' Depot Round' : 'No Scheduled Route'),
                                            ];
                                        @endphp
                                        <tr id="shift-row-{{ $shift->id }}"
                                            data-shift-id="{{ $shift->id }}"
                                            data-status="{{ $filterStatus }}"
                                            onclick="inspectShiftById('{{ $shift->id }}')"
                                            class="shift-table-row border-b transition-colors cursor-pointer hover:bg-muted/50 {{ $isSelected ? 'bg-muted/60 font-medium border-l-2 border-primary' : '' }}">
                                            <td class="p-4 align-middle font-mono font-bold">{{ $shift->display_id }}</td>
                                            <td class="p-4 align-middle">
                                                <div class="font-mono font-bold text-xs">{{ $shift->bus?->display_id ?? 'Bus —' }}</div>
                                                <span class="text-[11px] text-muted-foreground">{{ $driverUser->name ?? 'Unknown Driver' }}</span>
                                            </td>
                                            <td class="p-4 align-middle font-mono text-xs">
                                                <div>{{ $shift->started_at?->format('M j, Y') ?? '—' }}</div>
                                                <span class="text-muted-foreground text-[11px]">{{ $shift->started_at?->format('H:i') ?? '—' }}</span>
                                            </td>
                                            <td class="p-4 align-middle font-mono text-xs">
                                                <div>{{ number_format($shift->start_km) }} km</div>
                                                <span class="text-muted-foreground text-[11px]">{{ $shift->end_km ? number_format($shift->end_km) . ' km' : 'En route' }}</span>
                                            </td>
                                            <td class="p-4 align-middle font-mono font-bold text-xs">
                                                {{ $shift->distance_km !== null ? number_format($shift->distance_km, 1) . ' km' : '—' }}
                                            </td>
                                            <td class="p-4 align-middle">
                                                <span class="shift-badge inline-flex items-center rounded-md border px-2 py-0.5 text-[11px] font-semibold transition-colors {{ $shiftBadgeClass }}">
                                                    {{ $statusLabel }}
                                                </span>
                                            </td>
                                            <td class="p-4 align-middle text-right">
                                                <div class="inline-flex items-center gap-1.5 justify-end">
                                                    <button type="button"
                                                            onclick="event.stopPropagation(); inspectShiftById('{{ $shift->id }}')"
                                                            title="Inspect in side desk"
                                                            class="inline-flex items-center justify-center rounded-md border border-input bg-background h-8 w-8 hover:bg-accent hover:text-accent-foreground text-muted-foreground shadow-xs transition-colors">
                                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                    <a href="{{ route('shifts.show', $shift->id) }}"
                                                       title="Open Dedicated Shift Page with GPS Map"
                                                       class="inline-flex items-center justify-center rounded-md border border-input bg-background h-8 w-8 hover:bg-primary/10 hover:text-primary text-muted-foreground shadow-xs transition-colors">
                                                        <i data-lucide="map" class="w-3.5 h-3.5"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <script>
                                            window.__shiftsRegistry = window.__shiftsRegistry || {};
                                            window.__shiftsRegistry['{{ $shift->id }}'] = @json($shiftDataJson);
                                        </script>
                                    @empty
                                        <tr class="empty-row">
                                            <td colspan="7" class="p-8 text-center text-muted-foreground">
                                                No shifts found matching your query.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Inspection Desk (Selected Shift Detail Card) (1/3) -->
                    <div id="inspectionDeskCard" class="rounded-xl border bg-card text-card-foreground shadow-xs p-5 space-y-4 transition-all">
                        <div class="flex items-center justify-between pb-3 border-b">
                            <div class="flex items-center gap-2">
                                <i data-lucide="shield-check" class="w-4 h-4 text-primary"></i>
                                <h3 class="text-sm font-semibold leading-none">Inspection Desk</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold" id="deskShiftDisplayId">{{ $selected?->display_id ?? 'Shift #—' }}</span>
                                <span id="deskStatusBadge" class="inline-flex items-center rounded-md border px-2 py-0.5 text-[10px] font-semibold">
                                    {{ $selected ? ($selected->status === 'verified' ? 'Verified' : ($selected->status === 'in_progress' ? 'Active' : ($selected->end_km === null ? 'Flagged' : 'Pending Audit'))) : '—' }}
                                </span>
                                <a id="deskFullPageLink" href="{{ $selected ? route('shifts.show', $selected->id) : '#' }}" target="_self" class="inline-flex items-center gap-1 text-[11px] font-medium text-primary hover:underline ml-1" title="Open Dedicated Page with Live Map">
                                    <i data-lucide="external-link" class="w-3 h-3"></i>
                                    <span>Full Page</span>
                                </a>
                            </div>
                        </div>

                        <div id="deskEmptyState" class="{{ $selected ? 'hidden' : '' }} py-12 text-center text-xs text-muted-foreground">
                            Select a shift row from the table to inspect telemetry &amp; meter images.
                        </div>

                        <div id="deskContentState" class="{{ $selected ? '' : 'hidden' }} space-y-4">
                            <!-- Visual Lifecycle Timeline -->
                            <div class="p-3.5 rounded-lg border bg-muted/30 space-y-2">
                                <div class="flex items-center justify-between text-xs font-medium text-muted-foreground uppercase font-mono">
                                    <span>Lifecycle Track</span>
                                    <span id="deskLifecycleStatus" class="text-[11px] font-bold text-foreground font-sans"></span>
                                </div>
                                <div class="grid grid-cols-4 gap-1 text-[11px] pt-1">
                                    <!-- 1. Start Shift -->
                                    <div class="flex flex-col items-center text-center gap-1">
                                        <div id="trackStartDot" class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px] font-bold shadow-xs">
                                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                        </div>
                                        <span class="font-medium text-[11px]">Start</span>
                                        <span id="trackStartTime" class="text-[10px] text-muted-foreground"></span>
                                    </div>
                                    <!-- 2. Transit Duty -->
                                    <div class="flex flex-col items-center text-center gap-1">
                                        <div id="trackTransitDot" class="w-6 h-6 rounded-full bg-muted border text-muted-foreground flex items-center justify-center text-[10px] font-bold shadow-xs">
                                            <i data-lucide="bus" class="w-3.5 h-3.5"></i>
                                        </div>
                                        <span class="font-medium text-[11px]">Duty</span>
                                        <span id="trackTransitSub" class="text-[10px] text-muted-foreground">Route</span>
                                    </div>
                                    <!-- 3. End Shift -->
                                    <div class="flex flex-col items-center text-center gap-1">
                                        <div id="trackEndDot" class="w-6 h-6 rounded-full bg-muted border text-muted-foreground flex items-center justify-center text-[10px] font-bold shadow-xs">
                                            <i data-lucide="flag" class="w-3.5 h-3.5"></i>
                                        </div>
                                        <span class="font-medium text-[11px]">End</span>
                                        <span id="trackEndTime" class="text-[10px] text-muted-foreground"></span>
                                    </div>
                                    <!-- 4. Desk Verification -->
                                    <div class="flex flex-col items-center text-center gap-1">
                                        <div id="trackVerifyDot" class="w-6 h-6 rounded-full bg-muted border text-muted-foreground flex items-center justify-center text-[10px] font-bold shadow-xs">
                                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                                        </div>
                                        <span class="font-medium text-[11px]">Audit</span>
                                        <span id="trackVerifySub" class="text-[10px] text-muted-foreground">Desk</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Telemetry Specs Grid -->
                            <div class="space-y-2 text-xs">
                                <div class="flex justify-between items-center py-1 border-b border-border/40">
                                    <span class="text-muted-foreground">Vehicle:</span>
                                    <span class="font-mono font-bold" id="deskBusInfo">
                                        {{ $selected?->bus?->display_id }} ({{ $selected?->bus?->plate }})
                                    </span>
                                </div>
                                <div class="flex justify-between items-center py-1 border-b border-border/40">
                                    <span class="text-muted-foreground">Driver:</span>
                                    <span class="font-medium" id="deskDriverInfo">
                                        {{ $selected?->driver?->user?->name ?? 'Unassigned' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center py-1 border-b border-border/40">
                                    <span class="text-muted-foreground">Assigned Route:</span>
                                    <span class="font-medium text-foreground text-right" id="deskRouteLabel">
                                        {{ $selected?->trip ? ($selected->trip->origin . ' → ' . $selected->trip->destination) : 'Depot Service Round' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center py-1 border-b border-border/40">
                                    <span class="text-muted-foreground">Start Reading:</span>
                                    <span class="font-mono font-bold" id="deskStartKm">
                                        {{ $selected ? number_format($selected->start_km) . ' km' : '—' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center py-1 border-b border-border/40">
                                    <span class="text-muted-foreground">End Reading:</span>
                                    <span class="font-mono font-bold" id="deskEndKm">
                                        {{ $selected && $selected->end_km ? number_format($selected->end_km) . ' km' : 'Pending Close' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center pt-2 font-medium">
                                    <span>Distance Traveled:</span>
                                    <span class="font-mono font-bold text-primary text-sm" id="deskDistanceKm">
                                        {{ $selected && $selected->distance_km !== null ? number_format($selected->distance_km, 1) . ' km' : '—' }}
                                    </span>
                                </div>
                                <div class="pt-2 flex justify-end">
                                    <button type="button"
                                            onclick="openAdjustMileageModal()"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md border border-input bg-card hover:bg-muted text-[11px] font-medium text-foreground hover:text-primary transition-colors cursor-pointer shadow-2xs"
                                            title="Manually calibrate or adjust odometer numbers">
                                        <i data-lucide="sliders" class="w-3 h-3 text-primary"></i>
                                        <span>Adjust Mileage Range</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Photo Proofs (Start & End Meter Images) -->
                            <div class="pt-2 border-t space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-muted-foreground uppercase font-mono">Meter Verification Photos</span>
                                    <span class="text-[10px] text-muted-foreground">Click to inspect</span>
                                </div>

                                <!-- Start Meter Box -->
                                <div id="deskStartMeterBox">
                                    <div id="deskStartMeterContainer" class="{{ $selected && $selected->start_meter_image_url ? '' : 'hidden' }} rounded-lg overflow-hidden border bg-muted h-36 relative group cursor-pointer"
                                         onclick="openPhotoModal(document.getElementById('deskStartMeterImg').src, 'Start Meter Capture', document.getElementById('deskStartKm').textContent)">
                                        <img id="deskStartMeterImg"
                                             src="{{ $selected?->start_meter_image_url }}"
                                             alt="Start Meter"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"/>
                                        <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded bg-background/90 text-[10px] font-mono border flex items-center gap-1 shadow-xs">
                                            <i data-lucide="maximize-2" class="w-2.5 h-2.5"></i>
                                            <span>Start Meter Capture</span>
                                        </span>
                                    </div>
                                    <div id="deskStartMeterEmpty" class="{{ $selected && $selected->start_meter_image_url ? 'hidden' : '' }} rounded-lg border border-dashed p-3 text-center text-muted-foreground text-xs">
                                        No start meter photo recorded
                                    </div>
                                </div>

                                <!-- End Meter Box -->
                                <div id="deskEndMeterBox">
                                    <div id="deskEndMeterContainer" class="{{ $selected && $selected->end_meter_image_url ? '' : 'hidden' }} rounded-lg overflow-hidden border bg-muted h-36 relative group cursor-pointer mt-2"
                                         onclick="openPhotoModal(document.getElementById('deskEndMeterImg').src, 'End Meter Capture', document.getElementById('deskEndKm').textContent)">
                                        <img id="deskEndMeterImg"
                                             src="{{ $selected?->end_meter_image_url }}"
                                             alt="End Meter"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"/>
                                        <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded bg-background/90 text-[10px] font-mono border flex items-center gap-1 shadow-xs">
                                            <i data-lucide="maximize-2" class="w-2.5 h-2.5"></i>
                                            <span>End Meter Capture</span>
                                        </span>
                                    </div>
                                    <div id="deskEndMeterEmpty" class="{{ $selected && $selected->end_meter_image_url ? 'hidden' : '' }} rounded-lg border border-dashed p-3 text-center text-muted-foreground text-xs mt-2">
                                        End meter photo pending shift completion
                                    </div>
                                </div>
                            </div>

                            <!-- Shift Notes (if any) -->
                            <div id="deskNotesContainer" class="{{ $selected && ($selected->start_notes || $selected->end_notes) ? '' : 'hidden' }} p-3 rounded-lg bg-muted/40 border text-xs space-y-1">
                                <span class="font-medium text-foreground">Operator Notes:</span>
                                <p class="text-muted-foreground italic text-[11px]" id="deskNotesText">
                                    {{ $selected?->start_notes ?: $selected?->end_notes }}
                                </p>
                            </div>

                            <!-- Auditor Action Desk: Conditional views -->
                            <div class="pt-2 border-t">
                                <!-- State 1: In Progress (Trip started, but not yet ended -> No approval button) -->
                                <div id="deskActionInProgress" class="p-3.5 rounded-lg border border-amber-500/25 bg-amber-500/10 space-y-1.5 hidden">
                                    <div class="flex items-center gap-2 text-amber-700 dark:text-amber-400 text-xs font-semibold">
                                        <i data-lucide="clock" class="w-4 h-4 text-amber-500"></i>
                                        <span>Shift In Progress &bull; Driver Active</span>
                                    </div>
                                    <p class="text-[11px] text-muted-foreground leading-relaxed">
                                        Vehicle is en route. The approval &amp; verification desk unlocks automatically once the driver completes the trip and records the closing meter photo.
                                    </p>
                                </div>

                                <!-- State 2: Completed (Trip started AND ended -> Approval button unlocked!) -->
                                <div id="deskActionCompleted" class="space-y-2.5 hidden">
                                    <div class="p-3 rounded-lg border border-blue-500/25 bg-blue-500/10 flex items-start gap-2 text-xs">
                                        <i data-lucide="check-circle-2" class="w-4 h-4 text-blue-600 mt-0.5 shrink-0"></i>
                                        <div>
                                            <span class="font-semibold text-blue-700 dark:text-blue-400">Ready for Verification</span>
                                            <p class="text-[11px] text-muted-foreground mt-0.5">Start and end meter photos are logged. Review readings and approve shift mileage.</p>
                                        </div>
                                    </div>
                                    <button type="button"
                                            id="deskApproveBtn"
                                            onclick="verifyCurrentShift()"
                                            class="w-full inline-flex items-center justify-center rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold py-2.5 px-4 shadow transition-all gap-1.5 cursor-pointer">
                                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                                        <span>Approve &amp; Verify Shift</span>
                                    </button>
                                    <button type="button"
                                            id="deskFlagBtn"
                                            onclick="flagCurrentShift()"
                                            class="w-full inline-flex items-center justify-center rounded-lg border border-input bg-card text-foreground hover:bg-muted text-xs font-medium py-2 px-4 shadow-xs transition-colors gap-1.5 cursor-pointer">
                                        <i data-lucide="flag" class="w-3.5 h-3.5 text-destructive"></i>
                                        <span>Flag for Re-inspection</span>
                                    </button>
                                </div>

                                <!-- State 3: Verified (Already approved by desk -> Manageable status) -->
                                <div id="deskActionVerified" class="p-3.5 rounded-lg border border-emerald-500/30 bg-emerald-500/10 space-y-2 text-xs hidden">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5 font-bold text-emerald-700 dark:text-emerald-400 text-xs">
                                            <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                                            <span>Shift Verified &amp; Approved</span>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-600 text-white">
                                            VERIFIED
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-muted-foreground leading-relaxed">
                                        Start &amp; end meter readings, instrument cluster photos, and calculated mileage (<strong id="deskVerifiedDist" class="text-foreground"></strong>) verified by inspection desk.
                                    </p>
                                    <div class="pt-2 border-t border-emerald-500/20 flex items-center justify-between text-[11px]">
                                        <span class="text-muted-foreground">Manage status:</span>
                                        <div class="flex items-center gap-2">
                                            <button type="button" onclick="revertShiftStatus('completed')" class="font-medium text-foreground hover:underline cursor-pointer">
                                                Revert to Pending
                                            </button>
                                            <span class="text-muted-foreground">&bull;</span>
                                            <button type="button" onclick="flagCurrentShift()" class="font-medium text-destructive hover:underline cursor-pointer">
                                                Flag Shift
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- State 4: Flagged for audit -->
                                <div id="deskActionFlagged" class="p-3.5 rounded-lg border border-destructive/30 bg-destructive/10 space-y-2.5 text-xs hidden">
                                    <div class="flex items-center gap-2 font-bold text-destructive text-xs">
                                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                                        <span>Flagged for Audit Review</span>
                                    </div>
                                    <p class="text-[11px] text-muted-foreground leading-relaxed">
                                        Discrepancy detected in meter readings or closing photo. Requires supervisor physical recount.
                                    </p>
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                                onclick="openAdjustMileageModal()"
                                                class="flex-1 inline-flex items-center justify-center rounded-lg border border-input bg-card text-foreground hover:bg-muted text-xs font-semibold py-2 px-3 shadow-xs gap-1.5 cursor-pointer">
                                            <i data-lucide="sliders" class="w-3.5 h-3.5 text-primary"></i>
                                            <span>Calibrate Mileage</span>
                                        </button>
                                        <button type="button"
                                                id="deskClearFlagBtn"
                                                onclick="verifyCurrentShift()"
                                                class="flex-1 inline-flex items-center justify-center rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold py-2 px-3 shadow-xs gap-1.5 cursor-pointer">
                                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                                            <span>Clear &amp; Verify</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <!-- ================= shadcn Dialog: METER PHOTO MODAL ================= -->
    <div id="photoModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-xs hidden">
        <div class="relative w-full max-w-lg rounded-xl border bg-background p-6 shadow-lg modal-content text-left">
            <div class="flex items-center justify-between pb-3 border-b">
                <div>
                    <h3 class="text-base font-semibold leading-none tracking-tight" id="modalPhotoTitle">Meter Capture Inspection</h3>
                    <p class="text-xs text-muted-foreground mt-1" id="modalPhotoSubtitle">Optical verification of instrument cluster</p>
                </div>
                <button type="button" onclick="window.shadcnModal.close('photoModal')" class="text-muted-foreground hover:text-foreground">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="mt-4 rounded-lg overflow-hidden border bg-muted h-80 relative flex items-center justify-center">
                <img id="modalPhotoImg" src="" alt="Meter High-Res" class="max-h-full max-w-full object-contain"/>
            </div>
            <div class="mt-4 flex justify-between items-center text-xs">
                <span class="text-muted-foreground">Odometer Reading:</span>
                <span class="font-mono font-bold text-sm text-foreground" id="modalPhotoOdo"></span>
            </div>
        </div>
    </div>

    <!-- ================= shadcn Dialog: ADJUST MILEAGE MODAL ================= -->
    <div id="adjustMileageModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-xs hidden">
        <div class="relative w-full max-w-md rounded-xl border bg-background p-6 shadow-lg modal-content text-left">
            <div class="flex items-center justify-between pb-3 border-b">
                <div>
                    <h3 class="text-base font-semibold leading-none tracking-tight">Adjust Mileage Range</h3>
                    <p class="text-xs text-muted-foreground mt-1" id="mileageShiftRef">Calibrate odometer meter readings</p>
                </div>
                <button type="button" onclick="window.shadcnModal.close('adjustMileageModal')" class="text-muted-foreground hover:text-foreground">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form onsubmit="submitAdjustMileage(event)" class="mt-4 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-medium">Start Meter (KM)</label>
                        <input type="number" id="adjustStartKm" step="0.1" min="0" required oninput="calculatePreviewDistance()"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs font-mono focus:ring-1 focus:ring-ring"/>
                    </div>
                    <div>
                        <label class="text-xs font-medium">End Meter (KM)</label>
                        <input type="number" id="adjustEndKm" step="0.1" min="0" oninput="calculatePreviewDistance()"
                               placeholder="Optional if active"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs font-mono focus:ring-1 focus:ring-ring"/>
                    </div>
                </div>
                <div class="p-2.5 rounded-lg bg-muted/60 border text-xs flex items-center justify-between">
                    <span class="text-muted-foreground">Calculated Distance:</span>
                    <span id="previewNetDistance" class="font-mono font-bold text-xs text-primary">—</span>
                </div>
                <div>
                    <label class="text-xs font-medium">Adjustment Rationale / Supervisor Note</label>
                    <textarea id="adjustReason" rows="2" placeholder="e.g. Optical OCR misread last digit; recalibrated with physical cluster capture."
                              class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-xs shadow-xs focus:ring-1 focus:ring-ring"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t">
                    <button type="button" onclick="window.shadcnModal.close('adjustMileageModal')" class="rounded-md border px-3 py-1.5 text-xs font-medium hover:bg-muted">
                        Cancel
                    </button>
                    <button type="submit" id="submitAdjustMileageBtn" class="rounded-md bg-primary text-primary-foreground px-4 py-1.5 text-xs font-semibold hover:bg-primary/90 shadow-xs inline-flex items-center gap-1.5">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        <span>Save &amp; Recalculate</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentInspectedShiftId = '{{ $selected?->id }}';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        function inspectShiftById(shiftId) {
            const shift = (window.__shiftsRegistry && window.__shiftsRegistry[shiftId]) || null;
            if (!shift) return;

            currentInspectedShiftId = shiftId;

            // 1. Update row highlights in DataTable
            document.querySelectorAll('.shift-table-row').forEach(row => {
                row.classList.remove('bg-muted/60', 'font-medium', 'border-l-2', 'border-primary');
            });
            const activeRow = document.getElementById('shift-row-' + shiftId);
            if (activeRow) {
                activeRow.classList.add('bg-muted/60', 'font-medium', 'border-l-2', 'border-primary');
            }

            // 2. Update Inspection Desk details instantly (NO page reload)
            const emptyState = document.getElementById('deskEmptyState');
            const contentState = document.getElementById('deskContentState');

            if (emptyState) emptyState.classList.add('hidden');
            if (contentState) contentState.classList.remove('hidden');

            // Reset verify buttons to prevent loader from hanging on status revert
            const approveBtn = document.getElementById('deskApproveBtn');
            if (approveBtn) {
                approveBtn.disabled = false;
                approveBtn.innerHTML = '<i data-lucide="shield-check" class="w-4 h-4"></i><span>Approve &amp; Verify Shift</span>';
            }
            const clearFlagBtn = document.getElementById('deskClearFlagBtn');
            if (clearFlagBtn) {
                clearFlagBtn.disabled = false;
                clearFlagBtn.innerHTML = '<i data-lucide="shield-check" class="w-3.5 h-3.5"></i><span>Clear &amp; Verify</span>';
            }

            document.getElementById('deskShiftDisplayId').textContent = shift.display_id;

            const fullPageLink = document.getElementById('deskFullPageLink');
            if (fullPageLink) {
                fullPageLink.href = '/shifts/' + shiftId;
            }

            const statusBadge = document.getElementById('deskStatusBadge');
            if (statusBadge) {
                statusBadge.textContent = shift.status_label;
                statusBadge.className = 'inline-flex items-center rounded-md border px-2 py-0.5 text-[10px] font-semibold ' + shift.status_badge_class;
            }

            document.getElementById('deskBusInfo').textContent = shift.bus_id + ' (' + shift.bus_plate + ')';
            document.getElementById('deskDriverInfo').textContent = shift.driver_name + ' (' + shift.driver_badge + ')';
            document.getElementById('deskStartKm').textContent = shift.start_km;
            document.getElementById('deskEndKm').textContent = shift.end_km;
            document.getElementById('deskDistanceKm').textContent = shift.distance_km;
            document.getElementById('deskRouteLabel').textContent = shift.route_label;

            // Update Lifecycle Pipeline Track
            const lifecycleStatus = document.getElementById('deskLifecycleStatus');
            const trackStartTime = document.getElementById('trackStartTime');
            const trackEndTime = document.getElementById('trackEndTime');
            const trackTransitDot = document.getElementById('trackTransitDot');
            const trackTransitSub = document.getElementById('trackTransitSub');
            const trackEndDot = document.getElementById('trackEndDot');
            const trackVerifyDot = document.getElementById('trackVerifyDot');
            const trackVerifySub = document.getElementById('trackVerifySub');

            if (trackStartTime) trackStartTime.textContent = shift.started_at_time || 'Started';
            if (trackEndTime) trackEndTime.textContent = shift.ended_at_time && shift.ended_at_time !== '—' ? shift.ended_at_time : 'Pending';

            if (shift.status === 'in_progress' || !shift.has_start_and_end) {
                if (lifecycleStatus) {
                    lifecycleStatus.textContent = 'Active On Route';
                    lifecycleStatus.className = 'text-[11px] font-bold text-amber-500 font-sans';
                }
                if (trackTransitDot) trackTransitDot.className = 'w-6 h-6 rounded-full bg-amber-500 text-white flex items-center justify-center text-[10px] font-bold animate-pulse shadow-xs';
                if (trackTransitSub) trackTransitSub.textContent = 'Underway';
                if (trackEndDot) trackEndDot.className = 'w-6 h-6 rounded-full bg-muted border text-muted-foreground flex items-center justify-center text-[10px] font-bold shadow-xs';
                if (trackVerifyDot) trackVerifyDot.className = 'w-6 h-6 rounded-full bg-muted border text-muted-foreground flex items-center justify-center text-[10px] font-bold shadow-xs';
                if (trackVerifySub) trackVerifySub.textContent = 'Locked';
            } else if (shift.status === 'verified') {
                if (lifecycleStatus) {
                    lifecycleStatus.textContent = 'Verified & Approved';
                    lifecycleStatus.className = 'text-[11px] font-bold text-emerald-600 font-sans';
                }
                if (trackTransitDot) trackTransitDot.className = 'w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px] font-bold shadow-xs';
                if (trackTransitSub) trackTransitSub.textContent = 'Complete';
                if (trackEndDot) trackEndDot.className = 'w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px] font-bold shadow-xs';
                if (trackVerifyDot) trackVerifyDot.className = 'w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold shadow-xs';
                if (trackVerifySub) trackVerifySub.textContent = 'Approved';
            } else if (shift.status === 'flagged') {
                if (lifecycleStatus) {
                    lifecycleStatus.textContent = 'Flagged for Audit';
                    lifecycleStatus.className = 'text-[11px] font-bold text-destructive font-sans';
                }
                if (trackTransitDot) trackTransitDot.className = 'w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px] font-bold shadow-xs';
                if (trackTransitSub) trackTransitSub.textContent = 'Complete';
                if (trackEndDot) trackEndDot.className = 'w-6 h-6 rounded-full bg-amber-500 text-white flex items-center justify-center text-[10px] font-bold shadow-xs';
                if (trackVerifyDot) trackVerifyDot.className = 'w-6 h-6 rounded-full bg-destructive text-white flex items-center justify-center text-[10px] font-bold shadow-xs';
                if (trackVerifySub) trackVerifySub.textContent = 'Flagged';
            } else {
                // Completed & Pending audit
                if (lifecycleStatus) {
                    lifecycleStatus.textContent = 'Ready for Verification';
                    lifecycleStatus.className = 'text-[11px] font-bold text-blue-600 font-sans';
                }
                if (trackTransitDot) trackTransitDot.className = 'w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px] font-bold shadow-xs';
                if (trackTransitSub) trackTransitSub.textContent = 'Complete';
                if (trackEndDot) trackEndDot.className = 'w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px] font-bold shadow-xs';
                if (trackVerifyDot) trackVerifyDot.className = 'w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold shadow-xs animate-bounce';
                if (trackVerifySub) trackVerifySub.textContent = 'Action Ready';
            }

            // Photos
            const startContainer = document.getElementById('deskStartMeterContainer');
            const startEmpty = document.getElementById('deskStartMeterEmpty');
            const startImg = document.getElementById('deskStartMeterImg');
            if (shift.start_meter_image_url) {
                startImg.src = shift.start_meter_image_url;
                startContainer.classList.remove('hidden');
                startEmpty.classList.add('hidden');
            } else {
                startContainer.classList.add('hidden');
                startEmpty.classList.remove('hidden');
            }

            const endContainer = document.getElementById('deskEndMeterContainer');
            const endEmpty = document.getElementById('deskEndMeterEmpty');
            const endImg = document.getElementById('deskEndMeterImg');
            if (shift.end_meter_image_url) {
                endImg.src = shift.end_meter_image_url;
                endContainer.classList.remove('hidden');
                endEmpty.classList.add('hidden');
            } else {
                endContainer.classList.add('hidden');
                endEmpty.classList.remove('hidden');
            }

            // Notes
            const notesContainer = document.getElementById('deskNotesContainer');
            const notesText = document.getElementById('deskNotesText');
            const combinedNotes = (shift.start_notes ? 'Start: ' + shift.start_notes : '') + (shift.end_notes ? ' | End: ' + shift.end_notes : '');
            if (combinedNotes.trim() && notesContainer && notesText) {
                notesText.textContent = combinedNotes;
                notesContainer.classList.remove('hidden');
            } else if (notesContainer) {
                notesContainer.classList.add('hidden');
            }

            // Auditor Action Desk: Switch visible view
            const boxInProgress = document.getElementById('deskActionInProgress');
            const boxCompleted = document.getElementById('deskActionCompleted');
            const boxVerified = document.getElementById('deskActionVerified');
            const boxFlagged = document.getElementById('deskActionFlagged');

            if (boxInProgress) boxInProgress.classList.add('hidden');
            if (boxCompleted) boxCompleted.classList.add('hidden');
            if (boxVerified) boxVerified.classList.add('hidden');
            if (boxFlagged) boxFlagged.classList.add('hidden');

            if (shift.status === 'verified') {
                if (boxVerified) boxVerified.classList.remove('hidden');
                const distSpan = document.getElementById('deskVerifiedDist');
                if (distSpan) distSpan.textContent = shift.distance_km;
            } else if (shift.status === 'flagged') {
                if (boxFlagged) boxFlagged.classList.remove('hidden');
            } else if (shift.status === 'in_progress' || !shift.has_start_and_end) {
                // Trip started, not yet ended -> Locked notice, NO approval button
                if (boxInProgress) boxInProgress.classList.remove('hidden');
            } else {
                // Trip started AND ended -> Approval button show!
                if (boxCompleted) boxCompleted.classList.remove('hidden');
            }

            // Refresh icons if lucide exists
            if (window.lucide) window.lucide.createIcons();

            // 3. Update browser history cleanly without full page refresh
            window.history.replaceState(null, '', '/shifts/' + shift.id);
        }

        function openPhotoModal(imgSrc, title, odo) {
            if (!imgSrc) return;
            document.getElementById('modalPhotoImg').src = imgSrc;
            document.getElementById('modalPhotoTitle').textContent = title;
            document.getElementById('modalPhotoOdo').textContent = odo;
            window.shadcnModal.open('photoModal');
        }

        async function verifyCurrentShift() {
            if (!currentInspectedShiftId) return;
            const shift = window.__shiftsRegistry && window.__shiftsRegistry[currentInspectedShiftId];
            if (!shift) return;

            const btn = document.getElementById('deskApproveBtn');
            const originalContent = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin"></i><span>Verifying...</span>';
                if (window.lucide) window.lucide.createIcons();
            }

            try {
                const res = await fetch('/shifts/' + currentInspectedShiftId + '/verify', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();

                if (!res.ok || !data.success) {
                    window.toast?.error('Verification Failed', data.message || 'Unable to verify shift.');
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = originalContent;
                        if (window.lucide) window.lucide.createIcons();
                    }
                    return;
                }

                // Update registry
                const prevStatus = shift.status;
                shift.status = 'verified';
                shift.status_label = 'Verified';
                shift.status_badge_class = 'border-emerald-500/30 bg-emerald-500/15 text-emerald-600 dark:text-emerald-400';
                shift.filter_status = 'verified';

                // Update row in table
                const row = document.getElementById('shift-row-' + currentInspectedShiftId);
                if (row) {
                    row.setAttribute('data-status', 'verified');
                    const badgeSpan = row.querySelector('.shift-badge');
                    if (badgeSpan) {
                        badgeSpan.className = 'shift-badge inline-flex items-center rounded-md border px-2 py-0.5 text-[11px] font-semibold transition-colors border-emerald-500/30 bg-emerald-500/15 text-emerald-600 dark:text-emerald-400';
                        badgeSpan.textContent = 'Verified';
                    }
                }

                // Re-inspect to render Verified card
                inspectShiftById(currentInspectedShiftId);

                // Update top counters
                adjustCounter('statCountVerified', 1);
                if (prevStatus === 'completed') adjustCounter('statCountCompleted', -1);
                if (prevStatus === 'flagged') adjustCounter('statCountFlagged', -1);

                window.toast?.success(
                    'Shift Verified & Approved',
                    (shift.display_id || 'Shift') + ' mileage and telemetry verified.'
                );
            } catch (err) {
                window.toast?.error('Network Error', 'Could not reach server to verify shift.');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalContent || '<i data-lucide="shield-check" class="w-4 h-4"></i><span>Approve &amp; Verify Shift</span>';
                    if (window.lucide) window.lucide.createIcons();
                }
            }
        }

        async function flagCurrentShift() {
            if (!currentInspectedShiftId) return;
            const shift = window.__shiftsRegistry && window.__shiftsRegistry[currentInspectedShiftId];
            if (!shift) return;

            try {
                const res = await fetch('/shifts/' + currentInspectedShiftId + '/flag', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ reason: 'Flagged for supervisor physical recount.' }),
                });
                const data = await res.json();

                if (!res.ok || !data.success) {
                    window.toast?.error('Action Failed', data.message || 'Unable to flag shift.');
                    return;
                }

                const prevStatus = shift.status;
                shift.status = 'flagged';
                shift.status_label = 'Flagged';
                shift.status_badge_class = 'border-destructive/30 bg-destructive/15 text-destructive';
                shift.filter_status = 'flagged';

                const row = document.getElementById('shift-row-' + currentInspectedShiftId);
                if (row) {
                    row.setAttribute('data-status', 'flagged');
                    const badgeSpan = row.querySelector('.shift-badge');
                    if (badgeSpan) {
                        badgeSpan.className = 'shift-badge inline-flex items-center rounded-md border px-2 py-0.5 text-[11px] font-semibold transition-colors border-destructive/30 bg-destructive/15 text-destructive';
                        badgeSpan.textContent = 'Flagged';
                    }
                }

                inspectShiftById(currentInspectedShiftId);

                adjustCounter('statCountFlagged', 1);
                if (prevStatus === 'completed') adjustCounter('statCountCompleted', -1);
                if (prevStatus === 'verified') adjustCounter('statCountVerified', -1);

                window.toast?.warning(
                    'Shift Flagged for Audit',
                    (shift.display_id || 'Shift') + ' marked for physical meter audit.'
                );
            } catch (err) {
                window.toast?.error('Network Error', 'Could not reach server to flag shift.');
            }
        }

        async function revertShiftStatus(targetStatus) {
            if (!currentInspectedShiftId) return;
            const shift = window.__shiftsRegistry && window.__shiftsRegistry[currentInspectedShiftId];
            if (!shift) return;

            try {
                const res = await fetch('/shifts/' + currentInspectedShiftId + '/status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ status: targetStatus }),
                });
                const data = await res.json();

                if (!res.ok || !data.success) {
                    window.toast?.error('Update Failed', data.message || 'Unable to revert shift status.');
                    return;
                }

                const prevStatus = shift.status;
                shift.status = targetStatus;
                shift.status_label = targetStatus === 'completed' ? 'Pending Audit' : ucfirst(targetStatus);
                shift.status_badge_class = targetStatus === 'completed'
                    ? 'border-blue-500/30 bg-blue-500/15 text-blue-600 dark:text-blue-400'
                    : 'border-transparent bg-secondary text-secondary-foreground';
                shift.filter_status = targetStatus;

                const row = document.getElementById('shift-row-' + currentInspectedShiftId);
                if (row) {
                    row.setAttribute('data-status', targetStatus);
                    const badgeSpan = row.querySelector('.shift-badge');
                    if (badgeSpan) {
                        badgeSpan.className = 'shift-badge inline-flex items-center rounded-md border px-2 py-0.5 text-[11px] font-semibold transition-colors ' + shift.status_badge_class;
                        badgeSpan.textContent = shift.status_label;
                    }
                }

                inspectShiftById(currentInspectedShiftId);

                if (prevStatus === 'verified') adjustCounter('statCountVerified', -1);
                if (targetStatus === 'completed') adjustCounter('statCountCompleted', 1);

                window.toast?.info('Status Reverted', (shift.display_id || 'Shift') + ' reverted to pending audit.');
            } catch (err) {
                window.toast?.error('Network Error', 'Could not reach server.');
            }
        }

        function adjustCounter(id, delta) {
            const el = document.getElementById(id);
            if (!el) return;
            const val = parseInt(el.textContent.replace(/\D/g, ''), 10) || 0;
            el.textContent = Math.max(0, val + delta);
        }

        function openAdjustMileageModal() {
            if (!currentInspectedShiftId) return;
            const shift = window.__shiftsRegistry && window.__shiftsRegistry[currentInspectedShiftId];
            if (!shift) return;

            document.getElementById('mileageShiftRef').textContent = (shift.display_id || 'Shift') + ' • ' + (shift.bus_id || 'Bus');
            const startKm = parseFloat(String(shift.start_km).replace(/[^0-9.]/g, '')) || 0;
            const endKm = shift.end_km && !String(shift.end_km).includes('Pending')
                ? parseFloat(String(shift.end_km).replace(/[^0-9.]/g, ''))
                : '';

            document.getElementById('adjustStartKm').value = startKm;
            document.getElementById('adjustEndKm').value = endKm;
            document.getElementById('adjustReason').value = '';
            calculatePreviewDistance();
            window.shadcnModal.open('adjustMileageModal');
            if (window.lucide) window.lucide.createIcons();
        }

        function calculatePreviewDistance() {
            const start = parseFloat(document.getElementById('adjustStartKm').value) || 0;
            const endVal = document.getElementById('adjustEndKm').value;
            const end = endVal !== '' ? parseFloat(endVal) : null;
            const previewEl = document.getElementById('previewNetDistance');

            if (end !== null && !isNaN(end)) {
                const diff = (end - start).toFixed(1);
                if (diff < 0) {
                    previewEl.textContent = 'Invalid: End reading cannot be lower than start';
                    previewEl.className = 'font-mono font-bold text-xs text-destructive';
                } else {
                    previewEl.textContent = diff + ' km net distance';
                    previewEl.className = 'font-mono font-bold text-xs text-primary';
                }
            } else {
                previewEl.textContent = 'Shift in progress (no closing reading yet)';
                previewEl.className = 'font-mono text-xs text-muted-foreground';
            }
        }

        async function submitAdjustMileage(e) {
            e.preventDefault();
            if (!currentInspectedShiftId) return;
            const shift = window.__shiftsRegistry && window.__shiftsRegistry[currentInspectedShiftId];
            if (!shift) return;

            const startKm = parseFloat(document.getElementById('adjustStartKm').value);
            const endKmVal = document.getElementById('adjustEndKm').value;
            const endKm = endKmVal !== '' ? parseFloat(endKmVal) : null;
            const reason = document.getElementById('adjustReason').value;

            if (endKm !== null && endKm < startKm) {
                window.toast?.error('Invalid Reading', 'End meter reading cannot be lower than start reading.');
                return;
            }

            const submitBtn = document.getElementById('submitAdjustMileageBtn');
            const orig = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i data-lucide="loader" class="w-3.5 h-3.5 animate-spin"></i><span>Saving...</span>';
            if (window.lucide) window.lucide.createIcons();

            try {
                const res = await fetch('/shifts/' + currentInspectedShiftId + '/mileage', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        start_km: startKm,
                        end_km: endKm,
                        adjustment_reason: reason,
                    }),
                });
                const data = await res.json();

                if (!res.ok || !data.success) {
                    window.toast?.error('Calibration Failed', data.message || 'Could not adjust mileage.');
                    return;
                }

                // Update shift registry
                shift.start_km = Number(startKm).toLocaleString() + ' km';
                shift.end_km = endKm !== null ? Number(endKm).toLocaleString() + ' km' : 'Pending Close';
                shift.distance_km = data.distance_km !== null ? Number(data.distance_km).toFixed(1) + ' km' : '—';
                shift.has_start_and_end = (startKm !== null && endKm !== null);

                // Update table row
                const row = document.getElementById('shift-row-' + currentInspectedShiftId);
                if (row) {
                    const cells = row.querySelectorAll('td');
                    if (cells.length >= 6) {
                        cells[3].textContent = shift.start_km + (endKm ? ' → ' + shift.end_km : '');
                        cells[4].textContent = shift.distance_km;
                    }
                }

                // Update detail desk
                inspectShiftById(currentInspectedShiftId);
                window.shadcnModal.close('adjustMileageModal');
                window.toast?.success('Mileage Calibrated', data.message);
            } catch (err) {
                window.toast?.error('Network Error', 'Failed to reach server.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = orig;
                if (window.lucide) window.lucide.createIcons();
            }
        }

        // Initialize first inspection if selected
        document.addEventListener('DOMContentLoaded', () => {
            if (currentInspectedShiftId) {
                inspectShiftById(currentInspectedShiftId);
            }
            initFleetLiveMap();
        });

        // ================= Leaflet Live Fleet Map Engine =================
        let fleetMap = null;
        let fleetMarkers = {};
        let fleetBounds = null;

        function initFleetLiveMap() {
            const container = document.getElementById('fleetLiveMap');
            if (!container) return;

            // Default center: Caracas / Central hub
            fleetMap = L.map('fleetLiveMap', {
                zoomControl: true,
                attributionControl: false
            }).setView([10.4806, -66.9036], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(fleetMap);

            refreshFleetMap();

            // Auto-refresh telemetry every 10 seconds
            setInterval(refreshFleetMap, 10000);
        }

        async function refreshFleetMap() {
            if (!fleetMap) return;
            try {
                const res = await fetch('/telemetry/live', { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                if (!data.success || !Array.isArray(data.buses)) return;

                let onlineCount = 0;
                let offlineCount = 0;
                const validCoords = [];

                // Remove existing markers
                Object.values(fleetMarkers).forEach(m => fleetMap.removeLayer(m));
                fleetMarkers = {};

                data.buses.forEach(bus => {
                    const isOnline = Boolean(bus.is_online);
                    if (isOnline) onlineCount++; else offlineCount++;

                    const rawLat = bus.latitude ?? bus.last_latitude;
                    const rawLng = bus.longitude ?? bus.last_longitude;
                    if (!rawLat || !rawLng) return;

                    const lat = parseFloat(rawLat);
                    const lng = parseFloat(rawLng);
                    if (isNaN(lat) || isNaN(lng) || (lat === 0 && lng === 0)) return;
                    validCoords.push([lat, lng]);

                    const markerHtml = isOnline
                        ? '<div class="bus-marker-online" title="Online pinging"></div>'
                        : '<div class="bus-marker-offline" title="Offline last location"></div>';

                    const icon = L.divIcon({
                        className: 'custom-fleet-pin',
                        html: markerHtml,
                        iconSize: isOnline ? [16, 16] : [14, 14],
                        iconAnchor: isOnline ? [8, 8] : [7, 7]
                    });

                    const marker = L.marker([lat, lng], { icon }).addTo(fleetMap);

                    const popupContent = `
                        <div style="font-family:inherit; min-width:180px; padding:2px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #E5E7EB; padding-bottom:4px; margin-bottom:6px;">
                                <strong style="font-size:13px;">${bus.display_id}</strong>
                                <span style="font-size:9px; font-weight:700; padding:2px 6px; border-radius:4px; ${isOnline ? 'background:#DCFCE7; color:#15803D;' : 'background:#F3F4F6; color:#6B7280;'}">
                                    ${isOnline ? 'ONLINE' : 'OFFLINE'}
                                </span>
                            </div>
                            <div style="font-size:11px; color:#4B5563; line-height:1.4;">
                                <div><b>Plate:</b> ${bus.plate || '—'} &bull; ${bus.model || 'Bus'}</div>
                                <div><b>Corridor:</b> ${bus.transit_route ? `<span style="color:#2563EB; font-weight:600;">${bus.transit_route.route_code}</span>` : 'Unassigned'}</div>
                                <div><b>Driver:</b> ${bus.active_shift?.driver_name || 'No active shift'}</div>
                                <div><b>Speed:</b> ${bus.speed ? Math.round(bus.speed) + ' km/h' : '0 km/h'}</div>
                                <div><b>Status:</b> ${isOnline ? '<span style="color:#10B981;">Pinging Realtime</span>' : `Offline &bull; ${bus.last_seen_human || 'Unknown'}`}</div>
                            </div>
                            ${bus.active_shift ? `
                                <div style="margin-top:8px; padding-top:6px; border-top:1px solid #E5E7EB;">
                                    <a href="/shifts/${bus.active_shift.id}" style="color:#2563EB; font-weight:600; font-size:11px; text-decoration:underline;">
                                        Inspect Active Shift &rarr;
                                    </a>
                                </div>
                            ` : ''}
                        </div>
                    `;

                    marker.bindPopup(popupContent);
                    fleetMarkers[bus.id] = marker;
                });

                const onlineEl = document.getElementById('mapOnlineCount');
                const offlineEl = document.getElementById('mapOfflineCount');
                if (onlineEl) onlineEl.textContent = onlineCount;
                if (offlineEl) offlineEl.textContent = offlineCount;

                if (validCoords.length > 0 && !fleetBounds) {
                    fleetBounds = L.latLngBounds(validCoords);
                    fleetMap.fitBounds(fleetBounds, { padding: [30, 30] });
                }
            } catch (err) {
                console.error('Error fetching fleet live telemetry', err);
            }
        }

        function fitFleetMap() {
            if (fleetMap && fleetBounds) {
                fleetMap.fitBounds(fleetBounds, { padding: [30, 30] });
            }
        }
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</body>
</html>
