{{--
    shadcn/ui Application Sidebar with Real-Time Telemetry status
--}}
@php
    $currentRoute = request()->route()?->getName();

    $coreNav = [
        ['label' => 'Dashboard', 'icon' => 'layout-dashboard', 'route' => 'dashboard', 'badge' => null],
        ['label' => 'Transit Routes', 'icon' => 'route', 'route' => 'routes.index', 'badge' => \App\Models\TransitRoute::where('status', 'active')->count() ?: null],
        ['label' => 'Buses Roster', 'icon' => 'bus', 'route' => 'buses', 'badge' => \App\Models\Bus::where('status', 'active')->count() ?: null],
        ['label' => 'Driver Shifts', 'icon' => 'clock-3', 'route' => 'shifts', 'badge' => null],
        ['label' => 'Drivers Roster', 'icon' => 'users', 'route' => 'drivers', 'badge' => null],
    ];

    $logNav = [
        ['label' => 'Fuel Logs', 'icon' => 'fuel', 'route' => 'fuel', 'badge' => null],
        ['label' => 'Maintenance', 'icon' => 'wrench', 'route' => 'maintenance', 'badge' => null],
        ['label' => 'Notifications', 'icon' => 'bell', 'route' => 'notifications', 'badge' => \App\Models\AppNotification::where('user_id', auth()->id())->whereNull('read_at')->count() ?: null],
    ];

    $sysNav = [
        ['label' => 'Account Settings', 'icon' => 'settings', 'route' => 'settings', 'badge' => null],
    ];

    $user = auth()->user();
    $userName = $user->name ?? 'Admin User';
    $userRole = $user && $user->role ? \Illuminate\Support\Str::headline($user->role) : 'Fleet Manager';
    $initials = strtoupper(substr(trim($userName), 0, 2));
@endphp

<aside class="fixed left-0 top-0 h-full w-64 bg-card border-r border-border z-50 flex flex-col justify-between shadow-xs select-none">
    <div class="flex flex-col flex-1 overflow-y-auto">
        <!-- Sidebar Header / Brand -->
        <div class="h-16 px-5 border-b border-border/60 flex items-center justify-between">
            @include('partials.brand-logo', ['logoMarkHeight' => '2rem'])
        </div>

        <!-- Company Status Pill -->
        <div class="px-4 pt-4 pb-1">
            <div class="flex items-center justify-between px-3 py-2 rounded-lg bg-muted/60 border border-border/50 text-xs">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="font-medium text-foreground">{{ config('app.name', 'Lancotri') }}</span>
                </div>
                <span class="text-[10px] text-emerald-600 font-mono font-medium">Online</span>
            </div>
        </div>

        <!-- Navigation Groups -->
        <div class="px-3 py-3 space-y-6">
            <!-- Group 1: Core Operations -->
            <div>
                <div class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground/80 font-mono">
                    Operations
                </div>
                <nav class="space-y-1">
                    @foreach($coreNav as $item)
                        @php
                            $isActive = $item['route'] !== null && $currentRoute === $item['route'];
                            $href = $item['route'] !== null ? route($item['route']) : '#';
                        @endphp
                        <a href="{{ $href }}"
                           class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-all group {{ $isActive ? 'bg-primary/10 text-primary font-semibold border-l-2 border-primary shadow-xs' : 'text-muted-foreground hover:bg-muted/80 hover:text-foreground' }}">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 {{ $isActive ? 'text-primary' : 'text-muted-foreground group-hover:text-foreground' }}"></i>
                                <span>{{ $item['label'] }}</span>
                            </div>
                            @if($item['badge'])
                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                    {{ $item['badge'] }}
                                </span>
                            @endif
                        </a>
                    @endforeach
                </nav>
            </div>

            <!-- Group 2: Logs & Monitoring -->
            <div>
                <div class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground/80 font-mono">
                    Telemetry &amp; Logs
                </div>
                <nav class="space-y-1">
                    @foreach($logNav as $item)
                        @php
                            $isActive = $item['route'] !== null && $currentRoute === $item['route'];
                            $href = $item['route'] !== null ? route($item['route']) : '#';
                        @endphp
                        <a href="{{ $href }}"
                           class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-all group {{ $isActive ? 'bg-primary/10 text-primary font-semibold border-l-2 border-primary shadow-xs' : 'text-muted-foreground hover:bg-muted/80 hover:text-foreground' }}">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 {{ $isActive ? 'text-primary' : 'text-muted-foreground group-hover:text-foreground' }}"></i>
                                <span>{{ $item['label'] }}</span>
                            </div>
                        </a>
                    @endforeach
                </nav>
            </div>

            <!-- Group 3: System & Security -->
            <div>
                <div class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground/80 font-mono">
                    System
                </div>
                <nav class="space-y-1">
                    @foreach($sysNav as $item)
                        @php
                            $isActive = $item['route'] !== null && $currentRoute === $item['route'];
                            $href = $item['route'] !== null ? route($item['route']) : '#';
                        @endphp
                        <a href="{{ $href }}"
                           class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-all group {{ $isActive ? 'bg-primary/10 text-primary font-semibold border-l-2 border-primary shadow-xs' : 'text-muted-foreground hover:bg-muted/80 hover:text-foreground' }}">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 {{ $isActive ? 'text-primary' : 'text-muted-foreground group-hover:text-foreground' }}"></i>
                                <span>{{ $item['label'] }}</span>
                            </div>
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>
    </div>

    <!-- Sidebar Footer: Real-time Telemetry Widget, User Row & Company Brand -->
    <div class="p-3 border-t border-border space-y-2 bg-card">
        <!-- Live Telemetry Card -->
        <div class="p-2.5 rounded-xl border border-border/70 bg-muted/40 flex items-center justify-between">
            <div class="flex items-center gap-2 min-w-0">
                <span class="relative flex h-2 w-2 shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <div class="flex flex-col min-w-0">
                    <span class="text-[11px] font-semibold text-foreground truncate">GPS Feed Synced</span>
                    <span class="text-[10px] text-muted-foreground font-mono" id="telemetryLastSync">Synced just now</span>
                </div>
            </div>
            <button type="button" id="telemetrySyncBtn" title="Sync Telemetry Now" class="p-1.5 rounded-md hover:bg-muted text-muted-foreground hover:text-foreground transition-colors shrink-0">
                <i data-lucide="refresh-cw" id="telemetrySyncIcon" class="w-3.5 h-3.5"></i>
            </button>
        </div>

        <!-- User Profile Strip (Clickable to Settings) -->
        <div class="flex items-center justify-between px-2 py-1.5 rounded-lg hover:bg-muted/50 transition-colors">
            <a href="{{ route('settings') }}" class="flex items-center gap-2.5 min-w-0 flex-1 hover:opacity-80 transition-opacity" title="Open Settings">
                <div class="w-8 h-8 rounded-full bg-primary text-primary-foreground font-semibold flex items-center justify-center text-xs shrink-0 shadow-xs">
                    {{ $initials }}
                </div>
                <div class="flex flex-col min-w-0 text-left">
                    <span class="text-xs font-semibold text-foreground truncate leading-tight">{{ $userName }}</span>
                    <span class="text-[10px] text-muted-foreground truncate leading-tight">{{ $userRole }}</span>
                </div>
            </a>
            <div class="flex items-center gap-1">
                <a href="{{ route('settings') }}" title="Account Settings" class="p-1.5 rounded-md text-muted-foreground hover:text-foreground hover:bg-muted transition-colors">
                    <i data-lucide="settings" class="w-3.5 h-3.5"></i>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" title="Sign Out" class="p-1.5 rounded-md text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors">
                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Bottom Company Branding -->
        <div class="px-2 pt-1 border-t border-border/40 flex items-center justify-between text-[10px] text-muted-foreground font-mono">
            <span>{{ config('app.name', 'Lancotri') }}</span>
            <span>&copy; {{ date('Y') }}</span>
        </div>
    </div>
</aside>
