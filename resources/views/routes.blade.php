<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Transit Routes &bull; {{ config('app.name', 'Lancotri') }}</title>
    @include('partials.brand-styles')
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/lancotri-mark.svg') }}">
    <!-- Leaflet CSS for Map rendering -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        .leaflet-container {
            font-family: inherit;
            border-radius: 0.75rem;
            z-index: 10;
        }
    </style>
</head>
<body class="bg-background text-foreground antialiased min-h-screen">
    @include('partials.sidebar')

    <div class="pl-64 flex flex-col min-h-screen">
        @include('partials.topbar', [
            'topbarSearchPlaceholder' => 'Search transit route code, name, or terminal...',
            'topbarSearchId' => 'routeNavSearch',
            'topbarStatus' => $activeRoutes . ' active corridors',
        ])

        <main class="w-full pt-16 flex-1 px-8 py-8 bg-background">
            <div class="flex flex-col w-full gap-6 max-w-7xl mx-auto">

                <!-- Page Header: Title + Action Buttons -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b">
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-xs text-muted-foreground font-mono">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-muted border">
                                <i data-lucide="route" class="w-3.5 h-3.5 text-primary"></i>
                                <span>TRANSIT NETWORK MANAGEMENT</span>
                            </span>
                            <span>&bull;</span>
                            <span class="inline-flex items-center gap-1.5 text-emerald-600 font-medium">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                Live Map Corridors
                            </span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">Transit Routes &amp; Corridors</h1>
                            <p class="text-xs text-muted-foreground mt-2 leading-relaxed max-w-2xl">
                                Define bus corridors, manage waypoints, map paths, and assign vehicles to scheduled routes with live GPS sync.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5">
                        <div class="relative w-64">
                            <i data-lucide="search" class="w-3.5 h-3.5 text-muted-foreground absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <input id="routeSearch"
                                   placeholder="Search route code, name..."
                                   class="flex h-9 w-full rounded-md border border-input bg-transparent pl-9 pr-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                        </div>

                        <button type="button" onclick="openAddRouteModal()"
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium bg-primary text-primary-foreground shadow-xs hover:bg-primary/90 h-9 px-4 py-2 gap-2">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Create Route</span>
                        </button>
                    </div>
                </div>

                <!-- 4 Metrics Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="rounded-xl border bg-card text-card-foreground shadow-xs p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">Total Routes</span>
                            <i data-lucide="route" class="w-4 h-4 text-primary"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono text-foreground">{{ $totalRoutes }}</div>
                            <span class="text-xs text-muted-foreground">Configured lines</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-xs p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">Active Corridors</span>
                            <i data-lucide="activity" class="w-4 h-4 text-emerald-600"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono text-emerald-600">{{ $activeRoutes }}</div>
                            <span class="text-xs text-muted-foreground">In daily service</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-xs p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">Total Track Distance</span>
                            <i data-lucide="navigation-2" class="w-4 h-4 text-blue-500"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono text-foreground">{{ $totalDistance }} <span class="text-sm font-normal text-muted-foreground">km</span></div>
                            <span class="text-xs text-muted-foreground">Combined network</span>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card text-card-foreground shadow-xs p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono uppercase text-muted-foreground">Assigned Vehicles</span>
                            <i data-lucide="bus" class="w-4 h-4 text-amber-500"></i>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold font-mono text-amber-500">{{ $assignedBusesCount }}</div>
                            <span class="text-xs text-muted-foreground">Buses en route</span>
                        </div>
                    </div>
                </div>

                <!-- Interactive Route Map Card -->
                <div class="rounded-xl border bg-card text-card-foreground shadow-xs overflow-hidden">
                    <div class="p-4 border-b bg-muted/30 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="p-2 rounded-lg bg-primary/10 text-primary">
                                <i data-lucide="map" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-sm text-foreground flex items-center gap-2">
                                    <span>Interactive Corridor Map</span>
                                    <span id="activeRouteBadge" class="text-[11px] font-mono px-2 py-0.5 rounded-full bg-primary/10 text-primary border border-primary/20">
                                        All Routes
                                    </span>
                                </h3>
                                <p class="text-xs text-muted-foreground">Click any route in the table below to zoom and highlight its corridor path and assigned buses.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" onclick="resetRouteMap()" class="text-xs font-medium px-2.5 py-1.5 rounded-md border bg-background hover:bg-muted text-muted-foreground transition">
                                Reset View
                            </button>
                        </div>
                    </div>

                    <!-- Leaflet Container -->
                    <div id="routeMap" class="w-full h-80 sm:h-96 bg-muted/20"></div>
                </div>

                <!-- Routes Table Card -->
                <div class="rounded-xl border bg-card text-card-foreground shadow-xs overflow-hidden">
                    <div class="p-4 border-b flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i data-lucide="list" class="w-4 h-4 text-primary"></i>
                            <h2 class="text-sm font-semibold tracking-tight">Configured Routes ({{ $routes->count() }})</h2>
                        </div>
                        <span class="text-xs text-muted-foreground font-mono">Real-time GPS Integrated</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b bg-muted/40 font-mono text-muted-foreground text-[11px]">
                                    <th class="py-3 px-4">CODE</th>
                                    <th class="py-3 px-4">ROUTE NAME</th>
                                    <th class="py-3 px-4">FROM &bull; TO</th>
                                    <th class="py-3 px-4">DISTANCE &bull; TIME</th>
                                    <th class="py-3 px-4">ASSIGNED BUSES</th>
                                    <th class="py-3 px-4">STATUS</th>
                                    <th class="py-3 px-4 text-right">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @forelse($routes as $route)
                                    @php
                                        $busList = $route->buses;
                                    @endphp
                                    <tr class="hover:bg-muted/40 transition cursor-pointer route-row"
                                        id="route-row-{{ $route->id }}"
                                        onclick="highlightRouteOnMap({{ $route->id }})">
                                        <td class="py-3.5 px-4 font-mono font-bold text-primary">
                                            {{ $route->route_code }}
                                        </td>
                                        <td class="py-3.5 px-4 font-medium text-foreground">
                                            <div class="font-semibold">{{ $route->name }}</div>
                                            @if($route->notes)
                                                <div class="text-[11px] text-muted-foreground truncate max-w-xs">{{ $route->notes }}</div>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="flex flex-col gap-0.5">
                                                <span class="text-foreground font-medium flex items-center gap-1.5">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    {{ $route->origin }}
                                                </span>
                                                <span class="text-muted-foreground flex items-center gap-1.5">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                    {{ $route->destination }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 font-mono font-medium">
                                            {{ $route->distance_km }} km
                                            <span class="text-[10px] text-muted-foreground block">~{{ $route->estimated_minutes }} mins</span>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            @if($busList->count() > 0)
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($busList as $b)
                                                        <span class="px-2 py-0.5 rounded-md font-mono text-[10px] bg-primary/10 text-primary border border-primary/20">
                                                            Bus #{{ $b->bus_number }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted-foreground italic text-[11px]">No bus assigned</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4">
                                            @if($route->status === 'active')
                                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-full">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-muted-foreground bg-muted border px-2 py-0.5 rounded-full">
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-right" onclick="event.stopPropagation()">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <button type="button"
                                                        onclick="openAssignBusModal({{ $route->id }}, '{{ $route->route_code }}', {{ json_encode($busList->pluck('id')) }})"
                                                        title="Assign Buses"
                                                        class="p-1.5 rounded-md hover:bg-muted text-muted-foreground hover:text-foreground transition">
                                                    <i data-lucide="bus" class="w-4 h-4"></i>
                                                </button>
                                                <button type="button"
                                                        onclick="editRoute({{ json_encode($route) }})"
                                                        title="Edit Route"
                                                        class="p-1.5 rounded-md hover:bg-muted text-muted-foreground hover:text-foreground transition">
                                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                                </button>
                                                <button type="button"
                                                        onclick="deleteRoute({{ $route->id }}, '{{ $route->route_code }}')"
                                                        title="Delete Route"
                                                        class="p-1.5 rounded-md hover:bg-red-500/10 text-muted-foreground hover:text-red-500 transition">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-12 text-muted-foreground">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <i data-lucide="route" class="w-8 h-8 text-muted-foreground/50"></i>
                                                <p class="font-medium">No transit routes found.</p>
                                                <button type="button" onclick="openAddRouteModal()" class="text-xs text-primary font-medium hover:underline">
                                                    Create your first corridor now &rarr;
                                                </button>
                                            </div>
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

    <!-- ADD / EDIT ROUTE MODAL -->
    <div id="routeModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-card border border-border w-full max-w-xl rounded-xl shadow-xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="p-4 border-b flex items-center justify-between bg-muted/30">
                <div class="flex items-center gap-2">
                    <i data-lucide="route" class="w-5 h-5 text-primary"></i>
                    <h3 id="modalTitle" class="font-bold text-base text-foreground">Create Transit Corridor</h3>
                </div>
                <button type="button" onclick="closeRouteModal()" class="p-1 rounded-md text-muted-foreground hover:text-foreground">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="routeForm" method="POST" action="{{ route('routes.store') }}" class="p-5 overflow-y-auto space-y-4">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="path_coordinates" id="input_path_coordinates" value="">
                <input type="hidden" name="waypoints" id="input_waypoints" value="">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-foreground">Route Code *</label>
                        <input type="text" name="route_code" id="input_route_code" required placeholder="e.g. RT-101"
                               class="w-full text-xs font-mono px-3 py-2 rounded-md border bg-background text-foreground focus:ring-1 focus:ring-primary"/>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-foreground">Corridor Name *</label>
                        <input type="text" name="name" id="input_name" required placeholder="e.g. Airport Express Line"
                               class="w-full text-xs px-3 py-2 rounded-md border bg-background text-foreground focus:ring-1 focus:ring-primary"/>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <!-- Origin with Google Maps-like auto-suggestions -->
                    <div class="relative">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-foreground">Route From (Origin) *</label>
                            <span class="text-[10px] text-emerald-600 font-mono">Auto-suggest &bull; GPS</span>
                        </div>
                        <div class="relative">
                            <input type="text" name="origin" id="input_origin" required placeholder="Type city or terminal (e.g. Central Metro)..."
                                   autocomplete="off"
                                   class="w-full text-xs px-3 py-2 rounded-md border bg-background text-foreground focus:ring-1 focus:ring-primary"/>
                            <div id="originLoader" class="absolute right-2.5 top-1/2 -translate-y-1/2 hidden">
                                <span class="animate-spin text-primary text-xs">&#9696;</span>
                            </div>
                        </div>
                        <div id="originSuggestions" class="absolute left-0 right-0 top-full mt-1 bg-card border border-border rounded-lg shadow-xl z-50 hidden max-h-48 overflow-y-auto divide-y divide-border/60"></div>
                    </div>

                    <!-- Destination with Google Maps-like auto-suggestions -->
                    <div class="relative">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-foreground">Route To (Destination) *</label>
                            <span class="text-[10px] text-emerald-600 font-mono">Auto-suggest &bull; GPS</span>
                        </div>
                        <div class="relative">
                            <input type="text" name="destination" id="input_destination" required placeholder="Type airport or station (e.g. Airport T2)..."
                                   autocomplete="off"
                                   class="w-full text-xs px-3 py-2 rounded-md border bg-background text-foreground focus:ring-1 focus:ring-primary"/>
                            <div id="destLoader" class="absolute right-2.5 top-1/2 -translate-y-1/2 hidden">
                                <span class="animate-spin text-primary text-xs">&#9696;</span>
                            </div>
                        </div>
                        <div id="destSuggestions" class="absolute left-0 right-0 top-full mt-1 bg-card border border-border rounded-lg shadow-xl z-50 hidden max-h-48 overflow-y-auto divide-y divide-border/60"></div>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-2 text-xs">
                    <div>
                        <label class="block font-semibold mb-1 text-muted-foreground">From Lat</label>
                        <input type="number" step="0.0001" name="origin_lat" id="input_origin_lat" placeholder="24.8607"
                               onchange="calculateAutoDrivingRoute()"
                               class="w-full text-xs font-mono px-2 py-1.5 rounded-md border bg-background text-foreground"/>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1 text-muted-foreground">From Lng</label>
                        <input type="number" step="0.0001" name="origin_lng" id="input_origin_lng" placeholder="67.0011"
                               onchange="calculateAutoDrivingRoute()"
                               class="w-full text-xs font-mono px-2 py-1.5 rounded-md border bg-background text-foreground"/>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1 text-muted-foreground">To Lat</label>
                        <input type="number" step="0.0001" name="destination_lat" id="input_destination_lat" placeholder="24.9065"
                               onchange="calculateAutoDrivingRoute()"
                               class="w-full text-xs font-mono px-2 py-1.5 rounded-md border bg-background text-foreground"/>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1 text-muted-foreground">To Lng</label>
                        <input type="number" step="0.0001" name="destination_lng" id="input_destination_lng" placeholder="67.1608"
                               onchange="calculateAutoDrivingRoute()"
                               class="w-full text-xs font-mono px-2 py-1.5 rounded-md border bg-background text-foreground"/>
                    </div>
                </div>

                <!-- Live Auto-Calculated Driving Route Canvas Preview -->
                <div class="rounded-lg border border-border/80 overflow-hidden bg-muted/20">
                    <div class="px-3 py-1.5 bg-muted/40 border-b flex items-center justify-between text-xs">
                        <div class="flex items-center gap-1.5 text-muted-foreground">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="font-semibold text-foreground text-[11px]">Auto Driving Route Preview</span>
                        </div>
                        <span id="routeCalcBadge" class="text-[10px] font-mono text-primary font-semibold">
                            Enter endpoints to calculate road path
                        </span>
                    </div>
                    <div id="modalRouteMap" class="w-full h-32 relative bg-muted/10"></div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-foreground">Distance (km)</label>
                        <input type="number" step="0.01" name="distance_km" id="input_distance_km" placeholder="28.5"
                               class="w-full text-xs font-mono px-3 py-2 rounded-md border bg-background text-foreground"/>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-foreground">Est. Duration (mins)</label>
                        <input type="number" name="estimated_minutes" id="input_estimated_minutes" placeholder="45"
                               class="w-full text-xs font-mono px-3 py-2 rounded-md border bg-background text-foreground"/>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-foreground">Status</label>
                        <select name="status" id="input_status" class="w-full text-xs px-3 py-2 rounded-md border bg-background text-foreground">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1 text-foreground">Route Notes &amp; Dispatch Instructions</label>
                    <textarea name="notes" id="input_notes" rows="2" placeholder="Express route, avoids downtown congestion during peak hours..."
                              class="w-full text-xs px-3 py-2 rounded-md border bg-background text-foreground"></textarea>
                </div>

                <div class="pt-2 border-t flex items-center justify-end gap-2">
                    <button type="button" onclick="closeRouteModal()"
                            class="px-4 py-2 text-xs font-medium rounded-md border bg-background hover:bg-muted text-muted-foreground">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-xs font-semibold rounded-md bg-primary text-primary-foreground hover:bg-primary/90">
                        Save Corridor
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ASSIGN BUSES MODAL -->
    <div id="assignBusModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-card border border-border w-full max-w-md rounded-xl shadow-xl overflow-hidden">
            <div class="p-4 border-b flex items-center justify-between bg-muted/30">
                <div class="flex items-center gap-2">
                    <i data-lucide="bus" class="w-5 h-5 text-primary"></i>
                    <h3 class="font-bold text-base text-foreground">Assign Fleet Buses</h3>
                </div>
                <button type="button" onclick="closeAssignBusModal()" class="p-1 rounded-md text-muted-foreground hover:text-foreground">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="assignBusForm" method="POST" class="p-5 space-y-4">
                @csrf
                <p class="text-xs text-muted-foreground">
                    Select the buses to deploy on route <strong id="assignRouteCode" class="text-foreground font-mono"></strong>.
                </p>

                <div class="max-h-60 overflow-y-auto space-y-2 border rounded-lg p-3 bg-muted/20">
                    @foreach($allBuses as $b)
                        <label class="flex items-center justify-between p-2 rounded-md hover:bg-muted/60 transition cursor-pointer text-xs">
                            <div class="flex items-center gap-2.5">
                                <input type="checkbox" name="bus_ids[]" value="{{ $b->id }}"
                                       class="rounded border-input text-primary focus:ring-primary bus-checkbox-{{ $b->id }}"/>
                                <span class="font-mono font-bold text-foreground">Bus #{{ $b->bus_number }}</span>
                                <span class="text-muted-foreground text-[11px] font-mono">({{ $b->plate }})</span>
                            </div>
                            <span class="text-[11px] text-muted-foreground">
                                {{ $b->driver?->user?->name ?? 'No Driver' }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <div class="pt-2 border-t flex items-center justify-end gap-2">
                    <button type="button" onclick="closeAssignBusModal()"
                            class="px-4 py-2 text-xs font-medium rounded-md border bg-background hover:bg-muted text-muted-foreground">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-xs font-semibold rounded-md bg-primary text-primary-foreground hover:bg-primary/90">
                        Update Assignments
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        const routesData = @json($routes);
        let map;
        let routeLayers = {};
        let activeRouteId = null;

        document.addEventListener('DOMContentLoaded', function () {
            initRouteMap();

            // Search filter
            document.getElementById('routeSearch')?.addEventListener('input', function (e) {
                const query = e.target.value.toLowerCase();
                document.querySelectorAll('.route-row').forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
                });
            });
        });

        function initRouteMap() {
            // Default center on first route or metropolitan area
            const defaultLat = routesData[0]?.origin_lat || 24.8607;
            const defaultLng = routesData[0]?.origin_lng || 67.0011;

            map = L.map('routeMap').setView([defaultLat, defaultLng], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            renderAllRoutes();
        }

        const routeColors = ['#BD1C23', '#2563EB', '#059669', '#7C3AED', '#D97706'];

        function renderAllRoutes() {
            // Clear existing
            Object.values(routeLayers).forEach(layer => map.removeLayer(layer));
            routeLayers = {};

            const allLatLngs = [];

            routesData.forEach((route, index) => {
                const color = routeColors[index % routeColors.length];
                const group = L.featureGroup();

                // Polyline path
                let pathPoints = [];
                if (route.path_coordinates && route.path_coordinates.length > 0) {
                    pathPoints = route.path_coordinates;
                } else if (route.origin_lat && route.destination_lat) {
                    pathPoints = [
                        [route.origin_lat, route.origin_lng],
                        [route.destination_lat, route.destination_lng]
                    ];
                }

                if (pathPoints.length > 0) {
                    const polyline = L.polyline(pathPoints, {
                        color: color,
                        weight: 4,
                        opacity: 0.85,
                        dashArray: '8, 8'
                    }).bindPopup(`<strong>${route.route_code}: ${route.name}</strong><br/>${route.distance_km} km &bull; ~${route.estimated_minutes} mins`);

                    polyline.addTo(group);
                    pathPoints.forEach(p => allLatLngs.push(p));
                }

                // Origin Marker (Green)
                if (route.origin_lat && route.origin_lng) {
                    const originMarker = L.circleMarker([route.origin_lat, route.origin_lng], {
                        radius: 7,
                        color: '#10B981',
                        fillColor: '#10B981',
                        fillOpacity: 1
                    }).bindPopup(`<strong>${route.route_code} From (Origin):</strong><br/>${route.origin}`);
                    originMarker.addTo(group);
                }

                // Destination Marker (Red - Point B / To)
                if (route.destination_lat && route.destination_lng) {
                    const destMarker = L.circleMarker([route.destination_lat, route.destination_lng], {
                        radius: 7,
                        color: '#EF4444',
                        fillColor: '#EF4444',
                        fillOpacity: 1
                    }).bindPopup(`<strong>${route.route_code} To (Destination):</strong><br/>${route.destination}`);
                    destMarker.addTo(group);
                }

                group.addTo(map);
                routeLayers[route.id] = group;
            });

            if (allLatLngs.length > 0) {
                map.fitBounds(allLatLngs, { padding: [30, 30] });
            }
        }

        function highlightRouteOnMap(routeId) {
            activeRouteId = routeId;
            const targetRoute = routesData.find(r => r.id === routeId);
            if (!targetRoute) return;

            document.getElementById('activeRouteBadge').innerText = `${targetRoute.route_code} • ${targetRoute.name}`;

            // Highlight row in table
            document.querySelectorAll('.route-row').forEach(row => row.classList.remove('bg-primary/10', 'border-l-4', 'border-primary'));
            const activeRow = document.getElementById(`route-row-${routeId}`);
            if (activeRow) {
                activeRow.classList.add('bg-primary/10', 'border-l-4', 'border-primary');
            }

            // Dim other route layers
            Object.keys(routeLayers).forEach(id => {
                const layer = routeLayers[id];
                if (parseInt(id) === routeId) {
                    layer.eachLayer(l => {
                        if (l instanceof L.Polyline) {
                            l.setStyle({ weight: 6, opacity: 1, dashArray: null });
                        }
                    });
                    map.fitBounds(layer.getBounds(), { padding: [40, 40] });
                } else {
                    layer.eachLayer(l => {
                        if (l instanceof L.Polyline) {
                            l.setStyle({ weight: 2, opacity: 0.3, dashArray: '6, 6' });
                        }
                    });
                }
            });
        }

        function resetRouteMap() {
            activeRouteId = null;
            document.getElementById('activeRouteBadge').innerText = 'All Routes';
            document.querySelectorAll('.route-row').forEach(row => row.classList.remove('bg-primary/10', 'border-l-4', 'border-primary'));
            renderAllRoutes();
        }

        // Modal Controls & Autocomplete
        let modalMap = null;
        let modalRouteLayer = null;

        function initModalRouteMap() {
            if (modalMap) {
                modalMap.invalidateSize();
                return;
            }
            const el = document.getElementById('modalRouteMap');
            if (!el) return;

            modalMap = L.map('modalRouteMap', {
                zoomControl: false,
                attributionControl: false
            }).setView([24.8607, 67.0011], 11);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(modalMap);
        }

        function openAddRouteModal() {
            document.getElementById('modalTitle').innerText = 'Create Transit Corridor';
            document.getElementById('routeForm').action = "{{ route('routes.store') }}";
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('routeForm').reset();
            document.getElementById('input_path_coordinates').value = '';
            document.getElementById('input_waypoints').value = '';
            document.getElementById('routeCalcBadge').innerText = 'Enter endpoints to calculate road path';
            document.getElementById('routeModal').classList.remove('hidden');

            setTimeout(() => {
                initModalRouteMap();
                if (modalRouteLayer) modalMap.removeLayer(modalRouteLayer);
            }, 100);
        }

        function editRoute(route) {
            document.getElementById('modalTitle').innerText = `Edit Route ${route.route_code}`;
            document.getElementById('routeForm').action = `/routes/${route.id}`;
            document.getElementById('formMethod').value = 'PUT';

            document.getElementById('input_route_code').value = route.route_code || '';
            document.getElementById('input_name').value = route.name || '';
            document.getElementById('input_origin').value = route.origin || '';
            document.getElementById('input_destination').value = route.destination || '';
            document.getElementById('input_origin_lat').value = route.origin_lat || '';
            document.getElementById('input_origin_lng').value = route.origin_lng || '';
            document.getElementById('input_destination_lat').value = route.destination_lat || '';
            document.getElementById('input_destination_lng').value = route.destination_lng || '';
            document.getElementById('input_distance_km').value = route.distance_km || '';
            document.getElementById('input_estimated_minutes').value = route.estimated_minutes || '';
            document.getElementById('input_status').value = route.status || 'active';
            document.getElementById('input_notes').value = route.notes || '';
            document.getElementById('input_path_coordinates').value = JSON.stringify(route.path_coordinates || []);
            document.getElementById('input_waypoints').value = JSON.stringify(route.waypoints || []);

            document.getElementById('routeModal').classList.remove('hidden');

            setTimeout(() => {
                initModalRouteMap();
                calculateAutoDrivingRoute();
            }, 100);
        }

        function closeRouteModal() {
            document.getElementById('routeModal').classList.add('hidden');
            hideSuggestions('originSuggestions');
            hideSuggestions('destSuggestions');
        }

        // ================= Google Maps-like Place Autocomplete =================
        let searchDebounceTimer = null;

        function setupAutocomplete(inputId, loaderId, dropdownId, onSelectCallback) {
            const input = document.getElementById(inputId);
            const loader = document.getElementById(loaderId);
            const dropdown = document.getElementById(dropdownId);
            if (!input || !dropdown) return;

            input.addEventListener('input', function (e) {
                const query = e.target.value.trim();
                clearTimeout(searchDebounceTimer);

                if (query.length < 3) {
                    dropdown.classList.add('hidden');
                    return;
                }

                if (loader) loader.classList.remove('hidden');

                searchDebounceTimer = setTimeout(async () => {
                    try {
                        const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&addressdetails=1`;
                        const res = await fetch(url, { headers: { 'Accept-Language': 'en' } });
                        const data = await res.json();

                        dropdown.innerHTML = '';
                        if (loader) loader.classList.add('hidden');

                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach(item => {
                                const cleanTitle = item.name || item.display_name.split(',')[0];
                                const subtitle = item.display_name.split(',').slice(1, 4).join(', ');

                                const div = document.createElement('div');
                                div.className = 'px-3 py-2 hover:bg-muted/60 cursor-pointer text-xs flex items-start gap-2 transition-colors';
                                div.innerHTML = `
                                    <span class="text-primary mt-0.5 text-xs">&#9678;</span>
                                    <div class="flex-1 overflow-hidden">
                                        <div class="font-semibold text-foreground truncate">${cleanTitle}</div>
                                        <div class="text-[10px] text-muted-foreground truncate">${subtitle}</div>
                                    </div>
                                `;
                                div.addEventListener('click', () => {
                                    onSelectCallback(item, cleanTitle);
                                    dropdown.classList.add('hidden');
                                });
                                dropdown.appendChild(div);
                            });
                            dropdown.classList.remove('hidden');
                        } else {
                            dropdown.classList.add('hidden');
                        }
                    } catch (err) {
                        if (loader) loader.classList.add('hidden');
                        console.warn('Place autocomplete error', err);
                    }
                }, 300);
            });
        }

        function hideSuggestions(id) {
            const el = document.getElementById(id);
            if (el) el.classList.add('hidden');
        }

        // Initialize autocompletes once DOM loads
        document.addEventListener('DOMContentLoaded', () => {
            setupAutocomplete('input_origin', 'originLoader', 'originSuggestions', (item, title) => {
                document.getElementById('input_origin').value = title;
                document.getElementById('input_origin_lat').value = parseFloat(item.lat).toFixed(4);
                document.getElementById('input_origin_lng').value = parseFloat(item.lon).toFixed(4);
                calculateAutoDrivingRoute();
            });

            setupAutocomplete('input_destination', 'destLoader', 'destSuggestions', (item, title) => {
                document.getElementById('input_destination').value = title;
                document.getElementById('input_destination_lat').value = parseFloat(item.lat).toFixed(4);
                document.getElementById('input_destination_lng').value = parseFloat(item.lon).toFixed(4);
                calculateAutoDrivingRoute();
            });

            // Close dropdowns on outside click
            document.addEventListener('click', (e) => {
                if (!e.target.closest('#input_origin') && !e.target.closest('#originSuggestions')) {
                    hideSuggestions('originSuggestions');
                }
                if (!e.target.closest('#input_destination') && !e.target.closest('#destSuggestions')) {
                    hideSuggestions('destSuggestions');
                }
            });
        });

        // ================= Automated Road Driving Route (OSRM / OpenStreetMap) =================
        async function calculateAutoDrivingRoute() {
            const oLat = parseFloat(document.getElementById('input_origin_lat')?.value);
            const oLng = parseFloat(document.getElementById('input_origin_lng')?.value);
            const dLat = parseFloat(document.getElementById('input_destination_lat')?.value);
            const dLng = parseFloat(document.getElementById('input_destination_lng')?.value);

            if (isNaN(oLat) || isNaN(oLng) || isNaN(dLat) || isNaN(dLng)) return;

            const badge = document.getElementById('routeCalcBadge');
            if (badge) badge.innerText = 'Calculating driving route...';

            try {
                const url = `https://router.project-osrm.org/route/v1/driving/${oLng},${oLat};${dLng},${dLat}?overview=full&geometries=geojson`;
                const res = await fetch(url);
                const data = await res.json();

                if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
                    const osrmRoute = data.routes[0];
                    const distanceKm = (osrmRoute.distance / 1000).toFixed(2);
                    const durationMins = Math.round(osrmRoute.duration / 60);

                    // Auto-fill distance and duration
                    document.getElementById('input_distance_km').value = distanceKm;
                    document.getElementById('input_estimated_minutes').value = durationMins;

                    // Convert geojson [lng, lat] to Leaflet [lat, lng]
                    const pathCoords = osrmRoute.geometry.coordinates.map(c => [c[1], c[0]]);
                    document.getElementById('input_path_coordinates').value = JSON.stringify(pathCoords);

                    const originName = document.getElementById('input_origin').value || 'Origin Terminal';
                    const destName = document.getElementById('input_destination').value || 'Destination Terminal';

                    // Direct route from Origin to Destination (no intermediate stops)
                    const waypoints = [
                        { name: originName, lat: oLat, lng: oLng, type: 'origin' },
                        { name: destName, lat: dLat, lng: dLng, type: 'destination' }
                    ];
                    document.getElementById('input_waypoints').value = JSON.stringify(waypoints);

                    if (badge) {
                        badge.innerText = `Auto-calculated: ${distanceKm} km \u2022 ~${durationMins} mins`;
                    }

                    // Render on modal preview map
                    if (modalMap) {
                        if (modalRouteLayer) modalMap.removeLayer(modalRouteLayer);
                        modalRouteLayer = L.featureGroup();

                        // Route polyline
                        L.polyline(pathCoords, {
                            color: '#2563EB',
                            weight: 4,
                            opacity: 0.9
                        }).addTo(modalRouteLayer);

                        // Origin pin (A)
                        L.circleMarker([oLat, oLng], {
                            radius: 6,
                            color: '#10B981',
                            fillColor: '#10B981',
                            fillOpacity: 1
                        }).bindPopup('<b>From: Origin</b>').addTo(modalRouteLayer);

                        // Destination pin (B - To)
                        L.circleMarker([dLat, dLng], {
                            radius: 6,
                            color: '#EF4444',
                            fillColor: '#EF4444',
                            fillOpacity: 1
                        }).bindPopup('<b>To: Destination</b>').addTo(modalRouteLayer);

                        modalRouteLayer.addTo(modalMap);
                        modalMap.fitBounds(modalRouteLayer.getBounds(), { padding: [15, 15] });
                    }
                }
            } catch (err) {
                console.warn('Auto routing API error', err);
                if (badge) badge.innerText = 'Straight-line coordinates set';
            }
        }

        function openAssignBusModal(routeId, routeCode, assignedBusIds) {
            document.getElementById('assignRouteCode').innerText = routeCode;
            document.getElementById('assignBusForm').action = `/routes/${routeId}/assign`;

            // Clear checkboxes
            document.querySelectorAll('#assignBusForm input[type="checkbox"]').forEach(cb => cb.checked = false);

            // Check existing
            if (Array.isArray(assignedBusIds)) {
                assignedBusIds.forEach(id => {
                    const cb = document.querySelector(`.bus-checkbox-${id}`);
                    if (cb) cb.checked = true;
                });
            }

            document.getElementById('assignBusModal').classList.remove('hidden');
        }

        function closeAssignBusModal() {
            document.getElementById('assignBusModal').classList.add('hidden');
        }

        function deleteRoute(routeId, routeCode) {
            if (confirm(`Are you sure you want to delete route ${routeCode}? Associated buses will be unassigned.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/routes/${routeId}`;

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = document.querySelector('meta[name="csrf-token"]').content;

                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';

                form.appendChild(csrf);
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
