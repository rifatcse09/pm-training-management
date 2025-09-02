<?php

namespace App\Services\v1;

use Carbon\Carbon;
use App\Models\GroupTraining;
use App\Enums\WorkingPlaceEnum;

class TrainingReportService
{
    public function generateReport(array $filters)
    {
        $query = GroupTraining::query();

        // Fiscal years (overlap)
        if (!empty($filters['fiscal_years'])) {
            $query->where(function ($q) use ($filters) {
                foreach ($filters['fiscal_years'] as $fy) {
                    $yearRange = is_array($fy) && isset($fy['value']) ? $fy['value'] : $fy;
                    if (is_string($yearRange) && str_contains($yearRange, '-')) {
                        [$startYear, $endYear] = array_map('trim', explode('-', $yearRange));
                        $fyStart = "{$startYear}-07-01";
                        $fyEnd   = "{$endYear}-06-30";
                        $q->orWhere(function ($w) use ($fyStart, $fyEnd) {
                            $w->where('start_date', '<=', $fyEnd)
                            ->where('end_date', '>=', $fyStart);
                        });
                    }
                }
            });
        }

        // Calendar date range (overlap)
        $filterStart = !empty($filters['start_date']) ? Carbon::parse($filters['start_date'])->toDateString() : null;
        $filterEnd   = !empty($filters['end_date'])   ? Carbon::parse($filters['end_date'])->toDateString()   : null;

        if ($filterStart && $filterEnd) {
            $query->where('start_date', '<=', $filterEnd)
                ->where('end_date', '>=', $filterStart);
        } elseif ($filterStart) {
            $query->where('end_date', '>=', $filterStart);
        } elseif ($filterEnd) {
            $query->where('start_date', '<=', $filterEnd);
        }

        // Eager load
        $query->with([
            'employeeTrainings' => function ($et) {
                $et->with([
                    'employee.designation',
                    'designation', // optional: if designation_id is on employee_trainings
                    'training.organizer',
                    'training.countries',
                ]);
            },
        ])->orderBy('start_date', 'asc');

        $groupTrainings = $query->get();

        // Build flat rows with pre-formatted strings
        $reportRows = $groupTrainings->flatMap(function ($gt) {
            // thanks to casts, these are Carbon|null
            $gtStart = $gt->start_date?->format('d/m/Y');
            $gtEnd   = $gt->end_date?->format('d/m/Y');

            return $gt->employeeTrainings->map(function ($et) use ($gt, $gtStart, $gtEnd) {
                $t = $et->training;

                $trainingType = ((int)($t->type ?? 1) === 2)
                    ? 'বৈদেশিক প্রশিক্ষণ'
                    : 'স্থানীয় প্রশিক্ষণ';

                $countryList = ((int)($t->type ?? 1) === 2)
                    ? $t->countries->pluck('name')->implode(', ')
                    : 'N/A';

                // designation from assignment first; fallback to employee
                $designationName = $et->designation->title
                    ?? $et->employee?->designation?->name
                    ?? 'N/A';

                // compute total days if not stored
                $totalDays = $gt->total_days
                    ?? ($gt->start_date && $gt->end_date
                        ? $gt->start_date->diffInDays($gt->end_date) + 1
                        : null);

                return [
                    // GroupTraining window (already formatted)
                    'group_training_id' => $gt->id,
                    'start_date'        => $gtStart,
                    'end_date'          => $gtEnd,
                    'total_days'        => $totalDays ?? 'N/A',
                    'file_name'         => $gt->file_name,
                    'file_link'         => $gt->file_link,

                    // Employee assignment
                    'employee_id'       => $et->employee?->id,
                    'employee_name'     => $et->employee?->name,
                    'mobile'            => $et->employee?->mobile ?? 'N/A',
                    'designation'       => $designationName,

                    // choose which working_place to show:
                    // from assignment row (historical snapshot)
                    'working_place'     => $et->working_place !== null
                        ? WorkingPlaceEnum::getNameById((int)$et->working_place)
                        : ($et->employee?->working_place_name ?? 'N/A'),

                    // Training details
                    'training_id'       => $t?->id,
                    'training_name'     => $t?->name ?? 'N/A',
                    'training_type'     => $trainingType,
                    'training_countries'=> $countryList,
                    'organizer_name'    => $t?->organizer?->name ?? 'N/A',
                ];
            });
        })->values();

        return $reportRows;
    }
}
