{{--
    Lancotri brand lockup (mark + wordmark).

    Usage:
        @include('partials.brand-logo')
        @include('partials.brand-logo', ['logoLink' => route('login'), 'logoMarkHeight' => '2.5rem'])
--}}
@php
    $logoLink = $logoLink ?? route('dashboard');
    $logoMarkHeight = $logoMarkHeight ?? null;
@endphp
<a href="{{ $logoLink }}" class="brand-lockup" aria-label="Lancotri">
    <svg class="brand-mark" @if($logoMarkHeight) style="height: {{ $logoMarkHeight }};" @endif viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true" focusable="false">
        {{-- Building / depot block --}}
        <path d="M3 56V20L27 8v48z" fill="#BD1C23"/>
        {{-- Road sweeping to the horizon --}}
        <path d="M1 50c19 0 44-10 62-26v7C45 47 20 57 1 57z" fill="#0A81B4"/>
        {{-- Lane markings --}}
        <path d="M11.5 53.4l7-2.6M27.5 47.6l7.4-3.8M43.5 40.4l6.6-3.8" fill="none" stroke="#ffffff" stroke-width="2.1" stroke-linecap="round"/>
        {{-- Location pin --}}
        <circle cx="42" cy="16" r="14" fill="#BD1C23"/>
        <path d="M31 25.5h22L42 45z" fill="#BD1C23"/>
        <circle cx="42" cy="16" r="5.4" fill="#ffffff"/>
    </svg>
    <span class="brand-word">Lancotr<span class="brand-i">&#305;<span class="brand-i-dot"></span></span></span>
</a>
