<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>{{ $report['meta']['title'] }} &bull; {{ config('app.name', 'LANCOMTRI') }}</title>
    @include('partials.brand-styles')
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/lancotri-mark.svg') }}">
    <style>
        /* Professional Spreadsheet Grid Styling */
        .report-grid-table {
            border-collapse: collapse;
            width: 100%;
            font-variant-numeric: tabular-nums;
            font-family: 'Roboto Mono', ui-monospace, SFMono-Regular, monospace;
            border: 1px solid #94a3b8;
        }
        .dark .report-grid-table {
            border-color: #475569;
        }

        .report-grid-table th,
        .report-grid-table td {
            border: 1px solid #cbd5e1;
            padding: 0.45rem 0.65rem;
            font-size: 0.72rem;
            white-space: nowrap;
            line-height: 1.25;
        }
        .dark .report-grid-table th,
        .dark .report-grid-table td {
            border-color: #334155;
        }

        /* Top Group Headers */
        .report-grid-table thead tr.group-header-row th {
            background-color: #e2e8f0;
            color: #0f172a;
            font-weight: 800;
            font-size: 0.72rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            text-align: center;
            border: 1px solid #94a3b8;
            padding: 0.5rem 0.5rem;
        }
        .dark .report-grid-table thead tr.group-header-row th {
            background-color: #1e293b;
            color: #f8fafc;
            border-color: #475569;
        }

        /* Sub Headers */
        .report-grid-table thead tr.sub-header-row th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: 700;
            font-size: 0.68rem;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            border: 1px solid #94a3b8;
            padding: 0.4rem 0.5rem;
        }
        .dark .report-grid-table thead tr.sub-header-row th {
            background-color: #0f172a;
            color: #e2e8f0;
            border-color: #475569;
        }

        /* Column Specific Styling */
        .col-unit-header {
            text-align: center !important;
            vertical-align: middle;
            font-weight: 800;
        }
        .col-unit-cell {
            text-align: center !important;
            font-weight: 700;
            font-family: 'Roboto', sans-serif;
            background-color: #f8fafc;
        }
        .dark .col-unit-cell {
            background-color: rgba(30, 41, 59, 0.4);
        }

        .col-breakdown-header,
        .col-breakdown-cell {
            text-align: right !important;
        }

        .col-subtotal-header,
        .col-subtotal-cell {
            text-align: right !important;
            font-weight: 700;
        }

        .col-vat-header,
        .col-vat-cell {
            text-align: right !important;
        }

        .col-total-header,
        .col-total-cell {
            text-align: right !important;
            font-weight: 700;
        }

        .col-excess-header,
        .col-excess-cell {
            text-align: right !important;
        }

        /* Red text for values exceeding average */
        .text-red-alert {
            color: #dc2626 !important;
            font-weight: 700 !important;
        }
        .dark .text-red-alert {
            color: #f87171 !important;
        }

        /* Summary rows & Statistics Box */
        .summary-row-subtotal {
            background-color: #f1f5f9 !important;
            font-weight: 800 !important;
            border-top: 2px solid #475569 !important;
        }
        .dark .summary-row-subtotal {
            background-color: #1e293b !important;
            border-top-color: #94a3b8 !important;
        }

        .summary-row-base {
            background-color: #f8fafc !important;
            font-weight: 700 !important;
        }
        .dark .summary-row-base {
            background-color: #0b0f19 !important;
        }

        /* KPI Statistics Box Highlight Cells */
        .stats-box-media {
            background-color: #eff6ff !important;
            color: #1d4ed8 !important;
            border-left: 2px solid #3b82f6 !important;
            font-weight: 800 !important;
        }
        .dark .stats-box-media {
            background-color: rgba(30, 58, 138, 0.35) !important;
            color: #93c5fd !important;
            border-left-color: #3b82f6 !important;
        }

        .stats-box-below {
            background-color: #ecfdf5 !important;
            color: #047857 !important;
            border-left: 2px solid #10b981 !important;
            font-weight: 800 !important;
        }
        .dark .stats-box-below {
            background-color: rgba(6, 78, 59, 0.35) !important;
            color: #6ee7b7 !important;
            border-left-color: #10b981 !important;
        }

        .stats-box-above {
            background-color: #fef2f2 !important;
            color: #b91c1c !important;
            border-left: 2px solid #ef4444 !important;
            font-weight: 800 !important;
        }
        .dark .stats-box-above {
            background-color: rgba(127, 29, 29, 0.35) !important;
            color: #fca5a5 !important;
            border-left-color: #ef4444 !important;
        }

        /* Screen only elements */
        @media screen {
            .print-only-header,
            .print-only-footer {
                display: none !important;
            }
        }

        /* High-Fidelity Print Mode - Matches PDF 1:1 */
        @media print {
            @page {
                size: A4 landscape;
                margin: 8mm 6mm 8mm 6mm;
            }

            html, body {
                background: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif !important;
                font-size: 7.5pt !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Hide everything outside the report */
            aside,
            header,
            nav,
            #topbar,
            .no-print,
            .no-print * {
                display: none !important;
            }

            .pl-64 {
                padding-left: 0 !important;
                margin-left: 0 !important;
                width: 100% !important;
            }

            main {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                background: transparent !important;
            }

            .print-only-header {
                display: block !important;
                text-align: center !important;
                margin-bottom: 6px !important;
            }

            .print-title {
                font-size: 11pt !important;
                font-weight: bold !important;
                text-transform: uppercase !important;
                letter-spacing: 0.5px !important;
                margin-bottom: 2px !important;
            }

            .print-subtitle {
                font-size: 8.5pt !important;
                font-weight: bold !important;
                text-transform: uppercase !important;
                color: #333 !important;
            }

            .report-card-wrapper {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                background: transparent !important;
            }

            .report-grid-table {
                width: 100% !important;
                border-collapse: collapse !important;
                table-layout: fixed !important;
                border: 1px solid #111111 !important;
            }

            .report-grid-table th,
            .report-grid-table td {
                border: 1px solid #111111 !important;
                padding: 2px 3px !important;
                font-size: 6.8pt !important;
                color: #000000 !important;
                white-space: nowrap !important;
            }

            .report-grid-table thead tr.group-header-row th {
                background-color: #f1f5f9 !important;
                font-weight: bold !important;
                font-size: 7pt !important;
                border: 1px solid #111111 !important;
            }

            .report-grid-table thead tr.sub-header-row th {
                background-color: #f8fafc !important;
                font-weight: bold !important;
                font-size: 6.5pt !important;
                border: 1px solid #111111 !important;
            }

            .col-unit-cell {
                background-color: #ffffff !important;
            }

            .summary-row-subtotal {
                background-color: #f1f5f9 !important;
                border-top: 2px solid #111111 !important;
            }

            .summary-row-base {
                background-color: #ffffff !important;
            }

            .stats-box-media,
            .stats-box-below,
            .stats-box-above {
                background-color: #ffffff !important;
                color: #000000 !important;
                border-left: 2px solid #111111 !important;
            }

            .text-red-alert {
                color: #cc0000 !important;
                font-weight: bold !important;
            }

            .print-only-footer {
                display: block !important;
                margin-top: 4px !important;
                font-size: 6pt !important;
                color: #666 !important;
                text-align: right !important;
            }
        }
    </style>
</head>
<body class="bg-background text-foreground antialiased min-h-screen">
    @include('partials.sidebar')

    <div class="pl-64 flex flex-col min-h-screen">
        @include('partials.topbar', [
            'topbarSearchPlaceholder' => trans('reports.filter', [], $lang) . '...',
            'topbarSearchId' => 'reportSearch',
            'topbarStatus' => $report['meta']['unit_count'] . ' ' . trans('reports.active_units', [], $lang),
        ])

        <main class="w-full pt-16 flex-1 px-8 py-8 bg-background">
            <div class="flex flex-col w-full gap-5 max-w-7xl mx-auto">

                <!-- Page Header & Action Controls (Screen Only) -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-3 border-b no-print">
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-xs text-muted-foreground font-mono">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-muted border">
                                <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5 text-primary"></i>
                                <span>{{ trans('reports.fleet_statement', [], $lang) }}</span>
                            </span>
                            <span>&bull;</span>
                            <span class="text-foreground font-semibold">{{ $report['meta']['subtitle'] }}</span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">
                                {{ $report['meta']['title'] }}
                            </h1>
                            <p class="text-xs text-muted-foreground mt-1 leading-relaxed">
                                {{ $report['meta']['subtitle'] }} &bull; {{ $report['meta']['unit_count'] }} {{ trans('reports.active_units', [], $lang) }}
                            </p>
                        </div>
                    </div>

                    <!-- Right Controls: Language Switcher, Export PDF, Print -->
                    <div class="flex flex-wrap items-center gap-2.5">
                        <!-- Bilingual Language Switcher -->
                        <div class="inline-flex items-center rounded-lg border border-border bg-card p-1 shadow-xs">
                            <a href="{{ request()->fullUrlWithQuery(['lang' => 'es']) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-md transition-all {{ $lang === 'es' ? 'bg-primary text-primary-foreground shadow-xs' : 'text-muted-foreground hover:text-foreground' }}">
                                <span>🇪🇸</span>
                                <span>{{ trans('reports.spanish', [], $lang) }}</span>
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-md transition-all {{ $lang === 'en' ? 'bg-primary text-primary-foreground shadow-xs' : 'text-muted-foreground hover:text-foreground' }}">
                                <span>🇺🇸</span>
                                <span>{{ trans('reports.english', [], $lang) }}</span>
                            </a>
                        </div>

                        <!-- Print Button -->
                        <button type="button" onclick="window.print()"
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-xs font-medium border border-input bg-background shadow-xs hover:bg-accent hover:text-accent-foreground h-9 px-3.5 gap-2">
                            <i data-lucide="printer" class="w-3.5 h-3.5 text-muted-foreground"></i>
                            <span>{{ trans('reports.print', [], $lang) }}</span>
                        </button>

                        <!-- Export PDF Button -->
                        <a href="{{ route('reports.fuel.pdf', array_merge(request()->query(), ['lang' => $lang])) }}"
                           class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-xs font-semibold bg-primary text-primary-foreground shadow-xs hover:bg-primary/90 h-9 px-4 gap-2 transition-all">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                            <span>{{ trans('reports.export_pdf', [], $lang) }} ({{ strtoupper($lang) }})</span>
                        </a>

                        <!-- Direct Spanish & English PDF dropdown -->
                        <div class="relative group">
                            <button type="button" class="inline-flex items-center justify-center rounded-md text-xs font-medium border border-input bg-card hover:bg-accent h-9 px-2.5">
                                <i data-lucide="more-horizontal" class="w-4 h-4 text-muted-foreground"></i>
                            </button>
                            <div class="absolute right-0 top-full mt-1.5 hidden group-hover:flex flex-col bg-card border border-border shadow-lg rounded-lg py-1.5 z-50 w-52 text-xs">
                                <a href="{{ route('reports.fuel.pdf', array_merge(request()->query(), ['lang' => 'es'])) }}" class="flex items-center gap-2 px-3 py-1.5 hover:bg-muted font-medium">
                                    <span>🇪🇸</span>
                                    <span>Exportar PDF en Español</span>
                                </a>
                                <a href="{{ route('reports.fuel.pdf', array_merge(request()->query(), ['lang' => 'en'])) }}" class="flex items-center gap-2 px-3 py-1.5 hover:bg-muted font-medium">
                                    <span>🇺🇸</span>
                                    <span>Export PDF in English</span>
                                </a>
                                <div class="border-t my-1"></div>
                                <a href="{{ route('reports.fuel.pdf', array_merge(request()->query(), ['lang' => $lang, 'preview' => 1])) }}" target="_blank" class="flex items-center gap-2 px-3 py-1.5 hover:bg-muted font-medium text-primary">
                                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                    <span>Ver Vista Previa PDF</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Frequency Mode Switcher Tabs (Monthly, Weekly, Daily) -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 no-print">
                    <div class="inline-flex items-center p-1 bg-muted/60 border border-border rounded-xl shadow-xs">
                        <a href="{{ request()->fullUrlWithQuery(['mode' => 'monthly']) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold transition-all {{ $mode === 'monthly' ? 'bg-card text-foreground shadow-xs' : 'text-muted-foreground hover:text-foreground' }}">
                            <i data-lucide="calendar" class="w-4 h-4 {{ $mode === 'monthly' ? 'text-primary' : '' }}"></i>
                            <span>{{ trans('reports.mode_monthly', [], $lang) }}</span>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['mode' => 'weekly']) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold transition-all {{ $mode === 'weekly' ? 'bg-card text-foreground shadow-xs' : 'text-muted-foreground hover:text-foreground' }}">
                            <i data-lucide="calendar-days" class="w-4 h-4 {{ $mode === 'weekly' ? 'text-primary' : '' }}"></i>
                            <span>{{ trans('reports.mode_weekly', [], $lang) }}</span>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['mode' => 'daily']) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold transition-all {{ $mode === 'daily' ? 'bg-card text-foreground shadow-xs' : 'text-muted-foreground hover:text-foreground' }}">
                            <i data-lucide="clock" class="w-4 h-4 {{ $mode === 'daily' ? 'text-primary' : '' }}"></i>
                            <span>{{ trans('reports.mode_daily', [], $lang) }}</span>
                        </a>
                    </div>

                    @if($mode === 'monthly')
                        <!-- Quick Presets for Monthly (Prevent forcing full year at once) -->
                        <div class="flex flex-wrap items-center gap-1.5 text-xs text-muted-foreground font-mono">
                            <span class="text-[11px] font-semibold uppercase mr-1">{{ trans('reports.quick_presets', [], $lang) }}:</span>
                            <a href="{{ request()->fullUrlWithQuery(['start_month' => 1, 'end_month' => 9]) }}"
                               class="px-2.5 py-1 rounded-md border text-[11px] font-medium transition-colors {{ $startMonth === 1 && $endMonth === 9 ? 'bg-primary/10 text-primary border-primary/30 font-semibold' : 'hover:bg-muted' }}">
                                Ene - Sep
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['start_month' => 1, 'end_month' => 3]) }}"
                               class="px-2.5 py-1 rounded-md border text-[11px] font-medium transition-colors {{ $startMonth === 1 && $endMonth === 3 ? 'bg-primary/10 text-primary border-primary/30 font-semibold' : 'hover:bg-muted' }}">
                                Q1
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['start_month' => 4, 'end_month' => 6]) }}"
                               class="px-2.5 py-1 rounded-md border text-[11px] font-medium transition-colors {{ $startMonth === 4 && $endMonth === 6 ? 'bg-primary/10 text-primary border-primary/30 font-semibold' : 'hover:bg-muted' }}">
                                Q2
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['start_month' => 7, 'end_month' => 9]) }}"
                               class="px-2.5 py-1 rounded-md border text-[11px] font-medium transition-colors {{ $startMonth === 7 && $endMonth === 9 ? 'bg-primary/10 text-primary border-primary/30 font-semibold' : 'hover:bg-muted' }}">
                                Q3
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['start_month' => 10, 'end_month' => 12]) }}"
                               class="px-2.5 py-1 rounded-md border text-[11px] font-medium transition-colors {{ $startMonth === 10 && $endMonth === 12 ? 'bg-primary/10 text-primary border-primary/30 font-semibold' : 'hover:bg-muted' }}">
                                Q4
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['start_month' => (int)now()->month, 'end_month' => (int)now()->month]) }}"
                               class="px-2.5 py-1 rounded-md border text-[11px] font-medium transition-colors {{ $startMonth === (int)now()->month && $endMonth === (int)now()->month ? 'bg-primary/10 text-primary border-primary/30 font-semibold' : 'hover:bg-muted' }}">
                                {{ trans('reports.current_month', [], $lang) }}
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Mode-Specific Filter Bar (Screen Only) -->
                <div class="rounded-xl border bg-card p-4 shadow-xs no-print">
                    <form method="GET" action="{{ route('reports.fuel') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-end">
                        <input type="hidden" name="mode" value="{{ $mode }}"/>
                        <input type="hidden" name="lang" value="{{ $lang }}"/>

                        <div>
                            <label class="text-xs font-medium text-muted-foreground mb-1 block">
                                {{ trans('reports.year', [], $lang) }}
                            </label>
                            @php
                                $currentYear = (int) now()->year;
                                $minYear = $currentYear - 3;
                            @endphp
                            <select name="year" class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-xs shadow-xs focus:outline-none focus:ring-1 focus:ring-ring">
                                @for($y = $currentYear; $y >= $minYear; $y--)
                                    <option value="{{ $y }}" {{ $year === $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        @if($mode === 'monthly')
                            <div>
                                <label class="text-xs font-medium text-muted-foreground mb-1 block">
                                    {{ trans('reports.from_month', [], $lang) }}
                                </label>
                                <select name="start_month" class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-xs shadow-xs focus:outline-none focus:ring-1 focus:ring-ring">
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ $startMonth === $m ? 'selected' : '' }}>
                                            {{ trans("reports.months.{$m}", [], $lang) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-xs font-medium text-muted-foreground mb-1 block">
                                    {{ trans('reports.to_month', [], $lang) }}
                                </label>
                                <select name="end_month" class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-xs shadow-xs focus:outline-none focus:ring-1 focus:ring-ring">
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ $endMonth === $m ? 'selected' : '' }}>
                                            {{ trans("reports.months.{$m}", [], $lang) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div>
                                <label class="text-xs font-medium text-muted-foreground mb-1 block">
                                    {{ trans('reports.month', [], $lang) }}
                                </label>
                                <select name="month" class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-xs shadow-xs focus:outline-none focus:ring-1 focus:ring-ring">
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>
                                            {{ trans("reports.months.{$m}", [], $lang) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if($mode === 'daily')
                                <div>
                                    <label class="text-xs font-medium text-muted-foreground mb-1 block">
                                        {{ trans('reports.daily_range', [], $lang) }}
                                    </label>
                                    <select name="day_range" class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-xs shadow-xs focus:outline-none focus:ring-1 focus:ring-ring">
                                        <option value="1-15" {{ $dayRange === '1-15' ? 'selected' : '' }}>{{ trans('reports.days_1_15', [], $lang) }}</option>
                                        <option value="16-end" {{ $dayRange === '16-end' ? 'selected' : '' }}>{{ trans('reports.days_16_end', [], $lang) }}</option>
                                        <option value="full" {{ $dayRange === 'full' ? 'selected' : '' }}>{{ trans('reports.days_full', ['max' => 31], $lang) }}</option>
                                    </select>
                                </div>
                            @else
                                <div>
                                    <label class="text-xs font-medium text-muted-foreground mb-1 block">
                                        {{ trans('reports.period', [], $lang) }}
                                    </label>
                                    <div class="flex h-9 items-center px-3 rounded-md bg-muted/40 border border-input text-xs font-medium text-muted-foreground">
                                        <span>5 {{ trans('reports.week', [], $lang) }}s (1 - 31)</span>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div class="flex items-center gap-2">
                            <button type="submit" class="flex-1 inline-flex items-center justify-center rounded-md bg-primary text-primary-foreground text-xs font-semibold h-9 px-3 hover:bg-primary/90 shadow-xs gap-1.5">
                                <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                                <span>{{ trans('reports.filter', [], $lang) }}</span>
                            </button>
                            <a href="{{ route('reports.fuel', ['mode' => $mode, 'lang' => $lang]) }}" class="inline-flex items-center justify-center rounded-md border border-input bg-background text-xs font-medium h-9 px-3 hover:bg-muted" title="{{ trans('reports.reset_filter', [], $lang) }}">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-muted-foreground"></i>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- KPI Metric Highlights (Screen Only) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 no-print">
                    <div class="rounded-xl border bg-card p-3.5 shadow-xs">
                        <div class="text-[11px] font-medium text-muted-foreground font-mono uppercase">
                            {{ trans('reports.kpi_total_spent', [], $lang) }}
                        </div>
                        <div class="text-lg font-bold text-foreground mt-1 font-mono">
                            ${{ number_format($report['summary']['grand_total'], 2) }}
                        </div>
                        <div class="text-[10px] text-muted-foreground mt-0.5">
                            LANCOMTRI SA Fleet
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card p-3.5 shadow-xs">
                        <div class="text-[11px] font-medium text-muted-foreground font-mono uppercase">
                            {{ trans('reports.active_units', [], $lang) }}
                        </div>
                        <div class="text-lg font-bold text-foreground mt-1 font-mono">
                            {{ $report['meta']['unit_count'] }}
                        </div>
                        <div class="text-[10px] text-emerald-600 font-medium mt-0.5">
                            {{ trans('reports.active_units', [], $lang) }}
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card p-3.5 shadow-xs">
                        <div class="text-[11px] font-medium text-muted-foreground font-mono uppercase">
                            {{ trans('reports.kpi_average', [], $lang) }}
                        </div>
                        <div class="text-lg font-bold text-foreground mt-1 font-mono">
                            ${{ number_format($report['summary']['overall_media'], 2) }}
                        </div>
                        <div class="text-[10px] text-muted-foreground mt-0.5">
                            100.00% {{ trans('reports.media', [], $lang) }}
                        </div>
                    </div>

                    <div class="rounded-xl border border-red-500/20 bg-red-500/5 p-3.5 shadow-xs">
                        <div class="text-[11px] font-medium text-red-600 dark:text-red-400 font-mono uppercase">
                            {{ trans('reports.kpi_above', [], $lang) }}
                        </div>
                        <div class="text-lg font-bold text-red-600 dark:text-red-400 mt-1 font-mono">
                            {{ $report['summary']['above_media_percent'] }}%
                        </div>
                        <div class="text-[10px] text-red-600/80 font-medium mt-0.5">
                            {{ $report['summary']['above_media_count'] }} {{ trans('reports.unit', [], $lang) }}s
                        </div>
                    </div>

                    <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-3.5 shadow-xs">
                        <div class="text-[11px] font-medium text-emerald-600 dark:text-emerald-400 font-mono uppercase">
                            {{ trans('reports.kpi_below', [], $lang) }}
                        </div>
                        <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-1 font-mono">
                            {{ $report['summary']['below_media_percent'] }}%
                        </div>
                        <div class="text-[10px] text-emerald-600/80 font-medium mt-0.5">
                            {{ $report['summary']['below_media_count'] }} {{ trans('reports.unit', [], $lang) }}s
                        </div>
                    </div>
                </div>

                <!-- Printable Document Section (Matches PDF 1:1) -->
                <div class="report-card-wrapper rounded-xl border bg-card text-card-foreground shadow-xs overflow-hidden flex flex-col">

                    <!-- Dedicated Print-Only Title Header -->
                    <div class="print-only-header">
                        <div class="print-title">{{ $report['meta']['title'] }}</div>
                        <div class="print-subtitle">{{ $report['meta']['subtitle'] }}</div>
                    </div>

                    <!-- Screen Card Header (Hidden on Print) -->
                    <div class="p-4 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-muted/30 no-print">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 rounded-md bg-primary/10 text-primary">
                                <i data-lucide="table" class="w-4 h-4"></i>
                            </span>
                            <div>
                                <h2 class="text-sm font-bold text-foreground">
                                    {{ $report['meta']['title'] }}
                                </h2>
                                <p class="text-[11px] text-muted-foreground font-mono">
                                    {{ $report['meta']['subtitle'] }}
                                </p>
                            </div>
                        </div>

                        <!-- Instant Filter Input -->
                        <div class="flex items-center gap-2">
                            <div class="relative w-48">
                                <i data-lucide="search" class="w-3 h-3 text-muted-foreground absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                                <input id="tableSearch" onkeyup="filterReportTable(this.value)" placeholder="{{ trans('reports.unit', [], $lang) }} #..."
                                       class="flex h-8 w-full rounded-md border border-input bg-background pl-8 pr-2.5 py-1 text-xs shadow-xs focus:outline-none focus:ring-1 focus:ring-ring"/>
                            </div>
                        </div>
                    </div>

                    @php
                        $columns = $report['meta']['columns'];
                        $columnKeys = array_keys($columns);
                        $colCount = count($columnKeys);

                        // Balanced proportional column width logic (Without VAT)
                        if ($colCount <= 3) {
                            $unitColWidth = '14%';
                            $totalColWidth = '22%';
                            $excessColWidth = '18%';
                            $breakdownColWidth = round(46 / max(1, $colCount), 2) . '%';
                        } else {
                            $unitColWidth = '6%';
                            $totalColWidth = '12%';
                            $excessColWidth = '10%';
                            $breakdownColWidth = round(72 / max(1, $colCount), 2) . '%';
                        }
                    @endphp

                    <!-- Table Container -->
                    <div class="overflow-x-auto w-full">
                        @if(empty($report['rows']))
                            <div class="p-12 text-center text-muted-foreground">
                                <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2 opacity-50"></i>
                                <p class="text-sm font-semibold text-foreground">No fleet buses or fuel logs found for this period</p>
                                <p class="text-xs text-muted-foreground mt-1">Register vehicles in the Buses Roster and log fuel entries to generate this statement.</p>
                            </div>
                        @else
                            <table class="report-grid-table" id="fuelReportTable">
                                <thead>
                                    <tr class="group-header-row">
                                        <th rowspan="2" style="width: {{ $unitColWidth }};" class="col-unit-header">
                                            {{ trans('reports.unit', [], $lang) }}
                                        </th>
                                        <th colspan="{{ $colCount }}">
                                            {{ $report['meta']['breakdown_title'] }}
                                        </th>
                                        <th colspan="2">
                                            {{ trans('reports.general_totals', [], $lang) }}
                                        </th>
                                    </tr>
                                    <tr class="sub-header-row">
                                        @foreach($columns as $ck => $col)
                                            <th style="width: {{ $breakdownColWidth }};" class="col-breakdown-header">{{ $col['label'] }}</th>
                                        @endforeach
                                        <th style="width: {{ $totalColWidth }};" class="col-total-header">{{ trans('reports.totals', [], $lang) }}</th>
                                        <th style="width: {{ $excessColWidth }};" class="col-excess-header">{{ trans('reports.excess', [], $lang) }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($report['rows'] as $row)
                                        <tr class="hover:bg-muted/30 transition-colors {{ $row['is_above_media'] ? 'bg-red-500/[0.03]' : '' }}" data-unit="{{ $row['unit'] }}">
                                            <td class="col-unit-cell">{{ $row['unit'] }}</td>
                                            @foreach($columnKeys as $ck)
                                                <td class="col-breakdown-cell">{{ number_format($row['columns'][$ck] ?? 0, 2) }}</td>
                                            @endforeach
                                            <td class="col-total-cell {{ $row['is_above_media'] ? 'text-red-alert' : '' }}">
                                                {{ number_format($row['total'], 2) }}
                                            </td>
                                            <td class="col-excess-cell {{ $row['is_above_media'] ? 'text-red-alert' : '' }}">
                                                {{ number_format($row['excess'], 2) }}
                                            </td>
                                        </tr>
                                    @endforeach

                                    <!-- Summary Rows (Matches PDF 1:1 Without VAT) -->
                                    <!-- Row 1: TOTAL -->
                                    <tr class="summary-row-subtotal">
                                        <td class="text-left font-sans pl-2.5 font-bold uppercase">{{ trans('reports.total', [], $lang) }}</td>
                                        @foreach($columnKeys as $ck)
                                            <td class="text-right font-bold">{{ number_format($report['summary']['col_totals'][$ck] ?? 0, 2) }}</td>
                                        @endforeach
                                        <td class="text-right font-bold">{{ number_format($report['summary']['grand_total'], 2) }}</td>
                                        <td class="text-right font-bold">0.00</td>
                                    </tr>

                                    <!-- Row 2: MEDIA (Average Fleet Consumption) -->
                                    <tr class="summary-row-base">
                                        <td class="text-left font-sans pl-2.5 font-bold uppercase">{{ trans('reports.media', [], $lang) }}</td>
                                        @foreach($columnKeys as $ck)
                                            <td class="text-right">{{ number_format($report['summary']['col_medias'][$ck] ?? 0, 2) }}</td>
                                        @endforeach
                                        <td class="text-right stats-box-media">
                                            {{ number_format($report['summary']['overall_media'], 2) }}
                                        </td>
                                        <td class="text-right stats-box-media">
                                            100.00%
                                        </td>
                                    </tr>

                                    <!-- Row 3: INFERIOR A LA MEDIA (Below Average) -->
                                    <tr class="summary-row-base">
                                        <td colspan="{{ $colCount + 1 }}" class="bg-muted/10"></td>
                                        <td class="text-left font-sans pl-3 stats-box-below uppercase">
                                            {{ trans('reports.below_media', [], $lang) }}
                                        </td>
                                        <td class="text-right stats-box-below">
                                            {{ number_format($report['summary']['below_media_percent'], 2) }}%
                                        </td>
                                    </tr>

                                    <!-- Row 4: SUPERIOR A LA MEDIA (Above Average) -->
                                    <tr class="summary-row-base">
                                        <td colspan="{{ $colCount + 1 }}" class="bg-muted/10"></td>
                                        <td class="text-left font-sans pl-3 stats-box-above uppercase">
                                            {{ trans('reports.above_media', [], $lang) }}
                                        </td>
                                        <td class="text-right stats-box-above">
                                            {{ number_format($report['summary']['above_media_percent'], 2) }}%
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        @endif
                    </div>

                    <!-- Table Footer Note (Screen) -->
                    <div class="p-3 border-t bg-muted/20 flex flex-col sm:flex-row items-center justify-between text-xs text-muted-foreground no-print">
                        <div>
                            <span>LANCOMTRI SA &bull; {{ trans('reports.generated_on', ['date' => $report['meta']['generated_at']], $lang) }}</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span>
                                <span class="text-red-600 font-medium">{{ trans('reports.above_media', [], $lang) }} ({{ $report['summary']['above_media_count'] }})</span>
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                                <span class="text-emerald-600 font-medium">{{ trans('reports.below_media', [], $lang) }} ({{ $report['summary']['below_media_count'] }})</span>
                            </span>
                        </div>
                    </div>

                    <!-- Print-Only Footer -->
                    <div class="print-only-footer">
                        {{ trans('reports.generated_on', ['date' => $report['meta']['generated_at']], $lang) }} &bull; LANCOMTRI SA Fleet Management System
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        function filterReportTable(query) {
            const cleanQuery = query.trim().toLowerCase();
            const rows = document.querySelectorAll('#fuelReportTable tbody tr[data-unit]');
            rows.forEach(row => {
                const unit = (row.getAttribute('data-unit') || '').toLowerCase();
                if (!cleanQuery || unit.includes(cleanQuery)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
