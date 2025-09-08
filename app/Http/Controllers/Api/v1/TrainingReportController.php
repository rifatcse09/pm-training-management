<?php

namespace App\Http\Controllers\Api\v1;

use Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\v1\TrainingReportService;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class TrainingReportController extends Controller
{
    protected $reportService;

    public function __construct(TrainingReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function generateReport(Request $request)
    {
        $filters = $request->only(['subject', 'fiscal_years', 'start_date', 'end_date']);

        try {
            $reportData = $this->reportService->generateReport($filters);
            $pdf = PDF::loadView('pdf.reports.employee-training', [
                'reportData' => $reportData,
                'filters' => $filters,
                'generatedAt' => now(),
            ], [], ['mode' => 'utf-8', 'format' => 'A4-L']);

            $filename = 'training-report-' . now()->format('Y-m-d_H-i-s') . '.pdf';

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', "attachment; filename=\"$filename\"");
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
