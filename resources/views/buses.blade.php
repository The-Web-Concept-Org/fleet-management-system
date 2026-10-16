<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Fleet Buses Roster &bull; Lancotri</title>
    @include('partials.brand-styles')
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/lancotri-mark.svg') }}">
</head>
<body class="bg-background text-foreground antialiased min-h-screen">
    @include('partials.sidebar')

    <div class="pl-64 flex flex-col min-h-screen">
        @include('partials.topbar', ['topbarStatus' => $activeBuses . ' active vehicles'])

        <main class="w-full pt-16 flex-1 px-8 py-8 bg-background">
            <div class="flex flex-col w-full gap-6 max-w-7xl mx-auto">

                <!-- Page Header: Title + Controls -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b">
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-xs text-muted-foreground font-mono">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-muted border">
                                <i data-lucide="radio" class="w-3.5 h-3.5 text-primary"></i>
                                <span>TERMINAL 4 TELEMETRY</span>
                            </span>
                            <span>&bull;</span>
                            <span class="inline-flex items-center gap-1.5 text-emerald-600 font-medium">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                Real-Time Tracking
                            </span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">Fleet Buses Roster</h1>
                            <p class="text-xs text-muted-foreground mt-2 leading-relaxed max-w-2xl">Manage and track company buses, assign drivers, and monitor service telemetry.</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5">
                        <div class="relative w-64">
                            <i data-lucide="search" class="w-3.5 h-3.5 text-muted-foreground absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <input id="rosterSearch"
                                   data-shadcn-table-search="busesRosterTable"
                                   placeholder="Search bus #, plate, model..."
                                   class="flex h-9 w-full rounded-md border border-input bg-transparent pl-9 pr-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                        </div>

                        <button id="exportCsvBtn" type="button"
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 gap-2">
                            <i data-lucide="download" class="w-3.5 h-3.5 text-muted-foreground"></i>
                            <span>Export</span>
                        </button>

                        <button id="addBusModalTrigger" type="button" onclick="window.shadcnModal.open('addBusModal')"
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2 gap-2 transition-all">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>Add New Bus</span>
                        </button>
                    </div>
                </div>

                <!-- 5 Stat Cards (shadcn Card default) -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">Total Fleet</span>
                            <i data-lucide="bus" class="w-4 h-4 text-primary"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono">{{ $totalBuses }}</div>
                            <span class="text-xs text-muted-foreground">Registered units</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-emerald-600">Active on Shift</span>
                            <i data-lucide="navigation" class="w-4 h-4 text-emerald-600"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono text-emerald-600">{{ $activeBuses }}</div>
                            <span class="text-xs text-muted-foreground">En route</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">Depot &amp; Garage</span>
                            <i data-lucide="warehouse" class="w-4 h-4 text-muted-foreground"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono">{{ $depotBuses }}</div>
                            <span class="text-xs text-muted-foreground">Standby / Bays</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-destructive">Out of Service</span>
                            <i data-lucide="alert-octagon" class="w-4 h-4 text-destructive"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono text-destructive">{{ $outOfServiceBuses }}</div>
                            <span class="text-xs text-muted-foreground">Flagged</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between col-span-2 md:col-span-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">Avg Fuel Econ</span>
                            <i data-lucide="gauge" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono">{{ $avgEfficiency ?: '0.0' }} <span class="text-xs font-normal text-muted-foreground">KM/L</span></div>
                            <span class="text-xs text-muted-foreground">Active fleet metric</span>
                        </div>
                    </div>
                </div>

                <!-- DataTable Container -->
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm overflow-hidden">
                    <!-- Table Toolbar -->
                    <div class="p-4 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-muted/20">
                        <div class="flex items-center gap-2" data-shadcn-filter-group="busesRosterTable">
                            <button type="button" data-filter="all" class="filter-pill px-3 py-1.5 rounded-md text-xs font-semibold bg-foreground text-background shadow-sm transition-colors">
                                All ({{ $totalBuses }})
                            </button>
                            <button type="button" data-filter="active" class="filter-pill px-3 py-1.5 rounded-md text-xs font-medium text-muted-foreground hover:text-foreground transition-colors">
                                Active ({{ $activeBuses }})
                            </button>
                            <button type="button" data-filter="standby" class="filter-pill px-3 py-1.5 rounded-md text-xs font-medium text-muted-foreground hover:text-foreground transition-colors">
                                Standby
                            </button>
                            <button type="button" data-filter="depot" class="filter-pill px-3 py-1.5 rounded-md text-xs font-medium text-muted-foreground hover:text-foreground transition-colors">
                                Depot
                            </button>
                            <button type="button" data-filter="out_of_service" class="filter-pill px-3 py-1.5 rounded-md text-xs font-medium text-muted-foreground hover:text-foreground transition-colors">
                                Out of Service
                            </button>
                        </div>
                        <div class="flex items-center gap-2">
                            <select onchange="window.location.href = this.value" class="h-8 rounded-md border border-input bg-background px-2 text-xs focus:ring-1 focus:ring-ring text-muted-foreground">
                                <option value="{{ route('buses') }}">All Service Routes</option>
                                <option value="{{ route('buses', ['route' => 'unassigned']) }}" @selected(request('route') === 'unassigned')>Unassigned Route</option>
                                @foreach($routes as $r)
                                    <option value="{{ route('buses', ['route' => $r]) }}" @selected(request('route') === $r)>{{ $r }}</option>
                                @endforeach
                            </select>
                            <span class="text-xs text-muted-foreground font-mono">Showing {{ $buses->count() }} vehicles</span>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="w-full overflow-x-auto">
                        <table class="w-full caption-bottom text-sm" id="busesRosterTable">
                            <thead>
                                <tr class="border-b bg-muted/50 font-mono text-[11px] uppercase tracking-wider text-muted-foreground">
                                    <th class="h-10 px-4 text-left align-middle font-medium">Bus #</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Chassis / Model</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Plate</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Service Route</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Assigned Driver</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Status</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Odometer</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Next Service</th>
                                    <th class="h-10 px-4 text-right align-middle font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y text-foreground">
                                @forelse($buses as $bus)
                                    @php
                                        $driverName = $bus->driver?->user?->name;
                                        $initials = $driverName ? strtoupper(substr($driverName, 0, 2)) : '';
                                        $statusBadgeClass = match($bus->status) {
                                            'active' => 'border-transparent bg-emerald-500/15 text-emerald-600',
                                            'standby', 'depot' => 'border-transparent bg-secondary text-secondary-foreground',
                                            'maintenance' => 'border-transparent bg-amber-500/15 text-amber-600',
                                            'out_of_service' => 'border-transparent bg-destructive text-destructive-foreground',
                                            default => 'border-transparent bg-secondary text-secondary-foreground'
                                        };
                                        $statusLabel = match($bus->status) {
                                            'active' => 'Active Shift',
                                            'standby' => 'Standby',
                                            'depot' => 'In Depot',
                                            'maintenance' => 'In Maintenance',
                                            'out_of_service' => 'Out of Service',
                                            default => ucfirst($bus->status)
                                        };
                                    @endphp
                                    <tr class="border-b transition-colors hover:bg-muted/50" data-status="{{ $bus->status }}">
                                        <td class="p-4 align-middle font-mono font-bold">{{ $bus->display_id }}</td>
                                        <td class="p-4 align-middle">
                                            <div class="font-medium text-foreground">{{ $bus->model ?: 'Transit Bus' }}</div>
                                            @if($bus->operator)
                                                <div class="text-[11px] text-muted-foreground font-mono">{{ $bus->operator }}</div>
                                            @endif
                                        </td>
                                        <td class="p-4 align-middle font-mono text-xs">{{ $bus->plate }}</td>
                                        <td class="p-4 align-middle text-xs font-medium">
                                            @if($bus->route)
                                                <button type="button"
                                                        onclick="openAssignRouteModal({{ $bus->id }}, '{{ addslashes($bus->bus_number) }}', '{{ addslashes($bus->route) }}')"
                                                        class="inline-flex items-center gap-1.5 text-foreground hover:text-primary transition-colors cursor-pointer group text-left"
                                                        title="Click to change service route">
                                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-primary shrink-0"></i>
                                                    <span class="font-medium group-hover:underline">{{ $bus->route }}</span>
                                                    <i data-lucide="edit-2" class="w-2.5 h-2.5 opacity-0 group-hover:opacity-100 text-muted-foreground transition-opacity"></i>
                                                </button>
                                            @else
                                                <button type="button"
                                                        onclick="openAssignRouteModal({{ $bus->id }}, '{{ addslashes($bus->bus_number) }}', '')"
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded border border-dashed border-border hover:border-primary/60 text-muted-foreground hover:text-foreground text-[11px] transition-colors cursor-pointer"
                                                        title="Assign Service Route">
                                                    <i data-lucide="plus" class="w-3 h-3 text-primary"></i>
                                                    <span>Assign Route</span>
                                                </button>
                                            @endif
                                        </td>
                                        <td class="p-4 align-middle">
                                            @if($driverName)
                                                <div class="flex items-center gap-2">
                                                    <div class="w-6 h-6 rounded-full bg-muted border flex items-center justify-center text-[10px] font-semibold">
                                                        {{ $initials }}
                                                    </div>
                                                    <span class="font-medium text-xs">{{ $driverName }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted-foreground italic text-xs">Unassigned</span>
                                            @endif
                                        </td>
                                        <td class="p-4 align-middle">
                                            <span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold transition-colors {{ $statusBadgeClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>
                                        <td class="p-4 align-middle font-mono text-xs">{{ number_format($bus->odometer) }} km</td>
                                        <td class="p-4 align-middle font-mono text-xs">
                                            @if($bus->next_service_km < 0)
                                                <span class="text-destructive font-semibold">{{ number_format(abs($bus->next_service_km)) }} km overdue</span>
                                            @else
                                                <span>{{ number_format($bus->next_service_km) }} km</span>
                                            @endif
                                        </td>
                                        <td class="p-4 align-middle text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <button type="button"
                                                        onclick="openEditBusModal({{ $bus->id }}, '{{ addslashes($bus->bus_number) }}', '{{ addslashes($bus->plate) }}', '{{ addslashes($bus->model ?? '') }}', '{{ $bus->status }}', '{{ $bus->driver_id }}', '{{ addslashes($bus->route ?? '') }}', {{ (int) $bus->odometer }}, {{ (int) $bus->next_service_km }}, '{{ addslashes($bus->operator ?? '') }}')"
                                                        title="Edit Bus"
                                                        class="inline-flex items-center justify-center rounded-md border border-input bg-background h-8 w-8 hover:bg-accent hover:text-accent-foreground text-muted-foreground shadow-sm transition-colors">
                                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                                </button>
                                                <button type="button"
                                                        onclick="openDeleteBusModal({{ $bus->id }}, '{{ addslashes($bus->display_id) }}', '{{ addslashes($bus->plate) }}')"
                                                        title="Delete Bus"
                                                        class="inline-flex items-center justify-center rounded-md border border-input bg-background h-8 w-8 hover:bg-destructive/10 hover:text-destructive text-muted-foreground shadow-sm transition-colors">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="9" class="p-8 text-center text-muted-foreground">
                                            No buses found matching your criteria.
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

    <!-- ================= shadcn Dialog: ADD NEW BUS ================= -->
    <div id="addBusModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-xs hidden">
        <div class="relative w-full max-w-lg rounded-xl border bg-background p-6 shadow-lg modal-content text-left">
            <div class="flex flex-col space-y-1.5 pb-3 border-b">
                <h3 class="text-lg font-semibold leading-none tracking-tight">Provision New Vehicle</h3>
                <p class="text-xs text-muted-foreground mt-1">Register a new transit bus into the active fleet roster.</p>
            </div>

            <form method="POST" action="{{ route('buses.store') }}" class="space-y-3 pt-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Bus Number</label>
                        <input name="bus_number" required placeholder="e.g. 45"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">License Plate</label>
                        <input name="plate" required placeholder="e.g. YT-901-XZ"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring font-mono"/>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Chassis Model / Type *</label>
                        <input list="chassisModelPresets" name="model" required placeholder="Type custom or select (e.g. Scania, Yutong, Volvo...)"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Operational Status *</label>
                        <select name="status" class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring">
                            <option value="standby">Ready / Standby</option>
                            <option value="active">Active Shift</option>
                            <option value="depot">In Depot</option>
                            <option value="maintenance">In Maintenance</option>
                            <option value="out_of_service">Out of Service</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Service Route / Line</label>
                        <input list="standardRouteOptions" name="route" placeholder="e.g. Route 42 - Downtown Loop"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Depot / Fleet Division</label>
                        <input name="operator" placeholder="e.g. Central Depot, Bay 4"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Current Odometer (km)</label>
                        <input type="number" name="odometer" min="0" value="0" placeholder="0"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring font-mono"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Next Service Interval (km)</label>
                        <input type="number" name="next_service_km" value="10000" placeholder="10000"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring font-mono"/>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium leading-none">Assigned Driver</label>
                    <select name="driver_id" class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring">
                        <option value="">-- Assign Driver Later (Standby) --</option>
                        @foreach($drivers as $driver)
                            @continue($driver->bus)
                            <option value="{{ $driver->id }}">{{ $driver->user->name ?? 'Driver' }} ({{ $driver->display_id }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2 pt-4 border-t">
                    <button type="button" onclick="window.shadcnModal.close('addBusModal')"
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                        Cancel
                    </button>
                    <button type="submit"
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                        Save Bus
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= shadcn Dialog: EDIT BUS ================= -->
    <div id="editBusModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-xs hidden">
        <div class="relative w-full max-w-lg rounded-xl border bg-background p-6 shadow-lg modal-content text-left">
            <div class="flex flex-col space-y-1.5 pb-3 border-b">
                <h3 class="text-lg font-semibold leading-none tracking-tight">Edit Vehicle Details</h3>
                <p class="text-xs text-muted-foreground mt-1">Update registration, status, custom chassis model, route, or driver assignment.</p>
            </div>

            <form id="editBusForm" method="POST" action="" class="space-y-3 pt-3">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Bus Number</label>
                        <input id="editBusNumber" name="bus_number" required
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">License Plate</label>
                        <input id="editBusPlate" name="plate" required
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring font-mono"/>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Chassis Model / Type *</label>
                        <input list="chassisModelPresets" id="editBusModel" name="model" required placeholder="Type custom or select model"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Operational Status *</label>
                        <select id="editBusStatus" name="status" class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring">
                            <option value="standby">Ready / Standby</option>
                            <option value="active">Active Shift</option>
                            <option value="depot">In Depot</option>
                            <option value="maintenance">In Maintenance</option>
                            <option value="out_of_service">Out of Service</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Service Route / Line</label>
                        <input list="standardRouteOptions" id="editBusRoute" name="route" placeholder="e.g. Route 42 - Downtown Loop"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Depot / Fleet Division</label>
                        <input id="editBusOperator" name="operator" placeholder="e.g. Central Depot, Bay 4"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Current Odometer (km)</label>
                        <input type="number" id="editBusOdometer" name="odometer" min="0" placeholder="0"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring font-mono"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Next Service Interval (km)</label>
                        <input type="number" id="editBusNextService" name="next_service_km" placeholder="10000"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring font-mono"/>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium leading-none">Assigned Driver</label>
                    <select id="editBusDriver" name="driver_id" class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring">
                        <option value="">-- No Driver Assigned (Standby) --</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->user->name ?? 'Driver' }} ({{ $driver->display_id }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2 pt-4 border-t">
                    <button type="button" onclick="window.shadcnModal.close('editBusModal')"
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                        Cancel
                    </button>
                    <button type="submit"
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                        Update Bus
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Universal Datalist for Chassis Models: Enables selecting presets or typing any custom model -->
    <datalist id="chassisModelPresets">
        <option value="Scania K310 City"></option>
        <option value="Volvo B8RLE Low-Floor"></option>
        <option value="Mercedes Citaro C2"></option>
        <option value="MAN Lion's City 12G"></option>
        <option value="Yutong ZK6128HG"></option>
        <option value="Higer KLQ6119GS"></option>
        <option value="BYD K9 Electric"></option>
        <option value="Isuzu Cityport"></option>
        <option value="Daewoo BS106"></option>
        <option value="Ashok Leyland Viking"></option>
        <option value="Tata Starbus Ultra"></option>
        <option value="Setra TopClass S 531"></option>
        <option value="Van Hool A330"></option>
        <option value="Solaris Urbino 12"></option>
    </datalist>

    <!-- Universal Datalist for Service Routes -->
    <datalist id="standardRouteOptions">
        @foreach($routes as $r)
            <option value="{{ $r }}"></option>
        @endforeach
        <option value="Route 101 - Metro Express"></option>
        <option value="Route 204 - Downtown Loop"></option>
        <option value="Route 305 - Airport Direct"></option>
        <option value="Route 410 - Suburban Corridor"></option>
        <option value="Route 515 - Industrial Parkway"></option>
        <option value="Route 620 - University Line"></option>
    </datalist>

    <!-- ================= shadcn Dialog: QUICK ASSIGN SERVICE ROUTE ================= -->
    <div id="assignRouteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-xs hidden">
        <div class="relative w-full max-w-md rounded-xl border bg-background p-6 shadow-lg modal-content text-left">
            <div class="flex items-center justify-between pb-4 border-b">
                <div>
                    <h3 class="text-base font-semibold leading-none tracking-tight">Assign Service Route</h3>
                    <p class="text-xs text-muted-foreground mt-1">Assign or change the daily operating line for <span id="assignRouteBusNumber" class="font-bold text-foreground"></span>.</p>
                </div>
                <button type="button" onclick="window.shadcnModal.close('assignRouteModal')" class="rounded-sm opacity-70 hover:opacity-100 transition-opacity">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form id="assignRouteForm" method="POST" action="" class="space-y-4 pt-4">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-xs font-medium leading-none">Service Route / Daily Line *</label>
                    <input list="standardRouteOptions" id="assignRouteInput" name="route" required placeholder="e.g. Route 42 - Downtown Loop"
                           class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring font-medium"/>
                    <p class="text-[11px] text-muted-foreground">Select an existing route, pick a quick preset below, or type a new custom route.</p>
                </div>

                <!-- Quick Route Presets -->
                <div class="space-y-1.5">
                    <span class="text-[11px] font-semibold text-muted-foreground uppercase tracking-wider">Quick Presets</span>
                    <div class="flex flex-wrap gap-1.5 pt-1">
                        @forelse($routes as $r)
                            <button type="button" onclick="setQuickRoute('{{ addslashes($r) }}')" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs rounded-md bg-secondary/80 hover:bg-secondary text-secondary-foreground border border-border/50 transition-colors">
                                <i data-lucide="map-pin" class="w-3 h-3 text-primary"></i>
                                <span>{{ $r }}</span>
                            </button>
                        @empty
                            <button type="button" onclick="setQuickRoute('Route 101 - Metro Express')" class="px-2.5 py-1 text-xs rounded-md bg-secondary/80 hover:bg-secondary text-secondary-foreground border">Route 101 - Metro Express</button>
                            <button type="button" onclick="setQuickRoute('Route 204 - Downtown Loop')" class="px-2.5 py-1 text-xs rounded-md bg-secondary/80 hover:bg-secondary text-secondary-foreground border">Route 204 - Downtown Loop</button>
                            <button type="button" onclick="setQuickRoute('Route 305 - Airport Direct')" class="px-2.5 py-1 text-xs rounded-md bg-secondary/80 hover:bg-secondary text-secondary-foreground border">Route 305 - Airport Direct</button>
                        @endforelse
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2 pt-4 border-t">
                    <button type="button" onclick="window.shadcnModal.close('assignRouteModal')"
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                        Cancel
                    </button>
                    <button type="submit"
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                        Save Service Route
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= shadcn Alert Dialog: DELETE BUS ================= -->
    <div id="deleteBusModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-xs hidden">
        <div class="relative w-full max-w-md rounded-xl border bg-background p-6 shadow-lg modal-content text-left">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-destructive/10 text-destructive flex items-center justify-center shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold leading-none tracking-tight">Remove Bus from Fleet?</h3>
                    <p class="text-xs text-muted-foreground mt-2">
                        Are you sure you want to delete <span id="deleteBusName" class="font-bold text-foreground"></span> (<span id="deleteBusPlateLabel" class="font-mono"></span>)? This action cannot be undone.
                    </p>
                </div>
            </div>

            <form id="deleteBusForm" method="POST" action="" class="flex items-center justify-end gap-2 pt-5 mt-4 border-t">
                @csrf
                @method('DELETE')
                <button type="button" onclick="window.shadcnModal.close('deleteBusModal')"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                    Cancel
                </button>
                <button type="submit"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium bg-destructive text-destructive-foreground shadow-sm hover:bg-destructive/90 h-9 px-4 py-2">
                    Yes, Delete Bus
                </button>
            </form>
        </div>
    </div>

    <script>
        function openEditBusModal(id, busNumber, plate, model, status, driverId, route, odometer, nextService, operator) {
            document.getElementById('editBusForm').action = '/buses/' + id;
            document.getElementById('editBusNumber').value = busNumber;
            document.getElementById('editBusPlate').value = plate;
            document.getElementById('editBusModel').value = model || '';
            document.getElementById('editBusStatus').value = status;
            document.getElementById('editBusDriver').value = driverId || '';
            document.getElementById('editBusRoute').value = route || '';
            document.getElementById('editBusOdometer').value = odometer ?? 0;
            document.getElementById('editBusNextService').value = nextService ?? 10000;
            document.getElementById('editBusOperator').value = operator || '';
            window.shadcnModal.open('editBusModal');
        }

        function openAssignRouteModal(id, busNumber, currentRoute) {
            document.getElementById('assignRouteForm').action = '/buses/' + id + '/route';
            document.getElementById('assignRouteBusNumber').textContent = busNumber;
            document.getElementById('assignRouteInput').value = currentRoute || '';
            window.shadcnModal.open('assignRouteModal');
        }

        function setQuickRoute(routeName) {
            const input = document.getElementById('assignRouteInput');
            if (input) {
                input.value = routeName;
                input.focus();
            }
        }

        function openDeleteBusModal(id, displayName, plate) {
            document.getElementById('deleteBusForm').action = '/buses/' + id;
            document.getElementById('deleteBusName').textContent = displayName;
            document.getElementById('deleteBusPlateLabel').textContent = plate;
            window.shadcnModal.open('deleteBusModal');
        }

        document.getElementById('exportCsvBtn')?.addEventListener('click', function () {
            window.toast?.success('Export Initiated', 'Fleet Buses Roster CSV has been downloaded.');
        });
    </script>
</body>
</html>
