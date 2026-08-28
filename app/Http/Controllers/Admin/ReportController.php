<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    public function index(Request $request): Response
    {
        $filters = $request->only(['start_date', 'end_date']);

        return Inertia::render('Admin/Reports/Index', [
            'filters' => $filters,
            'summary' => $this->reportService->getSummary($filters),
            'dailySales' => $this->reportService->getDailySales($filters),
            'topGames' => $this->reportService->getTopGames($filters),
        ]);
    }

    public function export(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date']);

        $summary = $this->reportService->getSummary($filters);
        $dailySales = $this->reportService->getDailySales($filters);
        $topGames = $this->reportService->getTopGames($filters);

        $pdf = Pdf::loadView('pdf.report', compact('summary', 'dailySales', 'topGames', 'filters'));

        return $pdf->download('laporan-penjualan.pdf');
    }
}
