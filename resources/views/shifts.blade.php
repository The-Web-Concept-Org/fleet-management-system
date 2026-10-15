<!DOCTYPE html>

<html lang="en"><head><meta charset="utf-8"/><meta content="width=device-width, initial-scale=1.0" name="viewport"/><meta content="web_dashboard" name="shell-type"/><link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/><link href="https://fonts.googleapis.com" rel="preconnect"/><link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;family=Sora:wght@400;500;600;700&amp;family=Poppins:wght@600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/><title>Shift Management &amp; Meter Audits &bull; Lancotri</title><style>@layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}main>:first-child{margin-top:0!important;}main>:last-child{margin-bottom:0!important;}}::-webkit-scrollbar{display:none;}</style><script src="https://cdn.tailwindcss.com"></script><script id="tailwind-config">tailwind.config = { darkMode: "class", theme: { extend: { "colors": { "outline-variant": "#D1D5DB", "on-surface": "#111827", "surface-variant": "#E1E5EC", "inverse-on-surface": "#F1F5F9", "on-primary": "#ffffff", "surface-container-high": "#E1E5EC", "on-secondary": "#ffffff", "secondary": "#0A81B4", "primary-fixed-dim": "#E9A0A2", "on-primary-container": "#ffffff", "background": "#F7F8FA", "error": "#ba1a1a", "border-strong": "#CBD5E1", "tertiary-fixed-dim": "#8FCBE5", "on-tertiary-container": "#E7F4FB", "tertiary-container": "#0A81B4", "tertiary-fixed": "#CFEAF7", "sync-active": "#10B981", "tertiary": "#0A6E99", "surface-container-low": "#F1F3F6", "surface": "#F7F8FA", "on-tertiary-fixed": "#052E42", "surface-tint": "#BD1C23", "on-surface-variant": "#4B5563", "on-secondary-fixed": "#052E42", "on-secondary-container": "#0A5473", "on-primary-fixed-variant": "#8E1418", "surface-card": "#FFFFFF", "on-secondary-fixed-variant": "#0A5C80", "inverse-surface": "#283044", "primary-container": "#BD1C23", "on-primary-fixed": "#3E0A0C", "surface-container-lowest": "#ffffff", "secondary-fixed": "#CFEAF7", "secondary-fixed-dim": "#8FCBE5", "surface-bright": "#F7F8FA", "on-error": "#ffffff", "primary-fixed": "#F7D2D3", "on-tertiary-fixed-variant": "#0A5C80", "surface-dim": "#D2D9E3", "primary": "#A11217", "inverse-primary": "#F2A7A9", "on-error-container": "#93000a", "surface-container": "#E9ECF1", "danger-critical": "#EF4444", "surface-container-highest": "#D8DEE7", "on-tertiary": "#ffffff", "error-container": "#ffdad6", "secondary-container": "#CFEAF7", "sync-pending": "#F59E0B", "on-background": "#111827", "sync-offline": "#64748B", "canvas-base": "#F8FAFC", "outline": "#6B7280" }, "borderRadius": { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" }, "spacing": { "space-xl": "2.25rem", "space-lg": "1.5rem", "space-md": "1rem", "space-sm": "0.5rem", "gutter-tablet": "1.5rem", "gutter": "1rem", "space-xs": "0.25rem", "margin": "1rem", "margin-tablet": "2rem" }, "fontFamily": { "label-action": [ "Plus Jakarta Sans" ], "headline-sm": [ "Sora" ], "headline-md": [ "Sora" ], "headline-lg": [ "Sora" ], "body-sm": [ "Plus Jakarta Sans" ], "body-lg": [ "Plus Jakarta Sans" ], "label-numeric": [ "Sora" ], "display-odometer": [ "Sora" ], "label-caps": [ "Sora" ], "body-md": [ "Plus Jakarta Sans" ] }, "fontSize": { "label-action": [ "18px", { "lineHeight": "24px", "fontWeight": "600" } ], "headline-sm": [ "18px", { "lineHeight": "24px", "fontWeight": "600" } ], "headline-md": [ "22px", { "lineHeight": "28px", "fontWeight": "600" } ], "headline-lg": [ "30px", { "lineHeight": "36px", "letterSpacing": "-0.01em", "fontWeight": "700" } ], "body-sm": [ "13px", { "lineHeight": "18px", "fontWeight": "400" } ], "body-lg": [ "17px", { "lineHeight": "24px", "fontWeight": "500" } ], "label-numeric": [ "20px", { "lineHeight": "24px", "letterSpacing": "0.02em", "fontWeight": "700" } ], "display-odometer": [ "44px", { "lineHeight": "48px", "letterSpacing": "-0.02em", "fontWeight": "700" } ], "label-caps": [ "12px", { "lineHeight": "16px", "letterSpacing": "0.08em", "fontWeight": "700" } ], "body-md": [ "15px", { "lineHeight": "22px", "fontWeight": "400" } ] } } } };</script>@include('partials.brand-styles')
  <link rel="icon" type="image/svg+xml" href="{{ asset('images/lancotri-mark.svg') }}">
</head><body class="bg-surface font-body-md text-body-md text-on-surface antialiased min-h-screen"><aside class="fixed left-0 top-0 h-full w-64 bg-surface-card z-50 flex flex-col justify-between shadow-[0_1px_8px_rgba(0,0,0,0.04)]"><div class="flex flex-col flex-1"><div class="h-16 px-space-md flex items-center gap-space-sm">@include('partials.brand-logo')</div><div class="px-space-md py-space-sm"><span class="font-label-caps text-label-caps text-outline uppercase tracking-wider">Transit Operations</span></div><nav class="flex-1 px-space-sm flex flex-col gap-1" data-active-classes="bg-primary-container text-on-primary font-bold shadow-sm"><a class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg text-on-surface-variant font-body-md text-body-md hover:bg-surface-container-high hover:text-on-surface transition-colors" data-path="dashboard" href="{{ route('dashboard') }}"><span class="material-symbols-outlined text-lg">space_dashboard</span><span>Dashboard</span></a><a class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg text-on-surface-variant font-body-md text-body-md hover:bg-surface-container-high hover:text-on-surface transition-colors" data-path="buses" href="{{ route('buses') }}"><span class="material-symbols-outlined text-lg">directions_bus</span><span>Buses</span></a><a aria-current="page" class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg transition-colors bg-primary-container text-on-primary font-bold shadow-sm" data-path="shifts" href="{{ route('shifts') }}"><span class="material-symbols-outlined text-lg">schedule</span><span>Shifts</span></a><a class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg text-on-surface-variant font-body-md text-body-md hover:bg-surface-container-high hover:text-on-surface transition-colors" data-path="fuel" href="#"><span class="material-symbols-outlined text-lg">local_gas_station</span><span>Fuel</span></a><a class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg text-on-surface-variant font-body-md text-body-md hover:bg-surface-container-high hover:text-on-surface transition-colors" data-path="maintenance" href="#"><span class="material-symbols-outlined text-lg">build</span><span>Maintenance</span></a><a class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg text-on-surface-variant font-body-md text-body-md hover:bg-surface-container-high hover:text-on-surface transition-colors" data-path="drivers" href="{{ route('drivers') }}"><span class="material-symbols-outlined text-lg">badge</span><span>Drivers</span></a><a class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg text-on-surface-variant font-body-md text-body-md hover:bg-surface-container-high hover:text-on-surface transition-colors" data-path="reports" href="#"><span class="material-symbols-outlined text-lg">bar_chart</span><span>Reports</span></a></nav></div><div class="p-space-md bg-surface-container-low m-space-sm rounded-xl flex items-center justify-between"><div class="flex items-center gap-space-xs"><span class="w-2.5 h-2.5 rounded-full bg-sync-active animate-pulse"></span><span class="font-body-sm text-body-sm text-on-surface">GPS Feed Synced</span></div><span class="material-symbols-outlined text-outline text-lg">wifi</span></div></aside><div class="pl-64 flex flex-col min-h-screen"><header class="fixed top-0 left-64 right-0 h-16 bg-surface/90 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-40 px-space-lg flex items-center justify-between gap-space-md"><div class="flex-1 max-w-md"><div class="relative flex items-center w-full"><span class="material-symbols-outlined absolute left-3 text-outline text-lg pointer-events-none">search</span><input class="w-full pl-9 pr-space-md py-2 bg-surface-card rounded-lg text-on-surface placeholder:text-outline font-body-sm text-body-sm focus:outline-none focus:ring-2 focus:ring-primary-container shadow-sm" placeholder="Search bus, driver, or shift #..." type="text"/></div></div><div class="flex items-center gap-space-lg"><div class="hidden lg:flex items-center gap-2 px-space-md py-1.5 rounded-full bg-surface-card shadow-sm"><span class="w-2 h-2 rounded-full bg-sync-active"></span><span class="font-body-sm text-body-sm font-semibold text-secondary">Depot Online</span><span class="text-outline text-xs">&bull;</span><span class="font-body-sm text-body-sm text-on-surface-variant">18 active vehicles</span></div><button class="relative p-2 rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors"><span class="material-symbols-outlined text-xl">notifications</span><span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-danger-critical ring-2 ring-surface"></span></button><div class="flex items-center gap-space-sm pl-space-sm"><img alt="Profile" class="w-8 h-8 rounded-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCttXgthIfoSu1OhXg4Jji6OMcw-zk-tlENHrN92xZQQFgNPXVbEp8Tzf4H0twMaL7bDdVr1LLutIwNcDT6slU_mcsYaDzxbxsP9mgyEs4qQQHXhw7n7ujABjdezf8GsxUN29HGq1GBzBV-WnTMIN18UHXw274zq65XOoEhqyRsNKcLVqcdF6kL8dPF_VA-32Yd8y7SR8rT-rfgvN_N_cnbx430fi4ZoCQyRwyMmfmfGpoSdVDFlA0y"/><div class="hidden sm:flex flex-col text-left"><span class="font-body-sm text-body-sm font-semibold text-on-surface leading-tight">{{ auth()->user()->name ?? 'Alex Turner' }}</span><span class="font-label-caps text-label-caps text-outline leading-tight">Operations Admin</span></div></div></div></header><main class="w-full pt-16 flex-1 px-space-lg py-space-lg bg-surface"><div class="flex flex-col w-full gap-space-lg">
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
<span class="font-headline-sm text-headline-sm text-on-surface leading-tight">18 <span class="font-body-sm text-body-sm text-outline">/ 32</span></span>
</div>
</div>
<div class="flex items-center gap-space-sm px-space-md py-1.5 bg-surface-container-low rounded-lg">
<span class="w-2.5 h-2.5 rounded-full bg-danger-critical animate-pulse"></span>
<div class="flex flex-col">
<span class="font-label-caps text-label-caps text-outline leading-tight">FLAGGED AUDITS</span>
<span class="font-headline-sm text-headline-sm text-danger-critical leading-tight">2 Logs</span>
</div>
</div>
<div class="flex items-center gap-space-sm px-space-md py-1.5 bg-surface-container-low rounded-lg">
<span class="material-symbols-outlined text-tertiary-container text-xl">speed</span>
<div class="flex flex-col">
<span class="font-label-caps text-label-caps text-outline leading-tight">DAY MILEAGE</span>
<span class="font-headline-sm text-headline-sm text-on-surface leading-tight">4,812 <span class="font-body-sm text-body-sm text-outline">KM</span></span>
</div>
</div>
</div>
</div>
<!-- Filter & Control Toolbar -->
<div class="bg-surface-card rounded-xl p-space-md shadow-sm flex flex-col gap-space-md">
<!-- Top Filter Row -->
<div class="flex flex-wrap items-center justify-between gap-space-md">
<div class="flex flex-wrap items-center gap-space-sm flex-1">
<!-- Date Range Picker -->
<div class="flex items-center gap-space-xs px-space-md py-2.5 bg-surface-container-low rounded-lg text-on-surface cursor-pointer hover:bg-surface-container transition-colors shadow-sm">
<span class="material-symbols-outlined text-outline text-lg">calendar_month</span>
<span class="font-body-md text-body-md font-medium">Oct 24, 2024 - Today</span>
<span class="material-symbols-outlined text-outline text-sm ml-1">expand_more</span>
</div>
<!-- Bus Filter Dropdown -->
<div class="relative flex items-center">
<select class="appearance-none bg-surface-container-low font-body-md text-body-md text-on-surface pl-9 pr-9 py-2.5 rounded-lg focus:outline-none focus:bg-surface-container transition-colors cursor-pointer shadow-sm">
<option>All Buses (32 total)</option>
<option selected="">Scania Bus #42</option>
<option>Volvo B8R #19</option>
<option>MAN Lion #07</option>
<option>BYD Electric #88</option>
</select>
<span class="material-symbols-outlined absolute left-3 text-outline text-lg pointer-events-none">directions_bus</span>
<span class="material-symbols-outlined absolute right-2.5 text-outline text-sm pointer-events-none">expand_more</span>
</div>
<!-- Driver Filter Dropdown -->
<div class="relative flex items-center">
<select class="appearance-none bg-surface-container-low font-body-md text-body-md text-on-surface pl-9 pr-9 py-2.5 rounded-lg focus:outline-none focus:bg-surface-container transition-colors cursor-pointer shadow-sm">
<option>All Drivers (28)</option>
<option selected="">Marcus Vance (#4812)</option>
<option>Elena Rostova (#3104)</option>
<option>Devon Miles (#5211)</option>
<option>Hanna Schmidt (#2099)</option>
</select>
<span class="material-symbols-outlined absolute left-3 text-outline text-lg pointer-events-none">badge</span>
<span class="material-symbols-outlined absolute right-2.5 text-outline text-sm pointer-events-none">expand_more</span>
</div>
<!-- Quick Reset Filter Button -->
<button class="px-space-sm py-2 text-outline hover:text-on-surface text-body-sm font-body-sm flex items-center gap-1 transition-colors">
<span class="material-symbols-outlined text-base">restart_alt</span>
<span>Reset</span>
</button>
</div>
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
<button class="px-space-md py-2 rounded-lg font-body-md text-body-md text-on-surface-variant hover:bg-surface-container-low transition-colors whitespace-nowrap">
        All Shifts <span class="ml-1 px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface text-body-sm font-semibold">32</span>
</button>
<button class="px-space-md py-2 rounded-lg font-body-md text-body-md text-on-surface-variant hover:bg-surface-container-low transition-colors whitespace-nowrap">
        Active / Open <span class="ml-1 px-2 py-0.5 rounded-full bg-surface-variant text-primary font-body-sm font-semibold">18</span>
</button>
<button class="px-space-md py-2 rounded-lg bg-primary text-on-primary font-body-md text-body-md font-semibold whitespace-nowrap shadow-sm">
        Completed <span class="ml-1 px-2 py-0.5 rounded-full bg-on-primary/20 text-on-primary text-body-sm font-bold">14</span>
</button>
<button class="px-space-md py-2 rounded-lg font-body-md text-body-md text-danger-critical hover:bg-error-container/40 transition-colors whitespace-nowrap flex items-center gap-1">
<span>Flagged / Needs Review</span>
<span class="ml-1 px-2 py-0.5 rounded-full bg-danger-critical text-on-error text-body-sm font-bold">2</span>
</button>
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
<span class="font-body-sm text-body-sm text-on-surface-variant">Showing today's verified &amp; active logs</span>
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
<!-- FEATURED ROW: Shift #SH-9042 (Selected / In Focus) -->
<tr class="bg-surface-container-high transition-colors group">
<td class="py-4 px-space-md align-middle">
<div class="flex items-center gap-2">
<span class="w-2 h-2 rounded-full bg-primary-container"></span>
<div class="flex flex-col">
<span class="font-label-numeric text-body-lg text-primary font-bold">#SH-9042</span>
<span class="font-body-sm text-body-sm text-outline">Oct 24 &bull; 06:15 AM</span>
</div>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col">
<span class="font-headline-sm text-headline-sm text-on-surface font-bold">BUS-42</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Scania Citywide LF</span>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex items-center gap-space-xs">
<div class="w-7 h-7 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-bold text-xs">MV</div>
<div class="flex flex-col">
<span class="font-body-md text-body-md font-semibold text-on-surface">Marcus Vance</span>
<span class="font-body-sm text-body-sm text-outline">ID: #4812</span>
</div>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex items-center gap-2">
<div class="flex flex-col">
<span class="font-label-numeric text-body-md text-on-surface">142,518 <span class="text-xs text-outline font-normal">km</span></span>
<button class="text-left font-body-sm text-body-sm text-primary hover:underline flex items-center gap-0.5">
<span class="material-symbols-outlined text-xs">photo_camera</span>
<span>06:14 AM</span>
</button>
</div>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex items-center gap-2">
<div class="flex flex-col">
<span class="font-label-numeric text-body-md text-on-surface font-bold">142,760 <span class="text-xs text-outline font-normal">km</span></span>
<button class="text-left font-body-sm text-body-sm text-primary hover:underline flex items-center gap-0.5">
<span class="material-symbols-outlined text-xs">verified</span>
<span>14:32 PM</span>
</button>
</div>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col">
<span class="font-label-numeric text-headline-sm text-on-surface font-bold text-secondary">+242 KM</span>
<span class="font-body-sm text-body-sm text-outline">8h 17m duration</span>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-card text-on-surface font-body-sm text-body-sm shadow-sm font-semibold">
<span class="w-2 h-2 rounded-full bg-sync-active"></span>
<span>Ready for Audit</span>
</span>
</td>
<td class="py-4 px-space-md align-middle text-right">
<div class="inline-flex items-center gap-1">
<button class="px-3 py-1.5 rounded-lg bg-primary-container text-on-primary font-body-sm text-body-sm font-semibold hover:opacity-90 shadow-sm transition-opacity">
                      Audit
                    </button>
<button class="p-1.5 text-on-surface-variant hover:text-on-surface hover:bg-surface-container rounded-lg transition-colors">
<span class="material-symbols-outlined text-xl">more_vert</span>
</button>
</div>
</td>
</tr>
<!-- ROW 2: Completed Shift -->
<tr class="hover:bg-surface-container-low transition-colors">
<td class="py-4 px-space-md align-middle">
<div class="flex items-center gap-2">
<span class="w-2 h-2 rounded-full bg-sync-active"></span>
<div class="flex flex-col">
<span class="font-label-numeric text-body-lg text-on-surface font-bold">#SH-9041</span>
<span class="font-body-sm text-body-sm text-outline">Oct 24 &bull; 05:45 AM</span>
</div>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col">
<span class="font-headline-sm text-headline-sm text-on-surface font-bold">BUS-19</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Volvo 7900 Hybrid</span>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex items-center gap-space-xs">
<div class="w-7 h-7 rounded-full bg-surface-container-high text-on-surface flex items-center justify-center font-bold text-xs">ER</div>
<div class="flex flex-col">
<span class="font-body-md text-body-md font-semibold text-on-surface">Elena Rostova</span>
<span class="font-body-sm text-body-sm text-outline">ID: #3104</span>
</div>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col">
<span class="font-label-numeric text-body-md text-on-surface">89,204 <span class="text-xs text-outline font-normal">km</span></span>
<button class="text-left font-body-sm text-body-sm text-outline hover:text-primary flex items-center gap-0.5">
<span class="material-symbols-outlined text-xs">photo_camera</span>
<span>Verified</span>
</button>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col">
<span class="font-label-numeric text-body-md text-on-surface">89,398 <span class="text-xs text-outline font-normal">km</span></span>
<button class="text-left font-body-sm text-body-sm text-outline hover:text-primary flex items-center gap-0.5">
<span class="material-symbols-outlined text-xs">verified</span>
<span>Verified</span>
</button>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col">
<span class="font-label-numeric text-headline-sm text-on-surface font-bold text-secondary">+194 KM</span>
<span class="font-body-sm text-body-sm text-outline">7h 45m duration</span>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-secondary-fixed/40 text-secondary font-body-sm text-body-sm font-semibold">
<span class="material-symbols-outlined text-xs">check_circle</span>
<span>Approved</span>
</span>
</td>
<td class="py-4 px-space-md align-middle text-right">
<button class="px-3 py-1.5 rounded-lg bg-surface-container-low text-on-surface font-body-sm text-body-sm font-medium hover:bg-surface-container transition-colors">
                    View
                  </button>
</td>
</tr>
<!-- ROW 3: Flagged Discrepancy Shift -->
<tr class="hover:bg-surface-container-low transition-colors bg-error-container/20">
<td class="py-4 px-space-md align-middle">
<div class="flex items-center gap-2">
<span class="w-2 h-2 rounded-full bg-danger-critical"></span>
<div class="flex flex-col">
<span class="font-label-numeric text-body-lg text-danger-critical font-bold">#SH-9039</span>
<span class="font-body-sm text-body-sm text-outline">Oct 24 &bull; 05:00 AM</span>
</div>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col">
<span class="font-headline-sm text-headline-sm text-on-surface font-bold">BUS-07</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">MAN Lion's Coach</span>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex items-center gap-space-xs">
<div class="w-7 h-7 rounded-full bg-danger-critical text-on-error flex items-center justify-center font-bold text-xs">DM</div>
<div class="flex flex-col">
<span class="font-body-md text-body-md font-semibold text-on-surface">Devon Miles</span>
<span class="font-body-sm text-body-sm text-outline">ID: #5211</span>
</div>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col">
<span class="font-label-numeric text-body-md text-on-surface">311,902 <span class="text-xs text-outline font-normal">km</span></span>
<button class="text-left font-body-sm text-body-sm text-outline flex items-center gap-0.5">
<span class="material-symbols-outlined text-xs">photo_camera</span>
<span>Depot Gate</span>
</button>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col">
<span class="font-label-numeric text-body-md text-danger-critical font-bold">312,410 <span class="text-xs text-outline font-normal">km</span></span>
<button class="text-left font-body-sm text-body-sm text-danger-critical font-bold flex items-center gap-0.5">
<span class="material-symbols-outlined text-xs">error</span>
<span>Blurry Image</span>
</button>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col">
<span class="font-label-numeric text-headline-sm text-danger-critical font-bold">+508 KM</span>
<span class="font-body-sm text-body-sm text-danger-critical font-semibold">&gt; 120km Over Route</span>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-danger-critical text-on-error font-body-sm text-body-sm font-bold shadow-sm">
<span class="material-symbols-outlined text-xs">warning</span>
<span>Discrepancy</span>
</span>
</td>
<td class="py-4 px-space-md align-middle text-right">
<button class="px-3 py-1.5 rounded-lg bg-danger-critical text-on-error font-body-sm text-body-sm font-semibold hover:opacity-90 shadow-sm transition-opacity">
                    Resolve
                  </button>
</td>
</tr>
<!-- ROW 4: In Progress Shift -->
<tr class="hover:bg-surface-container-low transition-colors">
<td class="py-4 px-space-md align-middle">
<div class="flex items-center gap-2">
<span class="w-2 h-2 rounded-full bg-sync-pending animate-ping"></span>
<div class="flex flex-col">
<span class="font-label-numeric text-body-lg text-on-surface font-bold">#SH-9043</span>
<span class="font-body-sm text-body-sm text-outline">Oct 24 &bull; 07:30 AM</span>
</div>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col">
<span class="font-headline-sm text-headline-sm text-on-surface font-bold">BUS-88</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">BYD K9 Electric</span>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex items-center gap-space-xs">
<div class="w-7 h-7 rounded-full bg-tertiary text-on-tertiary flex items-center justify-center font-bold text-xs">HS</div>
<div class="flex flex-col">
<span class="font-body-md text-body-md font-semibold text-on-surface">Hanna Schmidt</span>
<span class="font-body-sm text-body-sm text-outline">ID: #2099</span>
</div>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col">
<span class="font-label-numeric text-body-md text-on-surface">45,110 <span class="text-xs text-outline font-normal">km</span></span>
<button class="text-left font-body-sm text-body-sm text-primary flex items-center gap-0.5">
<span class="material-symbols-outlined text-xs">photo_camera</span>
<span>Verified</span>
</button>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex items-center gap-1.5 text-outline">
<span class="material-symbols-outlined text-sm animate-spin">timelapse</span>
<span class="font-body-sm text-body-sm italic">In Route (Open)</span>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<div class="flex flex-col">
<span class="font-label-numeric text-body-lg text-outline">Tracking...</span>
<span class="font-body-sm text-body-sm text-outline">GPS: 68 km live</span>
</div>
</td>
<td class="py-4 px-space-md align-middle">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-high text-primary font-body-sm text-body-sm font-semibold">
<span class="w-2 h-2 rounded-full bg-primary-container"></span>
<span>In Progress</span>
</span>
</td>
<td class="py-4 px-space-md align-middle text-right">
<button class="px-3 py-1.5 rounded-lg bg-surface-container-low text-on-surface font-body-sm text-body-sm font-medium hover:bg-surface-container transition-colors">
                    Track
                  </button>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Table Pagination Footer -->
<div class="p-space-md bg-surface-container-low flex flex-col sm:flex-row items-center justify-between gap-space-sm">
<span class="font-body-sm text-body-sm text-on-surface-variant">Showing 1 to 4 of 32 shift logs logged within selected period</span>
<div class="flex items-center gap-1">
<button class="p-2 rounded-lg bg-surface-card text-outline hover:text-on-surface shadow-sm disabled:opacity-50" disabled="">
<span class="material-symbols-outlined text-base">chevron_left</span>
</button>
<button class="px-3 py-1 rounded-lg bg-primary text-on-primary font-body-sm text-body-sm font-semibold shadow-sm">1</button>
<button class="px-3 py-1 rounded-lg bg-surface-card text-on-surface font-body-sm text-body-sm hover:bg-surface-container shadow-sm">2</button>
<button class="px-3 py-1 rounded-lg bg-surface-card text-on-surface font-body-sm text-body-sm hover:bg-surface-container shadow-sm">3</button>
<button class="p-2 rounded-lg bg-surface-card text-outline hover:text-on-surface shadow-sm">
<span class="material-symbols-outlined text-base">chevron_right</span>
</button>
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
<div class="bg-surface-card rounded-xl p-space-lg shadow-md flex flex-col gap-space-md relative overflow-hidden">
<!-- Header Strip of Drawer Card -->
<div class="flex items-start justify-between gap-space-sm pb-space-sm">
<div class="flex flex-col">
<div class="flex items-center gap-1.5 text-primary-container">
<span class="material-symbols-outlined text-sm">verified</span>
<span class="font-label-caps text-label-caps">INSPECTION DESK</span>
</div>
<h2 class="font-headline-md text-headline-md text-on-surface">Meter Audit: Shift #SH-9042</h2>
<span class="font-body-sm text-body-sm text-outline">Marcus Vance (#4812) &bull; Scania Bus #42</span>
</div>
<span class="px-2.5 py-1 rounded-md bg-secondary-fixed/40 text-secondary font-label-caps text-label-caps uppercase font-bold">Ready</span>
</div>
<!-- Depot Sync & Telemetry Match Badge -->
<div class="p-space-sm rounded-lg bg-surface-container-low flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-secondary text-lg">satellite_alt</span>
<span class="font-body-sm text-body-sm text-on-surface font-medium">Depot North Geofence Sync: <span class="text-secondary font-bold">100% Valid</span></span>
</div>
<span class="font-label-caps text-label-caps text-outline">&plusmn;0.4 km variance</span>
</div>
<!-- Meter Photo Verification Display (Start vs. End Side-by-Side Cards) -->
<div class="flex flex-col gap-space-md">
<!-- Start Meter Verification Box -->
<div class="flex flex-col gap-space-xs bg-surface-container-lowest p-space-sm rounded-xl shadow-sm">
<div class="flex items-center justify-between">
<span class="font-label-caps text-label-caps text-outline uppercase">Start Shift Reading</span>
<span class="font-body-sm text-body-sm text-on-surface-variant font-medium">06:14 AM &bull; Depot Gate 4</span>
</div>
<!-- Start Meter Photo Container -->
<div class="relative w-full h-44 rounded-lg overflow-hidden group shadow-inner">
<img class="w-full h-full object-cover" data-alt="Close-up crisp photo of a modern Scania bus dashboard digital odometer display showing 142518 km in illuminated green numerals under early morning depot garage lighting with clear steering wheel rim" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAN_5SCrE2SVFKlkOL2Zjz_SzxGLEPL_nXsu2Alz6DNM2GFYb_p8TxOrvqlnT_OxegbRLyq7UpOMPqlwyGZwH8xAUKCRqpFgFYVY4Jrsq_IpX58hxx90gXWzVwtxAkL9wQrNvBoCq4pSmdfh7m95PCRS7HcGp85ze0CAbc8CwkGvr0m_8n5Dr1GsIh5B5HRNT5h5qerGpM930zlD5j44aR7FoabXrRqgIAAVRGYGhOQqxyS0t5X5x9A"/>
<div class="absolute inset-0 bg-gradient-to-t from-on-surface/80 via-transparent to-transparent flex items-end p-space-sm justify-between">
<span class="px-2 py-0.5 rounded bg-surface-card/90 text-on-surface font-label-numeric text-body-sm backdrop-blur">142,518 KM</span>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-secondary text-on-secondary font-label-caps text-label-caps">
<span class="material-symbols-outlined text-xs">done_all</span> OCR OK
                </span>
</div>
</div>
</div>
<!-- End Meter Verification Box (Featured End Reading: 142,760 km) -->
<div class="flex flex-col gap-space-xs bg-surface-container-lowest p-space-sm rounded-xl shadow-sm">
<div class="flex items-center justify-between">
<span class="font-label-caps text-label-caps text-primary uppercase font-bold">End Shift Reading</span>
<span class="font-body-sm text-body-sm text-on-surface-variant font-medium">14:32 PM &bull; Depot Bay 12</span>
</div>
<!-- End Meter Photo Container -->
<div class="relative w-full h-44 rounded-lg overflow-hidden group shadow-inner">
<img class="w-full h-full object-cover" data-alt="High resolution detailed photo of digital dashboard odometer reading 142760 km on Scania transit bus during afternoon sunlight with driver steering hub visible in crisp focus" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCy8yTVHaKdLJpd5H8U2VMb749zG-L7-Jh9pAfCTZMzE0im0yXM5MjMNns1u5yYfGDRmJi_T2b0QVgJSyY48qyCyk8qiPC6y0p9tWclwA32xw91QY5SDBS70semqGepDEwxndL40321wDCqyDtNze-dQAej-kuQpIDtYR2o4L4SwpBSRw18srAFADph66sOrFfbE_99Tbg-T2ptEtNMLDzQ9gus8Eay2O_QnTt0S3EEzFvKaHKfD_tz"/>
<div class="absolute inset-0 bg-gradient-to-t from-on-surface/80 via-transparent to-transparent flex items-end p-space-sm justify-between">
<span class="px-2 py-0.5 rounded bg-surface-card/90 text-on-surface font-label-numeric text-body-sm backdrop-blur font-bold">142,760 KM</span>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-secondary text-on-secondary font-label-caps text-label-caps">
<span class="material-symbols-outlined text-xs">verified</span> OCR 99.4%
                </span>
</div>
</div>
</div>
</div>
<!-- Calculated Mileage Differential Breakout -->
<div class="bg-surface-container-high rounded-xl p-space-md flex flex-col gap-space-xs">
<div class="flex items-center justify-between">
<span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Meter Delta:</span>
<span class="font-label-numeric text-label-numeric text-on-surface font-bold">142,760 - 142,518 = 242.0 KM</span>
</div>
<div class="flex items-center justify-between">
<span class="font-body-sm text-body-sm text-on-surface-variant font-medium">GPS Tracked Route:</span>
<span class="font-label-numeric text-body-md text-secondary font-bold">241.6 KM</span>
</div>
<div class="flex items-center justify-between pt-1">
<span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Fuel Consumed (Est.):</span>
<span class="font-body-md text-body-md text-on-surface font-semibold">68.2 L (28.1 L/100km)</span>
</div>
</div>
<!-- Auditor Signature and Approval Buttons -->
<div class="flex flex-col gap-space-sm pt-space-xs">
<button class="w-full h-14 rounded-xl bg-primary-container text-on-primary font-label-action text-label-action flex items-center justify-center gap-2 hover:opacity-95 shadow-md active:translate-y-0.5 transition-all">
<span class="material-symbols-outlined text-2xl">check_circle</span>
<span>Approve Shift Mileage</span>
</button>
<div class="grid grid-cols-2 gap-space-sm">
<button class="h-12 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md font-semibold hover:bg-surface-container transition-colors flex items-center justify-center gap-1">
<span class="material-symbols-outlined text-lg">flag</span>
<span>Flag Recount</span>
</button>
<button class="h-12 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md font-semibold hover:bg-surface-container transition-colors flex items-center justify-center gap-1">
<span class="material-symbols-outlined text-lg">edit_note</span>
<span>Manual Entry</span>
</button>
</div>
</div>
<!-- Audit History Log Strip -->
<div class="pt-space-xs flex items-center gap-space-xs text-outline font-body-sm text-body-sm">
<span class="material-symbols-outlined text-base">history</span>
<span>Logged by {{ auth()->user()->name ?? 'Alex Turner' }} &bull; Auto-validated via Depot OCR Gateway</span>
</div>
</div>
<!-- Live Bus Route Preview Tile -->
<div class="bg-surface-card rounded-xl p-space-md shadow-sm flex flex-col gap-space-sm">
<div class="flex items-center justify-between">
<span class="font-label-caps text-label-caps text-outline uppercase">Active Route Breadcrumbs</span>
<span class="font-body-sm text-body-sm text-primary font-semibold">Route #42-North Express</span>
</div>
<div class="w-full h-36 bg-cover bg-center rounded-lg relative overflow-hidden shadow-inner flex items-end p-space-sm" data-location="Seattle Transit Depot, WA" style="">
<div class="bg-surface-card/90 backdrop-blur px-2.5 py-1 rounded text-xs font-semibold text-on-surface shadow-sm flex items-center gap-1">
<span class="w-2 h-2 rounded-full bg-sync-active"></span>
<span>Final Depot Check-In: 14:32 PM</span>
</div>
</div>
</div>
</div>
</div>
</div></main></div></body></html>
