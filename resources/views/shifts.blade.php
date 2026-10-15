<!DOCTYPE html>

<html lang="en"><head><meta charset="utf-8"/><meta content="width=device-width, initial-scale=1.0" name="viewport"/><meta content="web_dashboard" name="shell-type"/><meta name="csrf-token" content="{{ csrf_token() }}"/><link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/><link href="https://fonts.googleapis.com" rel="preconnect"/><link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;family=Sora:wght@400;500;600;700&amp;family=Poppins:wght@600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/><title>Shift Management &amp; Meter Audits &bull; Lancotri</title><style>@layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}main>:first-child{margin-top:0!important;}main>:last-child{margin-bottom:0!important;}}::-webkit-scrollbar{display:none;}</style><script src="https://cdn.tailwindcss.com"></script><script id="tailwind-config">tailwind.config = { darkMode: "class", theme: { extend: { "colors": { "outline-variant": "#D1D5DB", "on-surface": "#111827", "surface-variant": "#E1E5EC", "inverse-on-surface": "#F1F5F9", "on-primary": "#ffffff", "surface-container-high": "#E1E5EC", "on-secondary": "#ffffff", "secondary": "#0A81B4", "primary-fixed-dim": "#E9A0A2", "on-primary-container": "#ffffff", "background": "#F7F8FA", "error": "#ba1a1a", "border-strong": "#CBD5E1", "tertiary-fixed-dim": "#8FCBE5", "on-tertiary-container": "#E7F4FB", "tertiary-container": "#0A81B4", "tertiary-fixed": "#CFEAF7", "sync-active": "#10B981", "tertiary": "#0A6E99", "surface-container-low": "#F1F3F6", "surface": "#F7F8FA", "on-tertiary-fixed": "#052E42", "surface-tint": "#BD1C23", "on-surface-variant": "#4B5563", "on-secondary-fixed": "#052E42", "on-secondary-container": "#0A5473", "on-primary-fixed-variant": "#8E1418", "surface-card": "#FFFFFF", "on-secondary-fixed-variant": "#0A5C80", "inverse-surface": "#283044", "primary-container": "#BD1C23", "on-primary-fixed": "#3E0A0C", "surface-container-lowest": "#ffffff", "secondary-fixed": "#CFEAF7", "secondary-fixed-dim": "#8FCBE5", "surface-bright": "#F7F8FA", "on-error": "#ffffff", "primary-fixed": "#F7D2D3", "on-tertiary-fixed-variant": "#0A5C80", "surface-dim": "#D2D9E3", "primary": "#A11217", "inverse-primary": "#F2A7A9", "on-error-container": "#93000a", "surface-container": "#E9ECF1", "danger-critical": "#EF4444", "surface-container-highest": "#D8DEE7", "on-tertiary": "#ffffff", "error-container": "#ffdad6", "secondary-container": "#CFEAF7", "sync-pending": "#F59E0B", "on-background": "#111827", "sync-offline": "#64748B", "canvas-base": "#F8FAFC", "outline": "#6B7280" }, "borderRadius": { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" }, "spacing": { "space-xl": "2.25rem", "space-lg": "1.5rem", "space-md": "1rem", "space-sm": "0.5rem", "gutter-tablet": "1.5rem", "gutter": "1rem", "space-xs": "0.25rem", "margin": "1rem", "margin-tablet": "2rem" }, "fontFamily": { "label-action": [ "Plus Jakarta Sans" ], "headline-sm": [ "Sora" ], "headline-md": [ "Sora" ], "headline-lg": [ "Sora" ], "body-sm": [ "Plus Jakarta Sans" ], "body-lg": [ "Plus Jakarta Sans" ], "label-numeric": [ "Sora" ], "display-odometer": [ "Sora" ], "label-caps": [ "Sora" ], "body-md": [ "Plus Jakarta Sans" ] }, "fontSize": { "label-action": [ "18px", { "lineHeight": "24px", "fontWeight": "600" } ], "headline-sm": [ "18px", { "lineHeight": "24px", "fontWeight": "600" } ], "headline-md": [ "22px", { "lineHeight": "28px", "fontWeight": "600" } ], "headline-lg": [ "30px", { "lineHeight": "36px", "letterSpacing": "-0.01em", "fontWeight": "700" } ], "body-sm": [ "13px", { "lineHeight": "18px", "fontWeight": "400" } ], "body-lg": [ "17px", { "lineHeight": "24px", "fontWeight": "500" } ], "label-numeric": [ "20px", { "lineHeight": "24px", "letterSpacing": "0.02em", "fontWeight": "700" } ], "display-odometer": [ "44px", { "lineHeight": "48px", "letterSpacing": "-0.02em", "fontWeight": "700" } ], "label-caps": [ "12px", { "lineHeight": "16px", "letterSpacing": "0.08em", "fontWeight": "700" } ], "body-md": [ "15px", { "lineHeight": "22px", "fontWeight": "400" } ] } } } };</script>@include('partials.brand-styles')
  <link rel="icon" type="image/svg+xml" href="{{ asset('images/lancotri-mark.svg') }}">
</head><body class="bg-surface font-body-md text-body-md text-on-surface antialiased min-h-screen">@include('partials.sidebar')<div class="pl-64 flex flex-col min-h-screen">@include('partials.topbar', ['topbarStatus' => $activeBuses . ' active vehicles'])<main class="w-full pt-16 flex-1 px-space-lg py-space-lg bg-surface"><div class="flex flex-col w-full gap-space-lg">
<!-- Page Header & Operational Pulse Summary -->
<div class="flex flex-col xl:flex-row xl:items-end justify-between gap-space-md">
<div class="flex flex-col gap-1 max-w-2xl">
<div class="flex items-center gap-space-xs text-primary font-label-caps text-label-caps">
<span class="material-symbols-outlined text-base">verified_user</span>
<span>TELEMETRY &amp; AUDIT PIPELINE</span>
</div>
<h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Shift Management &amp; Meter Audits</h1>
<p class="font-body-md text-body-md text-on-surface-variant">Verify driver start and end shift meter photos, calculate mileage discrepancies, and approve daily logs across regional depot routes.</p>
</div>
<!-- Quick Stat Pill Array -->
<div class="flex flex-wrap items-center gap-space-sm bg-surface-card p-2 rounded-xl shadow-sm">
<div class="flex items-center gap-space-sm px-space-md py-1.5 bg-surface-container-low rounded-lg">
<span class="w-2.5 h-2.5 rounded-full bg-sync-active"></span>
<div class="flex flex-col">
<span class="font-label-caps text-label-caps text-outline leading-tight">ACTIVE FLEET</span>
<span class="font-headline-sm text-headline-sm text-on-surface leading-tight">{{ $activeBuses }} <span class="font-body-sm text-body-sm text-outline">/ {{ $totalBuses }}</span></span>
</div>
</div>
<div class="flex items-center gap-space-sm px-space-md py-1.5 bg-surface-container-low rounded-lg">
<span class="w-2.5 h-2.5 rounded-full bg-danger-critical animate-pulse"></span>
<div class="flex flex-col">
<span class="font-label-caps text-label-caps text-outline leading-tight">FLAGGED AUDITS</span>
<span class="font-headline-sm text-headline-sm text-danger-critical leading-tight">{{ $countFlagged }} {{ \Illuminate\Support\Str::plural('Log', $countFlagged) }}</span>
</div>
</div>
<div class="flex items-center gap-space-sm px-space-md py-1.5 bg-surface-container-low rounded-lg">
<span class="material-symbols-outlined text-tertiary-container text-xl">speed</span>
<div class="flex flex-col">
<span class="font-label-caps text-label-caps text-outline leading-tight">DAY MILEAGE</span>
<span class="font-headline-sm text-headline-sm text-on-surface leading-tight">{{ number_format($dayMileage, 0) }} <span class="font-body-sm text-body-sm text-outline">KM</span></span>
</div>
</div>
</div>
</div>
<!-- Filter & Control Toolbar -->
<div class="bg-surface-card rounded-xl p-space-md shadow-sm flex flex-col gap-space-md">
<!-- Top Filter Row -->
<div class="flex flex-wrap items-center justify-between gap-space-md">
<form action="{{ route('shifts') }}" class="flex flex-wrap items-center gap-space-sm flex-1" method="GET">
<input name="status" type="hidden" value="{{ $status }}"/>
<!-- Date Range Label -->
<div class="flex items-center gap-space-xs px-space-md py-2.5 bg-surface-container-low rounded-lg text-on-surface shadow-sm">
<span class="material-symbols-outlined text-outline text-lg">calendar_month</span>
<span class="font-body-md text-body-md font-medium">All logged shifts</span>
</div>
<!-- Bus Filter Dropdown -->
<div class="relative flex items-center">
<select class="appearance-none bg-surface-container-low font-body-md text-body-md text-on-surface pl-9 pr-9 py-2.5 rounded-lg focus:outline-none focus:bg-surface-container transition-colors cursor-pointer shadow-sm" name="bus_id" onchange="this.form.submit()">
<option value="">All Buses ({{ $totalBuses }} total)</option>
@foreach($buses as $bus)
<option value="{{ $bus->id }}" @selected((string) $busId === (string) $bus->id)>{{ $bus->model ?? 'Bus' }} #{{ $bus->bus_number }}</option>
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
<option value="{{ $driver->id }}" @selected((string) $driverId === (string) $driver->id)>{{ $driver->user->name ?? 'Driver' }}@if($driver->badge_id) (#{{ $driver->badge_id }})@endif</option>
@endforeach
</select>
<span class="material-symbols-outlined absolute left-3 text-outline text-lg pointer-events-none">badge</span>
<span class="material-symbols-outlined absolute right-2.5 text-outline text-sm pointer-events-none">expand_more</span>
</div>
<!-- Quick Reset Filter Button -->
<a class="px-space-sm py-2 text-outline hover:text-on-surface text-body-sm font-body-sm flex items-center gap-1 transition-colors" href="{{ route('shifts') }}">
<span class="material-symbols-outlined text-base">restart_alt</span>
<span>Reset</span>
</a>
</form>
<!-- Action Button Group -->
<div class="flex items-center gap-space-sm">
<button class="flex items-center gap-space-xs px-space-md py-2.5 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md hover:bg-surface-container transition-colors shadow-sm">
<span class="material-symbols-outlined text-lg">download</span>
<span>Export CSV / PDF</span>
</button>
<button class="flex items-center gap-space-xs px-space-md py-2.5 rounded-lg bg-primary-container text-on-primary font-label-action text-label-action hover:opacity-95 transition-opacity shadow-sm">
<span class="material-symbols-outlined text-xl">document_scanner</span>
<span>Batch OCR Audit</span>
</button>
</div>
</div>
<!-- Status Filter Tabs -->
<div class="flex items-center gap-2 overflow-x-auto pt-2">
@php
    $statusTabs = [
        ['key' => 'all', 'label' => 'All Shifts', 'count' => $countAll],
        ['key' => 'in_progress', 'label' => 'Active / Open', 'count' => $countOpen],
        ['key' => 'completed', 'label' => 'Completed', 'count' => $countCompleted],
        ['key' => 'flagged', 'label' => 'Flagged / Needs Review', 'count' => $countFlagged],
    ];
@endphp
@foreach($statusTabs as $tab)
@php $tabActive = $status === $tab['key']; @endphp
<a class="px-space-md py-2 rounded-lg font-body-md text-body-md whitespace-nowrap flex items-center gap-1 {{ $tabActive ? 'bg-primary text-on-primary font-semibold shadow-sm' : ($tab['key'] === 'flagged' ? 'text-danger-critical hover:bg-error-container/40 transition-colors' : 'text-on-surface-variant hover:bg-surface-container-low transition-colors') }}" href="{{ route('shifts', ['status' => $tab['key']]) }}">
<span>{{ $tab['label'] }}</span>
@if($tab['key'] === 'flagged')
<span class="ml-1 px-2 py-0.5 rounded-full bg-danger-critical text-on-error text-body-sm font-bold">{{ $tab['count'] }}</span>
@elseif($tab['key'] === 'in_progress')
<span class="ml-1 px-2 py-0.5 rounded-full bg-surface-variant text-primary font-body-sm font-semibold">{{ $tab['count'] }}</span>
@elseif($tab['key'] === 'completed')
<span class="ml-1 px-2 py-0.5 rounded-full {{ $tabActive ? 'bg-on-primary/20 text-on-primary text-body-sm font-bold' : 'bg-surface-container-high text-on-surface text-body-sm font-semibold' }}">{{ $tab['count'] }}</span>
@else
<span class="ml-1 px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface text-body-sm font-semibold">{{ $tab['count'] }}</span>
@endif
</a>
@endforeach
</div>
</div>
<!-- Primary Workspace: Split Layout (Master Shift Roster & Side Inspection Panel) -->
<div class="grid grid-cols-1 2xl:grid-cols-12 gap-space-lg items-start">
<!-- Left Column: Master Shifts Table (8 cols on 2xl) -->
<div class="2xl:col-span-8 flex flex-col gap-space-md">
<div class="bg-surface-card rounded-xl shadow-sm overflow-hidden flex flex-col">
<!-- Table Control Strip -->
<div class="px-space-lg py-space-md bg-surface-container-lowest flex items-center justify-between">
<div class="flex items-center gap-space-sm">
<span class="font-headline-sm text-headline-sm text-on-surface">Daily Log Records</span>
<span class="text-outline text-sm">&bull;</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Latest verified &amp; active driver logs</span>
</div>
<div class="flex items-center gap-2">
<span class="font-label-caps text-label-caps text-secondary-container bg-secondary px-2.5 py-1 rounded-md">LIVE TELEMETRY SYNC</span>
</div>
</div>
<!-- Table Viewport -->
<div class="overflow-x-auto w-full">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low text-on-surface-variant font-label-caps text-label-caps uppercase tracking-wider">
<th class="py-3.5 px-space-md">Shift Ref</th>
<th class="py-3.5 px-space-md">Vehicle</th>
<th class="py-3.5 px-space-md">Driver</th>
<th class="py-3.5 px-space-md">Start Meter</th>
<th class="py-3.5 px-space-md">End Meter</th>
<th class="py-3.5 px-space-md">Distance</th>
<th class="py-3.5 px-space-md">Status</th>
<th class="py-3.5 px-space-md text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y-0">
@forelse($shifts as $shift)
@php
    $rowBus = $shift->bus;
    $rowDriver = $shift->driver;
    $rowOpen = $shift->status === \App\Models\ShiftLog::STATUS_IN_PROGRESS;
    $rowFlagged = $shift->status === \App\Models\ShiftLog::STATUS_COMPLETED && $shift->end_km === null;
    $rowSelected = $selected !== null && $selected->id === $shift->id;

    $dotClass = $rowFlagged ? 'bg-danger-critical' : ($rowOpen ? 'bg-primary-container' : 'bg-sync-active');
    $refClass = $rowFlagged ? 'text-danger-critical' : ($rowSelected ? 'text-primary' : 'text-on-surface');
    $rowClass = $rowSelected ? 'bg-surface-container-high' : ($rowFlagged ? 'bg-error-container/20' : '');

    $driverFullName = $rowDriver?->user?->name;
    $driverInitials = collect(explode(' ', (string) $driverFullName))->filter()->map(fn ($p) => strtoupper(substr($p, 0, 1)))->take(2)->implode('');
    $avatarClass = $rowFlagged ? 'bg-danger-critical text-on-error' : ($rowSelected ? 'bg-primary-container text-on-primary' : 'bg-surface-container-high text-on-surface');

    $started = $shift->started_at;
    $ended = $shift->ended_at;
    $rowDistance = $shift->distance_km !== null ? (float) $shift->distance_km : null;
    $rowDuration = null;
    if ($started && $ended) {
        $minutes = $started->diffInMinutes($ended);
        $rowDuration = intdiv($minutes, 60) . 'h ' . ($minutes % 60) . 'm duration';
    }

    if ($rowFlagged) {
        $statusLabel = 'Needs Review';
        $statusClass = 'bg-danger-critical text-on-error font-bold shadow-sm';
        $statusIcon = 'warning';
    } elseif ($rowOpen) {
        $statusLabel = 'In Progress';
        $statusClass = 'bg-surface-container-high text-primary font-semibold';
        $statusIcon = null;
    } else {
        $statusLabel = 'Approved';
        $statusClass = 'bg-secondary-fixed/40 text-secondary font-semibold';
        $statusIcon = 'check_circle';
    }
@endphp
<tr class="shift-row cursor-pointer transition-colors hover:bg-surface-container-low {{ $rowClass }}" data-shift-id="{{ $shift->id }}" onclick="loadShiftDetail({{ $shift->id }})">
<td class="py-4 px-space-md align-middle">
<div class="flex items-center gap-2">
<span class="w-2 h-2 rounded-full {{ $dotClass }}{{ $rowOpen ? ' animate-pulse' : '' }}"></span>
<div class="flex flex-col">
<span class="font-label-numeric text-body-lg font-bold {{ $refClass }}">#{{ $shift->shift_number }}</span>
<span class="font-body-sm text-body-sm text-outline">{{ optional($started)->format('M d') }} &bull; {{ optional($started)->format('h:i A') }}</span>
</div>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col">
<span class="font-headline-sm text-headline-sm text-on-surface font-bold">{{ $rowBus ? 'BUS-' . $rowBus->bus_number : 'Unassigned' }}</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">{{ $rowBus->model ?? '—' }}</span>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex items-center gap-space-xs">
<div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs {{ $avatarClass }}">{{ $driverInitials ?: '—' }}</div>
<div class="flex flex-col">
<span class="font-body-md text-body-md font-semibold text-on-surface">{{ $driverFullName ?? 'Unassigned driver' }}</span>
<span class="font-body-sm text-body-sm text-outline">ID: #{{ $rowDriver->badge_id ?? '—' }}</span>
</div>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col">
<span class="font-label-numeric text-body-md text-on-surface">{{ number_format($shift->start_km) }} <span class="text-xs text-outline font-normal">km</span></span>
<span class="font-body-sm text-body-sm text-outline flex items-center gap-0.5">
<span class="material-symbols-outlined text-xs">photo_camera</span>
<span>{{ optional($started)->format('h:i A') ?? '—' }}</span>
</span>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col">
@if($shift->end_km !== null)
<span class="font-label-numeric text-body-md {{ $rowFlagged ? 'text-danger-critical font-bold' : 'text-on-surface' }}">{{ number_format($shift->end_km) }} <span class="text-xs text-outline font-normal">km</span></span>
<span class="font-body-sm text-body-sm {{ $rowFlagged ? 'text-danger-critical font-bold' : 'text-outline' }} flex items-center gap-0.5">
<span class="material-symbols-outlined text-xs">{{ $rowFlagged ? 'error' : 'verified' }}</span>
<span>{{ $rowFlagged ? 'Needs photo' : (optional($ended)->format('h:i A') ?? 'Verified') }}</span>
</span>
@elseif($rowOpen)
<div class="flex items-center gap-1.5 text-outline">
<span class="material-symbols-outlined text-sm animate-spin">timelapse</span>
<span class="font-body-sm text-body-sm italic">In Route (Open)</span>
</div>
@else
<span class="font-label-numeric text-body-md text-outline">—</span>
@endif
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col">
@if($rowDistance !== null)
<span class="font-label-numeric text-headline-sm font-bold {{ $rowFlagged ? 'text-danger-critical' : 'text-secondary' }}">+{{ number_format($rowDistance, 0) }} KM</span>
@if($rowDuration)<span class="font-body-sm text-body-sm text-outline">{{ $rowDuration }}</span>@endif
@elseif($rowOpen)
<span class="font-label-numeric text-body-lg text-outline">Tracking...</span>
<span class="font-body-sm text-body-sm text-outline">Shift still open</span>
@else
<span class="font-label-numeric text-headline-sm font-bold text-outline">—</span>
@endif
</div>
</td>
<td class="py-4 px-space-md align-middle">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-body-sm text-body-sm {{ $statusClass }}">
@if($statusIcon)
<span class="material-symbols-outlined text-xs">{{ $statusIcon }}</span>
@else
<span class="w-2 h-2 rounded-full bg-sync-active"></span>
@endif
<span>{{ $statusLabel }}</span>
</span>
</td>
<td class="py-4 px-space-md align-middle text-right">
<div class="inline-flex items-center gap-1">
@if($rowFlagged)
<button class="px-3 py-1.5 rounded-lg bg-danger-critical text-on-error font-body-sm text-body-sm font-semibold hover:opacity-90 shadow-sm transition-opacity" type="button">Resolve</button>
@elseif($rowOpen)
<button class="px-3 py-1.5 rounded-lg bg-surface-container-low text-on-surface font-body-sm text-body-sm font-medium hover:bg-surface-container transition-colors" type="button">Track</button>
@else
<button class="px-3 py-1.5 rounded-lg bg-primary-container text-on-primary font-body-sm text-body-sm font-semibold hover:opacity-90 shadow-sm transition-opacity" type="button">View</button>
@endif
<button class="p-1.5 text-on-surface-variant hover:text-on-surface hover:bg-surface-container rounded-lg transition-colors" onclick="event.stopPropagation()" type="button">
<span class="material-symbols-outlined text-xl">more_vert</span>
</button>
</div>
</td>
</tr>
@empty
<tr>
<td class="py-10 px-space-md text-center font-body-md text-body-md text-on-surface-variant" colspan="8">No shift logs match the selected filters.</td>
</tr>
@endforelse
</tbody>

</table>
</div>
<!-- Table Pagination Footer -->
<div class="p-space-md bg-surface-container-low flex flex-col sm:flex-row items-center justify-between gap-space-sm">
<span class="font-body-sm text-body-sm text-on-surface-variant">Showing {{ $shifts->count() }} of {{ $countAll }} shift logs logged within the selected period</span>
<div class="flex items-center gap-1">
<span class="font-body-sm text-body-sm text-outline">{{ $activeBuses }} vehicles active now</span>
</div>
</div>
</div>
<!-- Route Discrepancy & Fleet Variance Analytics Tile -->
<div class="bg-surface-card rounded-xl p-space-lg shadow-sm flex flex-col md:flex-row items-center justify-between gap-space-lg">
<div class="flex items-center gap-space-md">
<div class="w-14 h-14 rounded-xl bg-surface-variant flex items-center justify-center text-primary-container">
<span class="material-symbols-outlined text-3xl">analytics</span>
</div>
<div class="flex flex-col">
<h2 class="font-headline-sm text-headline-sm text-on-surface">Route Distance Variance (Automated Audit)</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">System flags shifts where manual meter difference deviates &gt; 5% from GPS route breadcrumbs.</p>
</div>
</div>
<div class="flex items-center gap-space-md">
<div class="text-right">
<span class="font-headline-md text-headline-md text-secondary font-bold">97.8%</span>
<span class="font-body-sm text-body-sm text-outline block">Depot Match Rate</span>
</div>
<!-- Sparkline Mini Chart (Inline SVG) -->
<svg class="w-24 h-10 text-secondary" fill="none" viewbox="0 0 100 40">
<path d="M0 32 L18 28 L36 30 L54 18 L72 22 L90 8 L100 12" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"></path>
<path d="M0 32 L18 28 L36 30 L54 18 L72 22 L90 8 L100 12 V 40 H 0 Z" fill="currentColor" fill-opacity="0.1"></path>
</svg>
</div>
</div>
</div>
<!-- Right Column: Meter Audit Inspection Detail Panel for #SH-9042 (4 cols on 2xl) -->
<div class="2xl:col-span-4 flex flex-col gap-space-md">
<div id="shiftDetailPanel" class="flex flex-col gap-space-md transition-opacity">
@if($selected)
@include('partials.shift-detail', ['shift' => $selected])
@else
<div class="bg-surface-card rounded-xl p-space-lg shadow-md text-center font-body-md text-body-md text-on-surface-variant">No shift logs available yet.</div>
@endif
</div>
</div>
</div></main></div>
<script>
    const shiftDetailPanel = document.getElementById('shiftDetailPanel');
    let shiftDetailController = null;

    // Load the inspection desk detail for a shift and swap it into the panel.
    async function loadShiftDetail(shiftId) {
      if (!shiftDetailPanel) return;

      document.querySelectorAll('.shift-row').forEach((row) => {
        row.classList.toggle('bg-surface-container-high', row.dataset.shiftId === String(shiftId));
      });

      shiftDetailController?.abort();
      shiftDetailController = new AbortController();

      shiftDetailPanel.classList.add('opacity-60');
      try {
        const response = await fetch(`/shifts/${shiftId}`, {
          headers: { 'Accept': 'text/html' },
          signal: shiftDetailController.signal,
        });
        if (!response.ok) throw new Error('Request failed');
        shiftDetailPanel.innerHTML = await response.text();
      } catch (error) {
        if (error.name !== 'AbortError') {
          shiftDetailPanel.innerHTML = '<div class="bg-surface-card rounded-xl p-space-lg shadow-md text-center font-body-md text-body-md text-danger-critical">Unable to load this shift. Please try again.</div>';
        }
      } finally {
        shiftDetailPanel.classList.remove('opacity-60');
      }
    }
  </script>
</body></html>
