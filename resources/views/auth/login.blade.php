<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sign In &bull; Lancotri &bull; Fleet Operations</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/lancotri-mark.svg') }}">

    <!-- Google Fonts: Plus Jakarta Sans + Sora + Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@400;500;600;700&family=Poppins:wght@600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Vite + Standalone fallback for 100% reliable rendering) -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        display: ['"Sora"', '"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#FDF2F2',
                            100: '#FBE3E4',
                            200: '#F6C6C8',
                            300: '#EE9CA0',
                            400: '#E2686D',
                            500: '#D0343B',
                            600: '#BD1C23',
                            700: '#9E151B',
                            800: '#7E1216',
                            900: '#5E0F12',
                        },
                        accent: {
                            50: '#EAF6FB',
                            100: '#CFEAF7',
                            200: '#9FD6EE',
                            300: '#63BBE0',
                            400: '#2E9FCC',
                            500: '#0A81B4',
                            600: '#086C99',
                            700: '#075A7F',
                            800: '#064A69',
                            900: '#063C56',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f6fa;
            color: #0f172a;
        }

        .dot-pattern {
            background-image: radial-gradient(#d5dce8 1.2px, transparent 1.2px);
            background-size: 24px 24px;
        }
    </style>
    @include('partials.brand-styles')
</head>
<body class="min-h-full flex flex-col justify-between selection:bg-brand-600 selection:text-white bg-[#f4f6fa] relative overflow-x-hidden">

    <!-- Subtle Dot Background -->
    <div class="fixed inset-0 dot-pattern pointer-events-none opacity-50 z-0"></div>

    <!-- Tailwind CDN safelist: classes injected at runtime by the alert helper -->
    <div class="hidden bg-accent-50 border-accent-200 text-accent-700 text-accent-600" aria-hidden="true"></div>

    <!-- Main Container: Centered Clean Login Form Only -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8 relative z-10">
        <div class="w-full max-w-[440px]">
            
            <!-- Brand Logo Header -->
            <div class="flex flex-col items-center mb-6">
                <div style="font-size: 1.05rem;">
                    @include('partials.brand-logo', ['logoLink' => route('login'), 'logoMarkHeight' => '2.6rem'])
                </div>
                <p class="text-[11px] font-bold text-slate-400 tracking-[0.22em] uppercase mt-3">Fleet Operations</p>
            </div>

            <!-- Login Card -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-[0_20px_50px_rgba(15,23,42,0.06)] p-6 sm:p-8 relative overflow-hidden">
                
                <!-- Top Brand Accent Bar -->
                <div class="absolute top-0 left-0 right-0 h-1.5" style="background:linear-gradient(90deg,#BD1C23 0%,#9E151B 50%,#0A81B4 100%)"></div>

                <!-- Form Heading -->
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Sign In</h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Enter your email and password to access your account.
                    </p>
                </div>

                <!-- Feedback Alert Banner -->
                <div id="alert-banner" class="hidden mb-5 p-3.5 rounded-2xl text-xs font-medium border flex items-start gap-2.5 transition-all">
                    <span id="alert-icon" class="mt-0.5 shrink-0"></span>
                    <div id="alert-text" class="flex-1 leading-snug"></div>
                </div>

                @if (isset($errors) && $errors->any())
                    <div class="mb-5 p-3.5 rounded-2xl text-xs font-medium border border-red-200 bg-red-50 text-red-700 flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-red-600 mt-0.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <div class="space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-5 p-3.5 rounded-2xl text-xs font-medium border border-accent-200 bg-accent-50 text-accent-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-accent-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Login Form -->
                <form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="20" height="16" x="2" y="4" rx="2"/>
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                </svg>
                            </div>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                required
                                autocomplete="username"
                                value="{{ old('email') }}"
                                placeholder="name@company.com"
                                class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm font-medium placeholder-slate-400 transition-all focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10"
                            >
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Password
                            </label>
                            <a href="javascript:void(0)" onclick="alert('Please contact your administrator to reset credentials.')" class="text-xs font-semibold text-brand-600 hover:text-brand-700 transition-colors">
                                Forgot password?
                            </a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </div>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                                class="w-full pl-10 pr-11 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm font-medium placeholder-slate-400 transition-all focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-4 focus:ring-brand-600/10"
                            >
                            <button
                                type="button"
                                id="toggle-password"
                                aria-label="Toggle password visibility"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
                            >
                                <!-- Eye Icon -->
                                <svg id="eye-icon" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <!-- Eye Off Icon -->
                                <svg id="eye-off-icon" class="w-4 h-4 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m15 18-.722-3.25"/>
                                    <path d="M2 8a10.645 10.645 0 0 0 20 0"/>
                                    <path d="m20 15-1.726-2.05"/>
                                    <path d="m4 15 1.726-2.05"/>
                                    <path d="m9 18 .722-3.25"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me Option -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input
                                type="checkbox"
                                name="remember"
                                id="remember"
                                class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500 transition-all cursor-pointer"
                            >
                            <span class="text-xs font-medium text-slate-600">Remember me</span>
                        </label>
                    </div>

                    <!-- Sign In Button -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            id="submit-btn"
                            class="w-full py-3 px-6 rounded-xl font-bold text-white bg-brand-600 hover:bg-brand-700 active:scale-[0.99] transition-all shadow-md shadow-brand-600/20 flex items-center justify-center gap-2 text-sm tracking-wide focus:outline-none focus:ring-4 focus:ring-brand-500/20 disabled:opacity-70 disabled:cursor-not-allowed"
                        >
                            <span id="btn-spinner" class="hidden">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </span>
                            <span id="btn-text" class="flex items-center gap-2">
                                Sign In
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                                </svg>
                            </span>
                        </button>
                    </div>

                </form>

            </div>

            <!-- Simple Footer Notice -->
            <div class="mt-6 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} Lancotri &bull; Fleet Operations
            </div>

        </div>
    </main>

    <!-- Client-side Interactive Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('login-form');
            const submitBtn = document.getElementById('submit-btn');
            const btnSpinner = document.getElementById('btn-spinner');
            const btnText = document.getElementById('btn-text');
            const alertBanner = document.getElementById('alert-banner');
            const alertText = document.getElementById('alert-text');
            const alertIcon = document.getElementById('alert-icon');
            const togglePassword = document.getElementById('toggle-password');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');
            const emailInput = document.getElementById('email');

            // Toggle Password Visibility
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                eyeIcon.classList.toggle('hidden');
                eyeOffIcon.classList.toggle('hidden');
            });

            function showAlert(message, type = 'error') {
                alertBanner.classList.remove('hidden', 'bg-red-50', 'border-red-200', 'text-red-700', 'bg-accent-50', 'border-accent-200', 'text-accent-700', 'bg-emerald-50', 'border-emerald-200', 'text-emerald-700');
                
                if (type === 'error') {
                    alertBanner.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
                    alertIcon.innerHTML = `<svg class="w-4 h-4 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`;
                } else if (type === 'success') {
                    alertBanner.classList.add('bg-emerald-50', 'border-emerald-200', 'text-emerald-700');
                    alertIcon.innerHTML = `<svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>`;
                } else {
                    alertBanner.classList.add('bg-accent-50', 'border-accent-200', 'text-accent-700');
                    alertIcon.innerHTML = `<svg class="w-4 h-4 text-accent-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="8"/></svg>`;
                }

                alertText.innerHTML = message;
            }

            // AJAX Form Submission
            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                const email = emailInput.value.trim();
                const password = passwordInput.value;
                const remember = document.getElementById('remember').checked;
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                if (!email || !password) {
                    showAlert('Please enter both email and password.');
                    return;
                }

                // Loading State
                submitBtn.disabled = true;
                btnSpinner.classList.remove('hidden');
                btnText.innerHTML = 'Signing In...';

                try {
                    const response = await fetch("{{ route('login') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({ email, password, remember })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        if (data.token) {
                            localStorage.setItem('fleet_token', data.token);
                        }
                        if (data.user) {
                            localStorage.setItem('fleet_user', JSON.stringify(data.user));
                        }

                        showAlert(data.message || 'Authentication successful. Redirecting...', 'success');
                        btnText.innerHTML = 'Redirecting...';

                        setTimeout(() => {
                            window.location.href = data.redirect || "{{ route('dashboard') }}";
                        }, 600);
                    } else {
                        showAlert(data.message || 'These credentials do not match our records.', 'error');
                        submitBtn.disabled = false;
                        btnSpinner.classList.add('hidden');
                        btnText.innerHTML = `Sign In <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>`;
                    }
                } catch (err) {
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>
