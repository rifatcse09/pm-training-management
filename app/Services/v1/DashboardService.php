<?php

namespace App\Services\v1;

use App\Models\EmployeeTraining;
use App\Models\GroupTraining;
use App\Models\Training;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function __construct()
    {
        //
    }

    /**
     * Get total trainings this year
     *
     * @return array
     */
    public function getTrainingStatsThisYear(): array
    {
        $currentYear = Carbon::now()->year;

        // Get unique training sessions using Eloquent
        $uniqueTrainingSessions = EmployeeTraining::select('employee_training.training_id', 'group_trainings.start_date', 'group_trainings.end_date')
            ->join('group_trainings', 'employee_training.group_training_id', '=', 'group_trainings.id')
            ->whereYear('employee_training.created_at', $currentYear)
            ->groupBy('group_trainings.start_date', 'group_trainings.end_date', 'employee_training.training_id')
            ->orderBy('employee_training.training_id')
            ->orderBy('group_trainings.start_date')
            ->orderBy('group_trainings.end_date')
            ->get();

        $totalTrainings = $uniqueTrainingSessions->count();

        // Count local trainings (type 1)
        $localTrainings = $uniqueTrainingSessions->filter(function($session) {
            $training = Training::find($session->training_id);
            return $training && $training->type == 1;
        })->count();

        // Count remote/front trainings (type 2)
        $remoteTrainings = $uniqueTrainingSessions->filter(function($session) {
            $training = Training::find($session->training_id);
            return $training && $training->type == 2;
        })->count();

        return [
            'total_trainings_this_year' => $totalTrainings ?? 0,
            'local_trainings' => $localTrainings ?? 0,
            'remote_trainings' => $remoteTrainings ?? 0,
            'year' => $currentYear
        ];
    }

    /**
     * Get training counts by group
     *
     * @return array
     */
    public function getTrainingCountsByGroup(): array
    {
        $currentYear = Carbon::now()->year;

        return EmployeeTraining::whereYear('created_at', $currentYear)
            ->selectRaw('group_id, COUNT(DISTINCT DATE(created_at)) as training_count')
            ->groupBy('group_id')
            ->get()
            ->toArray();
    }

    /**
     * Get dashboard statistics
     *
     * @return array
     */
    public function getDashboardStats(): array
    {
        $trainingStats = $this->getTrainingStatsThisYear();
        $groupStats = $this->getTrainingCountsByGroup();

        return [
            'training_statistics' => $trainingStats,
            'training_by_groups' => $groupStats
        ];
    }

    /**
     * Get recent activities
     *
     * @return array
     */
    public function getRecentActivities(): array
    {
        return [
            // Add your recent activities logic here
        ];
    }
}