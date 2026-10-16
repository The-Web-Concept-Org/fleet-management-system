<!DOCTYPE html>

<html lang="en"><head><meta charset="utf-8"/><meta content="width=device-width, initial-scale=1.0" name="viewport"/><meta content="web_dashboard" name="shell-type"/><meta name="csrf-token" content="{{ csrf_token() }}"/><link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/><link href="https://fonts.googleapis.com" rel="preconnect"/><link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;family=Sora:wght@400;500;600;700&amp;family=Poppins:wght@600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/><title>Maintenance &amp; Repairs &bull; Lancotri</title><style>@layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}main>:first-child{margin-top:0!important;}main>:last-child{margin-bottom:0!important;}}::-webkit-scrollbar{display:none;}</style><style>@keyframes fuelDialogIn{from{opacity:0;transform:scale(.94) translateY(10px);}to{opacity:1;transform:scale(1) translateY(0);}}.fuel-dialog{animation:fuelDialogIn .22s cubic-bezier(.2,.8,.2,1);}</style><script src="https://cdn.tailwindcss.com"></script><script id="tailwind-config">tailwind.config = { darkMode: "class", theme: { extend: { "colors": { "outline-variant": "#D1D5DB", "on-surface": "#111827", "surface-variant": "#E1E5EC", "inverse-on-surface": "#F1F5F9", "on-primary": "#ffffff", "surface-container-high": "#E1E5EC", "on-secondary": "#ffffff", "secondary": "#0A81B4", "primary-fixed-dim": "#E9A0A2", "on-primary-container": "#ffffff", "background": "#F7F8FA", "error": "#ba1a1a", "border-strong": "#CBD5E1", "tertiary-fixed-dim": "#8FCBE5", "on-tertiary-container": "#E7F4FB", "tertiary-container": "#0A81B4", "tertiary-fixed": "#CFEAF7", "sync-active": "#10B981", "tertiary": "#0A6E99", "surface-container-low": "#F1F3F6", "surface": "#F7F8FA", "on-tertiary-fixed": "#052E42", "surface-tint": "#BD1C23", "on-surface-variant": "#4B5563", "on-secondary-fixed": "#052E42", "on-secondary-container": "#0A5473", "on-primary-fixed-variant": "#8E1418", "surface-card": "#FFFFFF", "on-secondary-fixed-variant": "#0A5C80", "inverse-surface": "#283044", "primary-container": "#BD1C23", "on-primary-fixed": "#3E0A0C", "surface-container-lowest": "#ffffff", "secondary-fixed": "#CFEAF7", "secondary-fixed-dim": "#8FCBE5", "surface-bright": "#F7F8FA", "on-error": "#ffffff", "primary-fixed": "#F7D2D3", "on-tertiary-fixed-variant": "#0A5C80", "surface-dim": "#D2D9E3", "primary": "#A11217", "inverse-primary": "#F2A7A9", "on-error-container": "#93000a", "surface-container": "#E9ECF1", "danger-critical": "#EF4444", "surface-container-highest": "#D8DEE7", "on-tertiary": "#ffffff", "error-container": "#ffdad6", "secondary-container": "#CFEAF7", "sync-pending": "#F59E0B", "on-background": "#111827", "sync-offline": "#64748B", "canvas-base": "#F8FAFC", "outline": "#6B7280" }, "borderRadius": { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" }, "spacing": { "space-xl": "2.25rem", "space-lg": "1.5rem", "space-md": "1rem", "space-sm": "0.5rem", "gutter-tablet": "1.5rem", "gutter": "1rem", "space-xs": "0.25rem", "margin": "1rem", "margin-tablet": "2rem" }, "fontFamily": { "label-action": [ "Plus Jakarta Sans" ], "headline-sm": [ "Sora" ], "headline-md": [ "Sora" ], "headline-lg": [ "Sora" ], "body-sm": [ "Plus Jakarta Sans" ], "body-lg": [ "Plus Jakarta Sans" ], "label-numeric": [ "Sora" ], "display-odometer": [ "Sora" ], "label-caps": [ "Sora" ], "body-md": [ "Plus Jakarta Sans" ] }, "fontSize": { "label-action": [ "18px", { "lineHeight": "24px", "fontWeight": "600" } ], "headline-sm": [ "18px", { "lineHeight": "24px", "fontWeight": "600" } ], "headline-md": [ "22px", { "lineHeight": "28px", "fontWeight": "600" } ], "headline-lg": [ "30px", { "lineHeight": "36px", "letterSpacing": "-0.01em", "fontWeight": "700" } ], "body-sm": [ "13px", { "lineHeight": "18px", "fontWeight": "400" } ], "body-lg": [ "17px", { "lineHeight": "24px", "fontWeight": "500" } ], "label-numeric": [ "20px", { "lineHeight": "24px", "letterSpacing": "0.02em", "fontWeight": "700" } ], "display-odometer": [ "44px", { "lineHeight": "48px", "letterSpacing": "-0.02em", "fontWeight": "700" } ], "label-caps": [ "12px", { "lineHeight": "16px", "letterSpacing": "0.08em", "fontWeight": "700" } ], "body-md": [ "15px", { "lineHeight": "22px", "fontWeight": "400" } ] } } } };</script>@include('partials.brand-styles')
  <link rel="icon" type="image/svg+xml" href="{{ asset('images/lancotri-mark.svg') }}">
</head><body class="bg-surface font-body-md text-body-md text-on-surface antialiased min-h-screen">@include('partials.sidebar')<div class="pl-64 flex flex-col min-h-screen">
@include('partials.topbar', [
    'topbarSearchPlaceholder' => 'Search maintenance ref, bus, driver, or note...',
    'topbarSearchId' => 'maintenanceNavSearch',
    'topbarStatus' => $totalLogs . ' ' . \Illuminate\Support\Str::plural('maintenance record', $totalLogs),
])
<main class="w-full pt-16 flex-1 px-space-lg py-space-lg bg-surface"><div class="flex flex-col w-full gap-space-lg">

<!-- Page Header -->
<div class="flex flex-col xl:flex-row xl:items-center justify-between gap-space-md">
<div class="flex flex-col">
<div class="flex items-center gap-space-xs mb-1">
<span class="font-label-caps text-label-caps uppercase text-tertiary">Fleet Maintenance Audit</span>
<span class="inline-block w-1.5 h-1.5 rounded-full bg-sync-active"></span>
<span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Live workshop feed</span>
</div>
<h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Maintenance Logs</h1>
<p class="font-body-md text-body-md text-on-surface-variant">Every repair and service captured from the driver app: the bill or workshop invoice photo, amount spent, work note and the vehicle, driver and trip it belongs to.</p>
</div>
<div class="flex flex-wrap items-center gap-space-sm">
<div class="relative flex-1 sm:w-72">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg pointer-events-none">search</span>
<input class="w-full pl-9 pr-4 py-2.5 bg-surface-card rounded-lg text-on-surface placeholder:text-outline font-body-sm text-body-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-container" id="maintenanceSearch" placeholder="Filter by ref, bus, driver, or note..." type="text"/>
</div>
<button class="flex items-center gap-2 px-space-md py-2.5 rounded-lg bg-surface-card text-on-surface hover:bg-surface-container-high transition-colors font-body-md text-body-md shadow-sm" id="exportMaintenanceCsvBtn">
<span class="material-symbols-outlined text-lg text-outline">file_download</span>
<span>Export</span>
</button>
</div>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-space-md">
<div class="bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-caps text-label-caps uppercase text-outline">Total Records</span>
<span class="material-symbols-outlined text-primary text-xl">build</span>
</div>
<div class="mt-3 flex items-baseline gap-2">
<span class="font-display-odometer text-display-odometer text-on-surface">{{ $totalLogs }}</span>
<span class="font-body-sm text-body-sm text-outline">Logged</span>
</div>
</div>
<div class="bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-caps text-label-caps uppercase text-on-surface">Total Spend</span>
<span class="material-symbols-outlined text-on-surface text-xl">payments</span>
</div>
<div class="mt-3 flex items-baseline gap-1">
<span class="font-display-odometer text-display-odometer text-on-surface">{{ number_format($totalCost, 2) }}</span>
<span class="font-body-sm text-body-sm text-outline">{{ $maintenanceLogs->first()?->currency ?? 'USD' }}</span>
</div>
</div>
<div class="bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-caps text-label-caps uppercase text-tertiary">Avg per Job</span>
<span class="material-symbols-outlined text-tertiary text-xl">price_change</span>
</div>
<div class="mt-3 flex items-baseline gap-1">
<span class="font-display-odometer text-display-odometer text-on-surface">{{ number_format($averageCost, 2) }}</span>
<span class="font-body-sm text-body-sm text-outline">per record</span>
</div>
</div>
<div class="bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-caps text-label-caps uppercase text-secondary">Bill Photos</span>
<span class="material-symbols-outlined text-secondary text-xl">receipt_long</span>
</div>
<div class="mt-3 flex items-baseline gap-2">
<span class="font-display-odometer text-display-odometer text-secondary">{{ $withBillCount }}</span>
<span class="font-body-sm text-body-sm text-secondary font-medium">Uploaded</span>
</div>
</div>
<div class="col-span-2 md:col-span-1 bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-caps text-label-caps uppercase text-sync-pending">Needs Review</span>
<span class="material-symbols-outlined text-sync-pending text-xl">pending_actions</span>
</div>
<div class="mt-3 flex items-baseline gap-2">
<span class="font-display-odometer text-display-odometer {{ $pendingCount > 0 ? 'text-sync-pending' : 'text-on-surface' }}">{{ $pendingCount }}</span>
<span class="font-body-sm text-body-sm text-outline">{{ $flaggedCount }} flagged</span>
</div>
</div>
</div>

<!-- Filter & Control Toolbar -->
<div class="bg-surface-card rounded-xl p-space-md shadow-sm flex flex-col gap-space-md">
<form action="{{ route('maintenance') }}" class="flex flex-wrap items-center gap-space-sm" method="GET">
<!-- Bus Filter Dropdown -->
<div class="relative flex items-center">
<select class="appearance-none bg-surface-container-low font-body-md text-body-md text-on-surface pl-9 pr-9 py-2.5 rounded-lg focus:outline-none focus:bg-surface-container transition-colors cursor-pointer shadow-sm" name="bus_id" onchange="this.form.submit()">
<option value="">All Buses ({{ $buses->count() }} total)</option>
@foreach($buses as $bus)
<option value="{{ $bus->id }}" @selected((string) $busId === (string) $bus->id)>{{ $bus->display_id }}{{ $bus->plate ? ' · ' . $bus->plate : '' }}</option>
@endforeach
</select>
<span class="material-symbols-outlined absolute left-3 text-outline text-lg pointer-events-none">directions_bus</span>
<span class="material-symbols-outlined absolute right-2.5 text-outline text-sm pointer-events-none">expand_more</span>
</div>
<!-- Driver Filter Dropdown -->
<div class="relative flex items-center">
<select class="appearance-none bg-surface-container-low font-body-md text-body-md text-on-surface pl-9 pr-9 py-2.5 rounded-lg focus:outline-none focus:bg-surface-container transition-colors cursor-pointer shadow-sm" name="driver_id" onchange="this.form.submit()">
<option value="">All Drivers ({{ $drivers->count() }})</option>
@foreach($drivers as $driver)
<option value="{{ $driver->id }}" @selected((string) $driverId === (string) $driver->id)>{{ $driver->user->name ?? 'Driver' }}@if($driver->badge_id) ({{ $driver->display_id }})@endif</option>
@endforeach
</select>
<span class="material-symbols-outlined absolute left-3 text-outline text-lg pointer-events-none">badge</span>
<span class="material-symbols-outlined absolute right-2.5 text-outline text-sm pointer-events-none">expand_more</span>
</div>
<!-- Date Range -->
<div class="flex items-center gap-space-xs px-space-md py-2.5 bg-surface-container-low rounded-lg shadow-sm">
<span class="material-symbols-outlined text-outline text-lg">calendar_month</span>
<input class="bg-transparent font-body-sm text-body-sm text-on-surface focus:outline-none" name="from" onchange="this.form.submit()" type="date" value="{{ $from }}"/>
<span class="font-body-sm text-body-sm text-outline">to</span>
<input class="bg-transparent font-body-sm text-body-sm text-on-surface focus:outline-none" name="to" onchange="this.form.submit()" type="date" value="{{ $to }}"/>
</div>
<button class="flex items-center gap-1 px-space-md py-2.5 rounded-lg bg-primary-container text-on-primary font-body-md text-body-md font-semibold hover:opacity-95 shadow-sm transition-opacity" type="submit">
<span class="material-symbols-outlined text-lg">filter_alt</span>
<span>Apply</span>
</button>
<a class="px-space-sm py-2 text-outline hover:text-on-surface text-body-sm font-body-sm flex items-center gap-1 transition-colors" href="{{ route('maintenance') }}">
<span class="material-symbols-outlined text-base">restart_alt</span>
<span>Reset</span>
</a>
</form>
</div>

<!-- Maintenance Logs Table -->
<div class="bg-surface-card rounded-xl shadow-sm overflow-hidden">
<div class="px-space-lg py-space-md bg-surface-container-lowest flex items-center justify-between">
<div class="flex items-center gap-space-sm">
<span class="font-headline-sm text-headline-sm text-on-surface">Maintenance Records</span>
<span class="text-outline text-sm">&bull;</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">{{ $withBillCount }} with a bill / invoice photo</span>
</div>
<span class="font-label-caps text-label-caps text-secondary-container bg-secondary px-2.5 py-1 rounded-md">LIVE TELEMETRY SYNC</span>
</div>
<div class="overflow-x-auto w-full">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low text-on-surface-variant font-label-caps text-label-caps uppercase tracking-wider">
<th class="py-3.5 px-space-md">Ref</th>
<th class="py-3.5 px-space-md">Vehicle</th>
<th class="py-3.5 px-space-md">Driver</th>
<th class="py-3.5 px-space-md">Service</th>
<th class="py-3.5 px-space-md">Cost</th>
<th class="py-3.5 px-space-md">Note</th>
<th class="py-3.5 px-space-md">Performed At</th>
<th class="py-3.5 px-space-md">Status</th>
<th class="py-3.5 px-space-md text-right">Bill</th>
</tr>
</thead>
<tbody class="divide-y-0">
@forelse($maintenanceLogs as $log)
@php
    $rowBus = $log->bus;
    $rowDriver = $log->driver;
    $rowTrip = $log->trip;
    $performed = $log->performed_at;
    $price = $log->price !== null ? (float) $log->price : null;
    $imageUrl = \App\Support\StorageUrl::for($log->image);
    $currency = $log->currency ?: 'USD';

    $driverFullName = $rowDriver?->user?->name;

    $statusValue = strtolower((string) ($log->status ?: 'pending'));
    $statusMap = [
        'approved' => ['label' => 'Approved', 'class' => 'bg-secondary-fixed/40 text-secondary', 'icon' => 'check_circle'],
        'verified' => ['label' => 'Verified', 'class' => 'bg-secondary-fixed/40 text-secondary', 'icon' => 'verified'],
        'reconciled' => ['label' => 'Reconciled', 'class' => 'bg-secondary-fixed/40 text-secondary', 'icon' => 'task_alt'],
        'pending' => ['label' => 'Pending', 'class' => 'bg-sync-pending/15 text-sync-pending', 'icon' => 'schedule'],
        'flagged' => ['label' => 'Flagged', 'class' => 'bg-error-container text-danger-critical', 'icon' => 'warning'],
        'rejected' => ['label' => 'Rejected', 'class' => 'bg-error-container text-danger-critical', 'icon' => 'cancel'],
    ];
    $status = $statusMap[$statusValue] ?? ['label' => \Illuminate\Support\Str::headline($statusValue), 'class' => 'bg-surface-container-high text-on-surface-variant', 'icon' => 'info'];
    if ($log->flagged_reason !== null && $statusValue === 'pending') {
        $status = $statusMap['flagged'];
    }

    $searchHaystack = strtolower(implode(' ', array_filter([
        $log->reference, $rowBus?->bus_number, $rowBus?->plate, $rowBus?->model,
        $driverFullName, $rowDriver?->badge_id, $log->service_type, $log->note,
        $rowTrip?->trip_number, $statusValue,
    ])));
@endphp
<tr class="maintenance-row hover:bg-surface-container-low transition-colors"
    data-search="{{ $searchHaystack }}"
    data-ref="{{ $log->reference }}"
    data-bus="{{ $rowBus ? $rowBus->display_id : 'Unassigned' }}"
    data-driver="{{ $driverFullName ?? 'Unassigned driver' }}"
    data-service="{{ \Illuminate\Support\Str::headline($log->service_type ?: 'General') }}"
    data-cost="{{ $price !== null ? number_format($price, 2, '.', '') : '' }}"
    data-currency="{{ $currency }}"
    data-note="{{ $log->note }}"
    data-date="{{ optional($performed)->format('M d, Y') }}"
    data-time="{{ optional($performed)->format('h:i A') }}"
    data-status="{{ $status['label'] }}">
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col">
<span class="font-label-numeric text-body-lg font-bold text-on-surface">{{ $log->reference ?: 'MNT-' . str_pad((string) $log->id, 4, '0', STR_PAD_LEFT) }}</span>
<span class="font-body-sm text-body-sm text-outline">{{ optional($log->created_at)->format('M d, Y') }}</span>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col">
<span class="font-headline-sm text-headline-sm text-on-surface font-bold">{{ $rowBus ? $rowBus->display_id : 'Unassigned' }}</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">{{ $rowBus?->plate ?? ($rowBus?->model ?? '—') }}</span>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex items-center gap-space-xs">
<div class="flex flex-col">
<span class="font-body-md text-body-md font-semibold text-on-surface">{{ $driverFullName ?? 'Unassigned driver' }}</span>
<span class="font-body-sm text-body-sm text-outline">{{ $rowDriver->display_id ?? 'Driver #—' }}</span>
</div>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-surface-container-low text-on-surface-variant font-body-sm text-body-sm font-medium">
<span class="material-symbols-outlined text-sm">build</span>
{{ \Illuminate\Support\Str::headline($log->service_type ?: 'General') }}
</span>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col">
<span class="font-label-numeric text-headline-sm font-bold text-secondary">{{ $price !== null ? $currency . ' ' . number_format($price, 2) : '—' }}</span>
@if($log->odometer !== null)
<span class="font-body-sm text-body-sm text-outline">{{ number_format($log->odometer) }} km on clock</span>
@endif
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col max-w-[16rem]">
<span class="font-body-md text-body-md text-on-surface truncate" title="{{ $log->note }}">{{ $log->note ?: '—' }}</span>
@if($rowTrip)
<span class="font-body-sm text-body-sm text-outline truncate">{{ $rowTrip->display_id }}</span>
@else
<span class="font-body-sm text-body-sm text-outline">No trip linked</span>
@endif
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col">
<span class="font-body-md text-body-md text-on-surface">{{ optional($performed)->format('M d, Y') ?? '—' }}</span>
<span class="font-body-sm text-body-sm text-outline">{{ optional($performed)->format('h:i A') ?? 'No timestamp' }}</span>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-body-sm text-body-sm {{ $status['class'] }}">
<span class="material-symbols-outlined text-xs">{{ $status['icon'] }}</span>
<span>{{ $status['label'] }}</span>
</span>
</td>
<td class="py-4 px-space-md align-middle text-right">
@if($imageUrl)
<button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-container text-on-primary font-body-sm text-body-sm font-semibold hover:opacity-90 shadow-sm transition-opacity" onclick="openMaintenanceBill(this)" type="button"
    data-image="{{ $imageUrl }}"
    data-ref="{{ $log->reference }}"
    data-meta="{{ ($rowBus ? $rowBus->display_id : 'Unassigned') . ' • ' . \Illuminate\Support\Str::headline($log->service_type ?: 'General') . ' • ' . ($price !== null ? $currency . ' ' . number_format($price, 2) : '—') }}">
<span class="material-symbols-outlined text-base">receipt_long</span>
<span>View</span>
</button>
@else
<span class="inline-flex items-center gap-1 font-body-sm text-body-sm text-outline">
<span class="material-symbols-outlined text-sm">image_not_supported</span>
No bill
</span>
@endif
</td>
</tr>
@empty
<tr>
<td class="py-12 text-center text-outline" colspan="9">
<div class="flex flex-col items-center justify-center gap-2">
<span class="material-symbols-outlined text-4xl">build</span>
<span class="font-headline-sm text-headline-sm">No maintenance logs found</span>
<p class="font-body-sm text-body-sm max-w-sm">Repairs and services logged from the driver app will appear here. Try clearing the filters above.</p>
</div>
</td>
</tr>
@endforelse
</tbody>
</table>
</div>
<div class="px-space-md py-space-sm bg-surface-container-low flex flex-col sm:flex-row items-center justify-between gap-space-sm">
<span class="font-body-sm text-body-sm text-outline" id="maintenanceShowingCount">
Showing <span class="font-semibold text-on-surface">{{ $totalLogs }}</span> of <span class="font-semibold text-on-surface">{{ $totalLogs }}</span> maintenance records
</span>
<span class="font-body-sm text-body-sm text-outline">Totals update with the active filters.</span>
</div>
</div>

</div></main></div>

<!-- ================= BILL VIEWER MODAL ================= -->
<div class="fixed inset-0 z-[60] bg-inverse-surface/60 backdrop-blur-md flex items-center justify-center p-space-md hidden" id="maintenanceBillModal">
<div class="bg-surface-card w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[92vh] fuel-dialog">
<div class="px-space-lg py-space-md bg-surface-container-low flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-primary-container text-on-primary flex items-center justify-center">
<span class="material-symbols-outlined">receipt_long</span>
</div>
<div>
<h2 class="font-headline-sm text-headline-sm text-on-surface" id="maintenanceBillRef">Maintenance Bill</h2>
<p class="font-body-sm text-body-sm text-outline" id="maintenanceBillMeta"></p>
</div>
</div>
<button class="text-outline hover:text-on-surface transition-colors" onclick="closeMaintenanceBill()" type="button">
<span class="material-symbols-outlined text-xl">close</span>
</button>
</div>
<div class="p-space-lg bg-surface-container-lowest flex items-center justify-center overflow-y-auto">
<img alt="Maintenance bill / workshop invoice" class="max-h-[70vh] w-auto rounded-xl shadow-sm" id="maintenanceBillImage" src=""/>
</div>
</div>
</div>

<script>
    const maintenanceModal = document.getElementById('maintenanceBillModal');

    function openMaintenanceBill(button) {
      const image = button.dataset.image;
      if (!image || !maintenanceModal) return;
      document.getElementById('maintenanceBillImage').src = image;
      document.getElementById('maintenanceBillRef').textContent = button.dataset.ref || 'Maintenance Bill';
      document.getElementById('maintenanceBillMeta').textContent = button.dataset.meta || '';
      maintenanceModal.classList.remove('hidden');
    }

    function closeMaintenanceBill() {
      if (!maintenanceModal) return;
      maintenanceModal.classList.add('hidden');
      document.getElementById('maintenanceBillImage').src = '';
    }

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') closeMaintenanceBill();
    });

    // Client-side text search across the loaded rows.
    const maintenanceSearch = document.getElementById('maintenanceSearch');
    function applyMaintenanceFilters() {
      const query = (maintenanceSearch?.value || '').toLowerCase().trim();
      let visible = 0;
      document.querySelectorAll('.maintenance-row').forEach(row => {
        const show = !query || (row.dataset.search || '').includes(query);
        row.style.display = show ? '' : 'none';
        if (show) visible++;
      });
      const countEl = document.getElementById('maintenanceShowingCount');
      if (countEl) countEl.querySelector('span').textContent = visible;
    }
    maintenanceSearch?.addEventListener('input', applyMaintenanceFilters);

    // CSV export of the rows currently on screen.
    document.getElementById('exportMaintenanceCsvBtn')?.addEventListener('click', () => {
      let csv = 'Ref,Vehicle,Driver,Service,Cost,Currency,Note,Performed Date,Performed Time,Status\n';
      document.querySelectorAll('.maintenance-row').forEach(r => {
        if (r.style.display === 'none') return;
        const note = (r.dataset.note || '').replace(/"/g, '""');
        csv += `"${r.dataset.ref}","${r.dataset.bus}","${r.dataset.driver}","${r.dataset.service}","${r.dataset.cost}","${r.dataset.currency}","${note}","${r.dataset.date}","${r.dataset.time}","${r.dataset.status}"\n`;
      });
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.setAttribute('href', url);
      link.setAttribute('download', `maintenance_logs_${new Date().toISOString().slice(0, 10)}.csv`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    });
</script>
</body></html>
