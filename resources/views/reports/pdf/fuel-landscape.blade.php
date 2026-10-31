<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $report['meta']['title'] }} - {{ $report['meta']['subtitle'] }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 6mm 10mm 6mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 7.5pt;
            color: #000;
            background: #fff;
            line-height: 1.15;
            padding: 4px 6px;
        }

        .header-title-box {
            text-align: center;
            margin-bottom: 6px;
        }

        .main-title {
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .subtitle {
            font-size: 8.5pt;
            font-weight: bold;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            color: #222;
        }

        table.report-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.report-table th,
        table.report-table td {
            border: 1px solid #111;
            padding: 1.8px 3px;
            font-size: 6.8pt;
            vertical-align: middle;
            white-space: nowrap;
        }

        table.report-table thead {
            display: table-header-group;
        }

        table.report-table thead th {
            background-color: #f8fafc;
            font-weight: bold;
            text-align: center;
            font-size: 6.5pt;
        }

        .group-header {
            font-size: 7pt !important;
            padding: 3px 2px !important;
            letter-spacing: 0.2px;
        }

        .col-unit {
            width: 5%;
            text-align: center !important;
            font-weight: bold;
        }

        .col-subtotal {
            width: 8.5%;
            text-align: right !important;
            font-weight: bold;
        }

        .col-vat {
            width: 7%;
            text-align: right !important;
        }

        .col-total {
            width: 8%;
            text-align: right !important;
        }

        .col-excess {
            width: 7.5%;
            text-align: right !important;
        }

        /* Red text for values exceeding average consumption */
        .text-red {
            color: #cc0000 !important;
            font-weight: bold;
        }

        .summary-label {
            font-weight: bold;
            text-align: left;
            padding-left: 4px;
            background-color: #ffffff;
        }

        .summary-num {
            font-weight: bold;
            text-align: right;
            background-color: #ffffff;
        }

        .stats-box-label {
            font-weight: bold;
            font-size: 6.5pt;
            text-align: left !important;
            padding-left: 4px;
        }

        .stats-box-val {
            font-weight: bold;
            font-size: 6.8pt;
            text-align: right !important;
        }

        .page-break {
            page-break-after: always;
        }

        .footer-note {
            margin-top: 4px;
            font-size: 5.5pt;
            color: #666;
            text-align: right;
        }
    </style>
</head>
<body>

    @php
        $columns = $report['meta']['columns'];
        $columnKeys = array_keys($columns);
        $colCount = count($columnKeys);
        $taxPercent = $report['meta']['tax_rate_percent'];
        $rows = array_values($report['rows']);
        $totalRows = count($rows);

        // Dynamically compute width for breakdown columns (Without VAT)
        if ($colCount <= 3) {
            $unitColWidth = '14%';
            $totalColWidth = '22%';
            $excessColWidth = '18%';
            $breakdownColWidth = round(46 / max(1, $colCount), 2);
        } else {
            $unitColWidth = '6%';
            $totalColWidth = '12%';
            $excessColWidth = '10%';
            $breakdownColWidth = round(72 / max(1, $colCount), 2);
        }

        // Split into 2 pages if > 36 units
        $pageSize = ($totalRows > 40) ? 36 : max(1, $totalRows);
        $pages = array_chunk($rows, $pageSize);
        if (empty($pages)) {
            $pages = [[]];
        }
    @endphp

    @foreach($pages as $pageIndex => $pageRows)
        <div class="header-title-box">
            <div class="main-title">{{ $report['meta']['title'] }}</div>
            <div class="subtitle">{{ $report['meta']['subtitle'] }}</div>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th rowspan="2" class="col-unit group-header" style="width: {{ $unitColWidth }};">{{ trans('reports.unit', [], $lang) }}</th>
                    <th colspan="{{ $colCount }}" class="group-header">{{ $report['meta']['breakdown_title'] }}</th>
                    <th colspan="2" class="group-header">{{ trans('reports.general_totals', [], $lang) }}</th>
                </tr>
                <tr>
                    @foreach($columns as $cKey => $col)
                        <th style="width: {{ $breakdownColWidth }}%;">{{ $col['label'] }}</th>
                    @endforeach
                    <th class="col-total" style="width: {{ $totalColWidth }};">{{ trans('reports.totals', [], $lang) }}</th>
                    <th class="col-excess" style="width: {{ $excessColWidth }};">{{ trans('reports.excess', [], $lang) }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pageRows as $row)
                    <tr>
                        <td class="col-unit">{{ $row['unit'] }}</td>
                        @foreach($columnKeys as $ck)
                            <td style="text-align: right;">{{ number_format($row['columns'][$ck] ?? 0, 2) }}</td>
                        @endforeach
                        <td class="col-total {{ $row['is_above_media'] ? 'text-red' : '' }}">{{ number_format($row['total'], 2) }}</td>
                        <td class="col-excess {{ $row['is_above_media'] ? 'text-red' : '' }}">{{ number_format($row['excess'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $colCount + 3 }}" style="text-align: center; padding: 12px; font-style: italic;">
                            No fleet records found for this period.
                        </td>
                    </tr>
                @endforelse

                {{-- On final page, render official summary rows & statistics box --}}
                @if($loop->last && !empty($report['rows']))
                    {{-- Row 1: TOTAL --}}
                    <tr>
                        <td class="col-unit summary-label">{{ trans('reports.total', [], $lang) }}</td>
                        @foreach($columnKeys as $ck)
                            <td class="summary-num">{{ number_format($report['summary']['col_totals'][$ck] ?? 0, 2) }}</td>
                        @endforeach
                        <td class="col-total summary-num">{{ number_format($report['summary']['grand_total'], 2) }}</td>
                        <td class="col-excess summary-num">0.00</td>
                    </tr>

                    {{-- Row 2: MEDIA summary --}}
                    <tr>
                        <td class="col-unit summary-label">{{ trans('reports.media', [], $lang) }}</td>
                        @foreach($columnKeys as $ck)
                            <td class="summary-num">{{ number_format($report['summary']['col_medias'][$ck] ?? 0, 2) }}</td>
                        @endforeach
                        <td class="stats-box-val">{{ number_format($report['summary']['overall_media'], 2) }}</td>
                        <td class="stats-box-val">100.00%</td>
                    </tr>

                    {{-- Row 3: INFERIOR A LA MEDIA summary --}}
                    <tr>
                        <td colspan="{{ $colCount + 1 }}" style="background-color: #ffffff; border: 1px solid #111;"></td>
                        <td class="stats-box-label">{{ trans('reports.below_media', [], $lang) }}</td>
                        <td class="stats-box-val">{{ number_format($report['summary']['below_media_percent'], 2) }}%</td>
                    </tr>

                    {{-- Row 4: SUPERIOR A LA MEDIA summary --}}
                    <tr>
                        <td colspan="{{ $colCount + 1 }}" style="background-color: #ffffff; border: 1px solid #111;"></td>
                        <td class="stats-box-label">{{ trans('reports.above_media', [], $lang) }}</td>
                        <td class="stats-box-val text-red">{{ number_format($report['summary']['above_media_percent'], 2) }}%</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="footer-note">
            {{ trans('reports.generated_on', ['date' => $report['meta']['generated_at']], $lang) }} &bull; {{ trans('reports.page', [], $lang) }} {{ $pageIndex + 1 }} {{ trans('reports.of', [], $lang) }} {{ count($pages) }}
        </div>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

</body>
</html>
