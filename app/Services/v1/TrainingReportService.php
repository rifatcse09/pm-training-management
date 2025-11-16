<?php

namespace App\Services\v1;

use Carbon\Carbon;
use App\Models\GroupTraining;
use App\Enums\WorkingPlaceEnum;
use App\Enums\SubjectGradeMappingEnum;
use Illuminate\Support\Facades\Log;

class TrainingReportService
{

    public function generateGradeWiseAllEmployeeReport(array $filters)
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
        $filterEnd   = !empty($filters['end_date'])   ? Carbon::parse($filters['end_date'])->toDateString() : null;

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
            'employeeTrainings' => function ($et) use ($filters) {
                // Dynamic designation filtering based on subject
                $et->where(function ($q) use ($filters) {
                    if (!empty($filters['subject'])) {
                        $subject = (int)$filters['subject'];
                        $this->applyDesignationFilter($q, $subject);
                    } else {
                        // Default to 9th grade if no subject provided
                        $this->applyDesignationRangeFilter($q, range(1, 29));
                    }
                });

                // Add training_id filter if provided
                if (!empty($filters['training_id'])) {
                    $et->where('training_id', $filters['training_id']);
                }

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
                $designationName = $et->designation->name
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

    /**
     * Apply designation filter based on subject using enum
     */
    private function applyDesignationFilter($query, int $subject)
    {
        $subjectEnum = SubjectGradeMappingEnum::fromInt($subject);
        
        if (!$subjectEnum) {
            // Default to 9th grade if subject not found
            $this->applyDesignationRangeFilter($query, range(1, 29));
            return;
        }

        $designationRange = $subjectEnum->getDesignationRange();

        if ($designationRange === '>45') {
            // Special case for designations greater than 45
            $query->where('designation_id', '>', 45)
                  ->orWhereHas('designation', function ($desigQuery) {
                      $desigQuery->where('id', '>', 45);
                  });
        } elseif (!empty($designationRange)) {
            // Normal range filtering
            $this->applyDesignationRangeFilter($query, $designationRange);
        }
        // If empty array (like PROJECT_BASED), no designation filter applied
    }

    /**
     * Apply designation range filter (helper method to reduce repetition)
     */
    private function applyDesignationRangeFilter($query, array $range)
    {
        $query->whereIn('designation_id', $range)
              ->orWhereHas('designation', function ($desigQuery) use ($range) {
                  $desigQuery->whereIn('id', $range);
              });
    }

    public function singleEmployeeWiseReport(array $filters)
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
        $filterEnd   = !empty($filters['end_date'])   ? Carbon::parse($filters['end_date'])->toDateString() : null;

        if ($filterStart && $filterEnd) {
            $query->where('start_date', '<=', $filterEnd)
                ->where('end_date', '>=', $filterStart);
        } elseif ($filterStart) {
            $query->where('end_date', '>=', $filterStart);
        } elseif ($filterEnd) {
            $query->where('start_date', '<=', $filterEnd);
        }

        // Filter by employee_id
        if (!empty($filters['employee_id'])) {
            $query->whereHas('employeeTrainings', function ($q) use ($filters) {
                $q->where('employee_id', $filters['employee_id']);
            });
        }


        // Eager load only the specific employee's data
        $query->with([
            'employeeTrainings' => function ($et) use ($filters) {
                $et->where('employee_id', $filters['employee_id']) // Ensure only the specific employee's data is loaded
                    ->with([
                        'employee.designation',
                        'designation',
                        'training.organizer',
                        'training.countries',
                    ]);
            },
        ])->orderBy('start_date', 'asc');

        $groupTrainings = $query->get();

        // Build flat rows with pre-formatted strings
        $reportRows = $groupTrainings->flatMap(function ($gt) {
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

                $designationName = $et->designation->name
                    ?? 'N/A';

                $totalDays = $gt->total_days
                    ?? ($gt->start_date && $gt->end_date
                        ? $gt->start_date->diffInDays($gt->end_date) + 1
                        : null);

                return [
                    'group_training_id' => $gt->id,
                    'start_date'        => $gtStart,
                    'end_date'          => $gtEnd,
                    'total_days'        => $totalDays ?? 'N/A',
                    'file_name'         => $gt->file_name,
                    'file_link'         => $gt->file_link,
                    'employee_id'       => $et->employee?->id,
                    'employee_name'     => $et->employee?->name,
                    'mobile'            => $et->employee?->mobile ?? 'N/A',
                    'designation'       => $designationName,
                    'working_place'     => $et->working_place !== null
                        ? WorkingPlaceEnum::getNameById((int)$et->working_place)
                        : ($et->employee?->working_place_name ?? 'N/A'),
                    'training_id'       => $t?->id,
                    'training_name'     => $t?->name ?? 'N/A',
                    'training_type'     => $trainingType,
                    'training_countries'=> $countryList,
                    'organizer_name'    => $t?->organizer?->name ?? 'N/A',
                ];
            });
        })->values();

        Log::info('Single Employee Report Rows:', $reportRows->toArray());

        return $reportRows;
    }
}