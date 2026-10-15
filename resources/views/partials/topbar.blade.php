{{--
    Shared application top bar.

    Every authenticated screen includes this partial so the header markup lives
    in one place. Only the per-screen data changes, via these optional variables:

        $topbarSearchPlaceholder  Placeholder for the search input.
        $topbarSearchId           Optional id for the search input (used by page JS).
        $topbarStatus             Text shown after "Depot Online" (e.g. "18 active vehicles").

    Everything else (notification bell, logout, and the signed-in user name,
    role and avatar) is derived once here so it stays identical on every screen.

    Usage:
        @include('partials.topbar')

        @include('partials.topbar', [
            'topbarSearchPlaceholder' => 'Search driver name, badge, email, or bus...',
            'topbarSearchId' => 'topNavSearch',
            'topbarStatus' => $totalDrivers . ' registered drivers',
        ])
--}}
@php
    $searchPlaceholder = $topbarSearchPlaceholder ?? 'Search bus, driver, or shift #...';
    $searchId = $topbarSearchId ?? null;

    $user = auth()->user();
    $userName = $user->name ?? 'Signed in user';
    $userRole = $user && $user->role
        ? \Illuminate\Support\Str::headline($user->role)
        : 'Administrator';
    $initials = strtoupper(substr(trim($userName), 0, 2));

    // Fall back to a live fleet figure so the status pill is never empty.
    $status = $topbarStatus ?? (\App\Models\Bus::where('status', 'active')->count() . ' active vehicles');
@endphp
<header class="fixed top-0 left-64 right-0 h-16 bg-surface/90 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-40 px-space-lg flex items-center justify-between gap-space-md">
  <div class="flex-1 max-w-md">
    <div class="relative flex items-center w-full">
      <span class="material-symbols-outlined absolute left-3 text-outline text-lg pointer-events-none">search</span>
      <input class="w-full pl-9 pr-space-md py-2 bg-surface-card rounded-lg text-on-surface placeholder:text-outline font-body-sm text-body-sm focus:outline-none focus:ring-2 focus:ring-primary-container shadow-sm" @if($searchId) id="{{ $searchId }}" @endif placeholder="{{ $searchPlaceholder }}" type="text"/>
    </div>
  </div>
  <div class="flex items-center gap-space-lg">
    <div class="hidden lg:flex items-center gap-2 px-space-md py-1.5 rounded-full bg-surface-card shadow-sm">
      <span class="w-2 h-2 rounded-full bg-sync-active"></span>
      <span class="font-body-sm text-body-sm font-semibold text-secondary">Depot Online</span>
      @if($status)
        <span class="text-outline text-xs">&bull;</span>
        <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $status }}</span>
      @endif
    </div>
    <button class="relative p-2 rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors" title="Notifications">
      <span class="material-symbols-outlined text-xl">notifications</span>
      <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-danger-critical ring-2 ring-surface"></span>
    </button>
    <form method="POST" action="{{ route('logout') }}" class="inline">
      @csrf
      <button type="submit" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-outline hover:text-danger-critical hover:bg-error-container/20 text-body-sm font-medium transition-colors" title="Log out">
        <span class="material-symbols-outlined text-base">logout</span>
        <span class="hidden sm:inline">Logout</span>
      </button>
    </form>
    <div class="flex items-center gap-space-sm pl-space-sm">
      <div class="w-8 h-8 rounded-full bg-primary-container text-on-primary font-bold flex items-center justify-center text-xs shadow-sm">
        {{ $initials }}
      </div>
      <div class="hidden sm:flex flex-col text-left">
        <span class="font-body-sm text-body-sm font-semibold text-on-surface leading-tight">{{ $userName }}</span>
        <span class="font-label-caps text-label-caps text-outline leading-tight">{{ $userRole }}</span>
      </div>
    </div>
  </div>
</header>
