<?php

namespace App\Services\v1;

use App\Models\EmployeeTraining;
use Illuminate\Support\Facades\DB;

class MonthlyChartService
{
    public function getMonthlyData($year, $designationType = 'ninth_grade')
    {
        return $this->getMonthlyEmployeesTrainedByDesignation($year, $designationType);
    }

    private function getMonthlyEmployeesTrainedByDesignation($year, $designationType)
    {
        $query = EmployeeTraining::select(
            DB::raw('MONTH(group_trainings.start_date) as month'),
            DB::raw('COUNT(employee_training.employee_id) as count')
        )
        ->join('group_trainings', 'employee_training.group_training_id', '=', 'group_trainings.id');

        if ($designationType === 'ninth_grade') {
            $query->whereBetween('employee_training.designation_id', [1, 29]);
            $seriesName = 'Employees Trained (9th Grade)';
        } else {
            $query->where('employee_training.designation_id', '>', 29);
            $seriesName = 'Employees Trained (Upper Grade)';
        }

        // Add query logging
        \Log::info('Query SQL: ' . $query->toSql());
        \Log::info('Query Bindings: ', $query->getBindings());

        // Check if start_date is not null
        $query->whereNotNull('group_trainings.start_date');

        $monthlyData = $query
            ->whereYear('group_trainings.start_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        \Log::info('Monthly Data Result: ', $monthlyData);
        \Log::info('Year: ' . $year);

        // Check total records in table
        $totalRecords = EmployeeTraining::count();
        \Log::info('Total EmployeeTraining records: ' . $totalRecords);

        // Check records for the year with join
        $yearRecords = EmployeeTraining::join('group_trainings', 'employee_training.group_training_id', '=', 'group_trainings.id')
            ->whereYear('group_trainings.start_date', $year)
            ->count();
        \Log::info('Records for year ' . $year . ': ' . $yearRecords);

        return $this->formatMonthlyData($monthlyData, $seriesName);
    }

    private function formatMonthlyData($data, $seriesName = 'Monthly Data')
    {
        $formattedData = [];

        // Initialize all 12 months with 0
        for ($i = 1; $i <= 12; $i++) {
            $formattedData[] = $data[$i] ?? 0;
        }

        return [
            'series' => [
                [
                    'name' => $seriesName,
                    'data' => $formattedData
                ]
            ],
            'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        ];
    }
}