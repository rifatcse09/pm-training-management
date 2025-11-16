<?php

namespace App\Http\Controllers\Api\v1;

use Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\v1\TrainingReportService;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;
use App\Enums\SubjectGradeMappingEnum;

class TrainingReportController extends Controller
{
    protected $reportService;

    public function __construct(TrainingReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function generateReport(Request $request)
    {
        $filters = $request->only(['subject', 'employee_id','training_id', 'fiscal_years', 'start_date', 'end_date']);

        try {
            $subject = (int)$filters['subject'];
            $subjectEnum = SubjectGradeMappingEnum::fromInt($subject);

            if (!$subjectEnum) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid subject parameter provided.',
                ], 400);
            }

            if ($subjectEnum->isGradeWiseAllReport()) {
                return $this->generateGradeWiseAllEmployeeReport($filters);
            } elseif ($subjectEnum->isSingleEmployeeReport()) {
                return $this->generateSingleEmployeeBasedReport($filters);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unsupported report type.',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function generateGradeWiseAllEmployeeReport(array $filters)
    {
        $reportData = $this->reportService->generateGradeWiseAllEmployeeReport($filters);
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