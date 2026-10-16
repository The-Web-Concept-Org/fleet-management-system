<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Fuel &amp; Refuel Logs &bull; Lancotri</title>
    @include('partials.brand-styles')
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/lancotri-mark.svg') }}">
</head>
<body class="bg-background text-foreground antialiased min-h-screen">
    @include('partials.sidebar')

    <div class="pl-64 flex flex-col min-h-screen">
        @include('partials.topbar', [
            'topbarSearchPlaceholder' => 'Search fuel ref, bus, driver, or station...',
            'topbarSearchId' => 'fuelNavSearch',
            'topbarStatus' => $totalLogs . ' ' . \Illuminate\Support\Str::plural('fill-up', $totalLogs),
        ])

        <main class="w-full pt-16 flex-1 px-8 py-8 bg-background">
            <div class="flex flex-col w-full gap-6 max-w-7xl mx-auto">

                <!-- Page Header -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b">
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-xs text-muted-foreground font-mono">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-muted border">
                                <i data-lucide="fuel" class="w-3.5 h-3.5 text-primary"></i>
                                <span>FUEL &amp; CONSUMPTION AUDIT</span>
                            </span>
                            <span>&bull;</span>
                            <span class="inline-flex items-center gap-1.5 text-emerald-600 font-medium">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                Live Dispenser Feed
                            </span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">Fuel Logs</h1>
                            <p class="text-xs text-muted-foreground mt-2 leading-relaxed max-w-2xl">Every fill-up captured from mobile telemetry: dispenser receipts, liters dispensed, fuel unit price, and station logs.</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5">
                        <div class="relative w-64">
                            <i data-lucide="search" class="w-3.5 h-3.5 text-muted-foreground absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <input id="fuelSearch"
                                   data-shadcn-table-search="fuelLogsTable"
                                   placeholder="Filter ref, bus, station..."
                                   class="flex h-9 w-full rounded-md border border-input bg-transparent pl-9 pr-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                        </div>

                        <button id="exportFuelCsvBtn" type="button" onclick="window.toast?.success('Fuel Log Exported', 'CSV report downloaded.')"
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 gap-2">
                            <i data-lucide="download" class="w-3.5 h-3.5 text-muted-foreground"></i>
                            <span>Export</span>
                        </button>
                    </div>
                </div>

                <!-- 5 Stat Cards (shadcn Card default) -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">Total Fill-ups</span>
                            <i data-lucide="fuel" class="w-4 h-4 text-primary"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono">{{ $totalLogs }}</div>
                            <span class="text-xs text-muted-foreground">Receipts logged</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-blue-600">Volume</span>
                            <i data-lucide="droplet" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono text-blue-600">{{ number_format($totalLiters, 1) }}</div>
                            <span class="text-xs text-muted-foreground">Total Liters</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">Total Spend</span>
                            <i data-lucide="credit-card" class="w-4 h-4 text-emerald-600"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono text-foreground">${{ number_format($totalCost, 2) }}</div>
                            <span class="text-xs text-muted-foreground">USD Spend</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">Avg Unit Price</span>
                            <i data-lucide="tag" class="w-4 h-4 text-amber-600"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono">${{ number_format($averageUnitPrice, 2) }}</div>
                            <span class="text-xs text-muted-foreground">Per Liter</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between col-span-2 md:col-span-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">Needs Review</span>
                            <i data-lucide="alert-circle" class="w-4 h-4 text-muted-foreground"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono">{{ $pendingCount }}</div>
                            <span class="text-xs text-muted-foreground">{{ $flaggedCount }} flagged</span>
                        </div>
                    </div>
                </div>

                <!-- Filter Toolbar (shadcn Card) -->
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4">
                    <form action="{{ route('fuel') }}" method="GET" class="flex flex-wrap items-center gap-3">
                        <div class="w-48">
                            <select name="bus_id" onchange="this.form.submit()"
                                    class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring">
                                <option value="">All Buses ({{ $buses->count() }})</option>
                                @foreach($buses as $bus)
                                    <option value="{{ $bus->id }}" @selected((string) $busId === (string) $bus->id)>
                                        {{ $bus->display_id }}{{ $bus->plate ? ' · ' . $bus->plate : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-48">
                            <select name="driver_id" onchange="this.form.submit()"
                                    class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring">
                                <option value="">All Drivers ({{ $drivers->count() }})</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" @selected((string) $driverId === (string) $driver->id)>
                                        {{ $driver->user->name ?? 'Driver' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-40">
                            <select name="type" onchange="this.form.submit()"
                                    class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring">
                                <option value="">All Fuel Types</option>
                                @foreach($types as $fuelType)
                                    <option value="{{ $fuelType }}" @selected($type === $fuelType)>{{ ucfirst($fuelType) }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($busId || $driverId || $type || $from || $to)
                            <a href="{{ route('fuel') }}"
                               class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-3 gap-1.5 text-muted-foreground">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                <span>Reset</span>
                            </a>
                        @endif
                    </form>
                </div>

                <!-- DataTable Container -->
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm overflow-hidden">
                    <div class="w-full overflow-x-auto">
                        <table class="w-full caption-bottom text-sm" id="fuelLogsTable">
                            <thead>
                                <tr class="border-b bg-muted/50 font-mono text-[11px] uppercase tracking-wider text-muted-foreground">
                                    <th class="h-10 px-4 text-left align-middle font-medium">Ref #</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Date &amp; Time</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Vehicle / Driver</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Fuel Type</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Liters</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Unit Price</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Total Cost</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Station</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">Status</th>
                                    <th class="h-10 px-4 text-right align-middle font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y text-foreground">
                                @forelse($fuelLogs as $log)
                                    <tr class="border-b transition-colors hover:bg-muted/50">
                                        <td class="p-4 align-middle font-mono font-bold">{{ $log->display_id }}</td>
                                        <td class="p-4 align-middle font-mono text-xs">
                                            <div>{{ $log->filled_at?->format('M j, Y') ?? '—' }}</div>
                                            <span class="text-muted-foreground text-[11px]">{{ $log->filled_at?->format('H:i') ?? '—' }}</span>
                                        </td>
                                        <td class="p-4 align-middle">
                                            <div class="font-mono font-bold text-xs">{{ $log->bus?->display_id ?? 'Bus —' }}</div>
                                            <span class="text-[11px] text-muted-foreground">{{ $log->driver?->user?->name ?? 'Unassigned' }}</span>
                                        </td>
                                        <td class="p-4 align-middle">
                                            <span class="inline-flex items-center rounded-md border border-transparent bg-secondary text-secondary-foreground px-2 py-0.5 text-xs font-semibold">
                                                {{ ucfirst($log->fuel_type ?? 'Diesel') }}
                                            </span>
                                        </td>
                                        <td class="p-4 align-middle font-mono text-xs font-bold">{{ number_format($log->liters, 1) }} L</td>
                                        <td class="p-4 align-middle font-mono text-xs">${{ number_format($log->unit_price, 2) }}/L</td>
                                        <td class="p-4 align-middle font-mono text-xs font-bold text-emerald-600">${{ number_format($log->cost, 2) }}</td>
                                        <td class="p-4 align-middle text-xs">{{ $log->station_name ?: 'Depot Pump' }}</td>
                                        <td class="p-4 align-middle">
                                            @php
                                                $status = $log->status ?? 'pending';
                                                $badgeClass = match($status) {
                                                    'approved' => 'border-emerald-500/30 bg-emerald-500/15 text-emerald-700 dark:text-emerald-400',
                                                    'pending' => 'border-amber-500/30 bg-amber-500/15 text-amber-700 dark:text-amber-400',
                                                    'flagged' => 'border-destructive/30 bg-destructive/15 text-destructive',
                                                    default => 'border-transparent bg-secondary text-secondary-foreground'
                                                };
                                                $statusLabel = match($status) {
                                                    'approved' => 'Approved',
                                                    'pending' => 'Pending Review',
                                                    'flagged' => 'Flagged',
                                                    default => ucfirst($status)
                                                };
                                            @endphp
                                            <button type="button"
                                                    onclick="openFuelStatusModal({{ $log->id }}, '{{ $status }}', '{{ $log->display_id }}', '{{ addslashes($log->flagged_reason ?? '') }}')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md border text-xs font-semibold {{ $badgeClass }} hover:opacity-85 transition-opacity cursor-pointer"
                                                    title="Click to change status">
                                                <span>{{ $statusLabel }}</span>
                                                <i data-lucide="chevron-down" class="w-3 h-3 opacity-60"></i>
                                            </button>
                                        </td>
                                        <td class="p-4 align-middle text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <button type="button"
                                                        onclick="openFuelStatusModal({{ $log->id }}, '{{ $status }}', '{{ $log->display_id }}', '{{ addslashes($log->flagged_reason ?? '') }}')"
                                                        class="inline-flex items-center justify-center rounded-md border border-input bg-background h-8 w-8 hover:bg-accent hover:text-accent-foreground text-muted-foreground shadow-sm transition-colors"
                                                        title="Update Status">
                                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                                </button>
                                                @if($log->image)
                                                    <button type="button" onclick="openReceiptModal('{{ $log->image_url }}', '{{ $log->display_id }}', '{{ number_format($log->cost, 2) }}')"
                                                            class="inline-flex items-center justify-center rounded-md border border-input bg-background h-8 w-8 hover:bg-accent hover:text-accent-foreground text-muted-foreground shadow-sm transition-colors" title="View Dispenser Receipt">
                                                        <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                @else
                                                    <span class="text-muted-foreground text-xs italic px-1">No photo</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="10" class="p-8 text-center text-muted-foreground">
                                            No fuel entries found.
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

    <!-- ================= shadcn Dialog: RECEIPT MODAL ================= -->
    <div id="receiptModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-xs hidden">
        <div class="relative w-full max-w-md rounded-xl border bg-background p-6 shadow-lg modal-content text-left">
            <div class="flex items-center justify-between pb-3 border-b">
                <div>
                    <h3 class="text-base font-semibold leading-none tracking-tight">Dispenser Receipt Proof</h3>
                    <p class="text-xs text-muted-foreground mt-1" id="receiptRef"></p>
                </div>
                <button type="button" onclick="window.shadcnModal.close('receiptModal')" class="text-muted-foreground hover:text-foreground">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="mt-4 rounded-lg overflow-hidden border bg-muted h-72">
                <img id="receiptImg" src="" alt="Receipt" class="w-full h-full object-contain bg-black/5"/>
            </div>
            <div class="mt-4 flex justify-between items-center text-xs">
                <span class="text-muted-foreground">Logged Total:</span>
                <span class="font-mono font-bold text-sm text-emerald-600" id="receiptTotal"></span>
            </div>
        </div>
    </div>

    <!-- ================= shadcn Dialog: FUEL STATUS MODAL ================= -->
    <div id="fuelStatusModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-xs hidden">
        <div class="relative w-full max-w-md rounded-xl border bg-background p-6 shadow-lg modal-content text-left">
            <div class="flex items-center justify-between pb-3 border-b">
                <div>
                    <h3 class="text-base font-semibold leading-none tracking-tight">Update Fuel Log Status</h3>
                    <p class="text-xs text-muted-foreground mt-1" id="fuelStatusModalRef"></p>
                </div>
                <button type="button" onclick="window.shadcnModal.close('fuelStatusModal')" class="text-muted-foreground hover:text-foreground">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form id="fuelStatusModalForm" method="POST" action="" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-medium">Record Status</label>
                    <select id="fuelModalStatusSelect" name="status" onchange="toggleFuelFlaggedReasonField(this.value)" class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring">
                        <option value="pending">🟡 Pending Review</option>
                        <option value="approved">🟢 Approved &amp; Reconciled</option>
                        <option value="flagged">🔴 Flagged / Disputed</option>
                    </select>
                </div>
                <div id="fuelFlaggedReasonGroup" class="hidden">
                    <label class="text-xs font-medium text-destructive">Dispute / Flag Reason</label>
                    <textarea id="fuelModalFlaggedReason" name="flagged_reason" rows="2" placeholder="State reason for flagging fuel receipt or volume..."
                              class="flex w-full rounded-md border border-destructive/40 bg-background px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-destructive"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t">
                    <button type="button" onclick="window.shadcnModal.close('fuelStatusModal')" class="rounded-md border px-3 py-1.5 text-xs font-medium hover:bg-muted">
                        Cancel
                    </button>
                    <button type="submit" class="rounded-md bg-primary text-primary-foreground px-4 py-1.5 text-xs font-semibold hover:bg-primary/90 shadow-xs">
                        Save Status
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openReceiptModal(url, ref, total) {
            document.getElementById('receiptImg').src = url;
            document.getElementById('receiptRef').textContent = ref;
            document.getElementById('receiptTotal').textContent = '$' + total;
            window.shadcnModal.open('receiptModal');
            if (window.lucide) window.lucide.createIcons();
        }

        function openFuelStatusModal(id, status, ref, reason) {
            document.getElementById('fuelStatusModalForm').action = '/fuel/' + id + '/status';
            document.getElementById('fuelStatusModalRef').textContent = ref;
            document.getElementById('fuelModalStatusSelect').value = status || 'pending';
            document.getElementById('fuelModalFlaggedReason').value = reason || '';
            toggleFuelFlaggedReasonField(status || 'pending');
            window.shadcnModal.open('fuelStatusModal');
            if (window.lucide) window.lucide.createIcons();
        }

        function toggleFuelFlaggedReasonField(status) {
            const group = document.getElementById('fuelFlaggedReasonGroup');
            if (group) {
                if (status === 'flagged') {
                    group.classList.remove('hidden');
                } else {
                    group.classList.add('hidden');
                }
            }
        }
    </script>
</body>
</html>
