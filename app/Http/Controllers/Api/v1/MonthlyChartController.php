<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\v1\MonthlyChartService;

class MonthlyChartController extends Controller
{
    protected $chartService;

    public function __construct(MonthlyChartService $chartService)
    {
        $this->chartService = $chartService;
    }

    public function getMonthlyData(Request $request)
    {
        try {
            $year = $request->get('year', date('Y'));
            $designationType = $request->get('designation_type', 'ninth_grade'); // ninth_grade, upper_grade, all

            $data = $this->chartService->getMonthlyData($year, $designationType);

            return response()->json([
                'success' => true,
                'data' => $data,
                'year' => $year,
                'designation_type' => $designationType
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch monthly data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}