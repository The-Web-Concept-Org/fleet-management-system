<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>{{ __('Fuel & Refuel Logs') }} &bull; Lancotri</title>
    @include('partials.brand-styles')
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/lancotri-mark.svg') }}">
</head>
<body class="bg-background text-foreground antialiased min-h-screen">
    @include('partials.sidebar')

    <div class="pl-64 flex flex-col min-h-screen">
        @include('partials.topbar', [
            'topbarSearchPlaceholder' => __('Search fuel ref, bus, driver, or station...'),
            'topbarSearchId' => 'fuelNavSearch',
            'topbarStatus' => $totalLogs . ' ' . ($totalLogs === 1 ? __('fill-up') : __('fill-ups')),
        ])

        <main class="w-full pt-16 flex-1 px-8 py-8 bg-background">
            <div class="flex flex-col w-full gap-6 max-w-7xl mx-auto">

                <!-- Page Header -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b">
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-xs text-muted-foreground font-mono">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-muted border">
                                <i data-lucide="fuel" class="w-3.5 h-3.5 text-primary"></i>
                                <span>{{ __('FUEL & CONSUMPTION AUDIT') }}</span>
                            </span>
                            <span>&bull;</span>
                            <span class="inline-flex items-center gap-1.5 text-emerald-600 font-medium">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                {{ __('Live Dispenser Feed') }}
                            </span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">{{ __('Fuel Logs') }}</h1>
                            <p class="text-xs text-muted-foreground mt-2 leading-relaxed max-w-2xl">{{ __('Every fill-up captured from mobile telemetry: dispenser receipts, liters dispensed, fuel unit price, and station logs.') }}</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5">
                        <div class="relative w-64">
                            <i data-lucide="search" class="w-3.5 h-3.5 text-muted-foreground absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <input id="fuelSearch"
                                   data-shadcn-table-search="fuelLogsTable"
                                   placeholder="{{ __('Filter ref, bus, station...') }}"
                                   class="flex h-9 w-full rounded-md border border-input bg-transparent pl-9 pr-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                        </div>

                        <button type="button" onclick="openAddFuelModal()"
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium bg-primary text-primary-foreground shadow-sm hover:bg-primary/90 h-9 px-4 py-2 gap-2 cursor-pointer">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>{{ __('Add Fuel Entry') }}</span>
                        </button>

                        <button id="exportFuelCsvBtn" type="button" onclick="window.toast?.success('{{ __('Fuel Log Exported') }}', '{{ __('CSV report downloaded.') }}')"
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 gap-2">
                            <i data-lucide="download" class="w-3.5 h-3.5 text-muted-foreground"></i>
                            <span>{{ __('Export') }}</span>
                        </button>
                    </div>
                </div>

                <!-- 5 Stat Cards (shadcn Card default) -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">{{ __('Total Fill-ups') }}</span>
                            <i data-lucide="fuel" class="w-4 h-4 text-primary"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono">{{ $totalLogs }}</div>
                            <span class="text-xs text-muted-foreground">{{ __('Receipts logged') }}</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-blue-600">{{ __('Volume') }}</span>
                            <i data-lucide="droplet" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono text-blue-600">{{ number_format($totalLiters, 1) }}</div>
                            <span class="text-xs text-muted-foreground">{{ __('Total Liters') }}</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">{{ __('Total Spend') }}</span>
                            <i data-lucide="credit-card" class="w-4 h-4 text-emerald-600"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono text-foreground">${{ number_format($totalCost, 2) }}</div>
                            <span class="text-xs text-muted-foreground">{{ __('USD Spend') }}</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">{{ __('Avg Unit Price') }}</span>
                            <i data-lucide="tag" class="w-4 h-4 text-amber-600"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono">${{ number_format($averageUnitPrice, 2) }}</div>
                            <span class="text-xs text-muted-foreground">{{ __('Per Liter') }}</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 flex flex-col justify-between col-span-2 md:col-span-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">{{ __('Needs Review') }}</span>
                            <i data-lucide="alert-circle" class="w-4 h-4 text-muted-foreground"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono">{{ $pendingCount }}</div>
                            <span class="text-xs text-muted-foreground">{{ $flaggedCount }} {{ __('flagged') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Filter Toolbar (shadcn Card) -->
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4">
                    <form action="{{ route('fuel') }}" method="GET" class="flex flex-wrap items-center gap-3">
                        <div class="w-48">
                            <select name="bus_id" onchange="this.form.submit()"
                                    class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring">
                                <option value="">{{ __('All Buses') }} ({{ $buses->count() }})</option>
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
                                <option value="">{{ __('All Drivers') }} ({{ $drivers->count() }})</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" @selected((string) $driverId === (string) $driver->id)>
                                        {{ $driver->user->name ?? __('Driver') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-40">
                            <select name="type" onchange="this.form.submit()"
                                    class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring">
                                <option value="">{{ __('All Fuel Types') }}</option>
                                @foreach($types as $fuelType)
                                    <option value="{{ $fuelType }}" @selected($type === $fuelType)>{{ ucfirst($fuelType) }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($busId || $driverId || $type || $from || $to)
                            <a href="{{ route('fuel') }}"
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-3 gap-1.5 text-muted-foreground">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                <span>{{ __('Reset') }}</span>
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
                                    <th class="h-10 px-4 text-left align-middle font-medium">{{ __('Ref #') }}</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">{{ __('Date & Time') }}</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">{{ __('Vehicle / Driver') }}</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">{{ __('Fuel Type') }}</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">{{ __('Liters') }}</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">{{ __('Unit Price') }}</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">{{ __('Total Cost') }}</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">{{ __('Station') }}</th>
                                    <th class="h-10 px-4 text-left align-middle font-medium">{{ __('Status') }}</th>
                                    <th class="h-10 px-4 text-right align-middle font-medium">{{ __('Actions') }}</th>
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
                                            <span class="text-[11px] text-muted-foreground">{{ $log->driver?->user?->name ?? __('Unassigned') }}</span>
                                        </td>
                                        <td class="p-4 align-middle">
                                            <span class="inline-flex items-center rounded-md border border-transparent bg-secondary text-secondary-foreground px-2 py-0.5 text-xs font-semibold">
                                                {{ ucfirst($log->fuel_type ?? 'Diesel') }}
                                            </span>
                                        </td>
                                        <td class="p-4 align-middle font-mono text-xs font-bold">{{ number_format($log->liters, 1) }} L</td>
                                        <td class="p-4 align-middle font-mono text-xs">${{ number_format($log->unit_price, 2) }}/L</td>
                                        <td class="p-4 align-middle font-mono text-xs font-bold text-emerald-600">${{ number_format($log->total_cost, 2) }}</td>
                                        <td class="p-4 align-middle text-xs">{{ $log->station_name ?: __('Depot Pump') }}</td>
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
                                                    'approved' => __('Approved'),
                                                    'pending' => __('Pending Review'),
                                                    'flagged' => __('Flagged'),
                                                    default => ucfirst($status)
                                                };
                                            @endphp
                                            <button type="button"
                                                    onclick="openFuelStatusModal({{ $log->id }}, '{{ $status }}', '{{ $log->display_id }}', '{{ addslashes($log->flagged_reason ?? '') }}')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md border text-xs font-semibold {{ $badgeClass }} hover:opacity-85 transition-opacity cursor-pointer"
                                                    title="{{ __('Click to change status') }}">
                                                <span>{{ $statusLabel }}</span>
                                                <i data-lucide="chevron-down" class="w-3 h-3 opacity-60"></i>
                                            </button>
                                        </td>
                                        <td class="p-4 align-middle text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <button type="button"
                                                        onclick="openFuelStatusModal({{ $log->id }}, '{{ $status }}', '{{ $log->display_id }}', '{{ addslashes($log->flagged_reason ?? '') }}')"
                                                        class="inline-flex items-center justify-center rounded-md border border-input bg-background h-8 w-8 hover:bg-accent hover:text-accent-foreground text-muted-foreground shadow-sm transition-colors"
                                                        title="{{ __('Update Status') }}">
                                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                                </button>
                                                @if($log->image)
                                                    <button type="button" onclick="openReceiptModal('{{ $log->image_url }}', '{{ $log->display_id }}', '{{ number_format($log->total_cost, 2) }}')"
                                                            class="inline-flex items-center justify-center rounded-md border border-input bg-background h-8 w-8 hover:bg-accent hover:text-accent-foreground text-muted-foreground shadow-sm transition-colors" title="{{ __('View Dispenser Receipt') }}">
                                                        <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                @else
                                                    <span class="text-muted-foreground text-xs italic px-1">{{ __('No photo') }}</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="10" class="p-8 text-center text-muted-foreground">
                                            {{ __('No fuel entries found.') }}
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
                    <h3 class="text-base font-semibold leading-none tracking-tight">{{ __('Dispenser Receipt Proof') }}</h3>
                    <p class="text-xs text-muted-foreground mt-1" id="receiptRef"></p>
                </div>
                <button type="button" onclick="window.shadcnModal.close('receiptModal')" class="text-muted-foreground hover:text-foreground">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="mt-4 rounded-lg overflow-hidden border bg-muted h-72">
                <img id="receiptImg" src="" alt="{{ __('Receipt') }}" class="w-full h-full object-contain bg-black/5"/>
            </div>
            <div class="mt-4 flex justify-between items-center text-xs">
                <span class="text-muted-foreground">{{ __('Logged Total:') }}</span>
                <span class="font-mono font-bold text-sm text-emerald-600" id="receiptTotal"></span>
            </div>
        </div>
    </div>

    <!-- ================= shadcn Dialog: FUEL STATUS MODAL ================= -->
    <div id="fuelStatusModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-xs hidden">
        <div class="relative w-full max-w-md rounded-xl border bg-background p-6 shadow-lg modal-content text-left">
            <div class="flex items-center justify-between pb-3 border-b">
                <div>
                    <h3 class="text-base font-semibold leading-none tracking-tight">{{ __('Update Fuel Log Status') }}</h3>
                    <p class="text-xs text-muted-foreground mt-1" id="fuelStatusModalRef"></p>
                </div>
                <button type="button" onclick="window.shadcnModal.close('fuelStatusModal')" class="text-muted-foreground hover:text-foreground">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form id="fuelStatusModalForm" method="POST" action="" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-medium">{{ __('Record Status') }}</label>
                    <select id="fuelModalStatusSelect" name="status" onchange="toggleFuelFlaggedReasonField(this.value)" class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring">
                        <option value="pending">🟡 {{ __('Pending Review') }}</option>
                        <option value="approved">🟢 {{ __('Approved & Reconciled') }}</option>
                        <option value="flagged">🔴 {{ __('Flagged / Disputed') }}</option>
                    </select>
                </div>
                <div id="fuelFlaggedReasonGroup" class="hidden">
                    <label class="text-xs font-medium text-destructive">{{ __('Dispute / Flag Reason') }}</label>
                    <textarea id="fuelModalFlaggedReason" name="flagged_reason" rows="2" placeholder="{{ __('State reason for flagging fuel receipt or volume...') }}"
                              class="flex w-full rounded-md border border-destructive/40 bg-background px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-destructive"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t">
                    <button type="button" onclick="window.shadcnModal.close('fuelStatusModal')" class="rounded-md border px-3 py-1.5 text-xs font-medium hover:bg-muted">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="rounded-md bg-primary text-primary-foreground px-4 py-1.5 text-xs font-semibold hover:bg-primary/90 shadow-xs">
                        {{ __('Save Status') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= shadcn Dialog: ADD FUEL ENTRY MODAL ================= -->
    <div id="addFuelModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-xs hidden">
        <div class="relative w-full max-w-lg rounded-xl border bg-background p-6 shadow-lg modal-content text-left max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b">
                <div>
                    <h3 class="text-base font-semibold leading-none tracking-tight">{{ __('Log Fuel Dispensation') }}</h3>
                    <p class="text-xs text-muted-foreground mt-1">{{ __('Record a new fuel fill-up with dispenser liters, price, and receipt.') }}</p>
                </div>
                <button type="button" onclick="window.shadcnModal.close('addFuelModal')" class="text-muted-foreground hover:text-foreground">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form id="addFuelModalForm" method="POST" action="{{ route('fuel.store') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-medium">{{ __('Vehicle / Bus') }} *</label>
                        <select name="bus_id" required class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring">
                            <option value="">{{ __('Select bus...') }}</option>
                            @foreach($buses as $bus)
                                <option value="{{ $bus->id }}">{{ $bus->display_id }}{{ $bus->plate ? ' · ' . $bus->plate : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium">{{ __('Fuel Type') }} *</label>
                        <select name="type" required class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring">
                            <option value="diesel" selected>{{ __('Diesel') }}</option>
                            <option value="petrol">{{ __('Petrol / Regular') }}</option>
                            <option value="super">{{ __('Super / Extra') }}</option>
                            <option value="cng">{{ __('CNG') }}</option>
                            <option value="lpg">{{ __('LPG') }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="text-xs font-medium">{{ __('Liters (Volume)') }} *</label>
                        <input type="number" step="0.01" min="0.01" name="liters" id="modalAddLiters" required placeholder="e.g. 50.0" oninput="recalcFromLitersOrUnit()"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring font-mono"/>
                    </div>
                    <div>
                        <label class="text-xs font-medium">{{ __('Unit Price ($/L)') }}</label>
                        <input type="number" step="0.001" min="0" name="unit_price" id="modalAddUnitPrice" placeholder="e.g. 1.25" oninput="recalcFromLitersOrUnit()"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring font-mono"/>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-emerald-600 font-semibold">{{ __('Total Cost ($)') }} *</label>
                        <input type="number" step="0.01" min="0.01" name="price" id="modalAddTotalPrice" required placeholder="e.g. 62.50" oninput="recalcFromTotalCost()"
                               class="flex h-9 w-full rounded-md border border-emerald-500/50 bg-background px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 font-mono font-bold text-emerald-600"/>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-medium">{{ __('Station Name') }}</label>
                        <input type="text" name="station" placeholder="e.g. Central Depot, Shell #104"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring"/>
                    </div>
                    <div>
                        <label class="text-xs font-medium">{{ __('Receipt / Invoice #') }}</label>
                        <input type="text" name="receipt_number" placeholder="e.g. RCP-98214"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring font-mono"/>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-medium">{{ __('Fill-up Date & Time') }}</label>
                        <input type="datetime-local" name="added_at" value="{{ now()->format('Y-m-d\TH:i') }}"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring"/>
                    </div>
                    <div>
                        <label class="text-xs font-medium">{{ __('Dispenser Receipt Photo') }}</label>
                        <input type="file" name="image" accept="image/*"
                               class="flex h-9 w-full rounded-md border border-input bg-background px-2 py-1 text-xs shadow-sm file:border-0 file:bg-transparent file:text-xs file:font-medium"/>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" onclick="window.shadcnModal.close('addFuelModal')" class="rounded-md border px-3 py-1.5 text-xs font-medium hover:bg-muted">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="rounded-md bg-primary text-primary-foreground px-4 py-1.5 text-xs font-semibold hover:bg-primary/90 shadow-xs cursor-pointer">
                        {{ __('Record Fill-up') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddFuelModal() {
            window.shadcnModal.open('addFuelModal');
            if (window.lucide) window.lucide.createIcons();
        }

        function recalcFromLitersOrUnit() {
            const liters = parseFloat(document.getElementById('modalAddLiters')?.value) || 0;
            const unit = parseFloat(document.getElementById('modalAddUnitPrice')?.value) || 0;
            if (liters > 0 && unit > 0) {
                document.getElementById('modalAddTotalPrice').value = (liters * unit).toFixed(2);
            }
        }

        function recalcFromTotalCost() {
            const liters = parseFloat(document.getElementById('modalAddLiters')?.value) || 0;
            const total = parseFloat(document.getElementById('modalAddTotalPrice')?.value) || 0;
            if (liters > 0 && total > 0) {
                document.getElementById('modalAddUnitPrice').value = (total / liters).toFixed(3);
            }
        }

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
