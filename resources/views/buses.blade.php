<!DOCTYPE html>

<html lang="en"><head><meta charset="utf-8"/><meta content="width=device-width, initial-scale=1.0" name="viewport"/><meta content="web_dashboard" name="shell-type"/><meta name="csrf-token" content="{{ csrf_token() }}"/><link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/><link href="https://fonts.googleapis.com" rel="preconnect"/><link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;family=Sora:wght@400;500;600;700&amp;family=Poppins:wght@600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/><title>Fleet Buses Roster &bull; Lancotri</title><style>@layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}main>:first-child{margin-top:0!important;}main>:last-child{margin-bottom:0!important;}}::-webkit-scrollbar{display:none;}</style><style>@keyframes busDialogIn{from{opacity:0;transform:scale(.94) translateY(10px);}to{opacity:1;transform:scale(1) translateY(0);}}.delete-dialog{animation:busDialogIn .22s cubic-bezier(.2,.8,.2,1);}</style><script src="https://cdn.tailwindcss.com"></script><script id="tailwind-config">tailwind.config = { darkMode: "class", theme: { extend: { "colors": { "outline-variant": "#D1D5DB", "on-surface": "#111827", "surface-variant": "#E1E5EC", "inverse-on-surface": "#F1F5F9", "on-primary": "#ffffff", "surface-container-high": "#E1E5EC", "on-secondary": "#ffffff", "secondary": "#0A81B4", "primary-fixed-dim": "#E9A0A2", "on-primary-container": "#ffffff", "background": "#F7F8FA", "error": "#ba1a1a", "border-strong": "#CBD5E1", "tertiary-fixed-dim": "#8FCBE5", "on-tertiary-container": "#E7F4FB", "tertiary-container": "#0A81B4", "tertiary-fixed": "#CFEAF7", "sync-active": "#10B981", "tertiary": "#0A6E99", "surface-container-low": "#F1F3F6", "surface": "#F7F8FA", "on-tertiary-fixed": "#052E42", "surface-tint": "#BD1C23", "on-surface-variant": "#4B5563", "on-secondary-fixed": "#052E42", "on-secondary-container": "#0A5473", "on-primary-fixed-variant": "#8E1418", "surface-card": "#FFFFFF", "on-secondary-fixed-variant": "#0A5C80", "inverse-surface": "#283044", "primary-container": "#BD1C23", "on-primary-fixed": "#3E0A0C", "surface-container-lowest": "#ffffff", "secondary-fixed": "#CFEAF7", "secondary-fixed-dim": "#8FCBE5", "surface-bright": "#F7F8FA", "on-error": "#ffffff", "primary-fixed": "#F7D2D3", "on-tertiary-fixed-variant": "#0A5C80", "surface-dim": "#D2D9E3", "primary": "#A11217", "inverse-primary": "#F2A7A9", "on-error-container": "#93000a", "surface-container": "#E9ECF1", "danger-critical": "#EF4444", "surface-container-highest": "#D8DEE7", "on-tertiary": "#ffffff", "error-container": "#ffdad6", "secondary-container": "#CFEAF7", "sync-pending": "#F59E0B", "on-background": "#111827", "sync-offline": "#64748B", "canvas-base": "#F8FAFC", "outline": "#6B7280" }, "borderRadius": { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" }, "spacing": { "space-xl": "2.25rem", "space-lg": "1.5rem", "space-md": "1rem", "space-sm": "0.5rem", "gutter-tablet": "1.5rem", "gutter": "1rem", "space-xs": "0.25rem", "margin": "1rem", "margin-tablet": "2rem" }, "fontFamily": { "label-action": [ "Plus Jakarta Sans" ], "headline-sm": [ "Sora" ], "headline-md": [ "Sora" ], "headline-lg": [ "Sora" ], "body-sm": [ "Plus Jakarta Sans" ], "body-lg": [ "Plus Jakarta Sans" ], "label-numeric": [ "Sora" ], "display-odometer": [ "Sora" ], "label-caps": [ "Sora" ], "body-md": [ "Plus Jakarta Sans" ] }, "fontSize": { "label-action": [ "18px", { "lineHeight": "24px", "fontWeight": "600" } ], "headline-sm": [ "18px", { "lineHeight": "24px", "fontWeight": "600" } ], "headline-md": [ "22px", { "lineHeight": "28px", "fontWeight": "600" } ], "headline-lg": [ "30px", { "lineHeight": "36px", "letterSpacing": "-0.01em", "fontWeight": "700" } ], "body-sm": [ "13px", { "lineHeight": "18px", "fontWeight": "400" } ], "body-lg": [ "17px", { "lineHeight": "24px", "fontWeight": "500" } ], "label-numeric": [ "20px", { "lineHeight": "24px", "letterSpacing": "0.02em", "fontWeight": "700" } ], "display-odometer": [ "44px", { "lineHeight": "48px", "letterSpacing": "-0.02em", "fontWeight": "700" } ], "label-caps": [ "12px", { "lineHeight": "16px", "letterSpacing": "0.08em", "fontWeight": "700" } ], "body-md": [ "15px", { "lineHeight": "22px", "fontWeight": "400" } ] } } } };</script>@include('partials.brand-styles')
  <link rel="icon" type="image/svg+xml" href="{{ asset('images/lancotri-mark.svg') }}">
</head><body class="bg-surface font-body-md text-body-md text-on-surface antialiased min-h-screen"><aside class="fixed left-0 top-0 h-full w-64 bg-surface-card z-50 flex flex-col justify-between shadow-[0_1px_8px_rgba(0,0,0,0.04)]"><div class="flex flex-col flex-1"><div class="h-16 px-space-md flex items-center gap-space-sm">@include('partials.brand-logo')</div><div class="px-space-md py-space-sm"><span class="font-label-caps text-label-caps text-outline uppercase tracking-wider">Transit Operations</span></div><nav class="flex-1 px-space-sm flex flex-col gap-1" data-active-classes="bg-primary-container text-on-primary font-bold shadow-sm"><a class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg text-on-surface-variant font-body-md text-body-md hover:bg-surface-container-high hover:text-on-surface transition-colors" data-path="dashboard" href="{{ route('dashboard') }}"><span class="material-symbols-outlined text-lg">space_dashboard</span><span>Dashboard</span></a><a aria-current="page" class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg transition-colors bg-primary-container text-on-primary font-bold shadow-sm" data-path="buses" href="{{ route('buses') }}"><span class="material-symbols-outlined text-lg">directions_bus</span><span>Buses</span></a><a class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg text-on-surface-variant font-body-md text-body-md hover:bg-surface-container-high hover:text-on-surface transition-colors" data-path="shifts" href="{{ route('shifts') }}"><span class="material-symbols-outlined text-lg">schedule</span><span>Shifts</span></a><a class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg text-on-surface-variant font-body-md text-body-md hover:bg-surface-container-high hover:text-on-surface transition-colors" data-path="fuel" href="#"><span class="material-symbols-outlined text-lg">local_gas_station</span><span>Fuel</span></a><a class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg text-on-surface-variant font-body-md text-body-md hover:bg-surface-container-high hover:text-on-surface transition-colors" data-path="maintenance" href="#"><span class="material-symbols-outlined text-lg">build</span><span>Maintenance</span></a><a class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg text-on-surface-variant font-body-md text-body-md hover:bg-surface-container-high hover:text-on-surface transition-colors" data-path="drivers" href="{{ route('drivers') }}"><span class="material-symbols-outlined text-lg">badge</span><span>Drivers</span></a><a class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg text-on-surface-variant font-body-md text-body-md hover:bg-surface-container-high hover:text-on-surface transition-colors" data-path="reports" href="#"><span class="material-symbols-outlined text-lg">bar_chart</span><span>Reports</span></a></nav></div><div class="p-space-md bg-surface-container-low m-space-sm rounded-xl flex items-center justify-between"><div class="flex items-center gap-space-xs"><span class="w-2.5 h-2.5 rounded-full bg-sync-active animate-pulse"></span><span class="font-body-sm text-body-sm text-on-surface">GPS Feed Synced</span></div><span class="material-symbols-outlined text-outline text-lg">wifi</span></div></aside><div class="pl-64 flex flex-col min-h-screen"><header class="fixed top-0 left-64 right-0 h-16 bg-surface/90 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-40 px-space-lg flex items-center justify-between gap-space-md"><div class="flex-1 max-w-md"><div class="relative flex items-center w-full"><span class="material-symbols-outlined absolute left-3 text-outline text-lg pointer-events-none">search</span><input class="w-full pl-9 pr-space-md py-2 bg-surface-card rounded-lg text-on-surface placeholder:text-outline font-body-sm text-body-sm focus:outline-none focus:ring-2 focus:ring-primary-container shadow-sm" placeholder="Search bus, driver, or shift #..." type="text"/></div></div><div class="flex items-center gap-space-lg"><div class="hidden lg:flex items-center gap-2 px-space-md py-1.5 rounded-full bg-surface-card shadow-sm"><span class="w-2 h-2 rounded-full bg-sync-active"></span><span class="font-body-sm text-body-sm font-semibold text-secondary">Depot Online</span><span class="text-outline text-xs">&bull;</span><span class="font-body-sm text-body-sm text-on-surface-variant">18 active vehicles</span></div><button class="relative p-2 rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors"><span class="material-symbols-outlined text-xl">notifications</span><span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-danger-critical ring-2 ring-surface"></span></button><div class="flex items-center gap-space-sm pl-space-sm"><img alt="Profile" class="w-8 h-8 rounded-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCttXgthIfoSu1OhXg4Jji6OMcw-zk-tlENHrN92xZQQFgNPXVbEp8Tzf4H0twMaL7bDdVr1LLutIwNcDT6slU_mcsYaDzxbxsP9mgyEs4qQQHXhw7n7ujABjdezf8GsxUN29HGq1GBzBV-WnTMIN18UHXw274zq65XOoEhqyRsNKcLVqcdF6kL8dPF_VA-32Yd8y7SR8rT-rfgvN_N_cnbx430fi4ZoCQyRwyMmfmfGpoSdVDFlA0y"/><div class="hidden sm:flex flex-col text-left"><span class="font-body-sm text-body-sm font-semibold text-on-surface leading-tight">{{ auth()->user()->name ?? 'Alex Turner' }}</span><span class="font-label-caps text-label-caps text-outline leading-tight">Operations Admin</span></div></div></div></header><main class="w-full pt-16 flex-1 px-space-lg py-space-lg bg-surface"><div class="flex flex-col w-full gap-space-lg">
<div class="flex flex-col xl:flex-row xl:items-center justify-between gap-space-md">
<div class="flex flex-col">
<div class="flex items-center gap-space-xs mb-1">
<span class="font-label-caps text-label-caps uppercase text-tertiary">Real-Time Asset Tracking</span>
<span class="inline-block w-1.5 h-1.5 rounded-full bg-sync-active"></span>
<span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Terminal 4 Live Telemetry</span>
</div>
<h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Fleet Buses Roster</h1>
</div>
<div class="flex flex-wrap items-center gap-space-sm">
<div class="relative flex-1 sm:w-80">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg pointer-events-none">search</span>
<input class="w-full pl-9 pr-4 py-2.5 bg-surface-card rounded-lg text-on-surface placeholder:text-outline font-body-sm text-body-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-container" id="rosterSearch" placeholder="Filter by Bus #, Plate, or Chassis..." type="text"/>
</div>
<button class="flex items-center gap-2 px-space-md py-2.5 rounded-lg bg-surface-card text-on-surface hover:bg-surface-container-high transition-colors font-body-md text-body-md shadow-sm" id="exportCsvBtn">
<span class="material-symbols-outlined text-lg text-outline">file_download</span>
<span>Export Roster</span>
</button>
<button class="flex items-center gap-2 px-space-md py-2.5 rounded-lg bg-primary-container text-on-primary hover:bg-primary transition-all font-body-md text-body-md font-semibold shadow-sm" id="addBusModalTrigger">
<span class="material-symbols-outlined text-lg">add</span>
<span>Add New Bus</span>
</button>
</div>
</div>
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-space-md">
<div class="bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col justify-between relative overflow-hidden group">
<div class="absolute -right-3 -top-3 w-16 h-16 rounded-full bg-surface-container-low group-hover:scale-110 transition-transform pointer-events-none"></div>
<div class="flex items-center justify-between z-10">
<span class="font-label-caps text-label-caps uppercase text-outline">Total Fleet</span>
<span class="material-symbols-outlined text-primary text-xl">directions_bus</span>
</div>
<div class="mt-3 flex items-baseline gap-2 z-10">
<span class="font-display-odometer text-display-odometer text-on-surface">{{ $totalBuses }}</span>
<span class="font-body-sm text-body-sm text-outline">Registered</span>
</div>
<div class="mt-2 text-xs font-body-sm text-on-surface-variant flex items-center gap-1">
<span class="text-secondary font-semibold">100%</span> asset readiness audit
      </div>
</div>
<div class="bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col justify-between relative overflow-hidden group">
<div class="absolute -right-3 -top-3 w-16 h-16 rounded-full bg-secondary-container/40 group-hover:scale-110 transition-transform pointer-events-none"></div>
<div class="flex items-center justify-between z-10">
<span class="font-label-caps text-label-caps uppercase text-secondary">Active on Shift</span>
<span class="material-symbols-outlined text-secondary text-xl">commute</span>
</div>
<div class="mt-3 flex items-baseline gap-2 z-10">
<span class="font-display-odometer text-display-odometer text-secondary">{{ $activeBuses }}</span>
<span class="font-body-sm text-body-sm text-secondary font-medium">En Route</span>
</div>
<div class="mt-2 text-xs font-body-sm text-on-surface-variant flex items-center gap-1.5">
<span class="w-2 h-2 rounded-full bg-sync-active animate-ping"></span>
<span>Across 12 metro lines</span>
</div>
</div>
<div class="bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col justify-between relative overflow-hidden group">
<div class="absolute -right-3 -top-3 w-16 h-16 rounded-full bg-surface-container-high group-hover:scale-110 transition-transform pointer-events-none"></div>
<div class="flex items-center justify-between z-10">
<span class="font-label-caps text-label-caps uppercase text-sync-pending">In Depot Garage</span>
<span class="material-symbols-outlined text-sync-pending text-xl">garage</span>
</div>
<div class="mt-3 flex items-baseline gap-2 z-10">
<span class="font-display-odometer text-display-odometer text-on-surface">{{ $depotBuses }}</span>
<span class="font-body-sm text-body-sm text-outline">Bays &amp; Garage</span>
</div>
<div class="mt-2 text-xs font-body-sm text-on-surface-variant">
<span>Routine inspection &amp; fluids</span>
</div>
</div>
<div class="bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col justify-between relative overflow-hidden group">
<div class="absolute -right-3 -top-3 w-16 h-16 rounded-full bg-error-container/40 group-hover:scale-110 transition-transform pointer-events-none"></div>
<div class="flex items-center justify-between z-10">
<span class="font-label-caps text-label-caps uppercase text-danger-critical">Out of Service</span>
<span class="material-symbols-outlined text-danger-critical text-xl">error_med</span>
</div>
<div class="mt-3 flex items-baseline gap-2 z-10">
<span class="font-display-odometer text-display-odometer text-danger-critical">{{ $outOfServiceBuses }}</span>
<span class="font-body-sm text-body-sm text-outline">Flagged units</span>
</div>
<div class="mt-2 text-xs font-body-sm text-danger-critical font-medium">
<span>Differential overhaul (ETA: 48h)</span>
</div>
</div>
<div class="col-span-2 md:col-span-1 bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col justify-between relative overflow-hidden group">
<div class="absolute -right-3 -top-3 w-16 h-16 rounded-full bg-surface-container-low group-hover:scale-110 transition-transform pointer-events-none"></div>
<div class="flex items-center justify-between z-10">
<span class="font-label-caps text-label-caps uppercase text-tertiary">Fleet Avg Fuel Econ</span>
<span class="material-symbols-outlined text-tertiary text-xl">speed</span>
</div>
<div class="mt-3 flex items-baseline gap-2 z-10">
<span class="font-display-odometer text-display-odometer text-on-surface">{{ $avgEfficiency ?: '0.0' }}</span>
<span class="font-body-md text-body-md font-bold text-on-surface">KM / L</span>
</div>
<div class="mt-2 text-xs font-body-sm text-secondary flex items-center gap-1 font-semibold">
<span class="material-symbols-outlined text-sm">trending_up</span>
<span>+4.2% vs last 30d baseline</span>
</div>
</div>
</div>
<div class="bg-surface-card rounded-xl p-space-lg shadow-sm relative overflow-hidden">
<div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-space-md pb-space-md">
<div class="flex items-center gap-space-sm flex-wrap">
<span class="px-2.5 py-1 rounded-full bg-primary-container/10 text-primary-container font-label-caps text-label-caps uppercase tracking-wider">
          Spotlight Bus
        </span>
<span class="font-headline-md text-headline-md text-on-surface">Bus #42 Scania K310 Diesel Citywide</span>
<span class="px-2.5 py-0.5 rounded-full bg-sync-active/10 text-secondary text-body-sm font-semibold flex items-center gap-1.5">
<span class="w-2 h-2 rounded-full bg-sync-active"></span> Active Route: Line 14 North
        </span>
</div>
<div class="flex items-center gap-space-xs text-xs font-body-sm text-outline">
<span class="material-symbols-outlined text-sm text-outline">satellite_alt</span>
<span>GPS Telemetry Updated 45s ago</span>
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-space-md pt-space-sm">
<div class="flex items-center gap-space-md bg-surface-container-low p-space-md rounded-xl">
<img class="w-14 h-14 rounded-full object-cover shadow-sm ring-2 ring-surface-card" data-alt="Professional bus driver Marcus Vance headshot wearing transit uniform with blue collared shirt and badge smiling confident lighting" src="https://lh3.googleusercontent.com/aida-public/AB6AXuADDjTE_ox4YgdubjEvho_OTOnOpeQsOM3AsQ2yxjfcPLtCqqCu0JBSUN1KGtt_vmP4ObELvTf4oDzB6VUtrAgZl9CK9sZz0z6mjtVw-hfQ8S4qVIXAym-Zw2Twz1h4ugyTeD9ICffRSovcNimd1f62EtsTrHtY8d4S57-b1Q-z_ok1d-B5ydb4UT_HtNCESQtpuBXAuyGjq4lvD70fxJY21f4MnVDAtiHERd1A-o921Qb0_SeOt_Pn"/>
<div class="flex flex-col min-w-0">
<span class="font-label-caps text-label-caps text-outline uppercase">Active Operator</span>
<span class="font-headline-sm text-headline-sm text-on-surface truncate">Marcus Vance</span>
<div class="flex items-center gap-2 mt-0.5">
<span class="font-body-sm text-body-sm text-secondary font-medium">Shift: 06:00 - 14:30</span>
<button class="text-primary-container hover:underline font-body-sm text-body-sm text-xs font-semibold">Reassign</button>
</div>
</div>
</div>
<div class="bg-surface-container-low p-space-md rounded-xl flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-caps text-label-caps text-outline uppercase">Odometer &amp; Plate</span>
<span class="font-label-caps text-label-caps px-2 py-0.5 rounded bg-surface-card text-on-surface font-mono shadow-xs">EX-482-TX</span>
</div>
<div class="mt-2">
<div class="font-label-numeric text-label-numeric text-on-surface">142,760 <span class="text-xs text-outline font-normal">KM</span></div>
<span class="font-body-sm text-body-sm text-on-surface-variant text-xs">Service interval due in 7,240 KM</span>
</div>
</div>
<div class="bg-surface-container-low p-space-md rounded-xl flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-caps text-label-caps text-outline uppercase">Efficiency Score</span>
<span class="px-2 py-0.5 rounded text-xs font-semibold bg-secondary/15 text-secondary">Optimum (Good)</span>
</div>
<div class="mt-2 flex items-baseline gap-2">
<span class="font-display-odometer text-display-odometer text-secondary">3.6</span>
<span class="font-headline-sm text-headline-sm text-on-surface font-semibold">KM/L</span>
</div>
<span class="font-body-sm text-body-sm text-outline text-xs">0.2 KM/L above fleet baseline</span>
</div>
<div class="bg-surface-container-low p-space-md rounded-xl flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-caps text-label-caps text-outline uppercase">Depot Logged Action</span>
<span class="font-label-caps text-label-caps text-primary-container font-semibold">Today 04:15 AM</span>
</div>
<div class="mt-2">
<div class="font-body-lg text-body-lg font-bold text-on-surface truncate">Front Axle Tyre Replacement</div>
<div class="flex items-center justify-between mt-1">
<span class="font-body-sm text-body-sm text-on-surface-variant text-xs">Bay #2 (Goodyear K-Transit)</span>
<span class="font-label-caps text-label-caps text-on-surface font-bold bg-surface-card px-2 py-0.5 rounded shadow-xs">$340.00</span>
</div>
</div>
</div>
</div>
<div class="mt-space-md pt-space-md flex flex-wrap items-center justify-between gap-space-sm">
<div class="flex items-center gap-space-sm text-xs font-body-sm text-on-surface-variant">
<span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm text-sync-active">check_circle</span> Pre-trip checklist passed</span>
<span>&bull;</span>
<span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm text-primary">local_gas_station</span> Tank: 82% capacity</span>
<span>&bull;</span>
<span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm text-tertiary">thermostat</span> Engine Temp: 89&deg;C</span>
</div>
<div class="flex items-center gap-2">
<button class="px-3 py-1.5 rounded-lg bg-surface-card hover:bg-surface-container-high text-on-surface font-body-sm text-body-sm shadow-xs transition-colors flex items-center gap-1.5">
<span class="material-symbols-outlined text-sm">timeline</span> Telemetry Live
        </button>
<button class="px-3 py-1.5 rounded-lg bg-primary-container text-on-primary font-body-sm text-body-sm shadow-xs hover:bg-primary transition-colors flex items-center gap-1.5">
<span class="material-symbols-outlined text-sm">history_edu</span> Service Diary
        </button>
</div>
</div>
</div>
<div class="flex flex-col gap-space-md">
<div class="bg-surface-card p-space-md rounded-xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-space-md">
<div class="flex flex-wrap items-center gap-space-xs">
<span class="font-label-caps text-label-caps text-outline uppercase mr-2 flex items-center gap-1">
<span class="material-symbols-outlined text-sm">filter_alt</span> Quick Filters:
        </span>
<button class="filter-pill px-3 py-1.5 rounded-full font-body-sm text-body-sm font-semibold transition-all bg-primary-container text-on-primary shadow-xs" data-filter="all">
          All Buses ({{ $totalBuses }})
        </button>
<button class="filter-pill px-3 py-1.5 rounded-full font-body-sm text-body-sm transition-all bg-surface-container-low text-on-surface hover:bg-surface-container-high" data-filter="active">
          Active Shift ({{ $activeBuses }})
        </button>
<button class="filter-pill px-3 py-1.5 rounded-full font-body-sm text-body-sm transition-all bg-surface-container-low text-on-surface hover:bg-surface-container-high" data-filter="depot">
          Depot / Maintenance ({{ $depotBuses }})
        </button>
<button class="filter-pill px-3 py-1.5 rounded-full font-body-sm text-body-sm transition-all bg-surface-container-low text-on-surface hover:bg-surface-container-high" data-filter="out_of_service">
          Out of Service ({{ $outOfServiceBuses }})
        </button>
</div>
<div class="flex items-center gap-space-sm self-end md:self-auto">
<label class="font-body-sm text-body-sm text-outline">Chassis Model:</label>
<select class="bg-surface-container-low text-on-surface font-body-sm text-body-sm px-3 py-1.5 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-container" id="modelFilter">
<option value="all">All Vehicle Models</option>
<option value="Scania K310 City">Scania K310</option>
<option value="Volvo B8RLE Low-Floor">Volvo B8RLE</option>
<option value="Mercedes Citaro C2">Mercedes Citaro C2</option>
<option value="MAN Lion's City 12G">MAN Lion's City 12G</option>
</select>
</div>
</div>
<div class="bg-surface-card rounded-xl shadow-sm overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-left text-on-surface border-collapse">
<thead>
<tr class="bg-surface-container-low text-outline font-label-caps text-label-caps uppercase border-b-0">
<th class="py-3.5 px-space-md">Bus Identification</th>
<th class="py-3.5 px-space-md">License Plate</th>
<th class="py-3.5 px-space-md">Operator Assigned</th>
<th class="py-3.5 px-space-md">Status</th>
<th class="py-3.5 px-space-md">Odometer</th>
<th class="py-3.5 px-space-md">Month Fuel</th>
<th class="py-3.5 px-space-md">Km / Liter</th>
<th class="py-3.5 px-space-md">Next Service</th>
<th class="py-3.5 px-space-md text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y-0 text-body-md font-body-md" id="busesTableBody">
@forelse($buses as $bus)
@php
    $statusMap = [
        'active' => ['label' => 'Active Shift', 'badge' => 'bg-sync-active/15 text-secondary', 'dot' => 'bg-sync-active'],
        'standby' => ['label' => 'Ready / Standby', 'badge' => 'bg-primary-container/15 text-primary', 'dot' => 'bg-primary-container'],
        'depot' => ['label' => 'In Depot', 'badge' => 'bg-sync-pending/15 text-sync-pending', 'dot' => 'bg-sync-pending'],
        'maintenance' => ['label' => 'In Maintenance', 'badge' => 'bg-sync-pending/15 text-sync-pending', 'dot' => 'bg-sync-pending'],
        'out_of_service' => ['label' => 'Out of Service', 'badge' => 'bg-error-container text-danger-critical', 'dot' => 'bg-danger-critical'],
    ];
    $st = $statusMap[$bus->status] ?? $statusMap['standby'];

    $efficiency = (float) ($bus->km_per_liter ?? 0);
    if ($efficiency >= 3.5) {
        $effColor = 'text-secondary';
        $effLabel = 'Good';
    } elseif ($efficiency >= 3.0) {
        $effColor = 'text-sync-pending';
        $effLabel = 'Fair';
    } else {
        $effColor = 'text-danger-critical';
        $effLabel = 'Degraded';
    }

    $progress = $bus->service_progress;
    $overdue = $bus->next_service_km < 0;
    $barColor = $overdue ? 'bg-danger-critical' : ($progress <= 30 ? 'bg-sync-pending' : 'bg-sync-active');
@endphp
<tr class="hover:bg-surface-container-low transition-colors bus-row"
    data-id="{{ $bus->id }}"
    data-bus-number="{{ $bus->bus_number }}"
    data-plate="{{ $bus->plate }}"
    data-model="{{ $bus->model }}"
    data-operator="{{ $bus->driver?->user?->name ?? $bus->operator }}"
    data-driver-id="{{ $bus->driver_id }}"
    data-status="{{ $bus->status }}"
    data-odometer="{{ $bus->odometer }}"
    data-fuel="{{ $bus->fuel_month_liters }}"
    data-efficiency="{{ $bus->km_per_liter }}"
    data-next-service="{{ $bus->next_service_km }}"
    data-route="{{ $bus->route }}">
<td class="py-4 px-space-md">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center text-on-surface font-headline-sm text-headline-sm">
                    {{ str_pad($bus->bus_number, 2, '0', STR_PAD_LEFT) }}
                  </div>
<div class="flex flex-col">
<span class="font-headline-sm text-headline-sm text-on-surface font-bold">Bus #{{ $bus->bus_number }}</span>
<span class="font-body-sm text-body-sm text-outline">{{ $bus->model ?? 'Unspecified chassis' }}</span>
</div>
</div>
</td>
<td class="py-4 px-space-md font-mono font-medium text-body-sm">
<span class="bg-surface-card px-2 py-1 rounded shadow-xs">{{ $bus->plate }}</span>
</td>
<td class="py-4 px-space-md">
@if($bus->driver?->user)
<div class="flex flex-col">
<span class="font-body-md text-body-md font-semibold text-on-surface">{{ $bus->driver->user->name }}</span>
<span class="font-body-sm text-body-sm text-outline text-xs truncate max-w-[16rem]">{{ $bus->route ?: 'No route assigned' }}</span>
</div>
@else
<span class="font-body-sm text-body-sm text-outline italic">No Driver (Depot)</span>
@endif
</td>
<td class="py-4 px-space-md">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $st['badge'] }}">
<span class="w-2 h-2 rounded-full {{ $st['dot'] }}"></span>
                  {{ $st['label'] }}
                </span>
</td>
<td class="py-4 px-space-md font-label-numeric text-on-surface text-base">
                {{ number_format($bus->odometer) }} <span class="text-xs text-outline font-normal">KM</span>
</td>
<td class="py-4 px-space-md">
<div class="flex flex-col">
<span class="font-body-md text-body-md font-semibold">{{ number_format($bus->fuel_month_liters) }} L</span>
{{-- <span class="font-body-sm text-body-sm text-outline text-xs">${{ number_format((float) $bus->fuel_cost, 2) }} USD</span> --}}
</div>
</td>
<td class="py-4 px-space-md">
<div class="flex items-center gap-2">
<span class="font-label-numeric text-label-numeric font-bold {{ $effColor }}">{{ number_format($efficiency, 1) }}</span>
<span class="px-2 py-0.5 rounded text-xs font-semibold bg-surface-container-high text-on-surface-variant">{{ $effLabel }}</span>
</div>
</td>
<td class="py-4 px-space-md">
@if($overdue)
<div class="flex flex-col">
<span class="font-body-md text-body-md text-danger-critical font-bold">OVERDUE</span>
<span class="text-xs text-danger-critical">Exceeded by {{ number_format(abs($bus->next_service_km)) }} KM</span>
</div>
@else
<div class="flex flex-col">
<span class="font-body-md text-body-md text-on-surface font-medium">{{ number_format($bus->next_service_km) }} KM</span>
<div class="w-24 h-1.5 rounded-full bg-surface-container-highest mt-1 overflow-hidden">
<div class="{{ $barColor }} h-full rounded-full" style="width: {{ $progress }}%"></div>
</div>
</div>
@endif
</td>
<td class="py-4 px-space-md text-right">
<div class="flex items-center justify-end gap-1">
<button class="p-1.5 rounded-lg text-outline hover:text-primary hover:bg-surface-card transition-colors" title="Edit Bus Details" onclick="openEditBusModal({{ $bus->id }})">
<span class="material-symbols-outlined text-lg">edit</span>
</button>
<button class="p-1.5 rounded-lg text-outline hover:text-danger-critical hover:bg-error-container/20 transition-colors" title="Delete Bus" onclick="openDeleteBusModal({{ $bus->id }}, '{{ addslashes($bus->bus_number) }}', '{{ addslashes($bus->plate) }}')">
<span class="material-symbols-outlined text-lg">delete</span>
</button>
</div>
</td>
</tr>
@empty
<tr id="emptyBusesRow">
<td class="py-12 text-center text-outline" colspan="9">
<div class="flex flex-col items-center justify-center gap-2">
<span class="material-symbols-outlined text-4xl">directions_bus</span>
<span class="font-headline-sm text-headline-sm">No buses found</span>
<p class="font-body-sm text-body-sm max-w-sm">Click "+ Add New Bus" above to provision a vehicle into the fleet.</p>
</div>
</td>
</tr>
@endforelse
</tbody>
</table>
</div>
<div class="px-space-md py-space-sm bg-surface-container-low flex flex-col sm:flex-row items-center justify-between gap-space-sm">
<span class="font-body-sm text-body-sm text-outline" id="busesShowingCount">
          Showing <span class="font-semibold text-on-surface">{{ count($buses) }}</span> of <span class="font-semibold text-on-surface">{{ $totalBuses }}</span> configured vehicles
        </span>
<div class="flex items-center gap-1">
<button class="px-3 py-1.5 rounded-lg bg-surface-card text-on-surface-variant font-body-sm text-body-sm hover:bg-surface-container-high transition-colors shadow-xs">Previous</button>
<button class="px-3 py-1.5 rounded-lg bg-primary-container text-on-primary font-body-sm text-body-sm font-semibold shadow-xs">1</button>
<button class="px-3 py-1.5 rounded-lg bg-surface-card text-on-surface font-body-sm text-body-sm hover:bg-surface-container-high transition-colors shadow-xs">Next</button>
</div>
</div>
</div>
</div>

<!-- ================= ADD BUS MODAL ================= -->
<div class="fixed inset-0 z-50 bg-inverse-surface/60 backdrop-blur-sm flex items-center justify-center p-space-md hidden" id="addBusModal">
<div class="bg-surface-card w-full max-w-2xl rounded-2xl shadow-xl overflow-hidden flex flex-col max-h-[92vh]">
<div class="px-space-lg py-space-md bg-surface-container-low flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-primary-container text-on-primary flex items-center justify-center">
<span class="material-symbols-outlined">directions_bus</span>
</div>
<div>
<h2 class="font-headline-md text-headline-md text-on-surface">Provision New Vehicle</h2>
<p class="font-body-sm text-body-sm text-outline">Register a bus into the active fleet roster</p>
</div>
</div>
<button class="text-outline hover:text-on-surface transition-colors" onclick="toggleBusModal('addBusModal', false)">
<span class="material-symbols-outlined text-xl">close</span>
</button>
</div>
<form class="p-space-lg flex flex-col gap-space-md overflow-y-auto" id="addBusForm" method="POST" action="{{ route('buses.store') }}">
@csrf
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Bus Identifier #</label>
<div class="flex items-center gap-2">
<span class="font-body-md text-body-md font-semibold text-outline">Bus&nbsp;#</span>
<input class="flex-1 px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="addBusNumber" name="bus_number" placeholder="45" required type="text"/>
</div>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">License Plate</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md font-mono focus:outline-none focus:ring-2 focus:ring-primary-container" id="addBusPlate" name="plate" placeholder="YT-901-XZ" required type="text"/>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Chassis Model</label>
<select class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="addBusModel" name="model">
<option>Scania K310 City</option>
<option>Volvo B8RLE Low-Floor</option>
<option>Mercedes Citaro C2</option>
<option>MAN Lion's City 12G</option>
</select>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Fleet Status</label>
<select class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="addBusStatus" name="status">
<option value="standby">Ready / Standby</option>
<option value="active">Active Shift</option>
<option value="depot">In Depot</option>
<option value="maintenance">In Maintenance</option>
<option value="out_of_service">Out of Service</option>
</select>
</div>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Assigned Driver</label>
<select class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="addBusDriver" name="driver_id">
<option value="">-- Assign Driver Later (Depot Standby) --</option>
@foreach($drivers as $driver)
@continue($driver->bus)
<option value="{{ $driver->id }}">{{ $driver->user->name ?? 'Unnamed Driver' }}{{ $driver->badge_id ? ' (' . $driver->badge_id . ')' : '' }}</option>
@endforeach
</select>
<p class="font-body-sm text-body-sm text-outline text-xs">Only drivers not assigned to another bus are listed.</p>
</div>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-space-md">
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Odometer (KM)</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="addBusOdo" name="odometer" min="0" placeholder="12500" type="number"/>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Month Fuel (L)</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="addBusFuel" name="fuel_month_liters" min="0" placeholder="1000" type="number"/>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Km / Liter</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="addBusEfficiency" name="km_per_liter" min="0" step="0.01" placeholder="3.5" type="number"/>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Next Service (KM)</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="addBusNextService" name="next_service_km" placeholder="10000" type="number" value="10000"/>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Assigned Route</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="addBusRoute" name="route" placeholder="e.g. Route 14: Central Metro Ã¢â€ â€™ North Highlands" type="text"/>
</div>
</div>
<div class="flex items-center justify-end gap-space-sm pt-space-sm">
<button class="px-4 py-2.5 rounded-lg bg-surface-container-high text-on-surface font-body-md text-body-md hover:bg-surface-variant transition-colors" onclick="toggleBusModal('addBusModal', false)" type="button">
            Cancel
          </button>
<button class="flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary-container text-on-primary font-body-md text-body-md font-semibold hover:bg-primary transition-colors shadow-sm" id="addBusSubmitBtn" type="submit">
<span class="material-symbols-outlined text-lg">check</span>
<span>Save Asset to Fleet</span>
</button>
</div>
</form>
</div>
</div>

<!-- ================= EDIT BUS MODAL ================= -->
<div class="fixed inset-0 z-50 bg-inverse-surface/60 backdrop-blur-sm flex items-center justify-center p-space-md hidden" id="editBusModal">
<div class="bg-surface-card w-full max-w-2xl rounded-2xl shadow-xl overflow-hidden flex flex-col max-h-[92vh]">
<div class="px-space-lg py-space-md bg-surface-container-low flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-primary text-on-primary flex items-center justify-center">
<span class="material-symbols-outlined">edit</span>
</div>
<div>
<h2 class="font-headline-md text-headline-md text-on-surface">Edit Bus Asset</h2>
<p class="font-body-sm text-body-sm text-outline" id="editBusSubtitle">Update vehicle details and assignment</p>
</div>
</div>
<button class="text-outline hover:text-on-surface transition-colors" onclick="toggleBusModal('editBusModal', false)">
<span class="material-symbols-outlined text-xl">close</span>
</button>
</div>
<form class="p-space-lg flex flex-col gap-space-md overflow-y-auto" id="editBusForm" method="POST" action="">
@csrf
@method('PUT')
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Bus Identifier #</label>
<div class="flex items-center gap-2">
<span class="font-body-md text-body-md font-semibold text-outline">Bus&nbsp;#</span>
<input class="flex-1 px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="editBusNumber" name="bus_number" required type="text"/>
</div>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">License Plate</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md font-mono focus:outline-none focus:ring-2 focus:ring-primary-container" id="editBusPlate" name="plate" required type="text"/>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Chassis Model</label>
<select class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="editBusModel" name="model">
<option>Scania K310 City</option>
<option>Volvo B8RLE Low-Floor</option>
<option>Mercedes Citaro C2</option>
<option>MAN Lion's City 12G</option>
</select>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Fleet Status</label>
<select class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="editBusStatus" name="status">
<option value="standby">Ready / Standby</option>
<option value="active">Active Shift</option>
<option value="depot">In Depot</option>
<option value="maintenance">In Maintenance</option>
<option value="out_of_service">Out of Service</option>
</select>
</div>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Assigned Driver</label>
<select class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="editBusDriver" name="driver_id">
<option value="">-- Unassigned (Depot Standby) --</option>
@foreach($drivers as $driver)
<option value="{{ $driver->id }}" data-bus-id="{{ $driver->bus?->id }}">{{ $driver->user->name ?? 'Unnamed Driver' }}{{ $driver->badge_id ? ' (' . $driver->badge_id . ')' : '' }}</option>
@endforeach
</select>
<p class="font-body-sm text-body-sm text-outline text-xs">Only available drivers and the driver currently on this bus are listed.</p>
</div>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-space-md">
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Odometer (KM)</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="editBusOdo" name="odometer" min="0" type="number"/>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Month Fuel (L)</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="editBusFuel" name="fuel_month_liters" min="0" type="number"/>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Km / Liter</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="editBusEfficiency" name="km_per_liter" min="0" step="0.01" type="number"/>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Next Service (KM)</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="editBusNextService" name="next_service_km" type="number"/>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-caps text-label-caps text-outline uppercase">Assigned Route</label>
<input class="px-3 py-2 bg-surface-container-low rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container" id="editBusRoute" name="route" type="text"/>
</div>
</div>
<div class="flex items-center justify-end gap-space-sm pt-space-sm">
<button class="px-4 py-2.5 rounded-lg bg-surface-container-high text-on-surface font-body-md text-body-md hover:bg-surface-variant transition-colors" onclick="toggleBusModal('editBusModal', false)" type="button">
            Cancel
          </button>
<button class="flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary text-on-primary font-body-md text-body-md font-semibold hover:bg-primary-container transition-colors shadow-sm" id="editBusSubmitBtn" type="submit">
<span class="material-symbols-outlined text-lg">save</span>
<span>Update Bus Asset</span>
</button>
</div>
</form>
</div>
</div>

<!-- ================= MODERN DELETE CONFIRMATION ALERT ================= -->
<div class="fixed inset-0 z-[60] bg-inverse-surface/60 backdrop-blur-md flex items-center justify-center p-space-md hidden" id="deleteBusModal">
<div class="bg-surface-card w-full max-w-md rounded-2xl shadow-2xl overflow-hidden flex flex-col delete-dialog">
<div class="relative px-space-lg pt-space-lg pb-space-md flex flex-col items-center text-center gap-3">
<div class="w-16 h-16 rounded-full bg-error-container/70 flex items-center justify-center delete-icon-ring">
<span class="material-symbols-outlined text-danger-critical text-3xl">delete_forever</span>
</div>
<div>
<h2 class="font-headline-md text-headline-md text-on-surface">Remove Bus from Fleet?</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1">This action is permanent and cannot be undone.</p>
</div>
</div>
<div class="px-space-lg">
<div class="bg-surface-container-low rounded-xl p-space-md flex items-center justify-between gap-3 border border-outline-variant/30">
<div class="flex items-center gap-3 min-w-0">
<div class="w-11 h-11 rounded-lg bg-danger-critical/10 text-danger-critical flex items-center justify-center shrink-0">
<span class="material-symbols-outlined">directions_bus</span>
</div>
<div class="flex flex-col min-w-0 text-left">
<span class="font-headline-sm text-headline-sm text-on-surface font-bold truncate" id="deleteBusNumber">Bus</span>
<span class="font-mono text-body-sm text-outline truncate" id="deleteBusPlate">Ã¢â‚¬â€</span>
</div>
</div>
<span class="material-symbols-outlined text-danger-critical shrink-0">warning</span>
</div>
</div>
<div class="px-space-lg pt-space-md">
<div class="bg-error-container/30 border border-danger-critical/30 rounded-xl p-3 text-xs text-danger-critical font-medium flex items-start gap-2">
<span class="material-symbols-outlined text-base shrink-0 mt-0.5">info</span>
<span>The vehicle record, telemetry history, and roster assignment for this bus will be permanently deleted.</span>
</div>
</div>
<form class="p-space-lg flex items-center gap-3" id="deleteBusForm" method="POST" action="">
@csrf
@method('DELETE')
<button class="flex-1 px-4 py-2.5 rounded-xl bg-surface-container-high text-on-surface font-body-md text-body-md hover:bg-surface-variant transition-colors font-semibold" onclick="toggleBusModal('deleteBusModal', false)" type="button">
          Cancel
        </button>
<button class="flex-1 flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-danger-critical text-on-primary font-body-md text-body-md font-bold hover:bg-error transition-all shadow-md active:scale-95" id="deleteBusSubmitBtn" type="submit">
<span class="material-symbols-outlined text-lg">delete_forever</span>
<span>Delete Bus</span>
</button>
</form>
</div>
</div>

<!-- ================= FLOATING TOAST NOTIFICATION ================= -->
<div class="fixed top-24 right-space-lg z-[70] flex flex-col gap-2 pointer-events-none" id="busToastStack">
<div class="pointer-events-auto hidden items-center gap-3 min-w-[18rem] max-w-sm p-space-md rounded-xl shadow-xl border transition-all duration-300 translate-x-6 opacity-0" id="busToast">
<span class="material-symbols-outlined text-xl shrink-0" id="busToastIcon">check_circle</span>
<span class="font-body-sm text-body-sm font-medium flex-1" id="busToastMessage"></span>
<button class="hover:opacity-70 shrink-0" onclick="hideBusToast()">
<span class="material-symbols-outlined text-base">close</span>
</button>
</div>
</div>

</div>
<script>
    const busCsrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function toggleBusModal(modalId, show) {
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

    let busToastTimer = null;
    function showBusToast(message, isError = false) {
      const toast = document.getElementById('busToast');
      const icon = document.getElementById('busToastIcon');
      const msg = document.getElementById('busToastMessage');
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

      clearTimeout(busToastTimer);
      busToastTimer = setTimeout(hideBusToast, 5000);
    }

    function hideBusToast() {
      const toast = document.getElementById('busToast');
      if (!toast) return;
      toast.classList.add('translate-x-6', 'opacity-0');
      setTimeout(() => {
        toast.classList.add('hidden');
        toast.classList.remove('flex');
      }, 300);
    }

    // Add Bus trigger
    document.getElementById('addBusModalTrigger')?.addEventListener('click', () => {
      document.getElementById('addBusForm').reset();
      toggleBusModal('addBusModal', true);
    });

    // Hide drivers already tied to a different bus; keep the current one selectable.
    function filterAvailableDriverOptions(selectEl, currentBusId) {
      if (!selectEl) return;
      Array.from(selectEl.options).forEach((opt) => {
        const assignedBusId = opt.dataset.busId;
        if (!assignedBusId) {
          opt.hidden = false;
          return;
        }
        opt.hidden = String(assignedBusId) !== String(currentBusId);
      });
    }

    // Populate + open the edit modal from the table row data
    function openEditBusModal(busId) {
      const row = document.querySelector(`.bus-row[data-id="${busId}"]`);
      if (!row) return;

      document.getElementById('editBusNumber').value = row.dataset.busNumber || '';
      document.getElementById('editBusPlate').value = row.dataset.plate || '';
      document.getElementById('editBusModel').value = row.dataset.model || 'Scania K310 City';
      document.getElementById('editBusStatus').value = row.dataset.status || 'standby';
      const driverSelect = document.getElementById('editBusDriver');
      filterAvailableDriverOptions(driverSelect, busId);
      if (driverSelect) driverSelect.value = row.dataset.driverId || '';
      document.getElementById('editBusOdo').value = row.dataset.odometer || 0;
      document.getElementById('editBusFuel').value = row.dataset.fuel || 0;
      document.getElementById('editBusEfficiency').value = row.dataset.efficiency || '';
      document.getElementById('editBusNextService').value = row.dataset.nextService || 10000;
      document.getElementById('editBusRoute').value = row.dataset.route || '';
      document.getElementById('editBusSubtitle').textContent = `Updating Bus #${row.dataset.busNumber} Ã‚Â· ${row.dataset.plate}`;

      document.getElementById('editBusForm').action = `/buses/${busId}`;
      toggleBusModal('editBusModal', true);
    }

    // Open the modern delete confirmation
    function openDeleteBusModal(busId, busNumber, plate) {
      document.getElementById('deleteBusNumber').textContent = `Bus #${busNumber}`;
      document.getElementById('deleteBusPlate').textContent = plate;
      document.getElementById('deleteBusForm').action = `/buses/${busId}`;
      toggleBusModal('deleteBusModal', true);
    }

    async function submitBusForm(formId, buttonId, loadingLabel, isDelete = false) {
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
            'X-CSRF-TOKEN': busCsrfToken,
            'Accept': 'application/json',
          },
          body: new FormData(form),
        });

        let data = {};
        try { data = await response.json(); } catch (e) { data = {}; }

        if (response.ok && data.success) {
          toggleBusModal(formId.replace('Form', 'Modal'), false);
          showBusToast(data.message || 'Operation completed.');
          setTimeout(() => window.location.reload(), 900);
        } else {
          const errors = data.errors
            ? Object.values(data.errors).flat().join(' Ã‚Â· ')
            : (data.message || 'Something went wrong.');
          showBusToast(errors, true);
        }
      } catch (err) {
        showBusToast('Network error. Please try again.', true);
      } finally {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
      }
    }

    document.getElementById('addBusForm')?.addEventListener('submit', (e) => {
      e.preventDefault();
      submitBusForm('addBusForm', 'addBusSubmitBtn', 'Saving...');
    });

    document.getElementById('editBusForm')?.addEventListener('submit', (e) => {
      e.preventDefault();
      submitBusForm('editBusForm', 'editBusSubmitBtn', 'Updating...');
    });

    document.getElementById('deleteBusForm')?.addEventListener('submit', (e) => {
      e.preventDefault();
      submitBusForm('deleteBusForm', 'deleteBusSubmitBtn', 'Deleting...', true);
    });

    // Quick status filters
    const filterPills = document.querySelectorAll('.filter-pill');
    filterPills.forEach(pill => {
      pill.addEventListener('click', () => {
        filterPills.forEach(p => {
          p.classList.remove('bg-primary-container', 'text-on-primary', 'shadow-xs');
          p.classList.add('bg-surface-container-low', 'text-on-surface');
        });
        pill.classList.remove('bg-surface-container-low', 'text-on-surface');
        pill.classList.add('bg-primary-container', 'text-on-primary', 'shadow-xs');
        applyBusFilters();
      });
    });

    // Dynamic search + model filter
    const busSearch = document.getElementById('rosterSearch');
    const modelFilter = document.getElementById('modelFilter');

    function applyBusFilters() {
      const query = (busSearch?.value || '').toLowerCase().trim();
      const activePill = document.querySelector('.filter-pill.bg-primary-container');
      const statusFilter = activePill ? activePill.dataset.filter : 'all';
      const modelValue = modelFilter ? modelFilter.value : 'all';

      let visible = 0;
      document.querySelectorAll('.bus-row').forEach(row => {
        const haystack = [
          row.dataset.busNumber, row.dataset.plate, row.dataset.model,
          row.dataset.operator, row.dataset.route
        ].join(' ').toLowerCase();

        const matchesSearch = !query || haystack.includes(query);
        const matchesStatus = statusFilter === 'all'
          || row.dataset.status === statusFilter
          || (statusFilter === 'depot' && ['depot', 'maintenance'].includes(row.dataset.status));
        const matchesModel = modelValue === 'all' || row.dataset.model === modelValue;

        const show = matchesSearch && matchesStatus && matchesModel;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
      });

      const countEl = document.getElementById('busesShowingCount');
      if (countEl) countEl.querySelector('span').textContent = visible;
    }

    busSearch?.addEventListener('input', applyBusFilters);
    modelFilter?.addEventListener('change', applyBusFilters);

    // CSV Export
    document.getElementById('exportCsvBtn')?.addEventListener('click', () => {
      let csv = 'Bus Number,License Plate,Model,Driver,Status,Odometer (KM),Month Fuel (L),Km/L,Next Service (KM),Route\n';
      document.querySelectorAll('.bus-row').forEach(r => {
        csv += `"${r.dataset.busNumber}","${r.dataset.plate}","${r.dataset.model}","${r.dataset.operator}","${r.dataset.status}","${r.dataset.odometer}","${r.dataset.fuel}","${r.dataset.efficiency}","${r.dataset.nextService}","${r.dataset.route}"\n`;
      });
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.setAttribute('href', url);
      link.setAttribute('download', `fleet_buses_roster_${new Date().toISOString().slice(0, 10)}.csv`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    });
</script></main></div></body>
</html>
