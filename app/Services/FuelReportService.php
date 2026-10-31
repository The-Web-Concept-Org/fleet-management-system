<?php

namespace App\Services;

use App\Models\Bus;
use App\Models\FuelLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FuelReportService
{
    /**
     * Build the fuel consumption report strictly from real database records.
     * Supports 'monthly', 'weekly', and 'daily' frequency modes.
     */
    public function generateReport(
        int $year = 2026,
        string $mode = 'monthly',
        int $month = 10,
        int $startMonth = 1,
        int $endMonth = 10,
        string $dayRange = '1-15',
        float $taxRate = 0.0,
        string $lang = 'es'
    ): array {
        if (!in_array($mode, ['monthly', 'weekly', 'daily'], true)) {
            $mode = 'monthly';
        }

        $month = max(1, min(12, $month));
        $startMonth = max(1, min(12, $startMonth));
        $endMonth = max($startMonth, min(12, $endMonth));

        // Configure columns and date bounds based on mode
        $columnConfig = $this->buildColumnConfig($mode, $year, $month, $startMonth, $endMonth, $dayRange, $lang);

        $columns = $columnConfig['columns'];
        $columnKeys = array_keys($columns);
        $periodSubtitle = $columnConfig['subtitle'];
        $breakdownTitle = $columnConfig['breakdown_title'];

        // Fetch real fuel data for the selected period
        $sourceData = $this->fetchFromDatabase($mode, $year, $month, $columnConfig['start_date'], $columnConfig['end_date'], $columnKeys);

        $rows = [];
        $colSubtotals = array_fill_keys($columnKeys, 0.0);
        $totalFleetSubtotal = 0.0;
        $totalFleetVat = 0.0;
        $totalFleetGrand = 0.0;

        foreach ($sourceData as $unitCode => $colValues) {
            $unitSubtotal = 0.0;
            $unitCols = [];

            foreach ($columnKeys as $ck) {
                $val = (float) ($colValues[$ck] ?? 0.0);
                $unitCols[$ck] = $val;
                $unitSubtotal += $val;
                $colSubtotals[$ck] += $val;
            }

            $unitVat = round($unitSubtotal * $taxRate, 2);
            $unitTotal = round($unitSubtotal + $unitVat, 2);

            $rows[$unitCode] = [
                'unit' => str_pad((string) $unitCode, 2, '0', STR_PAD_LEFT),
                'columns' => $unitCols,
                'subtotal' => $unitSubtotal,
                'vat' => $unitVat,
                'total' => $unitTotal,
                'excess' => 0.0,
                'is_above_media' => false,
            ];

            $totalFleetSubtotal += $unitSubtotal;
            $totalFleetVat += $unitVat;
            $totalFleetGrand += $unitTotal;
        }

        // Natural sort by unit number
        ksort($rows, SORT_NATURAL);

        $unitCount = count($rows);
        $overallMedia = $unitCount > 0 ? round($totalFleetGrand / $unitCount, 2) : 0.0;

        // Second pass: Calculate Excedente and above/below media status
        $aboveMediaCount = 0;
        $belowMediaCount = 0;

        foreach ($rows as $code => &$r) {
            $diff = round($r['total'] - $overallMedia, 2);
            $r['excess'] = abs($diff);
            if ($r['total'] > $overallMedia) {
                $r['is_above_media'] = true;
                $aboveMediaCount++;
            } else {
                $r['is_above_media'] = false;
                $belowMediaCount++;
            }
        }
        unset($r);

        // Bottom summary rows calculations
        $colVat = [];
        $colTotals = [];
        $colMedias = [];

        foreach ($columnKeys as $ck) {
            $cSub = $colSubtotals[$ck];
            $cVat = round($cSub * $taxRate, 2);
            $cTot = round($cSub + $cVat, 2);
            $cMed = $unitCount > 0 ? round($cSub / $unitCount, 2) : 0.0;

            $colVat[$ck] = $cVat;
            $colTotals[$ck] = $cTot;
            $colMedias[$ck] = $cMed;
        }

        $aboveMediaPercent = $unitCount > 0 ? round(($aboveMediaCount / $unitCount) * 100, 2) : 0.0;
        $belowMediaPercent = $unitCount > 0 ? round(($belowMediaCount / $unitCount) * 100, 2) : 0.0;

        return [
            'meta' => [
                'title' => trans('reports.title', [], $lang),
                'subtitle' => $periodSubtitle,
                'breakdown_title' => $breakdownTitle,
                'company' => config('app.name', 'LANCOMTRI SA'),
                'mode' => $mode,
                'year' => $year,
                'month' => $month,
                'start_month' => $startMonth,
                'end_month' => $endMonth,
                'day_range' => $dayRange,
                'columns' => $columns,
                'tax_rate' => $taxRate,
                'tax_rate_percent' => round($taxRate * 100),
                'lang' => $lang,
                'unit_count' => $unitCount,
                'generated_at' => now()->format('Y-m-d H:i'),
            ],
            'rows' => $rows,
            'summary' => [
                'col_subtotals' => $colSubtotals,
                'col_vat' => $colVat,
                'col_totals' => $colTotals,
                'col_medias' => $colMedias,
                'total_subtotal' => $totalFleetSubtotal,
                'total_vat' => $totalFleetVat,
                'grand_total' => $totalFleetGrand,
                'overall_media' => $overallMedia,
                'above_media_count' => $aboveMediaCount,
                'above_media_percent' => $aboveMediaPercent,
                'below_media_count' => $belowMediaCount,
                'below_media_percent' => $belowMediaPercent,
            ],
        ];
    }

    /**
     * Build the columns and date bounds for the selected frequency mode.
     */
    protected function buildColumnConfig(string $mode, int $year, int $month, int $startMonth, int $endMonth, string $dayRange, string $lang): array
    {
        $monthName = trans("reports.months.{$month}", [], $lang);

        if ($mode === 'weekly') {
            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
            $columns = [];

            for ($w = 1; $w <= 5; $w++) {
                $startDay = ($w - 1) * 7 + 1;
                $endDay = min($w * 7, $daysInMonth);
                if ($startDay <= $daysInMonth) {
                    $label = trans('reports.week_short', ['num' => $w, 'start' => $startDay, 'end' => $endDay], $lang);
                    $columns["w{$w}"] = [
                        'label' => $label,
                        'start_day' => $startDay,
                        'end_day' => $endDay,
                    ];
                }
            }

            return [
                'columns' => $columns,
                'subtitle' => trans('reports.weekly_subtitle', ['month' => $monthName, 'year' => $year], $lang),
                'breakdown_title' => trans('reports.weekly_detail', [], $lang),
                'start_date' => Carbon::create($year, $month, 1, 0, 0, 0),
                'end_date' => Carbon::create($year, $month, $daysInMonth, 23, 59, 59),
            ];
        }

        if ($mode === 'daily') {
            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
            $startD = 1;
            $endD = min(15, $daysInMonth);
            $rangeLabel = trans('reports.days_1_15', [], $lang);

            if ($dayRange === '16-end') {
                $startD = 16;
                $endD = $daysInMonth;
                $rangeLabel = trans('reports.days_16_end', [], $lang);
            } elseif ($dayRange === 'full') {
                $startD = 1;
                $endD = $daysInMonth;
                $rangeLabel = trans('reports.days_full', ['max' => $daysInMonth], $lang);
            }

            $columns = [];
            for ($d = $startD; $d <= $endD; $d++) {
                $columns["d{$d}"] = [
                    'label' => str_pad((string) $d, 2, '0', STR_PAD_LEFT),
                    'day' => $d,
                ];
            }

            return [
                'columns' => $columns,
                'subtitle' => trans('reports.daily_subtitle', ['month' => $monthName, 'year' => $year, 'period' => $rangeLabel], $lang),
                'breakdown_title' => trans('reports.daily_detail', [], $lang),
                'start_date' => Carbon::create($year, $month, $startD, 0, 0, 0),
                'end_date' => Carbon::create($year, $month, $endD, 23, 59, 59),
            ];
        }

        // Monthly Mode
        $months = range($startMonth, $endMonth);
        $columns = [];
        foreach ($months as $m) {
            $columns["m{$m}"] = [
                'label' => trans("reports.months.{$m}", [], $lang),
                'month' => $m,
            ];
        }

        $startMonthName = trans("reports.months.{$startMonth}", [], $lang);
        $endMonthName = trans("reports.months.{$endMonth}", [], $lang);

        $subtitle = ($startMonth === 1 && $endMonth === 9)
            ? trans('reports.subtitle', ['year' => $year], $lang)
            : ($startMonth === 1 && $endMonth === 12
                ? trans('reports.full_year', ['year' => $year], $lang)
                : ($startMonth === $endMonth
                    ? "{$startMonthName} {$year}"
                    : trans('reports.custom_period', ['start' => $startMonthName, 'end' => $endMonthName, 'year' => $year], $lang)));

        $endDay = Carbon::create($year, $endMonth, 1)->daysInMonth;

        return [
            'columns' => $columns,
            'subtitle' => $subtitle,
            'breakdown_title' => trans('reports.monthly_detail', [], $lang),
            'start_date' => Carbon::create($year, $startMonth, 1, 0, 0, 0),
            'end_date' => Carbon::create($year, $endMonth, $endDay, 23, 59, 59),
        ];
    }

    /**
     * Fetch real fuel records from the database and aggregate into column buckets.
     */
    protected function fetchFromDatabase(
        string $mode,
        int $year,
        int $month,
        Carbon $startDate,
        Carbon $endDate,
        array $columnKeys
    ): array {
        $buses = Bus::where('status', '!=', 'inactive')->get();

        if ($buses->isEmpty()) {
            return [];
        }

        $logs = FuelLog::whereBetween('filled_at', [$startDate, $endDate])
            ->select('bus_id', 'total_cost', 'filled_at')
            ->get();

        $data = [];
        foreach ($buses as $bus) {
            $unit = str_pad((string) $bus->bus_number, 2, '0', STR_PAD_LEFT);
            $data[$unit] = array_fill_keys($columnKeys, 0.0);
        }

        foreach ($logs as $log) {
            $bus = $buses->firstWhere('id', $log->bus_id);
            if (!$bus) {
                continue;
            }

            $unit = str_pad((string) $bus->bus_number, 2, '0', STR_PAD_LEFT);
            $filledAt = Carbon::parse($log->filled_at);
            $amount = (float) $log->total_cost;

            $colKey = null;

            if ($mode === 'monthly') {
                $colKey = "m" . $filledAt->month;
            } elseif ($mode === 'weekly') {
                $day = $filledAt->day;
                $weekIndex = min(5, (int) ceil($day / 7));
                $colKey = "w{$weekIndex}";
            } elseif ($mode === 'daily') {
                $colKey = "d" . $filledAt->day;
            }

            if ($colKey && isset($data[$unit][$colKey])) {
                $data[$unit][$colKey] += $amount;
            }
        }

        return $data;
    }
}
