<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <meta content="web_dashboard" name="shell-type"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com" rel="preconnect"/>
  <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;family=Sora:wght@400;500;600;700&amp;family=Poppins:wght@600;700&amp;display=swap" rel="stylesheet"/>
  <title>Driver Roster &amp; Access &bull; Lancotri</title>
  <style>
    @layer base {
      html, body { margin: 0; padding: 0; }
      body { overscroll-behavior: none; }
      main > :first-child { margin-top: 0 !important; }
      main > :last-child { margin-bottom: 0 !important; }
    }
    ::-webkit-scrollbar { display: none; }
  </style>
  <script src="https://cdn.tailwindcss.com"></script>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "outline-variant": "#D1D5DB",
            "on-surface": "#111827",
            "surface-variant": "#E1E5EC",
            "inverse-on-surface": "#F1F5F9",
            "on-primary": "#ffffff",
            "surface-container-high": "#E1E5EC",
            "on-secondary": "#ffffff",
            "secondary": "#0A81B4",
            "primary-fixed-dim": "#E9A0A2",
            "on-primary-container": "#ffffff",
            "background": "#F7F8FA",
            "error": "#ba1a1a",
            "border-strong": "#CBD5E1",
            "tertiary-fixed-dim": "#8FCBE5",
            "on-tertiary-container": "#E7F4FB",
            "tertiary-container": "#0A81B4",
            "tertiary-fixed": "#CFEAF7",
            "sync-active": "#10B981",
            "tertiary": "#0A6E99",
            "surface-container-low": "#F1F3F6",
            "surface": "#F7F8FA",
            "on-tertiary-fixed": "#052E42",
            "surface-tint": "#BD1C23",
            "on-surface-variant": "#4B5563",
            "on-secondary-fixed": "#052E42",
            "on-secondary-container": "#0A5473",
            "on-primary-fixed-variant": "#8E1418",
            "surface-card": "#FFFFFF",
            "on-secondary-fixed-variant": "#0A5C80",
            "inverse-surface": "#283044",
            "primary-container": "#BD1C23",
            "on-primary-fixed": "#3E0A0C",
            "surface-container-lowest": "#ffffff",
            "secondary-fixed": "#CFEAF7",
            "secondary-fixed-dim": "#8FCBE5",
            "surface-bright": "#F7F8FA",
            "on-error": "#ffffff",
            "primary-fixed": "#F7D2D3",
            "on-tertiary-fixed-variant": "#0A5C80",
            "surface-dim": "#D2D9E3",
            "primary": "#A11217",
            "inverse-primary": "#F2A7A9",
            "on-error-container": "#93000a",
            "surface-container": "#E9ECF1",
            "danger-critical": "#EF4444",
            "surface-container-highest": "#D8DEE7",
            "on-tertiary": "#ffffff",
            "error-container": "#ffdad6",
            "secondary-container": "#CFEAF7",
            "sync-pending": "#F59E0B",
            "on-background": "#111827",
            "sync-offline": "#64748B",
            "canvas-base": "#F8FAFC",
            "outline": "#6B7280"
          },
          borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
          spacing: {
            "space-xl": "2.25rem", "space-lg": "1.5rem", "space-md": "1rem", "space-sm": "0.5rem",
            "gutter-tablet": "1.5rem", "gutter": "1rem", "space-xs": "0.25rem", "margin": "1rem", "margin-tablet": "2rem"
          },
          fontFamily: {
            "label-action": ["Plus Jakarta Sans"], "headline-sm": ["Sora"], "headline-md": ["Sora"],
            "headline-lg": ["Sora"], "body-sm": ["Plus Jakarta Sans"], "body-lg": ["Plus Jakarta Sans"],
            "label-numeric": ["Sora"], "display-odometer": ["Sora"],
            "label-caps": ["Sora"], "body-md": ["Plus Jakarta Sans"]
          }
        }
      }
    };
  </script>
@include('partials.brand-styles')
  <link rel="icon" type="image/svg+xml" href="{{ asset('images/lancotri-mark.svg') }}">
</head>
<body class="bg-surface font-body-md text-body-md text-on-surface antialiased min-h-screen">
  <!-- Sidebar Navigation -->
  @include('partials.sidebar')

  <!-- Main Content Wrapper -->
  <div class="pl-64 flex flex-col min-h-screen">
    <!-- Top Header (shared partial) -->
    @include('partials.topbar', [
        'topbarSearchPlaceholder' => 'Search driver name, badge, email, or bus...',
        'topbarSearchId' => 'topNavSearch',
        'topbarStatus' => $totalDrivers . ' registered drivers',
    ])

    <!-- Main View Content -->
    <main class="w-full pt-16 flex-1 px-space-lg py-space-lg bg-surface">
      <div class="flex flex-col w-full gap-space-lg">

        <!-- Flash Alert Messages -->
        @if(session('success'))
          <div class="bg-secondary-container/40 border border-secondary text-on-secondary-container p-space-md rounded-xl flex items-center justify-between shadow-sm" id="flashSuccessAlert">
            <div class="flex items-center gap-2 font-medium">
              <span class="material-symbols-outlined text-secondary">check_circle</span>
              <span>{{ session('success') }}</span>
            </div>
            <button onclick="document.getElementById('flashSuccessAlert').remove()" class="text-on-secondary-container hover:opacity-75">
              <span class="material-symbols-outlined text-lg">close</span>
            </button>
          </div>
        @endif

        @if($errors->any())
          <div class="bg-error-container/40 border border-danger-critical text-danger-critical p-space-md rounded-xl flex items-center justify-between shadow-sm">
            <div class="flex flex-col gap-1">
              <div class="flex items-center gap-2 font-bold">
                <span class="material-symbols-outlined">error</span>
                <span>Please fix the following issues:</span>
              </div>
              <ul class="list-disc pl-6 text-sm">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        @endif

        <!-- Dynamic Notification Banner (AJAX) -->
        <div id="dynamicToast" class="hidden p-space-md rounded-xl flex items-center justify-between shadow-sm transition-all duration-300">
          <div class="flex items-center gap-2 font-medium" id="dynamicToastMessage"></div>
          <button onclick="hideDynamicToast()" class="hover:opacity-75">
            <span class="material-symbols-outlined text-lg">close</span>
          </button>
        </div>

        <!-- Page Header & Action Controls -->
        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-space-md bg-surface-card p-space-md lg:p-space-lg rounded-xl shadow-sm">
          <div class="flex flex-col gap-1">
            <div class="flex items-center gap-space-xs text-outline">
              <span class="material-symbols-outlined text-sm">badge</span>
              <span class="font-label-caps text-label-caps uppercase tracking-wider">Transit Workforce &amp; Personnel</span>
              <span class="text-xs">&bull;</span>
              <span class="font-body-sm text-body-sm text-sync-active font-medium">Authentication &amp; Roster Management</span>
            </div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Driver Management &amp; Access</h1>
          </div>
          <div class="flex flex-wrap items-center gap-space-sm sm:gap-space-md">
            <div class="relative flex-1 sm:w-72">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg pointer-events-none">search</span>
              <input class="w-full pl-9 pr-4 py-2.5 bg-surface-container-low rounded-lg text-on-surface placeholder:text-outline font-body-sm text-body-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-container" id="rosterSearch" placeholder="Filter by Name, Badge, Bus..." type="text"/>
            </div>
            <button class="flex items-center gap-2 px-space-md py-2.5 rounded-lg bg-surface-container-high text-on-surface hover:bg-surface-container-highest transition-colors font-body-md text-body-md font-semibold shadow-sm" id="exportRosterBtn">
              <span class="material-symbols-outlined text-lg text-outline">file_download</span>
              <span>Export Roster</span>
            </button>
            <button class="flex items-center gap-2 px-space-lg py-2.5 rounded-lg bg-primary-container text-on-primary hover:bg-primary transition-all font-body-md text-body-md font-bold shadow-md hover:shadow-lg active:scale-95" id="addDriverBtn">
              <span class="material-symbols-outlined text-lg">person_add</span>
              <span>+ Add New Driver</span>
            </button>
          </div>
        </div>

        <!-- Metric KPI Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-space-md">
          <div class="bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute -right-3 -top-3 w-16 h-16 rounded-full bg-surface-container-low group-hover:scale-110 transition-transform pointer-events-none"></div>
            <div class="flex items-center justify-between z-10">
              <span class="font-label-caps text-label-caps uppercase text-outline">Total Drivers</span>
              <span class="material-symbols-outlined text-primary text-xl">groups</span>
            </div>
            <div class="mt-3 flex items-baseline gap-2 z-10">
              <span class="font-display-odometer text-display-odometer text-on-surface">{{ $totalDrivers }}</span>
              <span class="font-body-sm text-body-sm text-outline">Personnel</span>
            </div>
            <div class="mt-2 text-xs font-body-sm text-on-surface-variant flex items-center gap-1">
              <span class="text-secondary font-semibold">100%</span> active user profiles
            </div>
          </div>

          <div class="bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute -right-3 -top-3 w-16 h-16 rounded-full bg-secondary-container/40 group-hover:scale-110 transition-transform pointer-events-none"></div>
            <div class="flex items-center justify-between z-10">
              <span class="font-label-caps text-label-caps uppercase text-secondary">Active on Route</span>
              <span class="material-symbols-outlined text-secondary text-xl">commute</span>
            </div>
            <div class="mt-3 flex items-baseline gap-2 z-10">
              <span class="font-display-odometer text-display-odometer text-secondary">{{ $onRouteDrivers }}</span>
              <span class="font-body-sm text-body-sm text-secondary font-medium">On Shift</span>
            </div>
            <div class="mt-2 text-xs font-body-sm text-on-surface-variant flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-sync-active animate-ping"></span>
              <span>Telemetry synchronized</span>
            </div>
          </div>

          <div class="bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute -right-3 -top-3 w-16 h-16 rounded-full bg-surface-container-high group-hover:scale-110 transition-transform pointer-events-none"></div>
            <div class="flex items-center justify-between z-10">
              <span class="font-label-caps text-label-caps uppercase text-sync-pending">Standby / Available</span>
              <span class="material-symbols-outlined text-sync-pending text-xl">person_pin_circle</span>
            </div>
            <div class="mt-3 flex items-baseline gap-2 z-10">
              <span class="font-display-odometer text-display-odometer text-on-surface">{{ $availableDrivers }}</span>
              <span class="font-body-sm text-body-sm text-outline">Standby</span>
            </div>
            <div class="mt-2 text-xs font-body-sm text-on-surface-variant">
              <span>Ready for dispatch</span>
            </div>
          </div>

          <div class="bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute -right-3 -top-3 w-16 h-16 rounded-full bg-surface-container-low group-hover:scale-110 transition-transform pointer-events-none"></div>
            <div class="flex items-center justify-between z-10">
              <span class="font-label-caps text-label-caps uppercase text-outline">Off Duty / Leave</span>
              <span class="material-symbols-outlined text-outline text-xl">hotel</span>
            </div>
            <div class="mt-3 flex items-baseline gap-2 z-10">
              <span class="font-display-odometer text-display-odometer text-on-surface">{{ $offDutyDrivers }}</span>
              <span class="font-body-sm text-body-sm text-outline">Resting</span>
            </div>
            <div class="mt-2 text-xs font-body-sm text-on-surface-variant">
              <span>Shift rotation scheduled</span>
            </div>
          </div>

          <div class="col-span-2 md:col-span-1 bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute -right-3 -top-3 w-16 h-16 rounded-full bg-error-container/40 group-hover:scale-110 transition-transform pointer-events-none"></div>
            <div class="flex items-center justify-between z-10">
              <span class="font-label-caps text-label-caps uppercase text-danger-critical">Compliance</span>
              <span class="material-symbols-outlined text-danger-critical text-xl">verified</span>
            </div>
            <div class="mt-3 flex items-baseline gap-2 z-10">
              <span class="font-display-odometer text-display-odometer text-danger-critical">{{ $expiringLicensesCount }}</span>
              <span class="font-body-sm text-body-sm text-danger-critical font-medium">Alerts</span>
            </div>
            <div class="mt-2 text-xs font-body-sm text-on-surface-variant flex items-center gap-1">
              <span>CDL renew within 60 days</span>
            </div>
          </div>
        </div>

        <!-- Drivers Table Card -->
        <div class="bg-surface-card rounded-xl shadow-sm overflow-hidden flex flex-col">
          <div class="p-space-md lg:p-space-lg flex flex-col sm:flex-row sm:items-center justify-between gap-space-md bg-surface-container-low/50">
            <div class="flex items-center gap-space-sm">
              <div class="w-10 h-10 rounded-lg bg-primary text-on-primary flex items-center justify-center">
                <span class="material-symbols-outlined">badge</span>
              </div>
              <div>
                <h2 class="font-headline-md text-headline-md text-on-surface tracking-tight">Registered Transit Drivers</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Central depot roster with authentication, bus assignments, and license credentials</p>
              </div>
            </div>

            <!-- Quick Filter Tabs -->
            <div class="flex items-center gap-space-xs self-start sm:self-auto">
              <div class="flex rounded-lg bg-surface-container p-1 text-on-surface">
                <button class="filter-tab px-3 py-1 rounded bg-surface-card font-body-sm text-body-sm font-semibold shadow-sm transition-all" data-filter="all">All ({{ count($drivers) }})</button>
                <button class="filter-tab px-3 py-1 font-body-sm text-body-sm text-outline hover:text-on-surface transition-all" data-filter="on_route">On Route ({{ $onRouteDrivers }})</button>
                <button class="filter-tab px-3 py-1 font-body-sm text-body-sm text-outline hover:text-on-surface transition-all" data-filter="available">Available ({{ $availableDrivers }})</button>
                <button class="filter-tab px-3 py-1 font-body-sm text-body-sm text-outline hover:text-on-surface transition-all" data-filter="off_duty">Off Duty ({{ $offDutyDrivers }})</button>
              </div>
            </div>
          </div>

          <!-- Table Wrapper -->
          <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse" id="driversTable">
              <thead>
                <tr class="bg-surface-container-low text-on-surface-variant font-label-caps text-label-caps uppercase tracking-wider">
                  <th class="py-3.5 px-space-md font-bold">Driver Name &amp; Badge</th>
                  <th class="py-3.5 px-space-md font-bold">Login Email &amp; Contact</th>
                  <th class="py-3.5 px-space-md font-bold">License Details</th>
                  <th class="py-3.5 px-space-md font-bold">Assigned Bus</th>
                  <th class="py-3.5 px-space-md font-bold">Duty Status</th>
                  <th class="py-3.5 px-space-md font-bold">Portal Role &amp; Access</th>
                  <th class="py-3.5 px-space-md font-bold text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y-0 text-on-surface font-body-md text-body-md" id="driversTableBody">
                @forelse($drivers as $driver)
                  @php
                    $initials = '';
                    $parts = explode(' ', trim($driver->user->name ?? 'D'));
                    foreach ($parts as $p) {
                      $initials .= strtoupper(substr($p, 0, 1));
                    }
                    $initials = substr($initials, 0, 2);
                    $dutyStatus = strtolower($driver->duty_status ?? 'available');
                    $isExpiringSoon = $driver->license_expiry && \Carbon\Carbon::parse($driver->license_expiry)->isPast() || ($driver->license_expiry && \Carbon\Carbon::parse($driver->license_expiry)->diffInDays(now()) < 60);
                  @endphp
                  <tr class="hover:bg-surface-container-low/60 transition-colors driver-row"
                      id="driver-row-{{ $driver->id }}"
                      data-id="{{ $driver->id }}"
                      data-name="{{ $driver->user->name ?? '' }}"
                      data-email="{{ $driver->user->email ?? '' }}"
                      data-badge="{{ $driver->badge_id ?? '' }}"
                      data-phone="{{ $driver->phone ?? '' }}"
                      data-license-number="{{ $driver->license_number ?? '' }}"
                      data-license-type="{{ $driver->license_type ?? 'Class B CDL' }}"
                      data-license-expiry="{{ $driver->license_expiry ? \Carbon\Carbon::parse($driver->license_expiry)->format('Y-m-d') : '' }}"
                      data-bus="{{ $driver->assigned_bus ?? '' }}"
                      data-duty="{{ $driver->duty_status ?? 'available' }}"
                      data-status="{{ $driver->user->status ?? 'active' }}"
                      data-experience="{{ $driver->experience_years ?? 0 }}"
                      data-notes="{{ $driver->notes ?? '' }}">
                    
                    <!-- Driver Name & Badge -->
                    <td class="py-4 px-space-md">
                      <div class="flex items-center gap-space-sm">
                        <div class="w-10 h-10 rounded-full bg-surface-container-high text-primary font-bold flex items-center justify-center text-sm shadow-sm">
                          {{ $initials }}
                        </div>
                        <div class="flex flex-col">
                          <span class="font-headline-sm text-headline-sm font-bold text-on-surface leading-tight">{{ $driver->user->name ?? 'Unnamed Driver' }}</span>
                          <span class="font-body-sm text-body-sm text-outline flex items-center gap-1">
                            <span>{{ $driver->badge_id ?? 'N/A' }}</span>
                          </span>
                        </div>
                      </div>
                    </td>

                    <!-- Email & Phone -->
                    <td class="py-4 px-space-md">
                      <div class="flex flex-col">
                        <span class="font-semibold text-on-surface text-body-sm flex items-center gap-1">
                          <span class="material-symbols-outlined text-xs text-outline">mail</span>
                          <span>{{ $driver->user->email ?? 'No email' }}</span>
                        </span>
                        <span class="font-body-sm text-body-sm text-outline flex items-center gap-1 mt-0.5">
                          <span class="material-symbols-outlined text-xs">call</span>
                          <span>{{ $driver->phone ?? 'Unlisted' }}</span>
                        </span>
                      </div>
                    </td>

                    <!-- License Details -->
                    <td class="py-4 px-space-md">
                      <div class="flex flex-col">
                        <span class="font-mono text-body-sm font-semibold text-on-surface">
                          {{ $driver->license_number ?? 'No License #' }}
                        </span>
                        <div class="flex items-center gap-1.5 mt-0.5">
                          @if($driver->license_expiry)
                            <span class="text-xs font-body-sm {{ $isExpiringSoon ? 'text-danger-critical font-bold' : 'text-outline' }}">
                              Exp: {{ \Carbon\Carbon::parse($driver->license_expiry)->format('M d, Y') }}
                            </span>
                          @endif
                        </div>
                      </div>
                    </td>

                    <!-- Assigned Bus -->
                    <td class="py-4 px-space-md">
                      @if($driver->assigned_bus && $driver->assigned_bus !== 'Unassigned')
                        <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-surface-container-low text-on-surface font-semibold text-body-sm w-max border border-outline-variant/30">
                          <span class="material-symbols-outlined text-base text-primary">directions_bus</span>
                          <span>{{ $driver->assigned_bus }}</span>
                        </div>
                      @else
                        <span class="text-outline text-body-sm italic">Unassigned</span>
                      @endif
                    </td>

                    <!-- Duty Status -->
                    <td class="py-4 px-space-md">
                      @if($dutyStatus === 'on_route')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-secondary-fixed text-on-secondary-fixed-variant font-body-sm text-body-sm font-bold">
                          <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                          On Route
                        </span>
                      @elseif($dutyStatus === 'available')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-primary-fixed text-on-primary-fixed-variant font-body-sm text-body-sm font-bold">
                          <span class="w-2 h-2 rounded-full bg-primary-container"></span>
                          Available
                        </span>
                      @elseif($dutyStatus === 'off_duty')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-surface-container-highest text-on-surface-variant font-body-sm text-body-sm font-medium">
                          <span class="w-2 h-2 rounded-full bg-outline"></span>
                          Off Duty
                        </span>
                      @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-error-container/60 text-danger-critical font-body-sm text-body-sm font-semibold">
                          <span class="w-2 h-2 rounded-full bg-danger-critical"></span>
                          {{ ucfirst(str_replace('_', ' ', $dutyStatus)) }}
                        </span>
                      @endif
                    </td>

                    <!-- Login Role & Portal Access -->
                    <td class="py-4 px-space-md">
                      <div class="flex flex-col">
                        <div class="flex items-center gap-1.5">
                          <span class="px-2 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide bg-primary/10 text-primary">
                            Role: {{ $driver->user->role ?? 'driver' }}
                          </span>
                        </div>
                        <span class="text-xs font-medium mt-1 flex items-center gap-1 {{ ($driver->user->status ?? 'active') === 'active' ? 'text-secondary' : 'text-danger-critical' }}">
                          <span class="w-1.5 h-1.5 rounded-full {{ ($driver->user->status ?? 'active') === 'active' ? 'bg-secondary' : 'bg-danger-critical' }}"></span>
                          <span>{{ ($driver->user->status ?? 'active') === 'active' ? 'Login Allowed' : 'Account Suspended' }}</span>
                        </span>
                      </div>
                    </td>

                    <!-- Actions -->
                    <td class="py-4 px-space-md text-right">
                      <div class="flex items-center justify-end gap-1">
                        <button class="edit-driver-btn p-2 rounded-lg text-primary hover:bg-surface-container-high transition-colors"
                                title="Edit Driver &amp; Login"
                                onclick="openEditModal({{ $driver->id }})">
                          <span class="material-symbols-outlined text-lg">edit</span>
                        </button>
                        <button class="delete-driver-btn p-2 rounded-lg text-outline hover:text-danger-critical hover:bg-error-container/20 transition-colors"
                                title="Delete Driver"
                                onclick="openDeleteModal({{ $driver->id }}, '{{ addslashes($driver->user->name ?? 'Driver') }}', '{{ $driver->badge_id }}')">
                          <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr id="emptyRow">
                    <td colspan="7" class="py-12 text-center text-outline">
                      <div class="flex flex-col items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-4xl">no_accounts</span>
                        <span class="font-headline-sm text-headline-sm">No drivers found</span>
                        <p class="font-body-sm text-body-sm max-w-sm">Click "+ Add New Driver" above to register personnel and provide login credentials.</p>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <!-- Table Footer -->
          <div class="p-space-md flex flex-col sm:flex-row items-center justify-between gap-space-sm bg-surface-container-low/30 text-on-surface-variant font-body-sm text-body-sm">
            <span id="showingCount">Showing {{ count($drivers) }} of {{ $totalDrivers }} total fleet drivers</span>
            <div class="flex items-center gap-1">
              <span class="text-xs text-outline">All drivers can log in with their email and role: <span class="font-bold text-primary">driver</span></span>
            </div>
          </div>
        </div>

      </div>
    </main>
  </div>

  <!-- ================= ADD DRIVER MODAL ================= -->
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-inverse-surface/60 backdrop-blur-sm p-4 hidden" id="addDriverModal">
    <div class="bg-surface-card rounded-2xl max-w-2xl w-full p-space-lg shadow-xl flex flex-col gap-space-md max-h-[90vh] overflow-y-auto">
      <div class="flex items-center justify-between border-b border-surface-container pb-3">
        <div class="flex items-center gap-space-xs">
          <div class="w-9 h-9 rounded-lg bg-primary-container text-on-primary flex items-center justify-center">
            <span class="material-symbols-outlined text-xl">person_add</span>
          </div>
          <div>
            <h3 class="font-headline-md text-headline-md text-on-surface">Add New Driver</h3>
            <p class="text-xs font-body-sm text-outline">Registers driver in roster and sets up user login account (role: driver)</p>
          </div>
        </div>
        <button class="p-1 rounded-lg text-outline hover:text-on-surface hover:bg-surface-container-high" onclick="toggleModal('addDriverModal', false)">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>

      <!-- Info Banner -->
      <div class="bg-primary/5 border border-primary/20 rounded-xl p-3 flex items-start gap-2 text-xs text-on-surface">
        <span class="material-symbols-outlined text-primary text-base shrink-0 mt-0.5">info</span>
        <span>A user account will be created with role <strong>driver</strong>. An email containing the login email and the password you enter will be sent to the driver automatically. Assign a bus to them from the <strong>Buses</strong> page.</span>
      </div>

      <form id="addDriverForm" method="POST" action="{{ route('drivers.store') }}" class="flex flex-col gap-4">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Full Name -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">Full Name *</label>
            <input type="text" name="name" id="addName" required placeholder="e.g. Marcus Vance" class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs"/>
          </div>

          <!-- Email (Login) -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">Login Email *</label>
            <input type="email" name="email" id="addEmail" required placeholder="e.g. marcus@fleet.local" class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs"/>
          </div>

          <!-- Password (Login) -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">Password (For Login) *</label>
            <div class="relative">
              <input type="password" name="password" id="addPassword" required minlength="6" placeholder="Min. 6 characters" class="w-full pl-3 pr-10 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs"/>
              <button type="button" onclick="togglePasswordVisibility('addPassword', this)" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface">
                <span class="material-symbols-outlined text-lg">visibility</span>
              </button>
            </div>
          </div>

          <!-- Badge ID -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">Driver Badge / ID *</label>
            <input type="text" name="badge_id" id="addBadgeId" required placeholder="e.g. DRV-8802" class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs uppercase"/>
          </div>

          <!-- Phone -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">Phone Number</label>
            <input type="text" name="phone" id="addPhone" placeholder="e.g. +1 (555) 234-8802" class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs"/>
          </div>

          <!-- License Number -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">CDL / Driver License #</label>
            <input type="text" name="license_number" id="addLicenseNumber" placeholder="e.g. CDL-8802-TX" class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs"/>
          </div>

          <!-- License Type -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">License Classification</label>
            <select name="license_type" id="addLicenseType" class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs">
              <option value="Class A CDL">Class A CDL (Commercial / Heavy)</option>
              <option value="Class B CDL" selected>Class B CDL (Passenger Bus)</option>
              <option value="Class C CDL">Class C CDL (Shuttle / Van)</option>
              <option value="Standard Transit">Standard Transit</option>
            </select>
          </div>

          <!-- License Expiry -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">License Expiry Date</label>
            <input type="date" name="license_expiry" id="addLicenseExpiry" class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs"/>
          </div>

          <!-- Bus assignment is managed from the Buses page -->
          <div class="flex items-start gap-2 rounded-lg bg-surface-container-low px-3 py-2">
            <span class="material-symbols-outlined text-primary text-base mt-0.5">info</span>
            <span class="text-[11px] text-outline leading-snug">Bus assignment is managed from the <strong>Buses</strong> page Ã¢â‚¬â€ assign this driver to a vehicle there.</span>
          </div>

          <!-- Duty Status -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">Duty Status *</label>
            <select name="duty_status" id="addDutyStatus" required class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs">
              <option value="available" selected>Available (Depot Standby)</option>
              <option value="on_route">On Route (Active Shift)</option>
              <option value="off_duty">Off Duty</option>
              <option value="on_leave">On Leave / Rest</option>
            </select>
          </div>

          <!-- Account Status -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">Account Login Status *</label>
            <select name="status" id="addStatus" required class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs">
              <option value="active" selected>Active (Can Login)</option>
              <option value="inactive">Inactive (Suspended)</option>
            </select>
          </div>

          <!-- Experience Years -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">Experience (Years)</label>
            <input type="number" name="experience_years" id="addExperienceYears" min="0" max="50" value="3" class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs"/>
          </div>
        </div>

        <!-- Notes -->
        <div>
          <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">Notes / Certifications</label>
          <textarea name="notes" id="addNotes" rows="2" placeholder="Route specializations, endorsements, medical certifications..." class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs"></textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-3 border-t border-surface-container">
          <button type="button" onclick="toggleModal('addDriverModal', false)" class="px-space-md py-2.5 rounded-lg bg-surface-container-high text-on-surface hover:bg-surface-container-highest font-body-md text-body-md font-semibold transition-colors">
            Cancel
          </button>
          <button type="submit" id="addSubmitBtn" class="flex items-center gap-2 px-space-lg py-2.5 rounded-lg bg-primary-container text-on-primary hover:bg-primary font-body-md text-body-md font-bold shadow-md hover:shadow-lg transition-all">
            <span class="material-symbols-outlined text-lg">check</span>
            <span>Save Driver &amp; Create Login</span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ================= EDIT DRIVER MODAL ================= -->
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-inverse-surface/60 backdrop-blur-sm p-4 hidden" id="editDriverModal">
    <div class="bg-surface-card rounded-2xl max-w-2xl w-full p-space-lg shadow-xl flex flex-col gap-space-md max-h-[90vh] overflow-y-auto">
      <div class="flex items-center justify-between border-b border-surface-container pb-3">
        <div class="flex items-center gap-space-xs">
          <div class="w-9 h-9 rounded-lg bg-primary text-on-primary flex items-center justify-center">
            <span class="material-symbols-outlined text-xl">edit</span>
          </div>
          <div>
            <h3 class="font-headline-md text-headline-md text-on-surface">Edit Driver</h3>
            <p class="text-xs font-body-sm text-outline">Update driver details, bus assignments, and login credentials</p>
          </div>
        </div>
        <button class="p-1 rounded-lg text-outline hover:text-on-surface hover:bg-surface-container-high" onclick="toggleModal('editDriverModal', false)">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>

      <form id="editDriverForm" method="POST" action="" class="flex flex-col gap-4">
        @csrf
        @method('PUT')
        <input type="hidden" id="editDriverId" name="driver_id"/>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Full Name -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">Full Name *</label>
            <input type="text" name="name" id="editName" required class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs"/>
          </div>

          <!-- Email (Login) -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">Login Email *</label>
            <input type="email" name="email" id="editEmail" required class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs"/>
          </div>

          <!-- Password (Optional update) -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">New Password (Optional)</label>
            <div class="relative">
              <input type="password" name="password" id="editPassword" minlength="6" placeholder="Leave blank to keep current" class="w-full pl-3 pr-10 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs"/>
              <button type="button" onclick="togglePasswordVisibility('editPassword', this)" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface">
                <span class="material-symbols-outlined text-lg">visibility</span>
              </button>
            </div>
            <span class="text-[11px] text-outline">Only enter a value if you wish to change their password.</span>
          </div>

          <!-- Badge ID -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">Driver Badge / ID *</label>
            <input type="text" name="badge_id" id="editBadgeId" required class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs uppercase"/>
          </div>

          <!-- Phone -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">Phone Number</label>
            <input type="text" name="phone" id="editPhone" class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs"/>
          </div>

          <!-- License Number -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">CDL / Driver License #</label>
            <input type="text" name="license_number" id="editLicenseNumber" class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs"/>
          </div>

          <!-- License Type -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">License Classification</label>
            <select name="license_type" id="editLicenseType" class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs">
              <option value="Class A CDL">Class A CDL (Commercial / Heavy)</option>
              <option value="Class B CDL">Class B CDL (Passenger Bus)</option>
              <option value="Class C CDL">Class C CDL (Shuttle / Van)</option>
              <option value="Standard Transit">Standard Transit</option>
            </select>
          </div>

          <!-- License Expiry -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">License Expiry Date</label>
            <input type="date" name="license_expiry" id="editLicenseExpiry" class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs"/>
          </div>

          <!-- Bus assignment is managed from the Buses page -->
          <div class="flex items-start gap-2 rounded-lg bg-surface-container-low px-3 py-2">
            <span class="material-symbols-outlined text-primary text-base mt-0.5">info</span>
            <span class="text-[11px] text-outline leading-snug">Bus assignment is managed from the <strong>Buses</strong> page.</span>
          </div>

          <!-- Duty Status -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">Duty Status *</label>
            <select name="duty_status" id="editDutyStatus" required class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs">
              <option value="available">Available (Depot Standby)</option>
              <option value="on_route">On Route (Active Shift)</option>
              <option value="off_duty">Off Duty</option>
              <option value="on_leave">On Leave / Rest</option>
            </select>
          </div>

          <!-- Account Status -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">Account Login Status *</label>
            <select name="status" id="editStatus" required class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs">
              <option value="active">Active (Can Login)</option>
              <option value="inactive">Inactive (Suspended)</option>
            </select>
          </div>

          <!-- Experience Years -->
          <div>
            <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">Experience (Years)</label>
            <input type="number" name="experience_years" id="editExperienceYears" min="0" max="50" class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs"/>
          </div>
        </div>

        <!-- Notes -->
        <div>
          <label class="block font-label-caps text-label-caps uppercase text-outline mb-1">Notes / Certifications</label>
          <textarea name="notes" id="editNotes" rows="2" class="w-full px-3 py-2 bg-surface-container-low rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container text-body-md shadow-xs"></textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-3 border-t border-surface-container">
          <button type="button" onclick="toggleModal('editDriverModal', false)" class="px-space-md py-2.5 rounded-lg bg-surface-container-high text-on-surface hover:bg-surface-container-highest font-body-md text-body-md font-semibold transition-colors">
            Cancel
          </button>
          <button type="submit" id="editSubmitBtn" class="flex items-center gap-2 px-space-lg py-2.5 rounded-lg bg-primary text-on-primary hover:bg-primary-container font-body-md text-body-md font-bold shadow-md hover:shadow-lg transition-all">
            <span class="material-symbols-outlined text-lg">save</span>
            <span>Update Driver</span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ================= DELETE CONFIRMATION MODAL ================= -->
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-inverse-surface/60 backdrop-blur-sm p-4 hidden" id="deleteDriverModal">
    <div class="bg-surface-card rounded-2xl max-w-md w-full p-space-lg shadow-xl flex flex-col gap-space-md">
      <div class="flex items-center gap-space-sm text-danger-critical">
        <div class="w-10 h-10 rounded-full bg-error-container/60 flex items-center justify-center">
          <span class="material-symbols-outlined text-2xl">warning</span>
        </div>
        <h4 class="font-headline-md text-headline-md text-on-surface">Delete Driver?</h4>
      </div>

      <p class="font-body-md text-body-md text-on-surface-variant">
        Are you sure you want to delete <span class="font-bold text-on-surface" id="deleteDriverName"></span> (<span id="deleteDriverBadge"></span>)?
      </p>
      <div class="bg-error-container/30 border border-danger-critical/30 rounded-xl p-3 text-xs text-danger-critical font-medium">
        This action will permanently delete the driver profile and their associated portal login account.
      </div>

      <form id="deleteDriverForm" method="POST" action="" class="flex items-center justify-end gap-3 pt-2">
        @csrf
        @method('DELETE')
        <button type="button" onclick="toggleModal('deleteDriverModal', false)" class="px-space-md py-2.5 rounded-lg bg-surface-container-high text-on-surface hover:bg-surface-container-highest font-body-md text-body-md font-semibold transition-colors">
          Cancel
        </button>
        <button type="submit" id="deleteSubmitBtn" class="flex items-center gap-1.5 px-space-lg py-2.5 rounded-lg bg-danger-critical text-on-primary hover:bg-error font-body-md text-body-md font-bold shadow-md transition-all">
          <span class="material-symbols-outlined text-lg">delete_forever</span>
          <span>Delete Permanently</span>
        </button>
      </form>
    </div>
  </div>

  <!-- JavaScript Logics -->
  <script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function toggleModal(modalId, show) {
      const modal = document.getElementById(modalId);
      if (!modal) return;
      if (show) {
        modal.classList.remove('hidden');
      } else {
        modal.classList.add('hidden');
      }
    }

    function togglePasswordVisibility(inputId, btn) {
      const input = document.getElementById(inputId);
      const icon = btn.querySelector('.material-symbols-outlined');
      if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
      } else {
        input.type = 'password';
        icon.textContent = 'visibility';
      }
    }

    function showToast(message, isError = false) {
      const toast = document.getElementById('dynamicToast');
      const msgEl = document.getElementById('dynamicToastMessage');
      if (!toast || !msgEl) return;

      toast.className = isError
        ? 'p-space-md rounded-xl flex items-center justify-between shadow-sm bg-error-container/40 border border-danger-critical text-danger-critical'
        : 'p-space-md rounded-xl flex items-center justify-between shadow-sm bg-secondary-container/40 border border-secondary text-on-secondary-container';

      msgEl.innerHTML = `
        <span class="material-symbols-outlined text-lg">${isError ? 'error' : 'check_circle'}</span>
        <span>${message}</span>
      `;
      toast.classList.remove('hidden');

      setTimeout(() => {
        hideDynamicToast();
      }, 5000);
    }

    function hideDynamicToast() {
      const toast = document.getElementById('dynamicToast');
      if (toast) toast.classList.add('hidden');
    }

    // Add Driver trigger
    document.getElementById('addDriverBtn')?.addEventListener('click', () => {
      document.getElementById('addDriverForm').reset();
      toggleModal('addDriverModal', true);
    });

    // Open Edit Modal with row data
    function openEditModal(driverId) {
      const row = document.getElementById(`driver-row-${driverId}`);
      if (!row) return;

      document.getElementById('editDriverId').value = driverId;
      document.getElementById('editName').value = row.dataset.name || '';
      document.getElementById('editEmail').value = row.dataset.email || '';
      document.getElementById('editPassword').value = '';
      document.getElementById('editBadgeId').value = row.dataset.badge || '';
      document.getElementById('editPhone').value = row.dataset.phone || '';
      document.getElementById('editLicenseNumber').value = row.dataset.licenseNumber || '';
      document.getElementById('editLicenseType').value = row.dataset.licenseType || 'Class B CDL';
      document.getElementById('editLicenseExpiry').value = row.dataset.licenseExpiry || '';
      document.getElementById('editDutyStatus').value = row.dataset.duty || 'available';
      document.getElementById('editStatus').value = row.dataset.status || 'active';
      document.getElementById('editExperienceYears').value = row.dataset.experience || 0;
      document.getElementById('editNotes').value = row.dataset.notes || '';

      document.getElementById('editDriverForm').action = `/drivers/${driverId}`;
      toggleModal('editDriverModal', true);
    }

    // Open Delete Modal
    function openDeleteModal(driverId, name, badge) {
      document.getElementById('deleteDriverName').textContent = name;
      document.getElementById('deleteDriverBadge').textContent = badge;
      document.getElementById('deleteDriverForm').action = `/drivers/${driverId}`;
      toggleModal('deleteDriverModal', true);
    }

    // AJAX Handler for Add Driver
    document.getElementById('addDriverForm')?.addEventListener('submit', async function(e) {
      e.preventDefault();
      const btn = document.getElementById('addSubmitBtn');
      const originalHtml = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = `<span class="material-symbols-outlined text-lg animate-spin">progress_activity</span><span>Saving...</span>`;

      const formData = new FormData(this);

      try {
        const response = await fetch(this.action, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
          },
          body: formData,
        });

        const data = await response.json();

        if (response.ok && data.success) {
          toggleModal('addDriverModal', false);
          showToast(data.message);
          setTimeout(() => window.location.reload(), 800);
        } else {
          const errors = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'Error adding driver.');
          showToast(errors, true);
        }
      } catch (err) {
        showToast('Network error while saving driver.', true);
      } finally {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
      }
    });

    // AJAX Handler for Edit Driver
    document.getElementById('editDriverForm')?.addEventListener('submit', async function(e) {
      e.preventDefault();
      const btn = document.getElementById('editSubmitBtn');
      const originalHtml = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = `<span class="material-symbols-outlined text-lg animate-spin">progress_activity</span><span>Updating...</span>`;

      const formData = new FormData(this);

      try {
        const response = await fetch(this.action, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
          },
          body: formData,
        });

        const data = await response.json();

        if (response.ok && data.success) {
          toggleModal('editDriverModal', false);
          showToast(data.message);
          setTimeout(() => window.location.reload(), 800);
        } else {
          const errors = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'Error updating driver.');
          showToast(errors, true);
        }
      } catch (err) {
        showToast('Network error while updating driver.', true);
      } finally {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
      }
    });

    // AJAX Handler for Delete Driver
    document.getElementById('deleteDriverForm')?.addEventListener('submit', async function(e) {
      e.preventDefault();
      const btn = document.getElementById('deleteSubmitBtn');
      const originalHtml = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = `<span class="material-symbols-outlined text-lg animate-spin">progress_activity</span><span>Deleting...</span>`;

      const formData = new FormData(this);

      try {
        const response = await fetch(this.action, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
          },
          body: formData,
        });

        const data = await response.json();

        if (response.ok && data.success) {
          toggleModal('deleteDriverModal', false);
          showToast(data.message);
          setTimeout(() => window.location.reload(), 800);
        } else {
          showToast(data.message || 'Error deleting driver.', true);
        }
      } catch (err) {
        showToast('Network error while deleting driver.', true);
      } finally {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
      }
    });

    // Client-side search and filters
    const searchInput = document.getElementById('rosterSearch');
    const topNavSearch = document.getElementById('topNavSearch');

    function applySearch(query) {
      query = query.toLowerCase().trim();
      const rows = document.querySelectorAll('.driver-row');
      let visibleCount = 0;

      rows.forEach(row => {
        const text = (
          (row.dataset.name || '') + ' ' +
          (row.dataset.email || '') + ' ' +
          (row.dataset.badge || '') + ' ' +
          (row.dataset.phone || '') + ' ' +
          (row.dataset.bus || '') + ' ' +
          (row.dataset.licenseNumber || '')
        ).toLowerCase();

        if (!query || text.includes(query)) {
          row.style.display = '';
          visibleCount++;
        } else {
          row.style.display = 'none';
        }
      });

      const countEl = document.getElementById('showingCount');
      if (countEl) countEl.textContent = `Showing ${visibleCount} fleet driver(s)`;
    }

    if (searchInput) {
      searchInput.addEventListener('input', (e) => applySearch(e.target.value));
    }
    if (topNavSearch) {
      topNavSearch.addEventListener('input', (e) => {
        if (searchInput) searchInput.value = e.target.value;
        applySearch(e.target.value);
      });
    }

    // Filter tabs
    const filterTabs = document.querySelectorAll('.filter-tab');
    filterTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        filterTabs.forEach(t => {
          t.classList.remove('bg-surface-card', 'font-semibold', 'shadow-sm');
          t.classList.add('text-outline');
        });
        tab.classList.add('bg-surface-card', 'font-semibold', 'shadow-sm');
        tab.classList.remove('text-outline');

        const filter = tab.dataset.filter;
        const rows = document.querySelectorAll('.driver-row');
        let visibleCount = 0;

        rows.forEach(row => {
          const duty = row.dataset.duty;
          if (filter === 'all' || duty === filter) {
            row.style.display = '';
            visibleCount++;
          } else {
            row.style.display = 'none';
          }
        });

        const countEl = document.getElementById('showingCount');
        if (countEl) countEl.textContent = `Showing ${visibleCount} fleet driver(s)`;
      });
    });

    // CSV Export
    document.getElementById('exportRosterBtn')?.addEventListener('click', () => {
      const rows = document.querySelectorAll('.driver-row');
      let csv = 'Name,Badge,Email,Phone,License Number,License Type,Expiry Date,Assigned Bus,Duty Status,Account Status\n';

      rows.forEach(r => {
        csv += `"${r.dataset.name}","${r.dataset.badge}","${r.dataset.email}","${r.dataset.phone}","${r.dataset.licenseNumber}","${r.dataset.licenseType}","${r.dataset.licenseExpiry}","${r.dataset.bus}","${r.dataset.duty}","${r.dataset.status}"\n`;
      });

      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.setAttribute('href', url);
      link.setAttribute('download', `fleet_drivers_roster_${new Date().toISOString().slice(0, 10)}.csv`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    });
  </script>
</body>
</html>
