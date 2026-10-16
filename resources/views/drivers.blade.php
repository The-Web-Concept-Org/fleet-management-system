<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Drivers Roster &bull; Lancotri</title>
    @include('partials.brand-styles')
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/lancotri-mark.svg') }}">
</head>
<body class="bg-background text-foreground antialiased min-h-screen">
    @include('partials.sidebar')

    <div class="pl-64 flex flex-col min-h-screen">
        @include('partials.topbar', [
            'topbarSearchPlaceholder' => 'Search driver name, badge, email, or bus...',
            'topbarSearchId' => 'driverNavSearch',
            'topbarStatus' => $totalDrivers . ' registered drivers',
        ])

        <main class="w-full pt-16 flex-1 px-8 py-8 bg-background">
            <div class="flex flex-col w-full gap-6 max-w-7xl mx-auto">

                <!-- Page Header: Title + Toolbar -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b">
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-xs text-muted-foreground font-mono">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-muted border">
                                <i data-lucide="users" class="w-3.5 h-3.5 text-primary"></i>
                                <span>PERSONNEL MANAGEMENT</span>
                            </span>
                            <span>&bull;</span>
                            <span class="inline-flex items-center gap-1.5 text-emerald-600 font-medium">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                Active Roster
                            </span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">Drivers Roster</h1>
                            <p class="text-xs text-muted-foreground mt-2 leading-relaxed max-w-2xl">Manage certified operators, track duty availability, CDL license compliance, and bus assignments.</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5">
                        <div class="relative w-64">
                            <i data-lucide="search" class="w-3.5 h-3.5 text-muted-foreground absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <input id="driverSearch"
                                   data-shadcn-table-search="driversRosterTable"
                                   placeholder="Filter driver, badge, bus..."
                                   class="flex h-9 w-full rounded-md border border-input bg-transparent pl-9 pr-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                        </div>

                        <button id="exportDriversBtn" type="button"
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 gap-2">
                            <i data-lucide="download" class="w-3.5 h-3.5 text-muted-foreground"></i>
                            <span>Export</span>
                        </button>

                        <button id="addDriverModalTrigger" type="button" onclick="window.shadcnModal.open('addDriverModal')"
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2 gap-2 transition-all">
                            <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                            <span>Add Driver</span>
                        </button>
                    </div>
                </div>

                <!-- 5 Stat Cards (shadcn Card default) -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">Total Drivers</span>
                            <i data-lucide="users" class="w-4 h-4 text-primary"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono">{{ $totalDrivers }}</div>
                            <span class="text-xs text-muted-foreground">Certified operators</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-emerald-600">On Route</span>
                            <i data-lucide="navigation" class="w-4 h-4 text-emerald-600"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono text-emerald-600">{{ $onRouteDrivers }}</div>
                            <span class="text-xs text-muted-foreground">Operating shift</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-blue-600">Available</span>
                            <i data-lucide="check-circle" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono text-blue-600">{{ $availableDrivers }}</div>
                            <span class="text-xs text-muted-foreground">Depot standby</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">Off Duty</span>
                            <i data-lucide="clock" class="w-4 h-4 text-muted-foreground"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono">{{ $offDutyDrivers }}</div>
                            <span class="text-xs text-muted-foreground">Rest / Leave</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between col-span-2 md:col-span-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-amber-600">License Expiring</span>
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono text-amber-600">{{ $expiringLicensesCount }}</div>
                            <span class="text-xs text-muted-foreground">Within 60 days</span>
                        </div>
                    </div>
                </div>

                <!-- DataTable Container -->
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm overflow-hidden">
                    <!-- Table Toolbar -->
                    <div class="p-4 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-muted/20">
                        <div class="flex items-center gap-2" data-shadcn-filter-group="driversRosterTable">
                            <button type="button" data-filter="all" class="filter-pill px-3 py-1.5 rounded-md text-xs font-semibold bg-foreground text-background shadow-sm transition-colors">
                                All ({{ $totalDrivers }})
                            </button>
                            <button type="button" data-filter="on_route" class="filter-pill px-3 py-1.5 rounded-md text-xs font-medium text-muted-foreground hover:text-foreground transition-colors">
                                On Route ({{ $onRouteDrivers }})
                            </button>
                            <button type="button" data-filter="available" class="filter-pill px-3 py-1.5 rounded-md text-xs font-medium text-muted-foreground hover:text-foreground transition-colors">
                                Available ({{ $availableDrivers }})
                            </button>
                            <button type="button" data-filter="off_duty" class="filter-pill px-3 py-1.5 rounded-md text-xs font-medium text-muted-foreground hover:text-foreground transition-colors">
                                Off Duty
                            </button>
                        </div>
                        <span class="text-xs text-muted-foreground font-mono">Showing {{ $drivers->count() }} drivers</span>
                    </div>

                    <!-- Table -->
                    <div class="w-full overflow-x-auto">
                        <table class="w-full caption-bottom text-sm" id="driversRosterTable">
                            <thead>
                                <tr class="border-b bg-muted/50 font-mono text-[11px] uppercase tracking-wider text-muted-foreground">
                                    <th class="h-10 px-4 text-left align-middle font-medium">Driver</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Badge ID</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Duty Status</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Shift Hours</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Contact</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">License / Expiry</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Assigned Bus</th>
                                    <th class="h-10 px-4 text-right align-middle font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y text-foreground">
                                @forelse($drivers as $driver)
                                    @php
                                        $user = $driver->user;
                                        $initials = $user && $user->name ? strtoupper(substr($user->name, 0, 2)) : 'DR';
                                        $dutyClass = match($driver->duty_status) {
                                            'on_route' => 'border-transparent bg-emerald-500/15 text-emerald-600',
                                            'available' => 'border-transparent bg-blue-500/15 text-blue-600',
                                            'off_duty' => 'border-transparent bg-secondary text-secondary-foreground',
                                            'on_leave' => 'border-transparent bg-amber-500/15 text-amber-600',
                                            default => 'border-transparent bg-secondary text-secondary-foreground'
                                        };
                                        $dutyLabel = match($driver->duty_status) {
                                            'on_route' => 'On Route',
                                            'available' => 'Available',
                                            'off_duty' => 'Off Duty',
                                            'on_leave' => 'On Leave',
                                            default => ucfirst($driver->duty_status ?? 'Unknown')
                                        };
                                        $isExpiring = $driver->license_expiry && \Carbon\Carbon::parse($driver->license_expiry)->isBefore(now()->addDays(60));
                                    @endphp
                                    <tr class="border-b transition-colors hover:bg-muted/50" data-status="{{ $driver->duty_status }}">
                                        <td class="p-4 align-middle">
                                            <div class="flex items-center gap-3">
                                                @if($driver->avatar_url)
                                                    <img src="{{ $driver->avatar_url }}" alt="{{ $user->name ?? 'Driver' }}" class="w-8 h-8 rounded-full object-cover border" />
                                                @else
                                                    <div class="w-8 h-8 rounded-full bg-muted border flex items-center justify-center text-xs font-semibold">
                                                        {{ $initials }}
                                                    </div>
                                                @endif
                                                <div class="flex flex-col">
                                                    <span class="font-medium text-xs">{{ $user->name ?? 'Unnamed' }}</span>
                                                    <span class="text-[11px] text-muted-foreground">{{ $user->email ?? '—' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4 align-middle font-mono font-bold text-xs">{{ $driver->display_id }}</td>
                                        <td class="p-4 align-middle">
                                            <span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold transition-colors {{ $dutyClass }}">
                                                {{ $dutyLabel }}
                                            </span>
                                        </td>
                                        <td class="p-4 align-middle text-xs">
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-muted border text-foreground font-mono font-medium">
                                                <i data-lucide="clock" class="w-3 h-3 text-primary"></i>
                                                {{ $driver->shift ?: 'Morning (06:00 - 14:00)' }}
                                            </span>
                                        </td>
                                        <td class="p-4 align-middle text-xs font-mono">
                                            {{ $driver->phone ?: '—' }}
                                        </td>
                                        <td class="p-4 align-middle text-xs font-mono">
                                            <div>{{ $driver->license_number ?: '—' }}</div>
                                            @if($driver->license_expiry)
                                                <span class="text-[11px] {{ $isExpiring ? 'text-destructive font-semibold' : 'text-muted-foreground' }}">
                                                    Exp: {{ \Carbon\Carbon::parse($driver->license_expiry)->format('M d, Y') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-4 align-middle">
                                            @if($driver->bus)
                                                <div class="font-mono font-bold text-xs">{{ $driver->bus->display_id }}</div>
                                                <span class="text-[11px] text-muted-foreground">{{ $driver->bus->plate }}</span>
                                            @else
                                                <span class="text-muted-foreground italic text-xs">Unassigned</span>
                                            @endif
                                        </td>
                                        <td class="p-4 align-middle text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <button type="button"
                                                        onclick="openEditDriverModal({{ $driver->id }}, '{{ addslashes($user->name ?? '') }}', '{{ addslashes($user->email ?? '') }}', '{{ addslashes($driver->badge_id ?? '') }}', '{{ addslashes($driver->phone ?? '') }}', '{{ addslashes($driver->license_number ?? '') }}', '{{ addslashes($driver->license_type ?? '') }}', '{{ $driver->license_expiry }}', '{{ $driver->duty_status }}', '{{ addslashes($driver->shift ?? 'Morning (06:00 - 14:00)') }}', '{{ $user->status ?? 'active' }}')"
                                                        title="Edit Driver"
                                                        class="inline-flex items-center justify-center rounded-md border border-input bg-background h-8 w-8 hover:bg-accent hover:text-accent-foreground text-muted-foreground shadow-sm transition-colors">
                                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                                </button>
                                                <button type="button"
                                                        onclick="openDeleteDriverModal({{ $driver->id }}, '{{ addslashes($user->name ?? $driver->display_id) }}')"
                                                        title="Delete Driver"
                                                        class="inline-flex items-center justify-center rounded-md border border-input bg-background h-8 w-8 hover:bg-destructive/10 hover:text-destructive text-muted-foreground shadow-sm transition-colors">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="8" class="p-8 text-center text-muted-foreground">
                                            No drivers found matching your search.
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

    <!-- ================= shadcn Dialog: ADD DRIVER ================= -->
    <div id="addDriverModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-xs hidden">
        <div class="relative w-full max-w-lg rounded-xl border bg-background p-6 shadow-lg modal-content text-left max-h-[90vh] overflow-y-auto">
            <div class="flex flex-col space-y-1.5 pb-3 border-b">
                <h3 class="text-lg font-semibold leading-none tracking-tight">Add New Driver</h3>
                <p class="text-xs text-muted-foreground mt-1">Registers operator profile and sets up driver app login account.</p>
            </div>

            <form method="POST" action="{{ route('drivers.store') }}" class="space-y-3 pt-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Full Name *</label>
                        <input name="name" required placeholder="e.g. Marcus Vance"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Driver Badge / ID *</label>
                        <input name="badge_id" required placeholder="e.g. DRV-8802"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring font-mono uppercase"/>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Login Email *</label>
                        <input name="email" type="email" required placeholder="driver@fleet.local"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Initial Password *</label>
                        <input name="password" type="password" required minlength="6" placeholder="Min. 6 characters"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Phone Number</label>
                        <input name="phone" placeholder="+1 (555) 234-8802"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring font-mono"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Duty Status</label>
                        <select name="duty_status" class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring">
                            <option value="available">Available (Standby)</option>
                            <option value="on_route">On Route</option>
                            <option value="off_duty">Off Duty</option>
                            <option value="on_leave">On Leave / Holiday</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2 p-3 rounded-lg border bg-muted/30">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-semibold leading-none text-foreground flex items-center gap-1.5">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-primary"></i>
                            <span>Shift Hours / Schedule *</span>
                        </label>
                        <span class="text-[11px] text-muted-foreground font-mono">Preset or Custom Range</span>
                    </div>

                    <!-- Preset Selector -->
                    <div class="space-y-1">
                        <label class="text-[11px] text-muted-foreground">Select Preset</label>
                        <select id="addShiftPreset" class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring"
                                onchange="handleShiftPresetChange('addShiftPreset', 'addShiftFrom', 'addShiftTo', 'addShiftInput')">
                            <option value="Morning (06:00 - 14:00)" data-from="06:00" data-to="14:00" selected>Morning (06:00 - 14:00)</option>
                            <option value="Evening (14:00 - 22:00)" data-from="14:00" data-to="22:00">Evening (14:00 - 22:00)</option>
                            <option value="Night (22:00 - 06:00)" data-from="22:00" data-to="06:00">Night (22:00 - 06:00)</option>
                            <option value="Full Day (08:00 - 17:00)" data-from="08:00" data-to="17:00">Full Day (08:00 - 17:00)</option>
                            <option value="Split Shift (07:00 - 11:00 / 15:00 - 19:00)" data-from="07:00" data-to="19:00">Split Shift (07:00 - 11:00 / 15:00 - 19:00)</option>
                            <option value="custom" data-from="" data-to="">Custom (Manual Time Range)</option>
                        </select>
                    </div>

                    <!-- Manual Shift Time Range (From Time - To Time) -->
                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <div class="space-y-1">
                            <label class="text-[11px] text-muted-foreground">From Hour</label>
                            <input id="addShiftFrom" type="time" value="06:00"
                                   oninput="updateShiftFromManual('addShiftFrom', 'addShiftTo', 'addShiftInput', 'addShiftPreset')"
                                   class="flex h-8 w-full rounded-md border border-input bg-background px-2.5 py-1 text-xs shadow-xs focus:outline-none focus:ring-1 focus:ring-ring font-mono"/>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[11px] text-muted-foreground">To Hour</label>
                            <input id="addShiftTo" type="time" value="14:00"
                                   oninput="updateShiftFromManual('addShiftFrom', 'addShiftTo', 'addShiftInput', 'addShiftPreset')"
                                   class="flex h-8 w-full rounded-md border border-input bg-background px-2.5 py-1 text-xs shadow-xs focus:outline-none focus:ring-1 focus:ring-ring font-mono"/>
                        </div>
                    </div>

                    <!-- Final Shift Value sent in form -->
                    <div class="space-y-1 pt-1">
                        <label class="text-[11px] text-muted-foreground">Operating Shift Value</label>
                        <input id="addShiftInput" name="shift" required value="Morning (06:00 - 14:00)"
                               placeholder="e.g. 06:00 - 14:00"
                               class="flex h-8 w-full rounded-md border border-input bg-background px-2.5 py-1 text-xs shadow-xs font-medium text-foreground focus:outline-none focus:ring-1 focus:ring-ring"/>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium leading-none">Account Status</label>
                    <select name="status" class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">License Number</label>
                        <input name="license_number" placeholder="CDL-8802-TX"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring font-mono"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">License Expiry Date</label>
                        <input name="license_expiry" type="date"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring"/>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2 pt-4 border-t">
                    <button type="button" onclick="window.shadcnModal.close('addDriverModal')"
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                        Cancel
                    </button>
                    <button type="submit"
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                        Register Driver
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= shadcn Dialog: EDIT DRIVER ================= -->
    <div id="editDriverModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-xs hidden">
        <div class="relative w-full max-w-lg rounded-xl border bg-background p-6 shadow-lg modal-content text-left max-h-[90vh] overflow-y-auto">
            <div class="flex flex-col space-y-1.5 pb-3 border-b">
                <h3 class="text-lg font-semibold leading-none tracking-tight">Edit Driver Profile</h3>
                <p class="text-xs text-muted-foreground mt-1">Update operator credentials, contact info, shift hours, and status.</p>
            </div>

            <form id="editDriverForm" method="POST" action="" class="space-y-3 pt-3">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Full Name *</label>
                        <input id="editName" name="name" required
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Driver Badge / ID *</label>
                        <input id="editBadgeId" name="badge_id" required
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring font-mono uppercase"/>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Login Email *</label>
                        <input id="editEmail" name="email" type="email" required
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">New Password (Optional)</label>
                        <input id="editPassword" name="password" type="password" minlength="6" placeholder="Leave blank to keep"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Phone Number</label>
                        <input id="editPhone" name="phone"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring font-mono"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">Duty Status</label>
                        <select id="editDutyStatus" name="duty_status" class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring">
                            <option value="available">Available (Standby)</option>
                            <option value="on_route">On Route</option>
                            <option value="off_duty">Off Duty</option>
                            <option value="on_leave">On Leave / Holiday</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2 p-3 rounded-lg border bg-muted/30">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-semibold leading-none text-foreground flex items-center gap-1.5">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-primary"></i>
                            <span>Shift Hours / Schedule *</span>
                        </label>
                        <span class="text-[11px] text-muted-foreground font-mono">Preset or Custom Range</span>
                    </div>

                    <!-- Preset Selector -->
                    <div class="space-y-1">
                        <label class="text-[11px] text-muted-foreground">Select Preset</label>
                        <select id="editShiftPreset" class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring"
                                onchange="handleShiftPresetChange('editShiftPreset', 'editShiftFrom', 'editShiftTo', 'editShift')">
                            <option value="Morning (06:00 - 14:00)" data-from="06:00" data-to="14:00">Morning (06:00 - 14:00)</option>
                            <option value="Evening (14:00 - 22:00)" data-from="14:00" data-to="22:00">Evening (14:00 - 22:00)</option>
                            <option value="Night (22:00 - 06:00)" data-from="22:00" data-to="06:00">Night (22:00 - 06:00)</option>
                            <option value="Full Day (08:00 - 17:00)" data-from="08:00" data-to="17:00">Full Day (08:00 - 17:00)</option>
                            <option value="Split Shift (07:00 - 11:00 / 15:00 - 19:00)" data-from="07:00" data-to="19:00">Split Shift (07:00 - 11:00 / 15:00 - 19:00)</option>
                            <option value="custom" data-from="" data-to="">Custom (Manual Time Range)</option>
                        </select>
                    </div>

                    <!-- Manual Shift Time Range (From Time - To Time) -->
                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <div class="space-y-1">
                            <label class="text-[11px] text-muted-foreground">From Hour</label>
                            <input id="editShiftFrom" type="time" value="06:00"
                                   oninput="updateShiftFromManual('editShiftFrom', 'editShiftTo', 'editShift', 'editShiftPreset')"
                                   class="flex h-8 w-full rounded-md border border-input bg-background px-2.5 py-1 text-xs shadow-xs focus:outline-none focus:ring-1 focus:ring-ring font-mono"/>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[11px] text-muted-foreground">To Hour</label>
                            <input id="editShiftTo" type="time" value="14:00"
                                   oninput="updateShiftFromManual('editShiftFrom', 'editShiftTo', 'editShift', 'editShiftPreset')"
                                   class="flex h-8 w-full rounded-md border border-input bg-background px-2.5 py-1 text-xs shadow-xs focus:outline-none focus:ring-1 focus:ring-ring font-mono"/>
                        </div>
                    </div>

                    <!-- Final Shift Value sent in form -->
                    <div class="space-y-1 pt-1">
                        <label class="text-[11px] text-muted-foreground">Operating Shift Value</label>
                        <input id="editShift" name="shift" required value="Morning (06:00 - 14:00)"
                               placeholder="e.g. 06:00 - 14:00"
                               class="flex h-8 w-full rounded-md border border-input bg-background px-2.5 py-1 text-xs shadow-xs font-medium text-foreground focus:outline-none focus:ring-1 focus:ring-ring"/>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium leading-none">Account Status</label>
                    <select id="editStatus" name="status" class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">License Number</label>
                        <input id="editLicenseNumber" name="license_number"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring font-mono"/>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium leading-none">License Expiry Date</label>
                        <input id="editLicenseExpiry" name="license_expiry" type="date"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs focus:outline-none focus:ring-1 focus:ring-ring"/>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2 pt-4 border-t">
                    <button type="button" onclick="window.shadcnModal.close('editDriverModal')"
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                        Cancel
                    </button>
                    <button type="submit"
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                        Update Driver
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= shadcn Alert Dialog: DELETE DRIVER ================= -->
    <div id="deleteDriverModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-xs hidden">
        <div class="relative w-full max-w-md rounded-xl border bg-background p-6 shadow-lg modal-content text-left">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-destructive/10 text-destructive flex items-center justify-center shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold leading-none tracking-tight">Delete Driver Profile?</h3>
                    <p class="text-xs text-muted-foreground mt-2">
                        Are you sure you want to remove <span id="deleteDriverName" class="font-bold text-foreground"></span>? This will unassign any active bus and remove login permissions.
                    </p>
                </div>
            </div>

            <form id="deleteDriverForm" method="POST" action="" class="flex items-center justify-end gap-2 pt-5 mt-4 border-t">
                @csrf
                @method('DELETE')
                <button type="button" onclick="window.shadcnModal.close('deleteDriverModal')"
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                    Cancel
                </button>
                <button type="submit"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium bg-destructive text-destructive-foreground shadow-sm hover:bg-destructive/90 h-9 px-4 py-2">
                    Yes, Delete Driver
                </button>
            </form>
        </div>
    </div>

    <script>
        function handleShiftPresetChange(presetSelectId, fromInputId, toInputId, finalInputId) {
            const presetSelect = document.getElementById(presetSelectId);
            const fromInput = document.getElementById(fromInputId);
            const toInput = document.getElementById(toInputId);
            const finalInput = document.getElementById(finalInputId);
            if (!presetSelect || !fromInput || !toInput || !finalInput) return;

            const selectedOption = presetSelect.options[presetSelect.selectedIndex];
            const val = selectedOption.value;
            const from = selectedOption.getAttribute('data-from');
            const to = selectedOption.getAttribute('data-to');

            if (val === 'custom') {
                if (fromInput.value && toInput.value) {
                    finalInput.value = fromInput.value + ' - ' + toInput.value;
                }
            } else {
                if (from) fromInput.value = from;
                if (to) toInput.value = to;
                finalInput.value = val;
            }
        }

        function updateShiftFromManual(fromInputId, toInputId, finalInputId, presetSelectId) {
            const fromInput = document.getElementById(fromInputId);
            const toInput = document.getElementById(toInputId);
            const finalInput = document.getElementById(finalInputId);
            const presetSelect = document.getElementById(presetSelectId);
            if (!fromInput || !toInput || !finalInput) return;

            const fromVal = fromInput.value || '';
            const toVal = toInput.value || '';

            if (fromVal && toVal) {
                finalInput.value = fromVal + ' - ' + toVal;
            } else if (fromVal) {
                finalInput.value = fromVal;
            }

            if (presetSelect) {
                let matched = false;
                for (let i = 0; i < presetSelect.options.length; i++) {
                    const opt = presetSelect.options[i];
                    if (opt.getAttribute('data-from') === fromVal && opt.getAttribute('data-to') === toVal) {
                        presetSelect.selectedIndex = i;
                        matched = true;
                        break;
                    }
                }
                if (!matched) {
                    presetSelect.value = 'custom';
                }
            }
        }

        function openEditDriverModal(id, name, email, badgeId, phone, licenseNumber, licenseType, licenseExpiry, dutyStatus, shift, status) {
            document.getElementById('editDriverForm').action = '/drivers/' + id;
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editBadgeId').value = badgeId;
            document.getElementById('editPhone').value = phone || '';
            document.getElementById('editLicenseNumber').value = licenseNumber || '';
            document.getElementById('editLicenseExpiry').value = licenseExpiry || '';
            document.getElementById('editDutyStatus').value = dutyStatus || 'available';

            const shiftVal = shift || 'Morning (06:00 - 14:00)';
            if (document.getElementById('editShift')) {
                document.getElementById('editShift').value = shiftVal;
            }

            // Parse shift time range if present (e.g. 06:00 - 14:00)
            const match = shiftVal.match(/(\d{1,2}:\d{2})\s*[-–toTO]+\s*(\d{1,2}:\d{2})/);
            const fromInput = document.getElementById('editShiftFrom');
            const toInput = document.getElementById('editShiftTo');
            const presetSelect = document.getElementById('editShiftPreset');

            if (match && fromInput && toInput) {
                fromInput.value = match[1];
                toInput.value = match[2];
            }

            if (presetSelect) {
                let matched = false;
                for (let i = 0; i < presetSelect.options.length; i++) {
                    if (presetSelect.options[i].value === shiftVal) {
                        presetSelect.selectedIndex = i;
                        matched = true;
                        break;
                    }
                }
                if (!matched) {
                    presetSelect.value = 'custom';
                }
            }

            if (document.getElementById('editStatus')) {
                document.getElementById('editStatus').value = status || 'active';
            }
            document.getElementById('editPassword').value = '';
            window.shadcnModal.open('editDriverModal');
        }

        function openDeleteDriverModal(id, name) {
            document.getElementById('deleteDriverForm').action = '/drivers/' + id;
            document.getElementById('deleteDriverName').textContent = name;
            window.shadcnModal.open('deleteDriverModal');
        }

        document.getElementById('exportDriversBtn')?.addEventListener('click', function () {
            window.toast?.success('Drivers Roster Exported', 'CSV report has been downloaded.');
        });
    </script>
</body>
</html>
