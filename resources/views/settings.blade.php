<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>{{ __('app.settings') }} &bull; {{ config('app.name', 'Lancotri') }}</title>
    @include('partials.brand-styles')
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/lancotri-mark.svg') }}">
</head>
<body class="bg-background text-foreground antialiased min-h-screen">
    @include('partials.sidebar')

    <div class="pl-64 flex flex-col min-h-screen">
        @include('partials.topbar')

        <main class="w-full pt-16 flex-1 px-8 py-8 bg-background">
            <div class="flex flex-col w-full gap-6 max-w-7xl mx-auto">

                <!-- Page Header -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b">
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-xs text-muted-foreground font-mono">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-muted border border-border">
                                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-primary"></i>
                                <span>{{ __('app.nav_system') }}</span>
                            </span>
                            <span>&bull;</span>
                            <span class="inline-flex items-center gap-1.5 text-emerald-600 font-medium">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                {{ __('app.online') }}
                            </span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">{{ __('app.settings') }}</h1>
                            <p class="text-xs text-muted-foreground mt-2 leading-relaxed max-w-2xl">
                                {{ __('app.configure_system') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background shadow-xs hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 gap-2">
                            <i data-lucide="arrow-left" class="w-3.5 h-3.5 text-muted-foreground"></i>
                            <span>{{ __('app.back') }}</span>
                        </a>
                    </div>
                </div>

                <!-- Forms Grid (Language + Profile + Password) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

                    <!-- Language & Localization Card -->
                    <div class="rounded-xl border bg-card text-card-foreground shadow-xs p-6 space-y-5 lg:col-span-2">
                        <div class="flex flex-col space-y-1 pb-3 border-b">
                            <div class="flex items-center gap-2">
                                <i data-lucide="languages" class="w-4 h-4 text-primary"></i>
                                <h3 class="text-base font-semibold leading-none tracking-tight">{{ __('app.language') }} &amp; {{ __('app.preferences') }}</h3>
                            </div>
                            <p class="text-xs text-muted-foreground mt-1">{{ __('app.language') }}: {{ app()->getLocale() === 'es' ? __('app.spanish') : __('app.english') }}</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('locale.switch', 'es') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border text-sm font-medium transition-all {{ app()->getLocale() === 'es' ? 'bg-primary text-primary-foreground border-primary shadow-xs' : 'bg-background hover:bg-muted text-foreground' }}">
                                <span class="text-base">🇪🇸</span>
                                <span>{{ __('app.spanish') }} (Español)</span>
                                @if(app()->getLocale() === 'es')
                                    <i data-lucide="check" class="w-4 h-4 ml-1"></i>
                                @endif
                            </a>

                            <a href="{{ route('locale.switch', 'en') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border text-sm font-medium transition-all {{ app()->getLocale() === 'en' ? 'bg-primary text-primary-foreground border-primary shadow-xs' : 'bg-background hover:bg-muted text-foreground' }}">
                                <span class="text-base">🇺🇸</span>
                                <span>{{ __('app.english') }} (English)</span>
                                @if(app()->getLocale() === 'en')
                                    <i data-lucide="check" class="w-4 h-4 ml-1"></i>
                                @endif
                            </a>
                        </div>
                    </div>

                    <!-- Profile Information Card -->
                    <div class="rounded-xl border bg-card text-card-foreground shadow-xs p-6 space-y-5">
                        <div class="flex flex-col space-y-1 pb-3 border-b">
                            <div class="flex items-center gap-2">
                                <i data-lucide="user" class="w-4 h-4 text-primary"></i>
                                <h3 class="text-base font-semibold leading-none tracking-tight">{{ __('app.profile_settings') }}</h3>
                            </div>
                            <p class="text-xs text-muted-foreground mt-1">{{ __('app.manage_drivers') }}</p>
                        </div>

                        @if (session('profile_status'))
                            <div class="relative w-full rounded-lg border border-emerald-500/30 bg-emerald-500/10 p-3 text-xs text-emerald-600 flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                                <span>{{ session('profile_status') }}</span>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('settings.profile') }}" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div class="space-y-1">
                                <label for="profile_name" class="text-xs font-medium leading-none">{{ __('app.full_name') }}</label>
                                <input type="text"
                                       id="profile_name"
                                       name="name"
                                       required
                                       value="{{ old('name', $user->name) }}"
                                       class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                                @error('name')
                                    <p class="text-[11px] text-destructive mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1">
                                <label for="profile_email" class="text-xs font-medium leading-none">{{ __('app.email_address') }}</label>
                                <input type="email"
                                       id="profile_email"
                                       name="email"
                                       required
                                       value="{{ old('email', $user->email) }}"
                                       class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                                @error('email')
                                    <p class="text-[11px] text-destructive mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="text-xs font-medium leading-none">{{ __('app.company_name') }}</label>
                                <div class="flex items-center gap-2">
                                    <input type="text"
                                           disabled
                                           value="{{ config('app.name', 'Lancotri') }} &bull; {{ \Illuminate\Support\Str::headline($user->role ?? 'Administrator') }}"
                                           class="flex h-9 w-full rounded-md border border-input bg-muted/60 px-3 py-1 text-xs text-muted-foreground shadow-xs cursor-not-allowed"/>
                                </div>
                            </div>

                            <div class="pt-2 flex justify-end">
                                <button type="submit"
                                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2 gap-2 transition-all">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                    <span>{{ __('app.save') }}</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Change Password Card -->
                    <div class="rounded-xl border bg-card text-card-foreground shadow-xs p-6 space-y-5">
                        <div class="flex flex-col space-y-1 pb-3 border-b">
                            <div class="flex items-center gap-2">
                                <i data-lucide="key-round" class="w-4 h-4 text-primary"></i>
                                <h3 class="text-base font-semibold leading-none tracking-tight">{{ __('app.password_security') }}</h3>
                            </div>
                            <p class="text-xs text-muted-foreground mt-1">{{ __('app.update_password') }}</p>
                        </div>

                        @if (session('password_status'))
                            <div class="relative w-full rounded-lg border border-emerald-500/30 bg-emerald-500/10 p-3 text-xs text-emerald-600 flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                                <span>{{ session('password_status') }}</span>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('settings.password') }}" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div class="space-y-1">
                                <label for="current_password" class="text-xs font-medium leading-none">{{ __('app.current_password') }}</label>
                                <div class="relative">
                                    <input type="password"
                                           id="current_password"
                                           name="current_password"
                                           required
                                           placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                                           class="flex h-9 w-full rounded-md border border-input bg-background px-3 pr-9 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                                    <button type="button" onclick="togglePasswordVisibility('current_password', 'current_icon')"
                                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                                        <i data-lucide="eye" id="current_icon" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <p class="text-[11px] text-destructive mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1">
                                <label for="new_password" class="text-xs font-medium leading-none">{{ __('app.new_password') }}</label>
                                <div class="relative">
                                    <input type="password"
                                           id="new_password"
                                           name="password"
                                           required
                                           minlength="6"
                                           placeholder="Min. 6 characters"
                                           class="flex h-9 w-full rounded-md border border-input bg-background px-3 pr-9 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                                    <button type="button" onclick="togglePasswordVisibility('new_password', 'new_icon')"
                                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                                        <i data-lucide="eye" id="new_icon" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="text-[11px] text-destructive mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1">
                                <label for="password_confirmation" class="text-xs font-medium leading-none">{{ __('app.confirm_new_password') }}</label>
                                <div class="relative">
                                    <input type="password"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           required
                                           minlength="6"
                                           placeholder="Repeat new password"
                                           class="flex h-9 w-full rounded-md border border-input bg-background px-3 pr-9 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                                    <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'confirm_icon')"
                                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                                        <i data-lucide="eye" id="confirm_icon" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="pt-2 flex justify-end">
                                <button type="submit"
                                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2 gap-2 transition-all">
                                    <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                                    <span>{{ __('app.update_password') }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (!input || !icon) return;

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            icon.setAttribute('data-lucide', isPassword ? 'eye-off' : 'eye');
            if (window.lucide) window.lucide.createIcons();
        }
    </script>
</body>
</html>
