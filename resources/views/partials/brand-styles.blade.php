<!-- Google Font: Roboto & Roboto Mono -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&family=Roboto+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Tailwind CSS CDN with Official shadcn/ui Theme Tokens -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
    darkMode: ["class"],
    theme: {
        extend: {
            colors: {
                border: "hsl(var(--border, 240 5.9% 90%))",
                input: "hsl(var(--input, 240 5.9% 90%))",
                ring: "hsl(var(--ring, 357 74% 43%))",
                background: "hsl(var(--background, 0 0% 100%))",
                foreground: "hsl(var(--foreground, 240 10% 3.9%))",
                primary: {
                    DEFAULT: "hsl(var(--primary, 357 74% 43%))",
                    foreground: "hsl(var(--primary-foreground, 0 0% 100%))",
                },
                secondary: {
                    DEFAULT: "hsl(var(--secondary, 240 4.8% 95.9%))",
                    foreground: "hsl(var(--secondary-foreground, 240 5.9% 10%))",
                },
                destructive: {
                    DEFAULT: "hsl(var(--destructive, 0 84.2% 60.2%))",
                    foreground: "hsl(var(--destructive-foreground, 0 0% 98%))",
                },
                muted: {
                    DEFAULT: "hsl(var(--muted, 240 4.8% 95.9%))",
                    foreground: "hsl(var(--muted-foreground, 240 3.8% 46.1%))",
                },
                accent: {
                    DEFAULT: "hsl(var(--accent, 240 4.8% 95.9%))",
                    foreground: "hsl(var(--accent-foreground, 240 5.9% 10%))",
                },
                popover: {
                    DEFAULT: "hsl(var(--popover, 0 0% 100%))",
                    foreground: "hsl(var(--popover-foreground, 240 10% 3.9%))",
                },
                card: {
                    DEFAULT: "hsl(var(--card, 0 0% 100%))",
                    foreground: "hsl(var(--card-foreground, 240 10% 3.9%))",
                },
            },
            borderRadius: {
                lg: "var(--radius, 0.5rem)",
                md: "calc(var(--radius, 0.5rem) - 2px)",
                sm: "calc(var(--radius, 0.5rem) - 4px)",
                xl: "0.75rem",
            },
            fontFamily: {
                sans: ['Roboto', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif'],
                mono: ['Roboto Mono', 'ui-monospace', 'monospace'],
                body: ['Roboto', 'sans-serif'],
            }
        }
    }
};
</script>

<style>
    :root {
        --background: 0 0% 100%;
        --foreground: 240 10% 3.9%;
        --card: 0 0% 100%;
        --card-foreground: 240 10% 3.9%;
        --popover: 0 0% 100%;
        --popover-foreground: 240 10% 3.9%;
        --primary: 357 74% 43%;
        --primary-foreground: 0 0% 100%;
        --secondary: 240 4.8% 95.9%;
        --secondary-foreground: 240 5.9% 10%;
        --muted: 240 4.8% 95.9%;
        --muted-foreground: 240 3.8% 46.1%;
        --accent: 240 4.8% 95.9%;
        --accent-foreground: 240 5.9% 10%;
        --destructive: 0 84.2% 60.2%;
        --destructive-foreground: 0 0% 98%;
        --border: 240 5.9% 90%;
        --input: 240 5.9% 90%;
        --ring: 357 74% 43%;
        --radius: 0.5rem;
    }

    body {
        font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background-color: #fafafa;
        color: #09090b;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* ---- Lancotri brand lockup ---- */
    .brand-lockup {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        line-height: 1;
        text-decoration: none;
    }
    .brand-mark {
        height: 2rem;
        width: auto;
        display: block;
        flex-shrink: 0;
    }
    .brand-word {
        font-family: 'Roboto', sans-serif;
        font-weight: 700;
        font-size: 1.25rem;
        line-height: 1;
        letter-spacing: -.02em;
        color: #BD1C23;
        display: inline-block;
        white-space: nowrap;
    }
    .brand-i {
        position: relative;
        display: inline-block;
    }
    .brand-i-dot {
        position: absolute;
        top: .10em;
        left: 50%;
        width: .17em;
        height: .17em;
        margin-left: -.085em;
        border-radius: 50%;
        background: #0A81B4;
    }

    /* Modern subtle scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    ::-webkit-scrollbar-track {
        background: transparent;
    }
    ::-webkit-scrollbar-thumb {
        background: #e4e4e7;
        border-radius: 9999px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #a1a1aa;
    }

    /* shadcn Standard Form Controls & Spacing */
    label, .form-label {
        display: block;
        font-size: 0.75rem !important; /* text-xs */
        font-weight: 500 !important;
        line-height: 1 !important;
        color: hsl(var(--foreground)) !important;
        margin-bottom: 0.25rem !important; /* reduced tight gap between label and input */
    }

    input, select, textarea {
        font-family: 'Roboto', sans-serif !important;
    }

    select {
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        background-color: hsl(var(--background)) !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2371717a' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") !important;
        background-position: right 0.65rem center !important;
        background-repeat: no-repeat !important;
        background-size: 1.15em 1.15em !important;
        padding-right: 2.25rem !important;
        cursor: pointer;
    }

    input[type="date"], input[type="time"] {
        cursor: pointer;
    }

    input[type="date"]::-webkit-calendar-picker-indicator,
    input[type="time"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        opacity: 0.55;
        transition: opacity 0.15s ease;
    }

    input[type="date"]::-webkit-calendar-picker-indicator:hover,
    input[type="time"]::-webkit-calendar-picker-indicator:hover {
        opacity: 1;
    }
</style>

<!-- Load Lucide icons locally with fallback -->
<script src="{{ asset('js/lucide.min.js') }}"></script>
<script>
    if (typeof lucide === 'undefined') {
        const s = document.createElement('script');
        s.src = 'https://unpkg.com/lucide@latest';
        document.head.appendChild(s);
    }
</script>

<!-- Load shadcn components and Realtime Telemetry -->
<script src="{{ asset('js/shadcn.js') }}"></script>
<script src="{{ asset('js/realtime-fleet.js') }}"></script>
