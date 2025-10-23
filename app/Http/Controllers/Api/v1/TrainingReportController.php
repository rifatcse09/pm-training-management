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
        $filters = $request->only(['subject', 'employee_id', 'fiscal_years', 'start_date', 'end_date']);

        try {
            if ($filters['subject'] == 1) {
                return $this->generateNineGradeEmployeeReport($filters);
            }  elseif ($filters['subject'] == 2) {
                return $this->generateSingleNineEmployeeBasedReport($filters);
            }
            else {
                return $this->generateSingleEmployeeBasedReport($filters);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function generateNineGradeEmployeeReport(array $filters)
    {
        $reportData = $this->reportService->nineGradeEmployeeBasedReport($filters);
        $pdf = PDF::loadView('pdf.reports.employee-training', [
            'reportData' => $reportData,
            'filters' => $filters,
            'generatedAt' => now(),
        ], [], ['mode' => 'utf-8', 'format' => 'A4-L']);

        $filename = 'employee-training-report-' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }

    private function generateSingleNineEmployeeBasedReport(array $filters)
    {
        $reportData = $this->reportService->nineGradeSingleEmployeeWiseReport($filters);
        $pdf = PDF::loadView('pdf.reports.single-employee-training', [
            'reportData' => $reportData,
            'filters' => $filters,
            'generatedAt' => now(),
        ], [], ['mode' => 'utf-8', 'format' => 'A4-L']);

        $filename = 'single-employee-training-report-' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }

    private function generateEmployeeBasedReport(array $filters)
    {
        $reportData = $this->reportService->employeeBasedReport($filters);
        $pdf = PDF::loadView('pdf.reports.employee-training', [
            'reportData' => $reportData,
            'filters' => $filters,
            'generatedAt' => now(),
        ], [], ['mode' => 'utf-8', 'format' => 'A4-L']);

        $filename = 'employee-training-report-' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }

    private function generateSingleEmployeeBasedReport(array $filters)
    {
        $reportData = $this->reportService->singleEmployeeWiseReport($filters);
        $pdf = PDF::loadView('pdf.reports.single-employee-training', [
            'reportData' => $reportData,
            'filters' => $filters,
            'generatedAt' => now(),
        ], [], ['mode' => 'utf-8', 'format' => 'A4-L']);

        $filename = 'single-employee-training-report-' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }
}