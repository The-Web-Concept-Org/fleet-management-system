{{--
    shadcn/ui Shared Application Topbar with Real-Time Fleet Indicators
--}}
@php
    $searchPlaceholder = $topbarSearchPlaceholder ?? 'Search bus, driver, or shift...';
    $searchId = $topbarSearchId ?? 'topNavSearch';

    $user = auth()->user();
    $userName = $user->name ?? 'Dispatcher Admin';
    $userRole = $user && $user->role ? \Illuminate\Support\Str::headline($user->role) : 'Administrator';
    $initials = strtoupper(substr(trim($userName), 0, 2));

    $activeBusesCount = \App\Models\Bus::where('status', 'active')->count();
    $alertBusesCount = \App\Models\Bus::where('status', 'out_of_service')->orWhere('next_service_km', '<', 0)->count();
    $userNotifications = $user ? \App\Models\AppNotification::where('user_id', $user->id)->latest()->take(6)->get() : collect();
    $unreadNotificationCount = $user ? \App\Models\AppNotification::where('user_id', $user->id)->whereNull('read_at')->count() : 0;
    $totalAlertCount = $unreadNotificationCount + ($alertBusesCount > 0 ? 1 : 0);
@endphp

<header class="fixed top-0 left-64 right-0 h-16 bg-card/90 backdrop-blur-md border-b border-border z-40 px-6 flex items-center justify-between gap-4 shadow-xs">
    <!-- Search / Command Bar -->
    <div class="flex-1 max-w-md">
        <div class="relative flex items-center w-full">
            <i data-lucide="search" class="w-4 h-4 text-muted-foreground absolute left-3 pointer-events-none"></i>
            <input class="w-full pl-9 pr-12 py-1.5 h-9 bg-background rounded-lg border border-input text-foreground placeholder:text-muted-foreground text-xs shadow-xs focus:outline-none focus:ring-1 focus:ring-ring transition-all"
                   id="{{ $searchId }}"
                   placeholder="{{ $searchPlaceholder }}"
                   type="text"
                   autocomplete="off"/>
            <kbd class="pointer-events-none absolute right-2.5 hidden sm:inline-flex h-5 select-none items-center gap-1 rounded border border-border bg-muted px-1.5 font-mono text-[10px] font-medium text-muted-foreground opacity-90">
                ⌘K
            </kbd>
        </div>
    </div>

    <!-- Right Controls & Status Indicators -->
    <div class="flex items-center gap-3">
        <!-- Live Real-Time Telemetry Pill -->
        <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full border border-border bg-muted/60 text-xs">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span class="font-semibold text-foreground text-xs">{{ config('app.name', 'Lancotri') }} Online</span>
            <span class="text-muted-foreground text-[10px]">&bull;</span>
            <span class="text-muted-foreground text-xs font-mono" id="topbarTelemetryStatus">
                <span data-telemetry-key="activeBuses">{{ $activeBusesCount }}</span> active vehicles
            </span>
        </div>

        <!-- Telemetry Refresh Button -->
        <button type="button"
                onclick="window.fleetRealtime?.syncNow(true)"
                title="Force Telemetry Sync"
                class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-input bg-card hover:bg-muted text-muted-foreground hover:text-foreground transition-colors shadow-xs">
            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
        </button>

        <!-- Notification Bell with Dropdown Popover -->
        <div class="relative" id="notificationsDropdownWrapper">
            <button type="button"
                    onclick="toggleNotificationsPopover()"
                    class="relative inline-flex items-center justify-center w-9 h-9 rounded-lg border border-input bg-card hover:bg-muted text-muted-foreground hover:text-foreground transition-colors shadow-xs"
                    title="Real-time Notifications">
                <i data-lucide="bell" class="w-4 h-4"></i>
                <span id="topbarNotificationPing" class="{{ $totalAlertCount > 0 ? '' : 'hidden' }} absolute top-1.5 right-1.5 flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                </span>
            </button>

            <!-- Notifications Popover Card -->
            <div id="notificationsPopover"
                 class="hidden absolute right-0 mt-2 w-84 rounded-xl border border-border bg-card p-3 shadow-xl z-50 text-left">
                <div class="flex items-center justify-between pb-2 mb-2 border-b border-border/60">
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-semibold text-foreground">Fleet Notifications</span>
                        <span id="topbarUnreadBadge" class="text-[10px] font-mono px-1.5 py-0.2 rounded-full {{ $unreadNotificationCount > 0 ? 'bg-primary/10 text-primary font-bold' : 'bg-muted text-muted-foreground' }}">
                            {{ $unreadNotificationCount }} unread
                        </span>
                    </div>
                    @if($unreadNotificationCount > 0)
                        <button type="button" onclick="markAllNotificationsRead()" class="text-[10px] text-muted-foreground hover:text-primary hover:underline transition-colors">
                            Mark all read
                        </button>
                    @endif
                </div>

                <div class="space-y-2 max-h-72 overflow-y-auto" id="topbarNotificationsList">
                    @if($alertBusesCount > 0)
                        <div class="p-2 rounded-lg bg-rose-500/10 border border-rose-500/20 text-xs text-rose-700 dark:text-rose-400 flex items-start gap-2">
                            <i data-lucide="alert-triangle" class="w-3.5 h-3.5 shrink-0 mt-0.5"></i>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold">{{ $alertBusesCount }} vehicle(s) require review</p>
                                <p class="text-[11px] text-muted-foreground mt-0.5">Check Buses roster for out-of-service or overdue inspection units.</p>
                                <a href="{{ route('buses') }}" class="text-[10px] font-medium text-rose-600 dark:text-rose-400 hover:underline block mt-1">Review Vehicles &rarr;</a>
                            </div>
                        </div>
                    @endif

                    @forelse($userNotifications as $notif)
                        @php
                            $iconName = match($notif->type) {
                                'shift' => 'clock-3',
                                'fuel' => 'fuel',
                                'maintenance' => 'wrench',
                                'warning' => 'alert-triangle',
                                default => 'bell'
                            };
                            $iconColor = match($notif->type) {
                                'shift' => 'text-blue-500',
                                'fuel' => 'text-amber-500',
                                'maintenance' => 'text-purple-500',
                                'warning' => 'text-rose-500',
                                default => 'text-primary'
                            };
                        @endphp
                        <div class="p-2 rounded-lg {{ is_null($notif->read_at) ? 'bg-primary/5 border border-primary/20' : 'bg-muted/40 border border-border/50' }} text-xs flex items-start gap-2 transition-colors">
                            <div class="shrink-0 mt-0.5 {{ $iconColor }}">
                                <i data-lucide="{{ $iconName }}" class="w-3.5 h-3.5"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1">
                                    <p class="font-semibold text-foreground truncate text-[11px]">{{ $notif->title }}</p>
                                    <span class="text-[10px] text-muted-foreground shrink-0 font-mono">{{ $notif->created_at?->diffForHumans(null, true) }}</span>
                                </div>
                                <p class="text-[11px] text-muted-foreground mt-0.5 leading-snug">{{ $notif->message }}</p>
                                @if($notif->link)
                                    <a href="{{ $notif->link }}" class="text-[10px] font-medium text-primary hover:underline inline-block mt-1">View Details &rarr;</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        @if($alertBusesCount === 0)
                            <div class="p-4 text-center text-xs text-muted-foreground">
                                <i data-lucide="check-circle" class="w-5 h-5 mx-auto text-emerald-500 mb-1.5 opacity-80"></i>
                                All clear — no active alerts or pending notifications
                            </div>
                        @endif
                    @endforelse
                </div>

                <div class="pt-2 mt-2 border-t border-border/60 text-center flex items-center justify-between px-1">
                    <a href="{{ route('shifts') }}" class="text-[11px] font-medium text-muted-foreground hover:text-foreground">
                        Shifts Desk
                    </a>
                    <a href="{{ route('buses') }}" class="text-[11px] font-medium text-primary hover:underline">
                        Buses Roster &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- User Profile Pill (Links to Settings) -->
        <a href="{{ route('settings') }}" class="flex items-center gap-2 pl-2 border-l border-border hover:opacity-80 transition-opacity" title="Account Settings">
            <div class="w-8 h-8 rounded-full bg-primary text-primary-foreground font-semibold flex items-center justify-center text-xs shadow-xs">
                {{ $initials }}
            </div>
            <div class="hidden lg:flex flex-col text-left">
                <span class="text-xs font-semibold text-foreground leading-tight">{{ $userName }}</span>
                <span class="text-[10px] text-muted-foreground leading-tight">{{ $userRole }}</span>
            </div>
        </a>
    </div>
</header>

<script>
    function toggleNotificationsPopover() {
        const popover = document.getElementById('notificationsPopover');
        if (!popover) return;
        popover.classList.toggle('hidden');
    }

    async function markAllNotificationsRead() {
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token || ''
                }
            });
            if (res.ok) {
                const badge = document.getElementById('topbarUnreadBadge');
                if (badge) {
                    badge.textContent = '0 unread';
                    badge.className = 'text-[10px] font-mono px-1.5 py-0.2 rounded-full bg-muted text-muted-foreground';
                }
                const ping = document.getElementById('topbarNotificationPing');
                if (ping) {
                    ping.classList.add('hidden');
                }
                window.toast?.success('Updated', 'All notifications marked as read.');
            }
        } catch (e) {
            console.error('Failed to mark notifications read', e);
        }
    }

    // Dismiss notifications on outside click
    document.addEventListener('click', (e) => {
        const wrapper = document.getElementById('notificationsDropdownWrapper');
        const popover = document.getElementById('notificationsPopover');
        if (wrapper && popover && !wrapper.contains(e.target)) {
            popover.classList.add('hidden');
        }
    });
</script>
