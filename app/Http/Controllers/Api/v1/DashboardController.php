<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\HttpStatus;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\v1\DashboardService;

class DashboardController extends Controller
{
    protected $service;

    public function __construct(DashboardService $service)
    {
        $this->service = $service;
    }

    /**
     * Get dashboard statistics
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $stats = $this->service->getDashboardStats();

            return response()->json([
                'status' => HttpStatus::OK,
                'message' => 'Dashboard statistics retrieved successfully',
                'data' => $stats
            ], HttpStatus::OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => HttpStatus::NOT_FOUND,
                'message' => 'Failed to retrieve dashboard statistics',
                'error' => $e->getMessage()
            ], HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get training statistics for this year
     *
     * @return JsonResponse
     */
    public function trainingStats(): JsonResponse
    {
        try {
            $stats = $this->service->getTrainingStatsThisYear();

            return response()->json([
                'status' => HttpStatus::OK,
                'message' => 'Training statistics retrieved successfully',
                'data' => $stats
            ], HttpStatus::OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => HttpStatus::NOT_FOUND,
                'message' => 'Failed to retrieve training statistics',
                'error' => $e->getMessage()
            ], HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }
}