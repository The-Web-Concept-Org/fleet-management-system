<!DOCTYPE html>

<html lang="en"><head><meta charset="utf-8"/><meta content="width=device-width, initial-scale=1.0" name="viewport"/><meta content="web_dashboard" name="shell-type"/><meta name="csrf-token" content="{{ csrf_token() }}"/><link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/><link href="https://fonts.googleapis.com" rel="preconnect"/><link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;family=Sora:wght@400;500;600;700&amp;family=Poppins:wght@600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/><title>Trip Scheduler &bull; Lancotri</title><style>@layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}main>:first-child{margin-top:0!important;}main>:last-child{margin-bottom:0!important;}}::-webkit-scrollbar{display:none;}</style><style>@keyframes tripDialogIn{from{opacity:0;transform:scale(.94) translateY(10px);}to{opacity:1;transform:scale(1) translateY(0);}}.delete-dialog{animation:tripDialogIn .22s cubic-bezier(.2,.8,.2,1);}</style><script src="https://cdn.tailwindcss.com"></script><script id="tailwind-config">tailwind.config = { darkMode: "class", theme: { extend: { "colors": { "outline-variant": "#D1D5DB", "on-surface": "#111827", "surface-variant": "#E1E5EC", "inverse-on-surface": "#F1F5F9", "on-primary": "#ffffff", "surface-container-high": "#E1E5EC", "on-secondary": "#ffffff", "secondary": "#0A81B4", "primary-fixed-dim": "#E9A0A2", "on-primary-container": "#ffffff", "background": "#F7F8FA", "error": "#ba1a1a", "border-strong": "#CBD5E1", "tertiary-fixed-dim": "#8FCBE5", "on-tertiary-container": "#E7F4FB", "tertiary-container": "#0A81B4", "tertiary-fixed": "#CFEAF7", "sync-active": "#10B981", "tertiary": "#0A6E99", "surface-container-low": "#F1F3F6", "surface": "#F7F8FA", "on-tertiary-fixed": "#052E42", "surface-tint": "#BD1C23", "on-surface-variant": "#4B5563", "on-secondary-fixed": "#052E42", "on-secondary-container": "#0A5473", "on-primary-fixed-variant": "#8E1418", "surface-card": "#FFFFFF", "on-secondary-fixed-variant": "#0A5C80", "inverse-surface": "#283044", "primary-container": "#BD1C23", "on-primary-fixed": "#3E0A0C", "surface-container-lowest": "#ffffff", "secondary-fixed": "#CFEAF7", "secondary-fixed-dim": "#8FCBE5", "surface-bright": "#F7F8FA", "on-error": "#ffffff", "primary-fixed": "#F7D2D3", "on-tertiary-fixed-variant": "#0A5C80", "surface-dim": "#D2D9E3", "primary": "#A11217", "inverse-primary": "#F2A7A9", "on-error-container": "#93000a", "surface-container": "#E9ECF1", "danger-critical": "#EF4444", "surface-container-highest": "#D8DEE7", "on-tertiary": "#ffffff", "error-container": "#ffdad6", "secondary-container": "#CFEAF7", "sync-pending": "#F59E0B", "on-background": "#111827", "sync-offline": "#64748B", "canvas-base": "#F8FAFC", "outline": "#6B7280" }, "borderRadius": { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" }, "spacing": { "space-xl": "2.25rem", "space-lg": "1.5rem", "space-md": "1rem", "space-sm": "0.5rem", "gutter-tablet": "1.5rem", "gutter": "1rem", "space-xs": "0.25rem", "margin": "1rem", "margin-tablet": "2rem" }, "fontFamily": { "label-action": [ "Plus Jakarta Sans" ], "headline-sm": [ "Sora" ], "headline-md": [ "Sora" ], "headline-lg": [ "Sora" ], "body-sm": [ "Plus Jakarta Sans" ], "body-lg": [ "Plus Jakarta Sans" ], "label-numeric": [ "Sora" ], "display-odometer": [ "Sora" ], "label-caps": [ "Sora" ], "body-md": [ "Plus Jakarta Sans" ] }, "fontSize": { "label-action": [ "18px", { "lineHeight": "24px", "fontWeight": "600" } ], "headline-sm": [ "18px", { "lineHeight": "24px", "fontWeight": "600" } ], "headline-md": [ "22px", { "lineHeight": "28px", "fontWeight": "600" } ], "headline-lg": [ "30px", { "lineHeight": "36px", "letterSpacing": "-0.01em", "fontWeight": "700" } ], "body-sm": [ "13px", { "lineHeight": "18px", "fontWeight": "400" } ], "body-lg": [ "17px", { "lineHeight": "24px", "fontWeight": "500" } ], "label-numeric": [ "20px", { "lineHeight": "24px", "letterSpacing": "0.02em", "fontWeight": "700" } ], "display-odometer": [ "44px", { "lineHeight": "48px", "letterSpacing": "-0.02em", "fontWeight": "700" } ], "label-caps": [ "12px", { "lineHeight": "16px", "letterSpacing": "0.08em", "fontWeight": "700" } ], "body-md": [ "15px", { "lineHeight": "22px", "fontWeight": "400" } ] } } } };</script>@include('partials.brand-styles')
  <link rel="icon" type="image/svg+xml" href="{{ asset('images/lancotri-mark.svg') }}">
</head><body class="bg-surface font-body-md text-body-md text-on-surface antialiased min-h-screen">@include('partials.sidebar')<div class="pl-64 flex flex-col min-h-screen">
@include('partials.topbar', [
    'topbarSearchPlaceholder' => 'Search trip, route, bus, or driver...',
    'topbarSearchId' => 'tripNavSearch',
    'topbarStatus' => $totalTrips . ' trips on the board',
])
<main class="w-full pt-16 flex-1 px-space-lg py-space-lg bg-surface"><div class="flex flex-col w-full gap-space-lg">

<!-- Page Header -->
<div class="flex flex-col xl:flex-row xl:items-center justify-between gap-space-md">
<div class="flex flex-col">
<div class="flex items-center gap-space-xs mb-1">
<span class="font-label-caps text-label-caps uppercase text-tertiary">Dispatch &amp; Route Planning</span>
<span class="inline-block w-1.5 h-1.5 rounded-full bg-sync-active"></span>
<span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Live assignment board</span>
</div>
<h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Trip Scheduler</h1>
<p class="font-body-md text-body-md text-on-surface-variant">Create trips, assign a vehicle, and keep the daily schedule free of double bookings. Each trip's driver follows the assigned bus.</p>
</div>
<div class="flex flex-wrap items-center gap-space-sm">
<div class="relative flex-1 sm:w-72">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg pointer-events-none">search</span>
<input class="w-full pl-9 pr-4 py-2.5 bg-surface-card rounded-lg text-on-surface placeholder:text-outline font-body-sm text-body-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-container" id="tripSearch" placeholder="Filter by trip #, route, bus, or driver..." type="text"/>
</div>
<button class="flex items-center gap-2 px-space-md py-2.5 rounded-lg bg-surface-card text-on-surface hover:bg-surface-container-high transition-colors font-body-md text-body-md shadow-sm" id="exportTripsCsvBtn">
<span class="material-symbols-outlined text-lg text-outline">file_download</span>
<span>Export</span>
</button>
<button class="flex items-center gap-2 px-space-md py-2.5 rounded-lg bg-primary-container text-on-primary hover:bg-primary transition-all font-body-md text-body-md font-semibold shadow-sm" id="addTripModalTrigger">
<span class="material-symbols-outlined text-lg">add</span>
<span>Schedule Trip</span>
</button>
</div>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-space-md">
<div class="bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-caps text-label-caps uppercase text-outline">Total Trips</span>
<span class="material-symbols-outlined text-primary text-xl">route</span>
</div>
<div class="mt-3 flex items-baseline gap-2">
<span class="font-display-odometer text-display-odometer text-on-surface">{{ $totalTrips }}</span>
<span class="font-body-sm text-body-sm text-outline">Recorded</span>
</div>
</div>
<div class="bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-caps text-label-caps uppercase text-secondary">Scheduled</span>
<span class="material-symbols-outlined text-secondary text-xl">event_upcoming</span>
</div>
<div class="mt-3 flex items-baseline gap-2">
<span class="font-display-odometer text-display-odometer text-secondary">{{ $scheduledTrips }}</span>
<span class="font-body-sm text-body-sm text-secondary font-medium">Upcoming</span>
</div>
</div>
<div class="bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-caps text-label-caps uppercase text-primary">In Progress</span>
<span class="material-symbols-outlined text-primary text-xl">directions_bus</span>
</div>
<div class="mt-3 flex items-baseline gap-2">
<span class="font-display-odometer text-display-odometer text-primary">{{ $inProgressTrips }}</span>
<span class="font-body-sm text-body-sm text-outline">On the road</span>
</div>
</div>
<div class="bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-caps text-label-caps uppercase text-secondary">Completed</span>
<span class="material-symbols-outlined text-secondary text-xl">task_alt</span>
</div>
<div class="mt-3 flex items-baseline gap-2">
<span class="font-display-odometer text-display-odometer text-on-surface">{{ $completedTrips }}</span>
<span class="font-body-sm text-body-sm text-outline">Closed</span>
</div>
</div>
<div class="col-span-2 md:col-span-1 bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-caps text-label-caps uppercase text-sync-pending">Needs Assignment</span>
<span class="material-symbols-outlined text-sync-pending text-xl">link_off</span>
</div>
<div class="mt-3 flex items-baseline gap-2">
<span class="font-display-odometer text-display-odometer {{ $unassignedTrips > 0 ? 'text-sync-pending' : 'text-on-surface' }}">{{ $unassignedTrips }}</span>
<span class="font-body-sm text-body-sm text-outline">No bus assigned</span>
</div>
</div>
</div>

<!-- Filters & Table -->
<div class="flex flex-col gap-space-md">
<div class="bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-space-md">
<div class="flex flex-wrap items-center gap-space-xs">
<span class="font-label-caps text-label-caps text-outline uppercase mr-2 flex items-center gap-1">
<span class="material-symbols-outlined text-sm">filter_alt</span> Status:
</span>
<button class="filter-pill px-3 py-1.5 rounded-full font-body-sm text-body-sm font-semibold transition-all bg-primary-container text-on-primary shadow-xs" data-filter="all">
All ({{ $totalTrips }})
</button>
<button class="filter-pill px-3 py-1.5 rounded-full font-body-sm text-body-sm transition-all bg-surface-container-low text-on-surface hover:bg-surface-container-high" data-filter="scheduled">
Scheduled ({{ $scheduledTrips }})
</button>
<button class="filter-pill px-3 py-1.5 rounded-full font-body-sm text-body-sm transition-all bg-surface-container-low text-on-surface hover:bg-surface-container-high" data-filter="in_progress">
In Progress ({{ $inProgressTrips }})
</button>
<button class="filter-pill px-3 py-1.5 rounded-full font-body-sm text-body-sm transition-all bg-surface-container-low text-on-surface hover:bg-surface-container-high" data-filter="completed">
Completed ({{ $completedTrips }})
</button>
<button class="filter-pill px-3 py-1.5 rounded-full font-body-sm text-body-sm transition-all bg-surface-container-low text-on-surface hover:bg-surface-container-high" data-filter="cancelled">
Cancelled
</button>
</div>
<div class="flex items-center gap-space-sm self-end md:self-auto">
<label class="font-body-sm text-body-sm text-outline flex items-center gap-1">
<span class="material-symbols-outlined text-sm">calendar_month</span> Date:
</label>
<input class="bg-surface-container-low text-on-surface font-body-sm text-body-sm px-3 py-1.5 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-container" id="tripDateFilter" type="date"/>
</div>
</div>

<div class="bg-surface-card rounded-xl shadow-sm overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-left text-on-surface border-collapse">
<thead>
<tr class="bg-surface-container-low text-outline font-label-caps text-label-caps uppercase border-b-0">
<th class="py-3.5 px-space-md">Trip Ref</th>
<th class="py-3.5 px-space-md">Route</th>
<th class="py-3.5 px-space-md">Date &amp; Time</th>
<th class="py-3.5 px-space-md">Assigned Bus</th>
<th class="py-3.5 px-space-md">Assigned Driver</th>
<th class="py-3.5 px-space-md">Distance</th>
<th class="py-3.5 px-space-md">Status</th>
<th class="py-3.5 px-space-md text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y-0 text-body-md font-body-md" id="tripsTableBody">
@forelse($trips as $trip)
@php
    $statusMap = [
        'scheduled' => ['label' => 'Scheduled', 'badge' => 'bg-secondary-container/50 text-secondary', 'dot' => 'bg-secondary'],
        'in_progress' => ['label' => 'In Progress', 'badge' => 'bg-primary-container/15 text-primary', 'dot' => 'bg-primary-container animate-pulse'],
        'completed' => ['label' => 'Completed', 'badge' => 'bg-sync-active/15 text-secondary', 'dot' => 'bg-sync-active'],
        'cancelled' => ['label' => 'Cancelled', 'badge' => 'bg-error-container text-danger-critical', 'dot' => 'bg-danger-critical'],
    ];
    $st = $statusMap[$trip->status] ?? $statusMap['scheduled'];
    $departure = $trip->departure_time ? substr($trip->departure_time, 0, 5) : null;
    $arrival = $trip->arrival_time ? substr($trip->arrival_time, 0, 5) : null;
@endphp
<tr class="hover:bg-surface-container-low transition-colors trip-row"
    data-id="{{ $trip->id }}"
    data-trip-number="{{ $trip->trip_number }}"
    data-origin="{{ $trip->origin }}"
    data-destination="{{ $trip->destination }}"
    data-date="{{ optional($trip->trip_date)->toDateString() }}"
    data-date-label="{{ optional($trip->trip_date)->format('M d, Y') }}"
    data-departure="{{ $departure }}"
    data-arrival="{{ $arrival }}"
    data-distance="{{ $trip->distance_km }}"
    data-status="{{ $trip->status }}"
    data-bus-id="{{ $trip->bus_id }}"
    data-notes="{{ $trip->notes }}">
<td class="py-4 px-space-md">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center text-on-surface">
<span class="material-symbols-outlined text-lg">route</span>
</div>
<div class="flex flex-col">
<span class="font-label-numeric text-body-lg text-on-surface font-bold">{{ $trip->trip_number }}</span>
@if($trip->status === 'scheduled')
<span class="font-body-sm text-body-sm text-outline">Awaiting dispatch</span>
@elseif($trip->is_assigned)
<span class="font-body-sm text-body-sm text-secondary">Fully assigned</span>
@else
<span class="font-body-sm text-body-sm text-outline">Trip logged</span>
@endif
</div>
</div>
</td>
<td class="py-4 px-space-md">
<div class="flex flex-col max-w-[16rem]">
<span class="font-body-md text-body-md font-semibold text-on-surface truncate">{{ $trip->origin }}</span>
<span class="font-body-sm text-body-sm text-outline flex items-center gap-1 truncate">
<span class="material-symbols-outlined text-xs">arrow_forward</span>{{ $trip->destination }}
</span>
</div>
</td>
<td class="py-4 px-space-md">
<div class="flex flex-col">
<span class="font-body-md text-body-md font-medium text-on-surface">{{ optional($trip->trip_date)->format('M d, Y') }}</span>
<span class="font-body-sm text-body-sm text-outline">
@if($departure || $arrival)
{{ $departure ?: '--:--' }} &ndash; {{ $arrival ?: '--:--' }}
@else
No time set
@endif
</span>
</div>
</td>
<td class="py-4 px-space-md">
@if($trip->bus)
<div class="flex flex-col">
<span class="font-body-md text-body-md font-semibold text-on-surface">Bus #{{ $trip->bus->bus_number }}</span>
<span class="font-body-sm text-body-sm text-outline">{{ $trip->bus->plate }}</span>
</div>
@else
<span class="inline-flex items-center gap-1 font-body-sm text-body-sm text-sync-pending font-medium">
<span class="material-symbols-outlined text-sm">error</span> Unassigned
</span>
@endif
</td>
<td class="py-4 px-space-md">
@if($trip->bus?->driver?->user)
<div class="flex items-center gap-2">
{{-- <div class="w-7 h-7 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-bold text-xs">{{ strtoupper(substr($trip->bus->driver->user->name, 0, 2)) }}</div> --}}
<div class="flex flex-col">
<span class="font-body-md text-body-md font-semibold text-on-surface">{{ $trip->bus->driver->user->name }}</span>
<span class="font-body-sm text-body-sm text-outline">{{ $trip->bus->driver->badge_id ?: 'Driver' }}</span>
</div>
</div>
@elseif($trip->bus)
<span class="inline-flex items-center gap-1 font-body-sm text-body-sm text-sync-pending font-medium">
<span class="material-symbols-outlined text-sm">info</span> No driver on bus
</span>
@else
<span class="inline-flex items-center gap-1 font-body-sm text-body-sm text-sync-pending font-medium">
<span class="material-symbols-outlined text-sm">error</span> Unassigned
</span>
@endif
</td>
<td class="py-4 px-space-md">
@if($trip->distance_km !== null)
<span class="font-label-numeric text-label-numeric text-on-surface">{{ number_format((float) $trip->distance_km, 1) }}</span>
<span class="text-xs text-outline font-normal">KM</span>
@else
<span class="text-outline">&mdash;</span>
@endif
</td>
<td class="py-4 px-space-md">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $st['badge'] }}">
<span class="w-2 h-2 rounded-full {{ $st['dot'] }}"></span>
{{ $st['label'] }}
</span>
</td>
<td class="py-4 px-space-md text-right">
<div class="flex items-center justify-end gap-1">
<button class="p-1.5 rounded-lg text-outline hover:text-primary hover:bg-surface-card transition-colors" title="Edit / Assign Trip" onclick="openEditTripModal({{ $trip->id }})">
<span class="material-symbols-outlined text-lg">edit</span>
</button>
<button class="p-1.5 rounded-lg text-outline hover:text-danger-critical hover:bg-error-container/20 transition-colors" title="Delete Trip" onclick="openDeleteTripModal({{ $trip->id }}, '{{ addslashes($trip->trip_number) }}', '{{ addslashes($trip->origin . ' -> ' . $trip->destination) }}')">
<span class="material-symbols-outlined text-lg">delete</span>
</button>
</div>
</td>
</tr>
@empty
<tr id="emptyTripsRow">
<td class="py-12 text-center text-outline" colspan="8">
<div class="flex flex-col items-center justify-center gap-2">
<span class="material-symbols-outlined text-4xl">route</span>
<span class="font-headline-sm text-headline-sm">No trips scheduled</span>
<p class="font-body-sm text-body-sm max-w-sm">Click "Schedule Trip" above to plan a route and assign a bus and driver.</p>
</div>
</td>
</tr>
@endforelse
</tbody>
</table>
</div>
<div class="px-space-md py-space-sm bg-surface-container-low flex flex-col sm:flex-row items-center justify-between gap-space-sm">
<span class="font-body-sm text-body-sm text-outline" id="tripsShowingCount">
Showing <span class="font-semibold text-on-surface">{{ count($trips) }}</span> of <span class="font-semibold text-on-surface">{{ $totalTrips }}</span> trips
</span>
<span class="font-body-sm text-body-sm text-outline">Assignments are checked for overlapping bus bookings.</span>
</div>
</div>
</div>

</div>

<!-- ================= ADD TRIP MODAL ================= -->
<div class="fixed inset-0 z-50 bg-inverse-surface/60 backdrop-blur-sm flex items-center justify-center p-space-md hidden" id="addTripModal">
<div class="bg-surface-card w-full max-w-2xl rounded-2xl shadow-xl overflow-hidden flex flex-col max-h-[92vh]">
<div class="px-space-lg py-space-md bg-surface-container-low flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-primary-container text-on-primary flex items-center justify-center">
<span class="material-symbols-outlined">route</span>
</div>
<div>
<h2 class="font-headline-md text-headline-md text-on-surface">Schedule New Trip</h2>
<p class="font-body-sm text-body-sm text-outline">Plan a route and assign a vehicle</p>
</div>
</div>
<button class="text-outline hover:text-on-surface transition-colors" onclick="toggleTripModal('addTripModal', false)">
<span class="material-symbols-outlined text-xl">close</span>
</button>
</div>
<form class="p-space-lg flex flex-col gap-space-md overflow-y-auto" id="addTripForm" method="POST" action="{{ route('trips.store') }}">
@csrf
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Origin</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" name="origin" placeholder="Central Metro Terminal" required type="text"/>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Destination</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" name="destination" placeholder="North Highlands Depot" required type="text"/>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-space-md">
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Trip Date</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" name="trip_date" required type="date" value="{{ now()->toDateString() }}"/>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Departure</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" name="departure_time" type="time"/>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Arrival</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" name="arrival_time" type="time"/>
</div>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Assign Bus</label>
<select class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" name="bus_id">
<option value="">-- Assign Bus Later --</option>
@foreach($buses as $bus)
<option value="{{ $bus->id }}">Bus #{{ $bus->bus_number }} &middot; {{ $bus->plate }}{{ $bus->driver?->user ? ' - Driver: ' . $bus->driver->user->name : ' - No driver assigned' }}</option>
@endforeach
</select>
<p class="font-body-sm text-body-sm text-outline text-xs">The driver comes from the bus. Assign a driver on the Buses page first.</p>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Distance (KM)</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" min="0" name="distance_km" placeholder="42.5" step="0.01" type="number"/>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Status</label>
<select class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" name="status">
<option value="scheduled">Scheduled</option>
<option value="in_progress">In Progress</option>
<option value="completed">Completed</option>
<option value="cancelled">Cancelled</option>
</select>
</div>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Notes</label>
<textarea class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container resize-none" name="notes" placeholder="Optional dispatch notes..." rows="2"></textarea>
</div>
<div class="flex items-center justify-end gap-space-sm pt-space-sm">
<button class="px-4 py-2.5 rounded-lg bg-surface-container-high text-on-surface font-body-md text-body-md hover:bg-surface-variant transition-colors" onclick="toggleTripModal('addTripModal', false)" type="button">Cancel</button>
<button class="flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary-container text-on-primary font-body-md text-body-md font-semibold hover:bg-primary transition-colors shadow-sm" id="addTripSubmitBtn" type="submit">
<span class="material-symbols-outlined text-lg">check</span>
<span>Save Trip</span>
</button>
</div>
</form>
</div>
</div>

<!-- ================= EDIT TRIP MODAL ================= -->
<div class="fixed inset-0 z-50 bg-inverse-surface/60 backdrop-blur-sm flex items-center justify-center p-space-md hidden" id="editTripModal">
<div class="bg-surface-card w-full max-w-2xl rounded-2xl shadow-xl overflow-hidden flex flex-col max-h-[92vh]">
<div class="px-space-lg py-space-md bg-surface-container-low flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-primary text-on-primary flex items-center justify-center">
<span class="material-symbols-outlined">edit</span>
</div>
<div>
<h2 class="font-headline-md text-headline-md text-on-surface">Edit Trip</h2>
<p class="font-body-sm text-body-sm text-outline" id="editTripSubtitle">Update trip details and assignment</p>
</div>
</div>
<button class="text-outline hover:text-on-surface transition-colors" onclick="toggleTripModal('editTripModal', false)">
<span class="material-symbols-outlined text-xl">close</span>
</button>
</div>
<form class="p-space-lg flex flex-col gap-space-md overflow-y-auto" id="editTripForm" method="POST" action="">
@csrf
@method('PUT')
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Origin</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="editTripOrigin" name="origin" required type="text"/>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Destination</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="editTripDestination" name="destination" required type="text"/>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-space-md">
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Trip Date</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="editTripDate" name="trip_date" required type="date"/>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Departure</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="editTripDeparture" name="departure_time" type="time"/>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Arrival</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="editTripArrival" name="arrival_time" type="time"/>
</div>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Assign Bus</label>
<select class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="editTripBus" name="bus_id">
<option value="">-- Unassigned --</option>
@foreach($buses as $bus)
<option value="{{ $bus->id }}">Bus #{{ $bus->bus_number }} &middot; {{ $bus->plate }}{{ $bus->driver?->user ? ' - Driver: ' . $bus->driver->user->name : ' - No driver assigned' }}</option>
@endforeach
</select>
<p class="font-body-sm text-body-sm text-outline text-xs">The driver comes from the bus. Assign a driver on the Buses page first.</p>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Distance (KM)</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="editTripDistance" min="0" name="distance_km" step="0.01" type="number"/>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Status</label>
<select class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="editTripStatus" name="status">
<option value="scheduled">Scheduled</option>
<option value="in_progress">In Progress</option>
<option value="completed">Completed</option>
<option value="cancelled">Cancelled</option>
</select>
</div>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Notes</label>
<textarea class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container resize-none" id="editTripNotes" name="notes" rows="2"></textarea>
</div>
<div class="flex items-center justify-end gap-space-sm pt-space-sm">
<button class="px-4 py-2.5 rounded-lg bg-surface-container-high text-on-surface font-body-md text-body-md hover:bg-surface-variant transition-colors" onclick="toggleTripModal('editTripModal', false)" type="button">Cancel</button>
<button class="flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary text-on-primary font-body-md text-body-md font-semibold hover:bg-primary-container transition-colors shadow-sm" id="editTripSubmitBtn" type="submit">
<span class="material-symbols-outlined text-lg">save</span>
<span>Update Trip</span>
</button>
</div>
</form>
</div>
</div>

<!-- ================= DELETE TRIP MODAL ================= -->
<div class="fixed inset-0 z-[60] bg-inverse-surface/60 backdrop-blur-md flex items-center justify-center p-space-md hidden" id="deleteTripModal">
<div class="bg-surface-card w-full max-w-md rounded-2xl shadow-2xl overflow-hidden flex flex-col delete-dialog">
<div class="relative px-space-lg pt-space-lg pb-space-md flex flex-col items-center text-center gap-3">
<div class="w-16 h-16 rounded-full bg-error-container/70 flex items-center justify-center">
<span class="material-symbols-outlined text-danger-critical text-3xl">delete_forever</span>
</div>
<div>
<h2 class="font-headline-md text-headline-md text-on-surface">Delete Trip?</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1">This action is permanent and cannot be undone.</p>
</div>
</div>
<div class="px-space-lg">
<div class="bg-surface-container-low rounded-xl p-space-md flex items-center justify-between gap-3 border border-outline-variant/30">
<div class="flex items-center gap-3 min-w-0">
<div class="w-11 h-11 rounded-lg bg-danger-critical/10 text-danger-critical flex items-center justify-center shrink-0">
<span class="material-symbols-outlined">route</span>
</div>
<div class="flex flex-col min-w-0 text-left">
<span class="font-headline-sm text-headline-sm text-on-surface font-bold truncate" id="deleteTripNumber">Trip</span>
<span class="font-body-sm text-body-sm text-outline truncate" id="deleteTripRoute">&mdash;</span>
</div>
</div>
<span class="material-symbols-outlined text-danger-critical shrink-0">warning</span>
</div>
</div>
<div class="px-space-lg pt-space-md">
<div class="bg-error-container/30 border border-danger-critical/30 rounded-xl p-3 text-xs text-danger-critical font-medium flex items-start gap-2">
<span class="material-symbols-outlined text-base shrink-0 mt-0.5">info</span>
<span>The trip record and its bus assignment will be removed from the schedule.</span>
</div>
</div>
<form class="p-space-lg flex items-center gap-3" id="deleteTripForm" method="POST" action="">
@csrf
@method('DELETE')
<button class="flex-1 px-4 py-2.5 rounded-xl bg-surface-container-high text-on-surface font-body-md text-body-md hover:bg-surface-variant transition-colors font-semibold" onclick="toggleTripModal('deleteTripModal', false)" type="button">Cancel</button>
<button class="flex-1 flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-danger-critical text-on-primary font-body-md text-body-md font-bold hover:bg-error transition-all shadow-md active:scale-95" id="deleteTripSubmitBtn" type="submit">
<span class="material-symbols-outlined text-lg">delete_forever</span>
<span>Delete Trip</span>
</button>
</form>
</div>
</div>

<!-- ================= TOAST ================= -->
<div class="fixed top-24 right-space-lg z-[70] flex flex-col gap-2 pointer-events-none" id="tripToastStack">
<div class="pointer-events-auto hidden items-center gap-3 min-w-[18rem] max-w-sm p-space-md rounded-xl shadow-xl border transition-all duration-300 translate-x-6 opacity-0" id="tripToast">
<span class="material-symbols-outlined text-xl shrink-0" id="tripToastIcon">check_circle</span>
<span class="font-body-sm text-body-sm font-medium flex-1" id="tripToastMessage"></span>
<button class="hover:opacity-70 shrink-0" onclick="hideTripToast()">
<span class="material-symbols-outlined text-base">close</span>
</button>
</div>
</div>

</div>
<script>
    const tripCsrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function toggleTripModal(modalId, show) {
      const modal = document.getElementById(modalId);
      if (!modal) return;
      modal.classList.toggle('hidden', !show);
      if (show) {
        modal.classList.remove('hidden');
        const dialog = modal.querySelector('.delete-dialog');
        if (dialog) {
          dialog.classList.remove('delete-dialog-open');
          void dialog.offsetWidth;
          dialog.classList.add('delete-dialog-open');
        }
      }
    }

    let tripToastTimer = null;
    function showTripToast(message, isError = false) {
      const toast = document.getElementById('tripToast');
      const icon = document.getElementById('tripToastIcon');
      const msg = document.getElementById('tripToastMessage');
      if (!toast) return;

      toast.classList.remove(
        'bg-secondary-container/60', 'border-secondary', 'text-on-secondary-container',
        'bg-error-container/60', 'border-danger-critical', 'text-danger-critical'
      );

      if (isError) {
        toast.classList.add('bg-error-container/60', 'border-danger-critical', 'text-danger-critical');
        icon.textContent = 'error';
      } else {
        toast.classList.add('bg-secondary-container/60', 'border-secondary', 'text-on-secondary-container');
        icon.textContent = 'check_circle';
      }

      msg.textContent = message;
      toast.classList.remove('hidden');
      toast.classList.add('flex');
      requestAnimationFrame(() => {
        toast.classList.remove('translate-x-6', 'opacity-0');
      });

      clearTimeout(tripToastTimer);
      tripToastTimer = setTimeout(hideTripToast, 5000);
    }

    function hideTripToast() {
      const toast = document.getElementById('tripToast');
      if (!toast) return;
      toast.classList.add('translate-x-6', 'opacity-0');
      setTimeout(() => {
        toast.classList.add('hidden');
        toast.classList.remove('flex');
      }, 300);
    }

    document.getElementById('addTripModalTrigger')?.addEventListener('click', () => {
      document.getElementById('addTripForm').reset();
      document.getElementById('addTripForm').querySelector('input[name="trip_date"]').value = new Date().toISOString().slice(0, 10);
      toggleTripModal('addTripModal', true);
    });

    // Populate + open the edit modal from the table row data
    function openEditTripModal(tripId) {
      const row = document.querySelector(`.trip-row[data-id="${tripId}"]`);
      if (!row) return;

      document.getElementById('editTripOrigin').value = row.dataset.origin || '';
      document.getElementById('editTripDestination').value = row.dataset.destination || '';
      document.getElementById('editTripDate').value = row.dataset.date || '';
      document.getElementById('editTripDeparture').value = row.dataset.departure || '';
      document.getElementById('editTripArrival').value = row.dataset.arrival || '';
      document.getElementById('editTripBus').value = row.dataset.busId || '';
      document.getElementById('editTripDistance').value = row.dataset.distance || '';
      document.getElementById('editTripStatus').value = row.dataset.status || 'scheduled';
      document.getElementById('editTripNotes').value = row.dataset.notes || '';
      document.getElementById('editTripSubtitle').textContent = `Updating ${row.dataset.tripNumber} · ${row.dataset.origin} to ${row.dataset.destination}`;

      document.getElementById('editTripForm').action = `/trips/${tripId}`;
      toggleTripModal('editTripModal', true);
    }

    function openDeleteTripModal(tripId, tripNumber, routeLabel) {
      document.getElementById('deleteTripNumber').textContent = tripNumber;
      document.getElementById('deleteTripRoute').textContent = routeLabel;
      document.getElementById('deleteTripForm').action = `/trips/${tripId}`;
      toggleTripModal('deleteTripModal', true);
    }

    async function submitTripForm(formId, buttonId, loadingLabel) {
      const form = document.getElementById(formId);
      const btn = document.getElementById(buttonId);
      if (!form || !btn) return;

      const originalHtml = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = `<span class="material-symbols-outlined text-lg animate-spin">progress_activity</span><span>${loadingLabel}</span>`;

      try {
        const response = await fetch(form.action, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': tripCsrfToken,
            'Accept': 'application/json',
          },
          body: new FormData(form),
        });

        let data = {};
        try { data = await response.json(); } catch (e) { data = {}; }

        if (response.ok && data.success) {
          toggleTripModal(formId.replace('Form', 'Modal'), false);
          showTripToast(data.message || 'Operation completed.');
          setTimeout(() => window.location.reload(), 900);
        } else {
          const errors = data.errors
            ? Object.values(data.errors).flat().join(' · ')
            : (data.message || 'Something went wrong.');
          showTripToast(errors, true);
        }
      } catch (err) {
        showTripToast('Network error. Please try again.', true);
      } finally {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
      }
    }

    document.getElementById('addTripForm')?.addEventListener('submit', (e) => {
      e.preventDefault();
      submitTripForm('addTripForm', 'addTripSubmitBtn', 'Saving...');
    });

    document.getElementById('editTripForm')?.addEventListener('submit', (e) => {
      e.preventDefault();
      submitTripForm('editTripForm', 'editTripSubmitBtn', 'Updating...');
    });

    document.getElementById('deleteTripForm')?.addEventListener('submit', (e) => {
      e.preventDefault();
      submitTripForm('deleteTripForm', 'deleteTripSubmitBtn', 'Deleting...');
    });

    // Status filter pills
    const tripFilterPills = document.querySelectorAll('.filter-pill');
    tripFilterPills.forEach(pill => {
      pill.addEventListener('click', () => {
        tripFilterPills.forEach(p => {
          p.classList.remove('bg-primary-container', 'text-on-primary', 'shadow-xs');
          p.classList.add('bg-surface-container-low', 'text-on-surface');
        });
        pill.classList.remove('bg-surface-container-low', 'text-on-surface');
        pill.classList.add('bg-primary-container', 'text-on-primary', 'shadow-xs');
        applyTripFilters();
      });
    });

    const tripSearch = document.getElementById('tripSearch');
    const tripDateFilter = document.getElementById('tripDateFilter');

    function applyTripFilters() {
      const query = (tripSearch?.value || '').toLowerCase().trim();
      const activePill = document.querySelector('.filter-pill.bg-primary-container');
      const statusFilter = activePill ? activePill.dataset.filter : 'all';
      const dateValue = tripDateFilter ? tripDateFilter.value : '';

      let visible = 0;
      document.querySelectorAll('.trip-row').forEach(row => {
        const haystack = [
          row.dataset.tripNumber, row.dataset.origin, row.dataset.destination,
          row.dataset.dateLabel, row.dataset.departure, row.dataset.arrival, row.dataset.status
        ].join(' ').toLowerCase();

        const matchesSearch = !query || haystack.includes(query);
        const matchesStatus = statusFilter === 'all' || row.dataset.status === statusFilter;
        const matchesDate = !dateValue || row.dataset.date === dateValue;

        const show = matchesSearch && matchesStatus && matchesDate;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
      });

      const countEl = document.getElementById('tripsShowingCount');
      if (countEl) countEl.querySelector('span').textContent = visible;
    }

    tripSearch?.addEventListener('input', applyTripFilters);
    tripDateFilter?.addEventListener('change', applyTripFilters);

    // CSV Export
    document.getElementById('exportTripsCsvBtn')?.addEventListener('click', () => {
      let csv = 'Trip Ref,Origin,Destination,Date,Departure,Arrival,Distance (KM),Status\n';
      document.querySelectorAll('.trip-row').forEach(r => {
        csv += `"${r.dataset.tripNumber}","${r.dataset.origin}","${r.dataset.destination}","${r.dataset.dateLabel}","${r.dataset.departure}","${r.dataset.arrival}","${r.dataset.distance}","${r.dataset.status}"\n`;
      });
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.setAttribute('href', url);
      link.setAttribute('download', `trips_schedule_${new Date().toISOString().slice(0, 10)}.csv`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    });
</script></main></div></body>
</html>
