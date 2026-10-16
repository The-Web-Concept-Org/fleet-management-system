<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Trip Scheduler &bull; Lancotri</title>
    @include('partials.brand-styles')
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/lancotri-mark.svg') }}">
</head>
<body class="bg-background text-foreground antialiased min-h-screen">
    @include('partials.sidebar')

    <div class="pl-64 flex flex-col min-h-screen">
        @include('partials.topbar', [
            'topbarSearchPlaceholder' => 'Search trip, route, bus, or driver...',
            'topbarSearchId' => 'tripNavSearch',
            'topbarStatus' => $totalTrips . ' trips on the board',
        ])

        <main class="w-full pt-16 flex-1 px-8 py-8 bg-background">
            <div class="flex flex-col w-full gap-6 max-w-7xl mx-auto">

                <!-- Page Header: Title + Toolbar -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b">
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-xs text-muted-foreground font-mono">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-muted border">
                                <i data-lucide="navigation" class="w-3.5 h-3.5 text-primary"></i>
                                <span>TRANSIT DISPATCH BOARD</span>
                            </span>
                            <span>&bull;</span>
                            <span class="inline-flex items-center gap-1.5 text-emerald-600 font-medium">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                Live Assignment Desk
                            </span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">Trip Scheduler</h1>
                            <p class="text-xs text-muted-foreground mt-2 leading-relaxed max-w-2xl">Schedule transit trips, assign available buses, and prevent overlapping driver assignments.</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5">
                        <div class="relative w-64">
                            <i data-lucide="search" class="w-3.5 h-3.5 text-muted-foreground absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <input id="tripSearch"
                                   data-shadcn-table-search="tripsBoardTable"
                                   placeholder="Filter trip #, route, driver..."
                                   class="flex h-9 w-full rounded-md border border-input bg-transparent pl-9 pr-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                        </div>

                        <button id="exportTripsCsvBtn" type="button"
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 gap-2">
                            <i data-lucide="download" class="w-3.5 h-3.5 text-muted-foreground"></i>
                            <span>Export</span>
                        </button>

                        <button id="addTripModalTrigger" type="button" onclick="window.shadcnModal.open('addTripModal')"
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2 gap-2 transition-all">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>Schedule Trip</span>
                        </button>
                    </div>
                </div>

                <!-- 5 Stat Cards (shadcn Card default) -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">Total Trips</span>
                            <i data-lucide="route" class="w-4 h-4 text-primary"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono">{{ $totalTrips }}</div>
                            <span class="text-xs text-muted-foreground">Scheduled &amp; Active</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-blue-600">Upcoming</span>
                            <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono text-blue-600">{{ $scheduledTrips }}</div>
                            <span class="text-xs text-muted-foreground">Scheduled</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-emerald-600">In Progress</span>
                            <i data-lucide="play" class="w-4 h-4 text-emerald-600"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono text-emerald-600">{{ $inProgressTrips }}</div>
                            <span class="text-xs text-muted-foreground">On the road</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">Completed</span>
                            <i data-lucide="check-circle" class="w-4 h-4 text-muted-foreground"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono">{{ $completedTrips }}</div>
                            <span class="text-xs text-muted-foreground">Finished</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between col-span-2 md:col-span-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-amber-600">Unassigned</span>
                            <i data-lucide="alert-circle" class="w-4 h-4 text-amber-600"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono text-amber-600">{{ $unassignedTrips }}</div>
                            <span class="text-xs text-muted-foreground">Need vehicle</span>
                        </div>
                    </div>
                </div>

                <!-- DataTable Container -->
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm overflow-hidden">
                    <!-- Table Toolbar -->
                    <div class="p-4 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-muted/20">
                        <div class="flex items-center gap-2" data-shadcn-filter-group="tripsBoardTable">
                            <button type="button" data-filter="all" class="filter-pill px-3 py-1.5 rounded-md text-xs font-semibold bg-foreground text-background shadow-sm transition-colors">
                                All ({{ $totalTrips }})
                            </button>
                            <button type="button" data-filter="scheduled" class="filter-pill px-3 py-1.5 rounded-md text-xs font-medium text-muted-foreground hover:text-foreground transition-colors">
                                Scheduled ({{ $scheduledTrips }})
                            </button>
                            <button type="button" data-filter="in_progress" class="filter-pill px-3 py-1.5 rounded-md text-xs font-medium text-muted-foreground hover:text-foreground transition-colors">
                                In Progress ({{ $inProgressTrips }})
                            </button>
                            <button type="button" data-filter="completed" class="filter-pill px-3 py-1.5 rounded-md text-xs font-medium text-muted-foreground hover:text-foreground transition-colors">
                                Completed ({{ $completedTrips }})
                            </button>
                        </div>
                        <span class="text-xs text-muted-foreground font-mono">Showing {{ $trips->count() }} trips</span>
                    </div>

                    <!-- Table -->
                    <div class="w-full overflow-x-auto">
                        <table class="w-full caption-bottom text-sm" id="tripsBoardTable">
                            <thead>
                                <tr class="border-b bg-muted/50 font-mono text-[11px] uppercase tracking-wider text-muted-foreground">
                                    <th class="h-10 px-4 text-left align-middle font-medium">Trip #</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Route / Destination</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Departure Date &amp; Time</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Distance</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Assigned Vehicle</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Driver</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Status</th>
                                    <th class="h-10 px-4 text-right align-middle font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y text-foreground">
                                @forelse($trips as $trip)
                                    @php
                                        $driver = $trip->bus?->driver;
                                        $driverName = $driver?->user?->name;
                                        $statusBadgeClass = match($trip->status) {
                                            'in_progress' => 'border-transparent bg-emerald-500/15 text-emerald-600',
                                            'scheduled' => 'border-transparent bg-blue-500/15 text-blue-600',
                                            'completed' => 'border-transparent bg-secondary text-secondary-foreground',
                                            'cancelled' => 'border-transparent bg-destructive text-destructive-foreground',
                                            default => 'border-transparent bg-secondary text-secondary-foreground'
                                        };
                                        $statusLabel = match($trip->status) {
                                            'in_progress' => 'In Progress',
                                            'scheduled' => 'Scheduled',
                                            'completed' => 'Completed',
                                            'cancelled' => 'Cancelled',
                                            default => ucfirst($trip->status)
                                        };
                                    @endphp
                                    <tr class="border-b transition-colors hover:bg-muted/50" data-status="{{ $trip->status }}">
                                        <td class="p-4 align-middle font-mono font-bold">{{ $trip->display_id }}</td>
                                        <td class="p-4 align-middle">
                                            <div class="font-medium text-xs">{{ $trip->origin }} &rarr; {{ $trip->destination }}</div>
                                            @if($trip->route_label)
                                                <span class="text-[11px] text-muted-foreground font-mono">{{ $trip->route_label }}</span>
                                            @endif
                                        </td>
                                        <td class="p-4 align-middle font-mono text-xs">
                                            <div>{{ $trip->trip_date?->format('M j, Y') ?? '—' }}</div>
                                            <span class="text-muted-foreground text-[11px]">{{ $trip->departure_time ? substr($trip->departure_time, 0, 5) : '—' }}</span>
                                        </td>
                                        <td class="p-4 align-middle font-mono text-xs">{{ $trip->distance_km ? number_format($trip->distance_km, 1) . ' km' : '—' }}</td>
                                        <td class="p-4 align-middle">
                                            @if($trip->bus)
                                                <div class="font-mono font-bold text-xs">{{ $trip->bus->display_id }}</div>
                                                <span class="text-[11px] text-muted-foreground">{{ $trip->bus->plate }}</span>
                                            @else
                                                <span class="text-amber-600 text-xs font-medium">Unassigned</span>
                                            @endif
                                        </td>
                                        <td class="p-4 align-middle text-xs">
                                            {{ $driverName ?: '—' }}
                                        </td>
                                        <td class="p-4 align-middle">
                                            <span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold transition-colors {{ $statusBadgeClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>
                                        <td class="p-4 align-middle text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <button type="button"
                                                        onclick="openEditTripModal({{ $trip->id }}, '{{ addslashes($trip->trip_number) }}', '{{ addslashes($trip->origin) }}', '{{ addslashes($trip->destination) }}', '{{ addslashes($trip->route_label ?? '') }}', '{{ $trip->trip_date?->format('Y-m-d') }}', '{{ $trip->departure_time }}', '{{ $trip->distance_km }}', '{{ $trip->bus_id }}', '{{ $trip->status }}', '{{ addslashes($trip->notes ?? '') }}')"
                                                        title="Edit Trip"
                                                        class="inline-flex items-center justify-center rounded-md border border-input bg-background h-8 w-8 hover:bg-accent hover:text-accent-foreground text-muted-foreground shadow-sm transition-colors">
                                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                                </button>
                                                <button type="button"
                                                        onclick="openDeleteTripModal({{ $trip->id }}, '{{ addslashes($trip->display_id) }}', '{{ addslashes($trip->origin . ' → ' . $trip->destination) }}')"
                                                        title="Delete Trip"
                                                        class="inline-flex items-center justify-center rounded-md border border-input bg-background h-8 w-8 hover:bg-destructive/10 hover:text-destructive text-muted-foreground shadow-sm transition-colors">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="8" class="p-8 text-center text-muted-foreground">
                                            No trips found on the board.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- ================= shadcn Dialog: SCHEDULE TRIP ================= -->
    <div id="addTripModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-xs hidden">
        <div class="relative w-full max-w-lg rounded-xl border bg-background p-6 shadow-lg modal-content text-left">
            <div class="flex flex-col space-y-1.5 pb-3 border-b">
                <h3 class="text-lg font-semibold leading-none tracking-tight">Schedule New Transit Trip</h3>
                <p class="text-xs text-muted-foreground mt-1">Create a route timetable and assign a fleet vehicle.</p>
            </div>

            <form method="POST" action="{{ route('trips.store') }}" class="space-y-3 pt-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Trip Number</label>
                        <input name="trip_number" required placeholder="e.g. TR-204"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring font-mono"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Route Code</label>
                        <input name="route_label" placeholder="e.g. Line 14 Express"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Origin Terminal</label>
                        <input name="origin" required placeholder="e.g. Central Depot"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Destination</label>
                        <input name="destination" required placeholder="e.g. North Terminal"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Date</label>
                        <input name="trip_date" type="date" required value="{{ date('Y-m-d') }}"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Departure</label>
                        <input name="departure_time" type="time" required value="08:00"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Est. Distance (km)</label>
                        <input name="distance_km" type="number" step="0.1" placeholder="45.0"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring font-mono"/>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Assigned Bus</label>
                        <select name="bus_id" class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring">
                            <option value="">-- Unassigned (Standby) --</option>
                            @foreach($buses as $bus)
                                <option value="{{ $bus->id }}">{{ $bus->display_id }} ({{ $bus->plate }}) - {{ $bus->driver?->user?->name ?? 'No Driver' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Status</label>
                        <select name="status" class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring">
                            <option value="scheduled">Scheduled</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2 pt-4 border-t">
                    <button type="button" onclick="window.shadcnModal.close('addTripModal')"
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                        Cancel
                    </button>
                    <button type="submit"
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                        Schedule Trip
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= shadcn Dialog: EDIT TRIP ================= -->
    <div id="editTripModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-xs hidden">
        <div class="relative w-full max-w-lg rounded-xl border bg-background p-6 shadow-lg modal-content text-left">
            <div class="flex flex-col space-y-1.5 pb-3 border-b">
                <h3 class="text-lg font-semibold leading-none tracking-tight">Edit Transit Trip</h3>
                <p class="text-xs text-muted-foreground mt-1">Update route, departure time, or vehicle assignment.</p>
            </div>

            <form id="editTripForm" method="POST" action="" class="space-y-3 pt-3">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Trip Number</label>
                        <input id="editTripNumber" name="trip_number" required
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring font-mono"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Route Code</label>
                        <input id="editRouteLabel" name="route_label"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Origin Terminal</label>
                        <input id="editOrigin" name="origin" required
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Destination</label>
                        <input id="editDestination" name="destination" required
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Date</label>
                        <input id="editTripDate" name="trip_date" type="date" required
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Departure</label>
                        <input id="editDepartureTime" name="departure_time" type="time" required
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Est. Distance (km)</label>
                        <input id="editDistanceKm" name="distance_km" type="number" step="0.1"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring font-mono"/>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Assigned Bus</label>
                        <select id="editBusId" name="bus_id" class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring">
                            <option value="">-- Unassigned (Standby) --</option>
                            @foreach($buses as $bus)
                                <option value="{{ $bus->id }}">{{ $bus->display_id }} ({{ $bus->plate }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Status</label>
                        <select id="editStatus" name="status" class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring">
                            <option value="scheduled">Scheduled</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2 pt-4 border-t">
                    <button type="button" onclick="window.shadcnModal.close('editTripModal')"
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                        Cancel
                    </button>
                    <button type="submit"
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                        Update Trip
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= shadcn Alert Dialog: DELETE TRIP ================= -->
    <div id="deleteTripModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-xs hidden">
        <div class="relative w-full max-w-md rounded-xl border bg-background p-6 shadow-lg modal-content text-left">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-destructive/10 text-destructive flex items-center justify-center shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold leading-none tracking-tight">Cancel &amp; Delete Trip?</h3>
                    <p class="text-xs text-muted-foreground mt-2">
                        Are you sure you want to delete <span id="deleteTripNumber" class="font-bold text-foreground"></span> (<span id="deleteTripRoute" class="text-muted-foreground"></span>)?
                    </p>
                </div>
            </div>

            <form id="deleteTripForm" method="POST" action="" class="flex items-center justify-end gap-2 pt-5 mt-4 border-t">
                @csrf
                @method('DELETE')
                <button type="button" onclick="window.shadcnModal.close('deleteTripModal')"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                    Cancel
                </button>
                <button type="submit"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium bg-destructive text-destructive-foreground shadow-sm hover:bg-destructive/90 h-9 px-4 py-2">
                    Yes, Delete Trip
                </button>
            </form>
        </div>
    </div>

    <script>
        function openEditTripModal(id, tripNumber, origin, destination, routeLabel, tripDate, departureTime, distanceKm, busId, status, notes) {
            document.getElementById('editTripForm').action = '/trips/' + id;
            document.getElementById('editTripNumber').value = tripNumber;
            document.getElementById('editOrigin').value = origin;
            document.getElementById('editDestination').value = destination;
            document.getElementById('editRouteLabel').value = routeLabel || '';
            document.getElementById('editTripDate').value = tripDate || '';
            document.getElementById('editDepartureTime').value = departureTime ? departureTime.substring(0, 5) : '';
            document.getElementById('editDistanceKm').value = distanceKm || '';
            document.getElementById('editBusId').value = busId || '';
            document.getElementById('editStatus').value = status;
            window.shadcnModal.open('editTripModal');
        }

        function openDeleteTripModal(id, tripNumber, route) {
            document.getElementById('deleteTripForm').action = '/trips/' + id;
            document.getElementById('deleteTripNumber').textContent = tripNumber;
            document.getElementById('deleteTripRoute').textContent = route;
            window.shadcnModal.open('deleteTripModal');
        }

        document.getElementById('exportTripsCsvBtn')?.addEventListener('click', function () {
            window.toast?.success('Trip Board Exported', 'CSV report has been generated.');
        });
    </script>
</body>
</html>
