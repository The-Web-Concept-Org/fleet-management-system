<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Fleet Notifications &amp; Alerts &bull; {{ config('app.name', 'Lancomtri') }}</title>
    @include('partials.brand-styles')
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/lancotri-mark.svg') }}">
</head>
<body class="bg-background text-foreground antialiased min-h-screen">
    @include('partials.sidebar')

    <div class="pl-64 flex flex-col min-h-screen">
        @include('partials.topbar', [
            'topbarSearchPlaceholder' => 'Search notifications...',
            'topbarSearchId' => 'notificationNavSearch',
            'topbarStatus' => ($activeBuses ?? 0) . ' active vehicles',
        ])

        <main class="w-full pt-16 flex-1 px-8 py-8 bg-background">
            <div class="flex flex-col w-full gap-6 max-w-5xl mx-auto">

                <!-- Header: Title + Mark All Read Action -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b">
                    <div class="flex flex-col gap-1.5">
                        <div class="flex items-center gap-2 text-xs text-muted-foreground font-mono">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-muted border">
                                <i data-lucide="bell" class="w-3.5 h-3.5 text-primary"></i>
                                <span>COMMUNICATION &amp; EVENT LOG</span>
                            </span>
                        </div>
                        <h1 class="text-2xl font-bold tracking-tight text-foreground flex items-center gap-3">
                            Fleet Notifications
                            @if(($unreadCount ?? 0) > 0)
                                <span class="text-xs font-mono px-2 py-0.5 rounded-full bg-primary/10 text-primary border border-primary/20">
                                    {{ $unreadCount }} unread
                                </span>
                            @endif
                        </h1>
                        <p class="text-xs text-muted-foreground">Real-time alerts for driver shifts, odometer audits, fuel fill-ups, and inspection requests.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        @if(($unreadCount ?? 0) > 0)
                            <button type="button"
                                    onclick="markAllNotificationsRead()"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-input bg-card hover:bg-muted text-xs font-medium text-foreground transition-colors shadow-xs">
                                <i data-lucide="check-check" class="w-4 h-4 text-primary"></i>
                                <span>Mark All as Read</span>
                            </button>
                        @endif

                        <button type="button"
                                onclick="openBroadcastModal()"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-primary hover:bg-primary/90 text-primary-foreground text-xs font-semibold shadow-xs transition-colors">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            <span>Create Notification</span>
                        </button>

                        <a href="{{ route('shifts') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-input bg-card hover:bg-muted text-xs font-medium text-foreground transition-colors shadow-xs">
                            <i data-lucide="clock-3" class="w-4 h-4"></i>
                            <span>Shifts Desk</span>
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-xs font-medium flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Notifications List -->
                <div class="space-y-3">
                    @forelse($notifications as $notif)
                        @php
                            $isUnread = is_null($notif->read_at);
                            $type = strtolower($notif->type ?? 'general');
                            $iconName = match($type) {
                                'shift', 'shift_started', 'shift_completed' => 'clock-3',
                                'fuel' => 'fuel',
                                'maintenance' => 'wrench',
                                'warning', 'alert' => 'alert-triangle',
                                default => 'bell'
                            };
                            $iconBg = match($type) {
                                'shift', 'shift_started', 'shift_completed' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
                                'fuel' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                                'maintenance' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
                                'warning', 'alert' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                                default => 'bg-primary/10 text-primary border-primary/20'
                            };
                            $targetLink = $notif->link ?: route('shifts');
                        @endphp

                        <div class="p-4 rounded-xl border transition-all duration-150 {{ $isUnread ? 'bg-card border-primary/30 shadow-xs ring-1 ring-primary/10' : 'bg-muted/20 border-border opacity-90' }} flex items-start gap-4">
                            <!-- Icon Pill -->
                            <div class="w-10 h-10 rounded-xl border flex items-center justify-center shrink-0 mt-0.5 {{ $iconBg }}">
                                <i data-lucide="{{ $iconName }}" class="w-5 h-5"></i>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-semibold text-sm text-foreground truncate">
                                            {{ $notif->title }}
                                        </h3>
                                        @if($isUnread)
                                            <span class="inline-block w-2 h-2 rounded-full bg-primary shrink-0" title="Unread"></span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-muted-foreground font-mono shrink-0">
                                        {{ $notif->created_at?->diffForHumans() }}
                                    </span>
                                </div>

                                <p class="text-xs text-muted-foreground mt-1 leading-relaxed">
                                    {{ $notif->message ?: $notif->body }}
                                </p>

                                <div class="flex items-center gap-4 mt-3">
                                    <a href="{{ $targetLink }}"
                                       onclick="fetch('/notifications/{{ $notif->id }}/read', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content } })"
                                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary hover:underline">
                                        <span>Open in Dashboard</span>
                                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                    </a>

                                    <span class="text-[11px] text-muted-foreground font-mono">
                                        {{ $notif->created_at?->format('M d, Y • H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center rounded-2xl border border-dashed border-border bg-card">
                            <div class="w-12 h-12 rounded-full bg-emerald-500/10 text-emerald-600 mx-auto flex items-center justify-center mb-3">
                                <i data-lucide="check-circle" class="w-6 h-6"></i>
                            </div>
                            <h3 class="text-base font-semibold text-foreground">Inbox Zero</h3>
                            <p class="text-xs text-muted-foreground mt-1 max-w-sm mx-auto">No notifications found. Shift events, fuel telemetry, and meter alerts will populate here in real time.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($notifications->hasPages())
                    <div class="pt-4 border-t border-border flex justify-between items-center">
                        <div class="text-xs text-muted-foreground font-mono">
                            Showing {{ $notifications->firstItem() }} to {{ $notifications->lastItem() }} of {{ $notifications->total() }} notifications
                        </div>
                        <div>
                            {{ $notifications->links() }}
                        </div>
                    </div>
                @endif

            </div>
        </main>
    </div>

    <!-- Modal: Broadcast Role-Based Notification -->
    <div id="broadcastModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-card border border-border rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-muted/30">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                        <i data-lucide="send" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-foreground">Send Fleet Notification</h2>
                        <p class="text-xs text-muted-foreground">Deliver role-targeted alerts or individual notifications</p>
                    </div>
                </div>
                <button type="button" onclick="closeBroadcastModal()" class="text-muted-foreground hover:text-foreground p-1 rounded-lg hover:bg-muted">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form action="{{ route('notifications.broadcast') }}" method="POST" class="p-6 space-y-4">
                @csrf

                <!-- Target Audience Selector -->
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1.5 uppercase tracking-wider font-mono">
                        Target Audience
                    </label>
                    <div class="grid grid-cols-2 gap-2 p-1 rounded-xl bg-muted/50 border border-border">
                        <button type="button" id="tabRoleBtn" onclick="switchTargetType('role')" class="py-1.5 px-3 rounded-lg text-xs font-bold transition-all bg-card text-foreground shadow-xs">
                            By User Role
                        </button>
                        <button type="button" id="tabUserBtn" onclick="switchTargetType('user')" class="py-1.5 px-3 rounded-lg text-xs font-medium transition-all text-muted-foreground hover:text-foreground">
                            Specific User / Driver
                        </button>
                    </div>
                    <input type="hidden" name="target_type" id="targetTypeInput" value="role">
                </div>

                <!-- Role Selection -->
                <div id="roleSelectGroup">
                    <label class="block text-xs font-medium text-foreground mb-1">Select Role Group</label>
                    <select name="role" class="w-full text-xs rounded-lg border border-input bg-background px-3 py-2 text-foreground focus:ring-1 focus:ring-primary focus:outline-hidden">
                        <option value="driver" selected>Drivers Only (Mobile App Users)</option>
                        <option value="all">All Fleet Users (Drivers &amp; Admins)</option>
                        <option value="admin">Administrators &amp; Supervisors</option>
                        <option value="super_admin">Super Admins Only</option>
                    </select>
                </div>

                <!-- Individual User Selection (Hidden by default) -->
                <div id="userSelectGroup" class="hidden">
                    <label class="block text-xs font-medium text-foreground mb-1">Select Recipient</label>
                    <select name="user_id" class="w-full text-xs rounded-lg border border-input bg-background px-3 py-2 text-foreground focus:ring-1 focus:ring-primary focus:outline-hidden">
                        @foreach($users ?? [] as $usr)
                            <option value="{{ $usr->id }}">
                                {{ $usr->name }} ({{ ucfirst($usr->role ?? 'user') }}) &bull; {{ $usr->email }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Type & Priority Row -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-foreground mb-1">Category</label>
                        <select name="type" class="w-full text-xs rounded-lg border border-input bg-background px-3 py-2 text-foreground focus:ring-1 focus:ring-primary focus:outline-hidden">
                            <option value="shift">Shift / Schedule</option>
                            <option value="announcement" selected>Fleet Announcement</option>
                            <option value="safety">Safety / Weather Alert</option>
                            <option value="maintenance">Maintenance Notice</option>
                            <option value="fuel">Fuel &amp; Efficiency</option>
                            <option value="general">General Notice</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-foreground mb-1">Priority</label>
                        <select name="priority" class="w-full text-xs rounded-lg border border-input bg-background px-3 py-2 text-foreground focus:ring-1 focus:ring-primary focus:outline-hidden">
                            <option value="normal" selected>Normal</option>
                            <option value="high">High Priority</option>
                            <option value="urgent">Urgent Alert</option>
                        </select>
                    </div>
                </div>

                <!-- Notification Title -->
                <div>
                    <label class="block text-xs font-medium text-foreground mb-1">Notification Title</label>
                    <input type="text" name="title" required placeholder="e.g. Schedule Update or Weather Precaution"
                           class="w-full text-xs rounded-lg border border-input bg-background px-3 py-2 text-foreground focus:ring-1 focus:ring-primary focus:outline-hidden">
                </div>

                <!-- Notification Body -->
                <div>
                    <label class="block text-xs font-medium text-foreground mb-1">Message Body</label>
                    <textarea name="body" rows="3" required placeholder="Write the details of the notification here..."
                              class="w-full text-xs rounded-lg border border-input bg-background px-3 py-2 text-foreground focus:ring-1 focus:ring-primary focus:outline-hidden resize-none"></textarea>
                </div>

                <!-- Optional Target Link -->
                <div>
                    <label class="block text-xs font-medium text-foreground mb-1">Action Link (Optional)</label>
                    <input type="text" name="link" placeholder="/shifts, /log-entry, or custom URL"
                           class="w-full text-xs rounded-lg border border-input bg-background px-3 py-2 text-foreground focus:ring-1 focus:ring-primary focus:outline-hidden">
                </div>

                <!-- Actions -->
                <div class="pt-2 flex items-center justify-end gap-2 border-t border-border">
                    <button type="button" onclick="closeBroadcastModal()" class="px-3.5 py-2 rounded-lg border border-input bg-card hover:bg-muted text-xs font-medium text-foreground transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-primary hover:bg-primary/90 text-primary-foreground text-xs font-semibold shadow-xs transition-colors">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Send Broadcast</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openBroadcastModal() {
            document.getElementById('broadcastModal').classList.remove('hidden');
            if (window.lucide) {
                window.lucide.createIcons();
            }
        }

        function closeBroadcastModal() {
            document.getElementById('broadcastModal').classList.add('hidden');
        }

        function switchTargetType(type) {
            document.getElementById('targetTypeInput').value = type;
            const roleBtn = document.getElementById('tabRoleBtn');
            const userBtn = document.getElementById('tabUserBtn');
            const roleGroup = document.getElementById('roleSelectGroup');
            const userGroup = document.getElementById('userSelectGroup');

            if (type === 'role') {
                roleBtn.className = 'py-1.5 px-3 rounded-lg text-xs font-bold transition-all bg-card text-foreground shadow-xs';
                userBtn.className = 'py-1.5 px-3 rounded-lg text-xs font-medium transition-all text-muted-foreground hover:text-foreground';
                roleGroup.classList.remove('hidden');
                userGroup.classList.add('hidden');
            } else {
                userBtn.className = 'py-1.5 px-3 rounded-lg text-xs font-bold transition-all bg-card text-foreground shadow-xs';
                roleBtn.className = 'py-1.5 px-3 rounded-lg text-xs font-medium transition-all text-muted-foreground hover:text-foreground';
                userGroup.classList.remove('hidden');
                roleGroup.classList.add('hidden');
            }
        }

        async function markAllNotificationsRead() {
            try {
                const res = await fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (res.ok) {
                    window.location.reload();
                }
            } catch (e) {
                console.error('Failed to mark all read:', e);
            }
        }
    </script>
</body>
</html>
