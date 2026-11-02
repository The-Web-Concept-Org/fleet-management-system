<?php

namespace App\Http\Controllers;

use App\Services\FuelReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class ReportController extends Controller
{
    protected FuelReportService $fuelReportService;

    public function __construct(FuelReportService $fuelReportService)
    {
        $this->fuelReportService = $fuelReportService;
    }

    /**
     * Display the interactive fuel report in the web portal.
     * Supports Monthly, Weekly, and Daily breakdowns, language toggling, and filtering.
     */
    public function fuel(Request $request): View
    {
        $mode = $request->input('mode', 'monthly');
        if (!in_array($mode, ['monthly', 'weekly', 'daily'], true)) {
            $mode = 'monthly';
        }

        $currentYear = (int) now()->year;
        $currentMonth = (int) now()->month;

        $year = (int) $request->input('year', $currentYear ?: 2026);
        // Prevent future years (e.g. 2027)
        if ($year > $currentYear) {
            $year = $currentYear;
        }
        $month = (int) $request->input('month', $currentMonth ?: 10);
        $startMonth = (int) $request->input('start_month', 1);
        $endMonth = (int) $request->input('end_month', max(10, $currentMonth));
        $dayRange = $request->input('day_range', '1-15');
        $taxRate = (float) $request->input('tax_rate', 0.0);
        $lang = $request->input('lang', App::getLocale() ?: 'es');

        if (!in_array($lang, ['es', 'en'], true)) {
            $lang = App::getLocale() ?: 'es';
        }

        App::setLocale($lang);

        $report = $this->fuelReportService->generateReport(
            $year,
            $mode,
            $month,
            $startMonth,
            $endMonth,
            $dayRange,
            $taxRate,
            $lang
        );

        return view('reports.fuel', compact(
            'report',
            'mode',
            'year',
            'month',
            'startMonth',
            'endMonth',
            'dayRange',
            'taxRate',
            'lang'
        ));
    }

    /**
     * Export the fuel report to a high-quality landscape PDF matching the official statement.
     */
    public function exportPdf(Request $request): Response
    {
        $mode = $request->input('mode', 'monthly');
        if (!in_array($mode, ['monthly', 'weekly', 'daily'], true)) {
            $mode = 'monthly';
        }

        $currentYear = (int) now()->year;
        $currentMonth = (int) now()->month;

        $year = (int) $request->input('year', $currentYear ?: 2026);
        // Prevent future years (e.g. 2027)
        if ($year > $currentYear) {
            $year = $currentYear;
        }
        $month = (int) $request->input('month', $currentMonth ?: 10);
        $startMonth = (int) $request->input('start_month', 1);
        $endMonth = (int) $request->input('end_month', max(10, $currentMonth));
        $dayRange = $request->input('day_range', '1-15');
        $taxRate = (float) $request->input('tax_rate', 0.0);
        $lang = $request->input('lang', App::getLocale() ?: 'es');

        if (!in_array($lang, ['es', 'en'], true)) {
            $lang = App::getLocale() ?: 'es';
        }

        App::setLocale($lang);

        $report = $this->fuelReportService->generateReport(
            $year,
            $mode,
            $month,
            $startMonth,
            $endMonth,
            $dayRange,
            $taxRate,
            $lang
        );

        $suffix = match ($mode) {
            'weekly' => "Semanal_M{$month}",
            'daily' => "Diario_M{$month}_{$dayRange}",
            default => "Mensual_M{$startMonth}-M{$endMonth}",
        };

        $filename = "Detalle_Combustible_LANCOMTRI_{$year}_{$suffix}_{$lang}.pdf";

        $pdf = Pdf::loadView('reports.pdf.fuel-landscape', compact('report', 'lang', 'mode'))
            ->setPaper('a4', 'landscape')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);

        if ($request->has('preview') || $request->has('view')) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }
}
