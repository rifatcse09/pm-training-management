<?php

namespace App\Services\v1;

use App\Models\Training;

class TrainingReportService
{
    public function generateReport(array $filters)
    {
        $query = Training::query();

        if (!empty($filters['subject'])) {
            $query->where('subject', $filters['subject']);
        }

        if (!empty($filters['fiscal_years'])) {
            $query->whereIn('fiscal_year', $filters['fiscal_years']);
        }

        if (!empty($filters['start_date'])) {
            $query->where('start_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->where('end_date', '<=', $filters['end_date']);
        }

        return $query->get();
    }
}
