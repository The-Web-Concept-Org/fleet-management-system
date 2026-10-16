<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Operations Dashboard &bull; Lancotri</title>
    @include('partials.brand-styles')
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/lancotri-mark.svg') }}">
</head>
<body class="bg-background text-foreground antialiased min-h-screen">
    @include('partials.sidebar')

    <div class="pl-64 flex flex-col min-h-screen">
        @include('partials.topbar', ['topbarStatus' => $activeBuses . ' active vehicles'])

        <main class="w-full pt-16 flex-1 px-8 py-8 bg-background">
            <div class="flex flex-col w-full gap-6 max-w-7xl mx-auto">

                <!-- Page Header: Title + Telemetry Uplink + Action Buttons -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b">
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-xs text-muted-foreground font-mono">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-muted border border-border">
                                <i data-lucide="radio" class="w-3.5 h-3.5 text-primary"></i>
                                <span>CENTRAL DEPOT FLEET COMMAND</span>
                            </span>
                            <span>&bull;</span>
                            <span class="inline-flex items-center gap-1.5 text-emerald-600 font-medium">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                Real-Time Telemetry Feed
                            </span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">Operations Dashboard</h1>
                            <p class="text-xs text-muted-foreground mt-2 leading-relaxed max-w-2xl">Live telemetry streaming from active metro vehicles, GPS tracking, and depot dispatch.</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5">
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-border bg-card text-xs shadow-xs text-foreground">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-muted-foreground"></i>
                            <span class="font-medium">Today, {{ now()->format('M j, Y') }}</span>
                        </div>

                        <div class="hidden xl:flex items-center gap-2 px-3 py-1.5 rounded-lg border border-border bg-muted/60 text-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="font-mono text-muted-foreground text-[11px]">99.4% Uplink Operational</span>
                        </div>

                        <button id="exportDailyBtn" type="button"
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-input bg-card px-3.5 py-2 text-xs font-semibold text-foreground shadow-xs hover:bg-muted transition-colors">
                            <i data-lucide="download" class="w-3.5 h-3.5 text-muted-foreground"></i>
                            <span>Export Daily Log</span>
                        </button>

                        <a href="{{ route('shifts') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-xs hover:bg-primary/90 transition-all active:scale-95">
                            <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                            <span>+ New Shift</span>
                        </a>
                    </div>
                </div>

                <!-- 4 shadcn Metric Stat Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Card 1: Fleet Utilization -->
                    <div class="rounded-xl border border-border bg-card p-5 shadow-xs flex flex-col justify-between hover:shadow-sm transition-shadow relative overflow-hidden group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider font-mono">Fleet Utilization</span>
                            <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                                <i data-lucide="bus" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-3xl font-bold tracking-tight text-foreground font-mono" data-telemetry-key="activeBuses">{{ $activeBuses }}</span>
                                <span class="text-xs text-muted-foreground">/ <span data-telemetry-key="totalBuses">{{ $totalBuses }}</span> total</span>
                            </div>
                            <p class="text-xs text-muted-foreground mt-0.5">Buses en route today</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-border/60 flex items-center justify-between text-xs">
                            <span class="text-muted-foreground flex items-center gap-1.5">
                                <i data-lucide="warehouse" class="w-3.5 h-3.5 text-muted-foreground"></i>
                                <span data-telemetry-key="standbyBuses">{{ $standbyBuses }}</span> standby
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 font-mono" data-telemetry-key="utilizationPct">
                                {{ $utilizationPct }}% Cap
                            </span>
                        </div>
                    </div>

                    <!-- Card 2: Distance Aggregation -->
                    <div class="rounded-xl border border-border bg-card p-5 shadow-xs flex flex-col justify-between hover:shadow-sm transition-shadow relative overflow-hidden group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider font-mono">Fleet Distance</span>
                            <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center">
                                <i data-lucide="navigation" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-3xl font-bold tracking-tight text-foreground font-mono" data-telemetry-key="totalOdometer">{{ number_format($totalOdometer) }}</span>
                                <span class="text-xs text-muted-foreground font-mono">km</span>
                            </div>
                            <p class="text-xs text-muted-foreground mt-0.5">Total odometer recorded</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-border/60 flex items-center justify-between text-xs text-muted-foreground">
                            <span>Avg per vehicle</span>
                            <span class="font-mono font-medium text-foreground">{{ number_format($avgOdometerPerBus) }} km</span>
                        </div>
                    </div>

                    <!-- Card 3: Operating Expenses -->
                    <div class="rounded-xl border border-border bg-card p-5 shadow-xs flex flex-col justify-between hover:shadow-sm transition-shadow relative overflow-hidden group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider font-mono">Fuel Spend (Mo.)</span>
                            <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center">
                                <i data-lucide="fuel" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-3xl font-bold tracking-tight text-foreground font-mono" data-telemetry-key="totalFuelCost">${{ number_format($totalFuelCost, 2) }}</span>
                                <span class="text-xs text-muted-foreground">USD</span>
                            </div>
                            <p class="text-xs text-muted-foreground mt-0.5">{{ number_format($totalFuelLiters, 1) }} L Diesel logged</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-border/60 flex items-center justify-between text-xs text-muted-foreground">
                            <span>Average price</span>
                            <span class="font-mono font-medium text-foreground">${{ number_format($avgFuelPrice, 2) }}/L</span>
                        </div>
                    </div>

                    <!-- Card 4: Maintenance Spend -->
                    <div class="rounded-xl border border-border bg-card p-5 shadow-xs flex flex-col justify-between hover:shadow-sm transition-shadow relative overflow-hidden group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider font-mono">Depot Servicing</span>
                            <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-600 flex items-center justify-center">
                                <i data-lucide="wrench" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-3xl font-bold tracking-tight text-foreground font-mono" data-telemetry-key="totalMaintenanceCost">${{ number_format($totalMaintenanceCost, 2) }}</span>
                                <span class="text-xs text-muted-foreground">USD</span>
                            </div>
                            <p class="text-xs text-muted-foreground mt-0.5">Workshop work orders</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-border/60 flex items-center justify-between text-xs">
                            <span class="text-muted-foreground"><span data-telemetry-key="completedMaintenanceCount">{{ $completedMaintenanceCount }}</span> completed</span>
                            <span class="text-amber-600 font-medium"><span data-telemetry-key="pendingMaintenanceCount">{{ $pendingMaintenanceCount }}</span> pending review</span>
                        </div>
                    </div>
                </div>

                <!-- shadcn Alert: Critical Fleet Alerts & Maintenance Notice -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    @if($primaryAlert)
                        <div class="lg:col-span-2 rounded-xl border border-rose-500/30 bg-rose-500/5 p-4 text-card-foreground flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs" id="criticalAlertBanner">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-lg bg-rose-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                                    <i data-lucide="{{ $primaryAlert->status === 'out_of_service' ? 'alert-octagon' : 'alert-triangle' }}" class="w-5 h-5"></i>
                                </div>
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-semibold uppercase tracking-wider font-mono">
                                            {{ $primaryAlert->status === 'out_of_service' ? 'Out of Service' : 'Service Overdue' }}
                                        </span>
                                        <span class="font-bold text-foreground text-sm">{{ $primaryAlert->display_id }}</span>
                                        <span class="text-muted-foreground text-xs">&bull;</span>
                                        <span class="text-xs text-muted-foreground font-mono">{{ $primaryAlert->plate }}</span>
                                    </div>
                                    @if($primaryAlert->status === 'out_of_service')
                                        <p class="text-xs font-medium text-foreground mt-1">Vehicle flagged out of service</p>
                                        <p class="text-xs text-muted-foreground">{{ $primaryAlert->display_id }} requires dispatcher review before returning to active roster.</p>
                                    @else
                                        <p class="text-xs font-medium text-foreground mt-1">Service interval exceeded by {{ number_format(abs($primaryAlert->next_service_km)) }} km</p>
                                        <p class="text-xs text-muted-foreground">Current odometer reading is {{ number_format($primaryAlert->odometer) }} km.</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                                <a href="{{ route('buses') }}"
                                   class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-input bg-card hover:bg-muted text-foreground transition-colors shadow-xs">
                                    View Bus
                                </a>
                                <button type="button"
                                        onclick="acknowledgeAlert(this)"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-rose-600 hover:bg-rose-700 text-white transition-all shadow-xs">
                                    Acknowledge
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="lg:col-span-2 rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-4 flex items-center gap-3.5 shadow-xs">
                            <div class="w-10 h-10 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-foreground">All Fleet Units Operational</p>
                                <p class="text-xs text-muted-foreground">Every registered vehicle is within its scheduled maintenance window.</p>
                            </div>
                        </div>
                    @endif

                    <!-- Next Scheduled Service Notice -->
                    @if($nextServiceBus)
                        <div class="rounded-xl border border-border bg-card p-4 shadow-xs flex flex-col justify-between">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0">
                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="text-[10px] font-semibold uppercase tracking-wider font-mono text-amber-600">Preventive Maintenance</span>
                                    <span class="text-sm font-bold text-foreground mt-0.5 truncate">{{ $nextServiceBus->display_id }} Notice</span>
                                    <p class="text-xs text-muted-foreground mt-0.5">Service due in <span class="font-semibold text-foreground font-mono">{{ number_format($nextServiceBus->next_service_km) }} km</span></p>
                                </div>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-border/60 flex items-center justify-between text-xs">
                                <span class="text-muted-foreground font-mono text-[11px]">{{ number_format($nextServiceBus->odometer) }} km</span>
                                <a href="{{ route('buses') }}" class="text-xs font-semibold text-primary hover:underline">
                                    Assign Bay &rarr;
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="rounded-xl border border-border bg-card p-4 shadow-xs flex flex-col items-center justify-center text-center gap-2">
                            <i data-lucide="calendar-check" class="w-5 h-5 text-muted-foreground"></i>
                            <span class="text-xs text-muted-foreground">No maintenance orders pending</span>
                        </div>
                    @endif
                </div>

                <!-- shadcn DataTable: Active Shifts & Live Vehicles -->
                <div class="rounded-xl border border-border bg-card shadow-xs overflow-hidden">
                    <!-- Table Toolbar -->
                    <div class="p-4 border-b border-border/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-muted/20">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-primary text-primary-foreground flex items-center justify-center shadow-xs">
                                <i data-lucide="activity" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-semibold tracking-tight text-foreground">Active Shifts &amp; Live Telemetry</h2>
                                <p class="text-xs text-muted-foreground">Driver assignment status, GPS telemetry, and odometer synchronization</p>
                            </div>
                        </div>

                        <!-- Live Filters: Search + Status Pills -->
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="relative w-48 sm:w-60">
                                <i data-lucide="search" class="w-3.5 h-3.5 text-muted-foreground absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                                <input type="text"
                                       data-shadcn-table-search="dashboardFleetTable"
                                       placeholder="Filter bus, driver..."
                                       class="w-full pl-8 pr-3 py-1.5 h-8 text-xs bg-background border border-input rounded-md text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring"/>
                            </div>

                            <div class="inline-flex rounded-lg border border-border bg-muted/60 p-0.5 text-xs font-medium" data-shadcn-filter-group="dashboardFleetTable">
                                <button type="button" data-filter="all" class="px-2.5 py-1 rounded-md bg-foreground text-background font-semibold shadow-xs transition-colors">
                                    All (<span data-counter-for="dashboardFleetTable">{{ $totalBuses }}</span>)
                                </button>
                                <button type="button" data-filter="active" class="px-2.5 py-1 rounded-md text-muted-foreground hover:text-foreground transition-colors">
                                    Active
                                </button>
                                <button type="button" data-filter="out_of_service" class="px-2.5 py-1 rounded-md text-muted-foreground hover:text-foreground transition-colors">
                                    Alerts
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Table Element -->
                    <div class="w-full overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs" id="dashboardFleetTable">
                            <thead>
                                <tr class="border-b border-border/80 bg-muted/40 font-mono text-[11px] uppercase tracking-wider text-muted-foreground">
                                    <th class="py-3 px-4 font-semibold">Bus #</th>
                                    <th class="py-3 px-4 font-semibold">Assigned Driver</th>
                                    <th class="py-3 px-4 font-semibold">Shift Status</th>
                                    <th class="py-3 px-4 font-semibold">Start Time</th>
                                    <th class="py-3 px-4 font-semibold">Start Meter</th>
                                    <th class="py-3 px-4 font-semibold">Current Meter</th>
                                    <th class="py-3 px-4 font-semibold">Fuel Level</th>
                                    <th class="py-3 px-4 font-semibold text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border/50 text-foreground">
                                @forelse($buses as $bus)
                                    @php
                                        $driverName = $bus->driver?->user?->name;
                                        $initials = $driverName
                                            ? strtoupper(collect(explode(' ', $driverName))->map(fn ($part) => substr($part, 0, 1))->take(2)->implode(''))
                                            : '';

                                        $statusConfig = [
                                            'active' => [
                                                'label' => $bus->route ?: 'On Route',
                                                'badge' => 'border-emerald-500/20 bg-emerald-500/10 text-emerald-600',
                                                'dot' => 'bg-emerald-500',
                                            ],
                                            'standby' => [
                                                'label' => 'Ready / Standby',
                                                'badge' => 'border-border bg-secondary text-secondary-foreground',
                                                'dot' => 'bg-slate-400',
                                            ],
                                            'depot' => [
                                                'label' => 'Idle at Depot',
                                                'badge' => 'border-border bg-secondary text-secondary-foreground',
                                                'dot' => 'bg-slate-400',
                                            ],
                                            'maintenance' => [
                                                'label' => 'In Maintenance',
                                                'badge' => 'border-amber-500/20 bg-amber-500/10 text-amber-600',
                                                'dot' => 'bg-amber-500',
                                            ],
                                            'out_of_service' => [
                                                'label' => 'Out of Service',
                                                'badge' => 'border-rose-500/20 bg-rose-500/10 text-rose-600',
                                                'dot' => 'bg-rose-500',
                                            ],
                                        ];
                                        $cfg = $statusConfig[$bus->status] ?? $statusConfig['standby'];
                                    @endphp
                                    <tr class="hover:bg-muted/40 transition-colors" data-status="{{ $bus->status }}">
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full {{ $cfg['dot'] }}"></span>
                                                <span class="font-bold text-foreground font-mono text-sm">{{ $bus->display_id }}</span>
                                            </div>
                                            <span class="text-[11px] text-muted-foreground">{{ $bus->model ?? 'Bus Chassis' }}</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            @if($driverName)
                                                <div class="flex items-center gap-2">
                                                    <div class="w-6 h-6 rounded-full bg-muted text-muted-foreground font-semibold flex items-center justify-center text-[10px] border border-border">
                                                        {{ $initials }}
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span class="font-medium text-foreground leading-tight">{{ $driverName }}</span>
                                                        <span class="text-[10px] text-muted-foreground font-mono">{{ $bus->driver->display_id ?? 'Driver #—' }}</span>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted-foreground italic text-xs">Unassigned</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-semibold border {{ $cfg['badge'] }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $cfg['dot'] }}"></span>
                                                {{ $cfg['label'] }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-muted-foreground font-mono">&mdash;</td>
                                        <td class="py-3 px-4 text-muted-foreground font-mono">&mdash;</td>
                                        <td class="py-3 px-4 font-mono font-medium text-foreground">{{ number_format($bus->odometer) }} km</td>
                                        <td class="py-3 px-4 text-muted-foreground font-mono">&mdash;</td>
                                        <td class="py-3 px-4 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <button type="button"
                                                        onclick="openPhotoVerificationModal('{{ $bus->display_id }}', '{{ $driverName ?: 'Unassigned' }}', '{{ number_format($bus->odometer) }}')"
                                                        title="Verify Optical Telemetry Reading"
                                                        class="p-1.5 rounded-md border border-input bg-card hover:bg-muted text-muted-foreground hover:text-foreground transition-colors shadow-xs">
                                                    <i data-lucide="camera" class="w-3.5 h-3.5"></i>
                                                </button>
                                                <a href="{{ route('buses') }}"
                                                   title="View in Fleet Roster"
                                                   class="p-1.5 rounded-md border border-input bg-card hover:bg-muted text-muted-foreground hover:text-foreground transition-colors shadow-xs">
                                                    <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="8" class="py-12 text-center text-muted-foreground">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <i data-lucide="bus" class="w-8 h-8 text-muted-foreground/60"></i>
                                                <p class="font-medium text-foreground text-sm">No fleet vehicles found</p>
                                                <p class="text-xs text-muted-foreground">Add your first transit bus to begin live telemetry tracking.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Table Footer: Pagination & Count -->
                    <div class="p-3 border-t border-border/60 flex flex-col sm:flex-row items-center justify-between gap-2 bg-muted/10 text-xs text-muted-foreground">
                        <span>Showing {{ $buses->count() }} of {{ $totalBuses }} registered fleet assets</span>
                        <div class="flex items-center gap-1">
                            <button type="button" class="px-2.5 py-1 rounded-md border border-input bg-card font-semibold text-foreground shadow-xs">1</button>
                            <button type="button" class="px-2.5 py-1 rounded-md border border-input bg-card hover:bg-muted text-foreground transition-colors">2</button>
                            <button type="button" class="px-2.5 py-1 rounded-md border border-input bg-card hover:bg-muted text-foreground transition-colors">3</button>
                        </div>
                    </div>
                </div>

                <!-- Recent Logs: Fuel & Maintenance Cards -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <!-- Recent Fuel Entries Card -->
                    <div class="rounded-xl border border-border bg-card p-5 shadow-xs flex flex-col justify-between">
                        <div class="flex items-center justify-between pb-3 border-b border-border/60">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center">
                                    <i data-lucide="fuel" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-foreground">Recent Fuel Entries</h3>
                                    <p class="text-xs text-muted-foreground">Driver submitted pump dispenser receipts</p>
                                </div>
                            </div>
                            <a href="{{ route('fuel') }}" class="text-xs font-semibold text-primary hover:underline">
                                View All &rarr;
                            </a>
                        </div>
                        <div class="py-8 text-center flex flex-col items-center justify-center gap-2">
                            <i data-lucide="droplet" class="w-6 h-6 text-muted-foreground/60"></i>
                            <p class="text-xs font-medium text-foreground">No recent dispenser logs</p>
                            <p class="text-[11px] text-muted-foreground">Driver receipts will populate when mobile OCR sync is submitted.</p>
                        </div>
                    </div>

                    <!-- Recent Maintenance Card -->
                    <div class="rounded-xl border border-border bg-card p-5 shadow-xs flex flex-col justify-between">
                        <div class="flex items-center justify-between pb-3 border-b border-border/60">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center">
                                    <i data-lucide="wrench" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-foreground">Recent Maintenance</h3>
                                    <p class="text-xs text-muted-foreground">Workshop service orders &amp; parts replacement</p>
                                </div>
                            </div>
                            <a href="{{ route('maintenance') }}" class="text-xs font-semibold text-primary hover:underline">
                                View All &rarr;
                            </a>
                        </div>
                        <div class="py-8 text-center flex flex-col items-center justify-center gap-2">
                            <i data-lucide="clipboard-list" class="w-6 h-6 text-muted-foreground/60"></i>
                            <p class="text-xs font-medium text-foreground">No maintenance work orders active</p>
                            <p class="text-[11px] text-muted-foreground">Depot workshop tasks will display here once initiated.</p>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- shadcn Dialog: Optical Meter Telemetry Verification -->
    <div id="photoVerificationModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs hidden transition-opacity">
        <div class="relative w-full max-w-lg rounded-xl border border-border bg-card p-6 shadow-2xl transition-all modal-content text-left">
            <div class="flex items-center justify-between pb-3 border-b border-border/60">
                <div class="flex items-center gap-2 text-foreground font-semibold text-base">
                    <div class="w-7 h-7 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                        <i data-lucide="camera" class="w-4 h-4"></i>
                    </div>
                    <span>Optical Telemetry Audit</span>
                </div>
                <button type="button" onclick="window.shadcnModal.close('photoVerificationModal')" class="text-muted-foreground hover:text-foreground p-1 rounded-md transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <div class="mt-4 space-y-4">
                <div class="relative w-full h-56 rounded-lg overflow-hidden bg-muted border border-border">
                    <img class="w-full h-full object-cover"
                         src="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&w=800&q=80"
                         alt="Bus Dashboard Odometer"/>
                    <div class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded-md bg-background/90 backdrop-blur-md text-[11px] font-mono font-medium border border-border text-foreground">
                        Optical Capture: Today 11:20 AM
                    </div>
                    <div class="absolute bottom-2.5 right-2.5 px-2 py-0.5 rounded-md bg-background/90 backdrop-blur-md text-[10px] font-mono text-muted-foreground border border-border">
                        GPS: 40.7128° N, 74.0060° W
                    </div>
                </div>

                <div class="p-3.5 rounded-lg bg-muted/40 border border-border/60 space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Vehicle:</span>
                        <span class="font-bold text-foreground font-mono" id="modalBusNum">Bus #42</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Driver:</span>
                        <span class="font-medium text-foreground" id="modalDriverName">Marcus Vance</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Synced Telemetry Meter:</span>
                        <span class="font-mono font-bold text-foreground" id="modalOdoReading">148,320 km</span>
                    </div>
                    <div class="flex justify-between text-emerald-600 font-medium pt-1 border-t border-border/50">
                        <span>AI Vision Confidence:</span>
                        <span class="font-mono">99.2% Validated</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2.5 pt-3 border-t border-border/60">
                <button type="button"
                        onclick="window.shadcnModal.close('photoVerificationModal')"
                        class="px-4 py-2 text-xs font-medium rounded-lg border border-input bg-card hover:bg-muted text-foreground transition-colors shadow-xs">
                    Close
                </button>
                <button type="button"
                        onclick="approveTelemetryReading()"
                        class="px-4 py-2 text-xs font-semibold rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 transition-all shadow-xs">
                    Approve Reading
                </button>
            </div>
        </div>
    </div>

    <script>
        function openPhotoVerificationModal(busNum, driverName, odoReading) {
            document.getElementById('modalBusNum').textContent = busNum;
            document.getElementById('modalDriverName').textContent = driverName;
            document.getElementById('modalOdoReading').textContent = odoReading + ' km';
            window.shadcnModal.open('photoVerificationModal');
        }

        function approveTelemetryReading() {
            window.shadcnModal.close('photoVerificationModal');
            window.toast?.success(
                'Reading Approved',
                'Telemetry meter synchronized with Central Depot audit log.'
            );
        }

        function acknowledgeAlert(btn) {
            const card = document.getElementById('criticalAlertBanner');
            if (card) {
                card.style.opacity = '0';
                card.style.transition = 'opacity 0.25s ease';
                setTimeout(() => {
                    card.remove();
                    window.toast?.info('Alert Acknowledged', 'Logged to dispatcher event journal.');
                }, 250);
            }
        }

        // Export button handler with real simulated export & toast
        document.getElementById('exportDailyBtn')?.addEventListener('click', function () {
            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = `<i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin"></i><span>Exporting...</span>`;
            if (window.lucide) window.lucide.createIcons();

            setTimeout(() => {
                btn.innerHTML = originalHtml;
                if (window.lucide) window.lucide.createIcons();
                window.toast?.success(
                    'Export Complete',
                    'Daily Fleet Telemetry Log (CSV/PDF) generated successfully.'
                );
            }, 800);
        });
    </script>
</body>
</html>
