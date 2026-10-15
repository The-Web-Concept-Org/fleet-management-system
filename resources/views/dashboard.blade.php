<!DOCTYPE html>

<html lang="en"><head><meta charset="utf-8"/><meta content="width=device-width, initial-scale=1.0" name="viewport"/><meta content="web_dashboard" name="shell-type"/><link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/><link href="https://fonts.googleapis.com" rel="preconnect"/><link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;family=Sora:wght@400;500;600;700&amp;family=Poppins:wght@600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/><title>Operations Dashboard &bull; Lancotri</title><style>@layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}main>:first-child{margin-top:0!important;}main>:last-child{margin-bottom:0!important;}}::-webkit-scrollbar{display:none;}</style><script src="https://cdn.tailwindcss.com"></script><script id="tailwind-config">tailwind.config = { darkMode: "class", theme: { extend: { "colors": { "outline-variant": "#D1D5DB", "on-surface": "#111827", "surface-variant": "#E1E5EC", "inverse-on-surface": "#F1F5F9", "on-primary": "#ffffff", "surface-container-high": "#E1E5EC", "on-secondary": "#ffffff", "secondary": "#0A81B4", "primary-fixed-dim": "#E9A0A2", "on-primary-container": "#ffffff", "background": "#F7F8FA", "error": "#ba1a1a", "border-strong": "#CBD5E1", "tertiary-fixed-dim": "#8FCBE5", "on-tertiary-container": "#E7F4FB", "tertiary-container": "#0A81B4", "tertiary-fixed": "#CFEAF7", "sync-active": "#10B981", "tertiary": "#0A6E99", "surface-container-low": "#F1F3F6", "surface": "#F7F8FA", "on-tertiary-fixed": "#052E42", "surface-tint": "#BD1C23", "on-surface-variant": "#4B5563", "on-secondary-fixed": "#052E42", "on-secondary-container": "#0A5473", "on-primary-fixed-variant": "#8E1418", "surface-card": "#FFFFFF", "on-secondary-fixed-variant": "#0A5C80", "inverse-surface": "#283044", "primary-container": "#BD1C23", "on-primary-fixed": "#3E0A0C", "surface-container-lowest": "#ffffff", "secondary-fixed": "#CFEAF7", "secondary-fixed-dim": "#8FCBE5", "surface-bright": "#F7F8FA", "on-error": "#ffffff", "primary-fixed": "#F7D2D3", "on-tertiary-fixed-variant": "#0A5C80", "surface-dim": "#D2D9E3", "primary": "#A11217", "inverse-primary": "#F2A7A9", "on-error-container": "#93000a", "surface-container": "#E9ECF1", "danger-critical": "#EF4444", "surface-container-highest": "#D8DEE7", "on-tertiary": "#ffffff", "error-container": "#ffdad6", "secondary-container": "#CFEAF7", "sync-pending": "#F59E0B", "on-background": "#111827", "sync-offline": "#64748B", "canvas-base": "#F8FAFC", "outline": "#6B7280" }, "borderRadius": { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" }, "spacing": { "space-xl": "2.25rem", "space-lg": "1.5rem", "space-md": "1rem", "space-sm": "0.5rem", "gutter-tablet": "1.5rem", "gutter": "1rem", "space-xs": "0.25rem", "margin": "1rem", "margin-tablet": "2rem" }, "fontFamily": { "label-action": [ "Plus Jakarta Sans" ], "headline-sm": [ "Sora" ], "headline-md": [ "Sora" ], "headline-lg": [ "Sora" ], "body-sm": [ "Plus Jakarta Sans" ], "body-lg": [ "Plus Jakarta Sans" ], "label-numeric": [ "Sora" ], "display-odometer": [ "Sora" ], "label-caps": [ "Sora" ], "body-md": [ "Plus Jakarta Sans" ] }, "fontSize": { "label-action": [ "18px", { "lineHeight": "24px", "fontWeight": "600" } ], "headline-sm": [ "18px", { "lineHeight": "24px", "fontWeight": "600" } ], "headline-md": [ "22px", { "lineHeight": "28px", "fontWeight": "600" } ], "headline-lg": [ "30px", { "lineHeight": "36px", "letterSpacing": "-0.01em", "fontWeight": "700" } ], "body-sm": [ "13px", { "lineHeight": "18px", "fontWeight": "400" } ], "body-lg": [ "17px", { "lineHeight": "24px", "fontWeight": "500" } ], "label-numeric": [ "20px", { "lineHeight": "24px", "letterSpacing": "0.02em", "fontWeight": "700" } ], "display-odometer": [ "44px", { "lineHeight": "48px", "letterSpacing": "-0.02em", "fontWeight": "700" } ], "label-caps": [ "12px", { "lineHeight": "16px", "letterSpacing": "0.08em", "fontWeight": "700" } ], "body-md": [ "15px", { "lineHeight": "22px", "fontWeight": "400" } ] } } } };</script>@include('partials.brand-styles')
  <link rel="icon" type="image/svg+xml" href="{{ asset('images/lancotri-mark.svg') }}">
</head><body class="bg-surface font-body-md text-body-md text-on-surface antialiased min-h-screen"><aside class="fixed left-0 top-0 h-full w-64 bg-surface-card z-50 flex flex-col justify-between shadow-[0_1px_8px_rgba(0,0,0,0.04)]"><div class="flex flex-col flex-1"><div class="h-16 px-space-md flex items-center gap-space-sm">@include('partials.brand-logo')</div><div class="px-space-md py-space-sm"><span class="font-label-caps text-label-caps text-outline uppercase tracking-wider">Transit Operations</span></div><nav class="flex-1 px-space-sm flex flex-col gap-1" data-active-classes="bg-primary-container text-on-primary font-bold shadow-sm"><a aria-current="page" class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg transition-colors bg-primary-container text-on-primary font-bold shadow-sm" data-path="dashboard" href="{{ route('dashboard') }}"><span class="material-symbols-outlined text-lg">space_dashboard</span><span>Dashboard</span></a><a class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg text-on-surface-variant font-body-md text-body-md hover:bg-surface-container-high hover:text-on-surface transition-colors" data-path="buses" href="{{ route('buses') }}"><span class="material-symbols-outlined text-lg">directions_bus</span><span>Buses</span></a><a class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg text-on-surface-variant font-body-md text-body-md hover:bg-surface-container-high hover:text-on-surface transition-colors" data-path="shifts" href="{{ route('shifts') }}"><span class="material-symbols-outlined text-lg">schedule</span><span>Shifts</span></a><a class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg text-on-surface-variant font-body-md text-body-md hover:bg-surface-container-high hover:text-on-surface transition-colors" data-path="fuel" href="#"><span class="material-symbols-outlined text-lg">local_gas_station</span><span>Fuel</span></a><a class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg text-on-surface-variant font-body-md text-body-md hover:bg-surface-container-high hover:text-on-surface transition-colors" data-path="maintenance" href="#"><span class="material-symbols-outlined text-lg">build</span><span>Maintenance</span></a><a class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg text-on-surface-variant font-body-md text-body-md hover:bg-surface-container-high hover:text-on-surface transition-colors" data-path="drivers" href="{{ route('drivers') }}"><span class="material-symbols-outlined text-lg">badge</span><span>Drivers</span></a><a class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg text-on-surface-variant font-body-md text-body-md hover:bg-surface-container-high hover:text-on-surface transition-colors" data-path="reports" href="#"><span class="material-symbols-outlined text-lg">bar_chart</span><span>Reports</span></a></nav></div><div class="p-space-md bg-surface-container-low m-space-sm rounded-xl flex items-center justify-between"><div class="flex items-center gap-space-xs"><span class="w-2.5 h-2.5 rounded-full bg-sync-active animate-pulse"></span><span class="font-body-sm text-body-sm text-on-surface">GPS Feed Synced</span></div><span class="material-symbols-outlined text-outline text-lg">wifi</span></div></aside><div class="pl-64 flex flex-col min-h-screen">@include('partials.topbar', ['topbarStatus' => $activeBuses . ' active vehicles'])<main class="w-full pt-16 flex-1 px-space-lg py-space-lg bg-surface"><div class="flex flex-col w-full gap-space-lg">
<div class="flex flex-col xl:flex-row xl:items-center justify-between gap-space-md bg-surface-card p-space-md lg:p-space-lg rounded-xl shadow-sm">
<div class="flex flex-col gap-1">
<div class="flex items-center gap-space-xs text-outline">
<span class="material-symbols-outlined text-sm">hub</span>
<span class="font-label-caps text-label-caps uppercase tracking-wider">Central Depot Fleet Command</span>
<span class="text-xs">&bull;</span>
<span class="font-body-sm text-body-sm text-sync-active font-medium">Real-Time Telemetry Feed</span>
</div>
<h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Operations Dashboard</h1>
</div>
<div class="flex flex-wrap items-center gap-space-sm sm:gap-space-md">
<div class="flex items-center gap-space-xs bg-surface-container-low px-space-md py-2 rounded-lg shadow-sm">
<span class="material-symbols-outlined text-outline text-lg">calendar_today</span>
<span class="font-body-md text-body-md font-semibold text-on-surface">Today, {{ now()->format('M j, Y') }}</span>
<button class="text-outline hover:text-on-surface flex items-center">
<span class="material-symbols-outlined text-base">expand_more</span>
</button>
</div>
<div class="hidden md:flex items-center gap-space-xs px-space-md py-2 rounded-lg bg-surface-container-high text-on-surface">
<span class="inline-block w-2.5 h-2.5 rounded-full bg-sync-active animate-ping"></span>
<span class="font-body-sm text-body-sm font-semibold">99.4% Uplink Operational</span>
</div>
<button class="flex items-center gap-space-xs px-space-md py-2.5 rounded-lg bg-surface-container-high text-on-surface hover:bg-surface-container-highest transition-colors font-body-md text-body-md font-semibold shadow-sm" id="exportDailyBtn">
<span class="material-symbols-outlined text-lg">download</span>
<span>Export Daily Log</span>
</button>
<button class="flex items-center gap-space-xs px-space-lg py-2.5 rounded-lg bg-primary-container text-on-primary hover:bg-primary transition-all font-body-md text-body-md font-bold shadow-md hover:shadow-lg active:scale-95" id="newShiftBtn">
<span class="material-symbols-outlined text-lg">add_circle</span>
<span>+ New Shift</span>
</button>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md">
<div class="bg-surface-card p-space-md lg:p-space-lg rounded-xl shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden">
<div class="absolute -right-4 -top-4 w-24 h-24 rounded-full bg-surface-container-low pointer-events-none"></div>
<div class="flex items-start justify-between">
<div class="flex flex-col">
<span class="font-label-caps text-label-caps text-outline uppercase tracking-wider">Fleet Utilization</span>
<span class="font-headline-sm text-headline-sm text-on-surface mt-1">Buses on Shift Today</span>
</div>
<div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center text-primary">
<span class="material-symbols-outlined">directions_bus</span>
</div>
</div>
<div class="mt-4 flex items-baseline gap-space-xs">
<span class="font-display-odometer text-display-odometer text-on-surface">{{ $activeBuses }}</span>
<span class="font-headline-md text-headline-md text-outline">/ {{ $totalBuses }} total</span>
</div>
<div class="mt-3 flex items-center justify-between pt-space-xs bg-surface-container-low p-space-sm rounded-lg">
<div class="flex items-center gap-1 text-secondary font-semibold font-body-sm text-body-sm">
<span class="material-symbols-outlined text-sm">garage</span>
<span>{{ $standbyBuses }} ready / standby</span>
</div>
<span class="font-label-caps text-label-caps font-bold px-2 py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed">{{ $utilizationPct }}% Cap</span>
</div>
</div>
<div class="bg-surface-card p-space-md lg:p-space-lg rounded-xl shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden">
<div class="absolute -right-4 -top-4 w-24 h-24 rounded-full bg-surface-container-low pointer-events-none"></div>
<div class="flex items-start justify-between">
<div class="flex flex-col">
<span class="font-label-caps text-label-caps text-outline uppercase tracking-wider">Distance Aggregation</span>
<span class="font-headline-sm text-headline-sm text-on-surface mt-1">Total Fleet Distance</span>
</div>
<div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center text-primary-container">
<span class="material-symbols-outlined">route</span>
</div>
</div>
<div class="mt-4 flex items-baseline gap-space-xs">
<span class="font-display-odometer text-display-odometer text-on-surface">{{ number_format($totalOdometer) }}</span>
<span class="font-label-numeric text-label-numeric text-outline">km</span>
</div>
<div class="mt-3 flex items-center justify-between bg-surface-container-low p-space-sm rounded-lg">
<span class="font-body-sm text-body-sm text-on-surface-variant">Average per Vehicle</span>
<span class="font-label-numeric text-label-numeric text-on-surface">{{ number_format($avgOdometerPerBus) }} km/bus</span>
</div>
</div>
<div class="bg-surface-card p-space-md lg:p-space-lg rounded-xl shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden">
<div class="absolute -right-4 -top-4 w-24 h-24 rounded-full bg-surface-container-low pointer-events-none"></div>
<div class="flex items-start justify-between">
<div class="flex flex-col">
<span class="font-label-caps text-label-caps text-outline uppercase tracking-wider">Operating Expenses</span>
<span class="font-headline-sm text-headline-sm text-on-surface mt-1">Fuel Spend (This Month)</span>
</div>
<div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center text-sync-pending">
<span class="material-symbols-outlined">local_gas_station</span>
</div>
</div>
<div class="mt-4 flex items-baseline gap-space-xs">
<span class="font-display-odometer text-display-odometer text-on-surface">${{ number_format($totalFuelCost, 0) }}</span>
<span class="font-body-sm text-body-sm text-outline">USD</span>
</div>
<div class="mt-3 flex items-center justify-between bg-surface-container-low p-space-sm rounded-lg">
<span class="font-body-sm text-body-sm text-on-surface-variant">{{ number_format($totalFuelLiters) }} L Diesel</span>
<span class="font-label-numeric text-label-numeric text-on-surface">${{ number_format($avgFuelPrice, 2) }}/L avg</span>
</div>
</div>
<div class="bg-surface-card p-space-md lg:p-space-lg rounded-xl shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden">
<div class="absolute -right-4 -top-4 w-24 h-24 rounded-full bg-surface-container-low pointer-events-none"></div>
<div class="flex items-start justify-between">
<div class="flex flex-col">
<span class="font-label-caps text-label-caps text-outline uppercase tracking-wider">Depot Servicing</span>
<span class="font-headline-sm text-headline-sm text-on-surface mt-1">Maintenance Spend</span>
</div>
<div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center text-tertiary-container">
<span class="material-symbols-outlined">build</span>
</div>
</div>
<div class="mt-4 flex items-baseline gap-space-xs">
<span class="font-display-odometer text-display-odometer text-on-surface">$0.00</span>
<span class="font-body-sm text-body-sm text-outline">USD</span>
</div>
<div class="mt-3 flex items-center justify-between bg-surface-container-low p-space-sm rounded-lg">
<span class="font-body-sm text-body-sm text-on-surface-variant">0 completed</span>
<span class="font-body-sm text-body-sm text-sync-pending font-semibold">0 pending review</span>
</div>
</div>
</div>
<div class="flex flex-col gap-space-sm">
<div class="flex items-center justify-between px-space-xs">
<div class="flex items-center gap-space-xs">
<span class="material-symbols-outlined text-danger-critical text-xl animate-pulse">crisis_alert</span>
<span class="font-headline-sm text-headline-sm text-on-surface">Critical Fleet Alerts</span>
</div>
<span class="font-label-caps text-label-caps text-outline uppercase">{{ $alertCount }} {{ \Illuminate\Support\Str::plural('item', $alertCount) }} require dispatcher action</span>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-space-md">
@if($primaryAlert)
<div class="lg:col-span-2 bg-error-container/40 p-space-md lg:p-space-lg rounded-xl shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-space-md">
<div class="flex items-start gap-space-md">
<div class="w-12 h-12 rounded-xl bg-danger-critical text-on-primary flex items-center justify-center shrink-0 shadow-sm">
<span class="material-symbols-outlined text-2xl">{{ $primaryAlert->status === 'out_of_service' ? 'error_med' : 'speed' }}</span>
</div>
<div class="flex flex-col">
<div class="flex items-center gap-space-xs flex-wrap">
<span class="px-2 py-0.5 rounded-full bg-danger-critical text-on-primary font-label-caps text-label-caps uppercase">{{ $primaryAlert->status === 'out_of_service' ? 'Out of Service' : 'Service Overdue' }}</span>
<span class="font-label-numeric text-label-numeric text-on-surface">Bus #{{ $primaryAlert->bus_number }}</span>
<span class="text-outline text-xs">&bull;</span>
<span class="font-body-sm text-body-sm text-outline">{{ $primaryAlert->plate }}</span>
</div>
@if($primaryAlert->status === 'out_of_service')
<p class="font-body-md text-body-md text-on-surface font-semibold mt-1">Vehicle flagged out of service</p>
<p class="font-body-sm text-body-sm text-on-surface-variant">Bus #{{ $primaryAlert->bus_number }} requires dispatcher review before it can return to the roster.</p>
@else
<p class="font-body-md text-body-md text-on-surface font-semibold mt-1">Service interval exceeded by {{ number_format(abs($primaryAlert->next_service_km)) }} km</p>
<p class="font-body-sm text-body-sm text-on-surface-variant">Current odometer reading is {{ number_format($primaryAlert->odometer) }} km.</p>
@endif
</div>
</div>
<div class="flex items-center sm:flex-col gap-space-xs shrink-0 self-end sm:self-center">
<a class="w-full sm:w-36 flex items-center justify-center gap-1 px-space-md py-2 rounded-lg bg-surface-card text-on-surface hover:bg-surface-container-high transition-colors font-body-sm text-body-sm font-semibold shadow-sm" href="{{ route('buses') }}">
<span class="material-symbols-outlined text-sm">visibility</span>
<span>View Bus</span>
</a>
<button class="w-full sm:w-36 flex items-center justify-center gap-1 px-space-md py-2 rounded-lg bg-danger-critical text-on-primary hover:bg-error transition-colors font-body-sm text-body-sm font-semibold shadow-sm" onclick="dismissAlert(this)">
<span class="material-symbols-outlined text-sm">check</span>
<span>Acknowledge</span>
</button>
</div>
</div>
@else
<div class="lg:col-span-2 bg-secondary-container/40 p-space-md lg:p-space-lg rounded-xl shadow-sm flex items-center gap-space-md">
<span class="material-symbols-outlined text-sync-active text-3xl">check_circle</span>
<div class="flex flex-col">
<p class="font-body-md text-body-md text-on-surface font-semibold">No critical fleet alerts</p>
<p class="font-body-sm text-body-sm text-on-surface-variant">Every registered bus is within its service window.</p>
</div>
</div>
@endif
@if($nextServiceBus)
<div class="bg-surface-card p-space-md lg:p-space-lg rounded-xl shadow-sm flex flex-col justify-between">
<div class="flex items-start gap-space-sm">
<div class="w-10 h-10 rounded-lg bg-surface-container-high text-sync-pending flex items-center justify-center shrink-0">
<span class="material-symbols-outlined">notifications_active</span>
</div>
<div class="flex flex-col">
<span class="font-label-caps text-label-caps text-sync-pending uppercase font-bold">Preventive Maintenance</span>
<span class="font-headline-sm text-headline-sm text-on-surface mt-0.5">Bus #{{ $nextServiceBus->bus_number }} Notice</span>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Scheduled service due in <span class="font-bold text-on-surface">{{ number_format($nextServiceBus->next_service_km) }} km</span>.</p>
</div>
</div>
<div class="mt-4 flex items-center justify-between pt-2">
<span class="font-label-caps text-label-caps text-outline">Odometer: {{ number_format($nextServiceBus->odometer) }} km</span>
<a class="px-space-md py-1.5 rounded-lg bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-body-sm text-body-sm font-semibold transition-colors" href="{{ route('buses') }}">
            Assign Depot Bay
          </a>
</div>
</div>
@else
<div class="bg-surface-card p-space-md lg:p-space-lg rounded-xl shadow-sm flex flex-col items-center justify-center text-center gap-2">
<span class="material-symbols-outlined text-outline text-3xl">event_available</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">No scheduled services pending.</span>
</div>
@endif
</div>
<div class="bg-surface-card rounded-xl shadow-sm overflow-hidden flex flex-col">
<div class="p-space-md lg:p-space-lg flex flex-col sm:flex-row sm:items-center justify-between gap-space-md bg-surface-container-low/50">
<div class="flex items-center gap-space-sm">
<div class="w-10 h-10 rounded-lg bg-primary text-on-primary flex items-center justify-center">
<span class="material-symbols-outlined">commute</span>
</div>
<div>
<h2 class="font-headline-md text-headline-md text-on-surface tracking-tight">Active Shifts &amp; Live Vehicles</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Real-time telemetry, driver status, and meter synchronization</p>
</div>
</div>
<div class="flex items-center gap-space-xs self-start sm:self-auto">
<div class="flex rounded-lg bg-surface-container p-1 text-on-surface">
<button class="px-3 py-1 rounded bg-surface-card font-body-sm text-body-sm font-semibold shadow-sm">All ({{ $totalBuses }})</button>
<button class="px-3 py-1 font-body-sm text-body-sm text-outline hover:text-on-surface">Active ({{ $activeBuses }})</button>
<button class="px-3 py-1 font-body-sm text-body-sm text-outline hover:text-on-surface">Alerts ({{ $alertCount }})</button>
</div>
</div>
</div>
<div class="w-full overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low text-on-surface-variant font-label-caps text-label-caps uppercase tracking-wider">
<th class="py-3 px-space-md font-bold">Bus #</th>
<th class="py-3 px-space-md font-bold">Assigned Driver</th>
<th class="py-3 px-space-md font-bold">Shift Status</th>
<th class="py-3 px-space-md font-bold">Start Time</th>
<th class="py-3 px-space-md font-bold">Start Meter</th>
<th class="py-3 px-space-md font-bold">Current / Est Meter</th>
<th class="py-3 px-space-md font-bold">Fuel Level</th>
<th class="py-3 px-space-md font-bold text-right">Quick Actions</th>
</tr>
</thead>
<tbody class="divide-y-0 text-on-surface font-body-md text-body-md">
@forelse($buses as $bus)
@php
    $driverName = $bus->driver?->user?->name;
    $initials = $driverName
        ? strtoupper(collect(explode(' ', $driverName))->map(fn ($part) => substr($part, 0, 1))->take(2)->implode(''))
        : '';

    $statusMap = [
        'active' => ['label' => $bus->route ?: 'On Route', 'badge' => 'bg-secondary-fixed text-on-secondary-fixed-variant', 'dot' => 'bg-secondary', 'row' => ''],
        'standby' => ['label' => 'Ready / Standby', 'badge' => 'bg-surface-container-highest text-on-surface-variant', 'dot' => 'bg-outline', 'row' => ''],
        'depot' => ['label' => 'Idle at Depot', 'badge' => 'bg-surface-container-highest text-on-surface-variant', 'dot' => 'bg-outline', 'row' => ''],
        'maintenance' => ['label' => 'In Maintenance', 'badge' => 'bg-primary-fixed text-on-primary-fixed-variant', 'dot' => 'bg-primary-container', 'row' => ''],
        'out_of_service' => ['label' => 'Out of Service', 'badge' => 'bg-danger-critical text-on-primary', 'dot' => 'bg-danger-critical', 'row' => 'bg-error-container/10'],
    ];
    $st = $statusMap[$bus->status] ?? $statusMap['standby'];
@endphp
<tr class="hover:bg-surface-container-low/60 transition-colors {{ $st['row'] }}">
<td class="py-3.5 px-space-md">
<div class="flex items-center gap-space-xs">
<span class="w-2 h-2 rounded-full {{ $st['dot'] }}"></span>
<span class="font-headline-sm text-headline-sm font-bold text-on-surface">Bus #{{ $bus->bus_number }}</span>
</div>
<span class="font-body-sm text-body-sm text-outline">{{ $bus->model ?? 'Unspecified chassis' }}</span>
</td>
<td class="py-3.5 px-space-md">
@if($driverName)
<div class="flex items-center gap-space-xs">
<div class="w-7 h-7 rounded-full bg-surface-container-high text-on-surface font-semibold flex items-center justify-center text-xs">{{ $initials }}</div>
<div class="flex flex-col">
<span class="font-semibold leading-tight">{{ $driverName }}</span>
<span class="font-body-sm text-body-sm text-outline">Badge: {{ $bus->driver->badge_id ?? 'N/A' }}</span>
</div>
</div>
@else
<span class="font-body-sm text-body-sm text-outline italic">Unassigned</span>
@endif
</td>
<td class="py-3.5 px-space-md">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ $st['badge'] }} font-body-sm text-body-sm font-bold">
<span class="w-2 h-2 rounded-full {{ $st['dot'] }}"></span>
                {{ $st['label'] }}
              </span>
</td>
<td class="py-3.5 px-space-md font-body-sm text-body-sm font-medium text-outline">&mdash;</td>
<td class="py-3.5 px-space-md font-label-numeric text-label-numeric text-outline">&mdash;</td>
<td class="py-3.5 px-space-md font-label-numeric text-label-numeric text-on-surface">{{ number_format($bus->odometer) }} km</td>
<td class="py-3.5 px-space-md font-label-numeric text-label-numeric text-outline">&mdash;</td>
<td class="py-3.5 px-space-md text-right">
<div class="flex items-center justify-end gap-1">
<a class="p-1.5 rounded-lg text-outline hover:text-on-surface hover:bg-surface-container-high" href="{{ route('buses') }}" title="View in fleet roster">
<span class="material-symbols-outlined text-lg">visibility</span>
</a>
</div>
</td>
</tr>
@empty
<tr>
<td class="py-10 text-center text-outline" colspan="8">No buses registered in the fleet yet.</td>
</tr>
@endforelse
</tbody>
</table>
</div>
<div class="p-space-md flex flex-col sm:flex-row items-center justify-between gap-space-sm bg-surface-container-low/30 text-on-surface-variant font-body-sm text-body-sm">
<span>Showing {{ $totalBuses }} of {{ $totalBuses }} total fleet vehicles</span>
<div class="flex items-center gap-1">
<button class="px-3 py-1 rounded bg-surface-card text-on-surface shadow-sm font-semibold">1</button>
<button class="px-3 py-1 rounded hover:bg-surface-container-high transition-colors">2</button>
<button class="px-3 py-1 rounded hover:bg-surface-container-high transition-colors">3</button>
</div>
</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-space-lg">
<div class="bg-surface-card p-space-md lg:p-space-lg rounded-xl shadow-sm flex flex-col gap-space-md">
<div class="flex items-center justify-between pb-space-xs">
<div class="flex items-center gap-space-xs">
<div class="w-8 h-8 rounded-lg bg-surface-container-high text-primary flex items-center justify-center">
<span class="material-symbols-outlined text-base">local_gas_station</span>
</div>
<div>
<h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Recent Fuel Entries</h3>
<span class="font-body-sm text-body-sm text-outline">Driver submitted dispenser receipts</span>
</div>
</div>
<a class="font-body-sm text-body-sm font-bold text-primary hover:underline" href="#">View All Fuel</a>
</div>
<div class="flex flex-col items-center justify-center text-center gap-2 py-space-lg">
<span class="material-symbols-outlined text-outline text-3xl">local_gas_station</span>
<p class="font-body-sm text-body-sm text-on-surface-variant">No fuel entries recorded yet.</p>
<p class="font-body-sm text-body-sm text-outline text-xs">Driver-submitted receipts will appear here once the fuel log is available.</p>
</div>
</div>
<div class="bg-surface-card p-space-md lg:p-space-lg rounded-xl shadow-sm flex flex-col gap-space-md">
<div class="flex items-center justify-between pb-space-xs">
<div class="flex items-center gap-space-xs">
<div class="w-8 h-8 rounded-lg bg-surface-container-high text-tertiary-container flex items-center justify-center">
<span class="material-symbols-outlined text-base">engineering</span>
</div>
<div>
<h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Recent Maintenance</h3>
<span class="font-body-sm text-body-sm text-outline">Workshop orders &amp; parts replacement</span>
</div>
</div>
<a class="font-body-sm text-body-sm font-bold text-primary hover:underline" href="#">View Maintenance</a>
</div>
<div class="flex flex-col items-center justify-center text-center gap-2 py-space-lg">
<span class="material-symbols-outlined text-outline text-3xl">engineering</span>
<p class="font-body-sm text-body-sm text-on-surface-variant">No maintenance records yet.</p>
<p class="font-body-sm text-body-sm text-outline text-xs">Workshop orders will appear here once the maintenance log is available.</p>
</div>
</div>
</div>
<div class="fixed inset-0 z-50 flex items-center justify-center bg-inverse-surface/60 backdrop-blur-sm p-4 hidden" id="photoModal">
<div class="bg-surface-card rounded-2xl max-w-lg w-full p-space-lg shadow-xl flex flex-col gap-space-md">
<div class="flex items-center justify-between">
<div class="flex items-center gap-space-xs">
<span class="material-symbols-outlined text-danger-critical">warning</span>
<h4 class="font-headline-md text-headline-md text-on-surface">Meter Verification Audit</h4>
</div>
<button class="p-1 rounded-lg text-outline hover:text-on-surface hover:bg-surface-container-high" onclick="togglePhotoModal(false)">
<span class="material-symbols-outlined">close</span>
</button>
</div>
<div class="relative w-full h-64 rounded-xl overflow-hidden bg-surface-container-highest shadow-inner">
<img class="w-full h-full object-cover" data-alt="High resolution crisp macro photo of an analog bus odometer dial inside vehicle dashboard showing 148,770 kilometers with steering wheel edge visible in soft daylight" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBB_Rp4xV-YqkuoDqWhIfkdK_wgYP6RZfYFySoVTbVsX3aQjX65KSFQIBt8QDauLaH89vNk5XXZ5YS4A441fnpd2IrWxnIvriySvyE1-QJXYEwYgRX2PKHTbq2LKtNX2GqVWRZZbm1iyfPDtDW7pi3_2KT3Ern-HuI_vZRF9u8kdyZsWuqOLbmge0PWDBT-bXEQQa2TQxrtH8G_lUTfFP16-2YlhGl4BKoHBrs1W3ESzvkJOrGN22B2"/>
<div class="absolute top-3 left-3 px-2 py-1 bg-surface-card/90 backdrop-blur-sm rounded-md shadow-sm">
<span class="font-label-caps text-label-caps text-on-surface font-bold">Captured: 11:20 AM</span>
</div>
<div class="absolute bottom-3 right-3 px-2.5 py-1 bg-on-surface/90 text-surface text-xs font-mono rounded">
          GPS: Lat 40.7128, Lon -74.0060
        </div>
</div>
<div class="bg-surface-container-low p-space-md rounded-xl flex flex-col gap-1">
<div class="flex justify-between text-body-sm font-semibold">
<span class="text-outline">Assigned Vehicle:</span>
<span class="text-on-surface font-headline-sm text-headline-sm">Bus #42</span>
</div>
<div class="flex justify-between text-body-sm">
<span class="text-outline">Driver:</span>
<span class="text-on-surface font-medium">Marcus Vance (DRV-8802)</span>
</div>
<div class="flex justify-between text-body-sm">
<span class="text-outline">Shift Start Meter:</span>
<span class="font-label-numeric text-label-numeric text-on-surface">148,320 km</span>
</div>
<div class="flex justify-between text-body-sm">
<span class="text-outline">Photo Reading:</span>
<span class="font-label-numeric text-label-numeric text-danger-critical font-bold">148,770 km</span>
</div>
<div class="flex justify-between text-body-sm font-semibold pt-1">
<span class="text-danger-critical">Delta Inconsistency:</span>
<span class="text-danger-critical">+450 km in 4.8 hrs (Expected &lt; 150 km)</span>
</div>
</div>
<div class="flex gap-space-sm">
<button class="flex-1 py-3 rounded-lg bg-surface-container-high text-on-surface font-body-md text-body-md font-bold hover:bg-surface-container-highest transition-colors" onclick="togglePhotoModal(false)">
          Flag For Inspection
        </button>
<button class="flex-1 py-3 rounded-lg bg-primary-container text-on-primary font-body-md text-body-md font-bold hover:bg-primary transition-colors" onclick="approveReading()">
          Accept Override
        </button>
</div>
</div>
</div>
<script>
    function togglePhotoModal(show) {
      const modal = document.getElementById('photoModal');
      if (show) {
        modal.classList.remove('hidden');
      } else {
        modal.classList.add('hidden');
      }
    }

    function dismissAlert(button) {
      const card = button.closest('.bg-error-container\\/40');
      if (card) {
        card.style.opacity = '0';
        card.style.transition = 'opacity 0.3s ease';
        setTimeout(() => {
          card.innerHTML = `
            <div class="w-full flex items-center justify-between p-2">
              <div class="flex items-center gap-2 text-secondary font-semibold font-body-md text-body-md">
                <span class="material-symbols-outlined text-sync-active">check_circle</span>
                <span>Alert acknowledged by dispatcher Alex Turner.</span>
              </div>
              <span class="font-body-sm text-body-sm text-outline">Logged to audit trail</span>
            </div>
          `;
          card.style.opacity = '1';
        }, 300);
      }
    }

    function approveReading() {
      togglePhotoModal(false);
      alert('Meter override acknowledged. Telemetry sync calibrated.');
    }

    document.getElementById('exportDailyBtn')?.addEventListener('click', () => {
      const originalText = document.getElementById('exportDailyBtn').innerHTML;
      document.getElementById('exportDailyBtn').innerHTML = `
        <span class="material-symbols-outlined text-lg animate-spin">progress_activity</span>
        <span>Generating Log...</span>
      `;
      setTimeout(() => {
        document.getElementById('exportDailyBtn').innerHTML = originalText;
        alert('Daily Log exported successfully (PDF/CSV ready in downloads).');
      }, 900);
    });

    document.getElementById('newShiftBtn')?.addEventListener('click', () => {
      alert('Opening Shift Dispatch modal: select bus & driver.');
    });
  </script>
</div></main></div></body></html>
