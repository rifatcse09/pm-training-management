<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrainingAssignmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'training_name' => $this->training->name,
            'employee_name' => $this->employee->name,
            'assigned_date' => $this->assigned_at->toDateString(),
            'assigned_by' => $this->assigned_by,
        ];
    }
}
