<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sign In &bull; Lancotri &bull; Fleet Operations</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/lancotri-mark.svg') }}">

    <!-- Google Font: Roboto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Roboto+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    @include('partials.brand-styles')
</head>
<body class="min-h-full flex flex-col justify-between bg-background text-foreground antialiased relative">

    <!-- Topbar navigation minimal -->
    <header class="h-16 border-b px-6 flex items-center justify-between">
        @include('partials.brand-logo', ['logoLink' => route('login'), 'logoMarkHeight' => '2rem'])
        <span class="text-xs font-mono text-muted-foreground">{{ config('app.name', 'Lancotri') }}</span>
    </header>

    <!-- Main Container: Centered shadcn Card -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-sm rounded-xl border bg-card text-card-foreground shadow-sm p-6 sm:p-8 space-y-6">

            <!-- Card Header -->
            <div class="flex flex-col space-y-1.5 text-center">
                <h2 class="text-2xl font-bold tracking-tight">Sign In</h2>
                <p class="text-xs text-muted-foreground">
                    Enter your email and credentials to access Fleet Operations.
                </p>
            </div>

            <!-- Error Alerts (shadcn Alert) -->
            @if (isset($errors) && $errors->any())
                <div class="relative w-full rounded-lg border border-destructive/50 bg-destructive/10 p-3 text-xs text-destructive flex items-start gap-2.5">
                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 mt-0.5"></i>
                    <div class="space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (session('status'))
                <div class="relative w-full rounded-lg border border-emerald-500/30 bg-emerald-500/10 p-3 text-xs text-emerald-600 flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Form -->
            <form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div class="space-y-1.5 text-left">
                    <label for="email" class="text-xs font-medium leading-none">Email Address</label>
                    <input type="email"
                           id="email"
                           name="email"
                           required
                           autocomplete="username"
                           value="{{ old('email') }}"
                           placeholder="dispatcher@lancotri.local"
                           class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                </div>

                <!-- Password -->
                <div class="space-y-1.5 text-left">
                    <div class="flex items-center justify-between">
                        <label for="password" class="text-xs font-medium leading-none">Password</label>
                        <a href="javascript:void(0)" onclick="window.toast?.info('Contact Administrator', 'Please reach out to the depot IT admin to reset credentials.')"
                           class="text-xs text-primary hover:underline">
                            Forgot?
                        </a>
                    </div>
                    <div class="relative">
                        <input type="password"
                               id="password"
                               name="password"
                               required
                               autocomplete="current-password"
                               placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                               class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 pr-9 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"/>
                        <button type="button"
                                id="toggle-password"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                            <i data-lucide="eye" id="eye-icon" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button (shadcn Button) -->
                <button type="submit" id="submit-btn"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2 w-full transition-all">
                    Sign In to Console
                </button>
            </form>

            <div class="pt-2 border-t text-center text-xs text-muted-foreground font-medium">
                {{ config('app.name', 'Lancotri') }}
            </div>
        </div>
    </main>

    <footer class="h-12 border-t px-6 flex items-center justify-center text-xs text-muted-foreground">
        &copy; {{ date('Y') }} {{ config('app.name', 'Lancotri') }}. All rights reserved.
    </footer>

    <script>
        const pwdInput = document.getElementById('password');
        const toggleBtn = document.getElementById('toggle-password');
        const eyeIcon = document.getElementById('eye-icon');

        toggleBtn?.addEventListener('click', () => {
            const isPassword = pwdInput.type === 'password';
            pwdInput.type = isPassword ? 'text' : 'password';
            if (eyeIcon) {
                eyeIcon.setAttribute('data-lucide', isPassword ? 'eye-off' : 'eye');
                if (window.lucide) window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>
