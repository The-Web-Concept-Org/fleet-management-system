{{--
    Shared application sidebar.

    Every authenticated screen includes this partial so the navigation markup
    lives in one place. The active item is derived from the current route name,
    so a screen never has to hard-code its own highlight.

    To add a new section, add one entry to $navItems with the matching route
    name (or null for a placeholder link).
--}}
@php
    $currentRoute = request()->route()?->getName();

    $navItems = [
        ['label' => 'Dashboard', 'icon' => 'space_dashboard', 'route' => 'dashboard'],
        ['label' => 'Buses', 'icon' => 'directions_bus', 'route' => 'buses'],
        ['label' => 'Trips', 'icon' => 'route', 'route' => 'trips'],
        ['label' => 'Shifts', 'icon' => 'schedule', 'route' => 'shifts'],
        ['label' => 'Fuel', 'icon' => 'local_gas_station', 'route' => null],
        ['label' => 'Maintenance', 'icon' => 'build', 'route' => null],
        ['label' => 'Drivers', 'icon' => 'badge', 'route' => 'drivers'],
        ['label' => 'Reports', 'icon' => 'bar_chart', 'route' => null],
    ];
@endphp
<aside class="fixed left-0 top-0 h-full w-64 bg-surface-card z-50 flex flex-col justify-between shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
  <div class="flex flex-col flex-1">
    <div class="h-16 px-space-md flex items-center gap-space-sm">@include('partials.brand-logo')</div>
    <div class="px-space-md py-space-sm">
      <span class="font-label-caps text-label-caps text-outline uppercase tracking-wider">Transit Operations</span>
    </div>
    <nav class="flex-1 px-space-sm flex flex-col gap-1" data-active-classes="bg-primary-container text-on-primary font-bold shadow-sm">
      @foreach($navItems as $item)
        @php
            $isActive = $item['route'] !== null && $currentRoute === $item['route'];
            $href = $item['route'] !== null ? route($item['route']) : '#';
            $classes = $isActive
                ? 'bg-primary-container text-on-primary font-bold shadow-sm'
                : 'text-on-surface-variant font-body-md text-body-md hover:bg-surface-container-high hover:text-on-surface';
        @endphp
        <a @if($isActive) aria-current="page" @endif class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg transition-colors {{ $classes }}" data-path="{{ strtolower($item['label']) }}" href="{{ $href }}">
          <span class="material-symbols-outlined text-lg">{{ $item['icon'] }}</span>
          <span>{{ $item['label'] }}</span>
        </a>
      @endforeach
    </nav>
  </div>
  <div class="p-space-md bg-surface-container-low m-space-sm rounded-xl flex items-center justify-between">
    <div class="flex items-center gap-space-xs">
      <span class="w-2.5 h-2.5 rounded-full bg-sync-active animate-pulse"></span>
      <span class="font-body-sm text-body-sm text-on-surface">GPS Feed Synced</span>
    </div>
    <span class="material-symbols-outlined text-outline text-lg">wifi</span>
  </div>
</aside>
